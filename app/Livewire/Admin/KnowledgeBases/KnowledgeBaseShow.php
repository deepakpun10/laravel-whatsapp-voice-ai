<?php

namespace App\Livewire\Admin\KnowledgeBases;

use App\Models\KbDocument;
use App\Models\KnowledgeBase;
use App\Services\Knowledge\RagRetrievalService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.admin', ['title' => 'Knowledge Base Details', 'breadcrumb' => 'Workspace / Knowledge Bases / Details'])]
class KnowledgeBaseShow extends Component
{
    use WithFileUploads;

    public KnowledgeBase $knowledgeBase;

    // Multiple file uploads (.md, .txt, .pdf, .json, .csv)
    public array $uploadedFiles = [];

    // Direct text source addition
    public string $docTitle = '';

    public string $docText = '';

    // Active upload mode tab: 'files' or 'text'
    public string $activeInputMode = 'files';

    // Test retrieval simulator
    public string $testQuery = '';

    public ?array $retrievalResults = null;

    // Document Content Viewer / Inspector
    public ?int $selectedDocId = null;

    public ?array $previewData = null;

    public bool $showDocModal = false;

    public string $previewTab = 'content'; // 'content' or 'chunks'

    public function mount(KnowledgeBase $knowledgeBase): void
    {
        $this->knowledgeBase = $knowledgeBase->load(['business', 'documents.chunks']);
    }

    public function toggleStatus(): void
    {
        $newStatus = in_array($this->knowledgeBase->status, ['inactive', 'disabled']) ? 'active' : 'inactive';
        $this->knowledgeBase->update(['status' => $newStatus]);
        $this->knowledgeBase->status = $newStatus;
        $this->dispatch('toast', message: 'Knowledge Base status changed to '.strtoupper($newStatus).'.');
    }

    public function toggleDocumentStatus(int $docId): void
    {
        $doc = KbDocument::where('knowledge_base_id', $this->knowledgeBase->id)->findOrFail($docId);
        $doc->status = ($doc->status === 'indexed') ? 'inactive' : 'indexed';
        $doc->save();
        $this->knowledgeBase->load('documents.chunks');
        $this->dispatch('toast', message: "Document '{$doc->title}' marked as {$doc->status}.");
    }

    public function uploadFiles(RagRetrievalService $ragService): void
    {
        $this->validate([
            'uploadedFiles' => 'required|array|min:1',
            'uploadedFiles.*' => [
                'file',
                'max:20480', // 20 MB per file
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if ($value instanceof UploadedFile) {
                        $ext = strtolower($value->getClientOriginalExtension());
                        $blocked = ['php', 'phtml', 'phar', 'cgi', 'pl', 'py', 'sh', 'exe', 'bat', 'cmd', 'vbs', 'js', 'jar', 'html', 'htm'];
                        if (in_array($ext, $blocked, true)) {
                            $fail('Files with extension .'.$ext.' are not allowed for security reasons.');
                        }
                    }
                },
            ],
        ]);

        $indexedFiles = 0;
        $totalChunks = 0;

        foreach ($this->uploadedFiles as $file) {
            $originalName = $file->getClientOriginalName();
            $extension = strtolower($file->getClientOriginalExtension());

            // Defense-in-depth: Disallow executable scripts
            $disallowedExtensions = ['php', 'phtml', 'phar', 'cgi', 'pl', 'py', 'sh', 'exe', 'bat', 'cmd', 'vbs', 'js', 'jar', 'html', 'htm'];
            if (in_array($extension, $disallowedExtensions)) {
                continue;
            }

            $title = pathinfo($originalName, PATHINFO_FILENAME);
            $title = Str::substr(preg_replace('/[^\p{L}\p{N}_\-\s]/u', '', $title), 0, 150) ?: 'Document';
            $size = $file->getSize();

            // Store securely
            $storedPath = $file->store('knowledge_bases/'.$this->knowledgeBase->id, 'local');

            // Read text content
            $content = '';
            if (in_array($extension, ['md', 'markdown', 'txt', 'text', 'json', 'csv', 'html', 'log'])) {
                $content = (string) file_get_contents($file->getRealPath());
            } else {
                $content = (string) @file_get_contents($file->getRealPath());
            }

            if (empty(trim($content))) {
                $content = "Uploaded document: {$title} (Type: {$extension}, Size: {$size} bytes)";
            }

            $doc = KbDocument::create([
                'knowledge_base_id' => $this->knowledgeBase->id,
                'title' => $title,
                'source_type' => 'file',
                'file_path' => $storedPath,
                'file_type' => $extension,
                'file_size' => $size,
                'status' => 'processing',
            ]);

            $chunkCount = $ragService->processAndIndexDocument($doc, $content);
            $totalChunks += $chunkCount;
            $indexedFiles++;
        }

        $this->reset('uploadedFiles');
        $this->knowledgeBase->load('documents.chunks');

        $this->dispatch('toast', message: "{$indexedFiles} file(s) uploaded & parsed into {$totalChunks} vector chunk(s).");
    }

    public function addTextDocument(RagRetrievalService $ragService): void
    {
        $this->validate([
            'docTitle' => 'nullable|string|max:255',
            'docText' => 'required|string|min:5',
        ]);

        $title = trim($this->docTitle);
        if (empty($title)) {
            $firstLine = trim(explode("\n", strip_tags($this->docText))[0] ?? '');
            $title = ! empty($firstLine) ? Str::limit($firstLine, 50) : 'Note '.now()->format('d M, h:i A');
        }

        $doc = KbDocument::create([
            'knowledge_base_id' => $this->knowledgeBase->id,
            'title' => $title,
            'source_type' => 'text',
            'file_type' => 'txt',
            'status' => 'processing',
        ]);

        $chunkCount = $ragService->processAndIndexDocument($doc, $this->docText);

        $this->reset(['docTitle', 'docText']);
        $this->knowledgeBase->load('documents.chunks');

        $this->dispatch('toast', message: "Document '{$title}' parsed and indexed into {$chunkCount} chunk(s).");
    }

    public function deleteDocument(int $id): void
    {
        $doc = KbDocument::findOrFail($id);
        if ($doc->file_path && Storage::disk('local')->exists($doc->file_path)) {
            Storage::disk('local')->delete($doc->file_path);
        }
        $doc->chunks()->delete();
        $doc->delete();

        $this->knowledgeBase->load('documents.chunks');
        $this->dispatch('toast', message: 'Document and vector chunks removed.');
    }

    public function previewDocument(int $id): void
    {
        $doc = KbDocument::with(['chunks' => fn ($q) => $q->orderBy('chunk_index')])
            ->where('knowledge_base_id', $this->knowledgeBase->id)
            ->findOrFail($id);

        $rawContent = '';
        if ($doc->file_path && Storage::disk('local')->exists($doc->file_path)) {
            $rawContent = (string) Storage::disk('local')->get($doc->file_path);
        }

        if (empty($rawContent)) {
            $rawContent = $doc->chunks->pluck('content')->implode("\n\n---\n\n");
        }

        $this->previewData = [
            'id' => $doc->id,
            'title' => $doc->title,
            'source_type' => $doc->source_type,
            'file_type' => $doc->file_type,
            'file_size' => $doc->file_size ? number_format($doc->file_size / 1024, 1).' KB' : null,
            'chunk_count' => $doc->chunks->count(),
            'status' => $doc->status,
            'created_at' => $doc->created_at->format('d M Y, h:i A'),
            'raw_content' => $rawContent,
            'chunks' => $doc->chunks->map(fn ($c) => [
                'index' => $c->chunk_index,
                'token_count' => $c->token_count,
                'content' => $c->content,
            ])->toArray(),
        ];

        $this->selectedDocId = $id;
        $this->showDocModal = true;
        $this->previewTab = 'content';
    }

    public function closeDocModal(): void
    {
        $this->showDocModal = false;
        $this->previewData = null;
        $this->selectedDocId = null;
    }

    public function testRetrieval(RagRetrievalService $ragService): void
    {
        if (empty(trim($this->testQuery))) {
            return;
        }

        $chunks = $ragService->retrieve(
            $this->testQuery,
            $this->knowledgeBase->business_id,
            null,
            5
        );

        $this->retrievalResults = $chunks;
    }

    public function render()
    {
        return view('livewire.admin.knowledge-bases.show');
    }
}
