<?php

namespace App\Livewire\Admin\KnowledgeBases;

use App\Models\Business;
use App\Models\KbDocument;
use App\Models\KnowledgeBase;
use App\Services\Knowledge\RagRetrievalService;
use App\Services\TenantContext;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.admin', ['title' => 'Create Knowledge Base', 'breadcrumb' => 'Workspace / Knowledge Bases / Create'])]
class KnowledgeBaseCreate extends Component
{
    use WithFileUploads;

    public ?int $business_id = null;

    public string $name = '';

    public string $description = '';

    public string $status = 'active';

    public array $uploadedFiles = [];

    public function mount(): void
    {
        $activeId = TenantContext::getActiveBusinessId();
        if ($activeId) {
            $this->business_id = $activeId;
        } else {
            $this->business_id = Business::first()?->id;
        }
    }

    protected function rules(): array
    {
        return [
            'business_id' => ['required', 'exists:businesses,id'],
            'name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'in:active,inactive'],
            'uploadedFiles.*' => [
                'nullable',
                'file',
                'max:20480',
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
        ];
    }

    public function save(RagRetrievalService $ragService)
    {
        $this->validate();

        // Title is optional: if empty, auto-derive from uploaded file or fallback
        $collectionName = trim($this->name);
        if (empty($collectionName)) {
            if (! empty($this->uploadedFiles)) {
                $firstFile = $this->uploadedFiles[0];
                $collectionName = Str::headline(pathinfo($firstFile->getClientOriginalName(), PATHINFO_FILENAME));
            } else {
                $collectionName = 'Knowledge Base - '.now()->format('d M Y');
            }
        }

        $kb = KnowledgeBase::create([
            'business_id' => $this->business_id,
            'name' => $collectionName,
            'description' => $this->description,
            'status' => $this->status,
        ]);

        $indexedFiles = 0;
        $totalChunks = 0;

        if (! empty($this->uploadedFiles)) {
            foreach ($this->uploadedFiles as $file) {
                $originalName = $file->getClientOriginalName();
                $extension = strtolower($file->getClientOriginalExtension());

                // Defense-in-depth: Reject any executable or dangerous file extensions
                $disallowedExtensions = ['php', 'phtml', 'phar', 'cgi', 'pl', 'py', 'sh', 'exe', 'bat', 'cmd', 'vbs', 'js', 'jar', 'html', 'htm'];
                if (in_array($extension, $disallowedExtensions)) {
                    continue;
                }

                $title = pathinfo($originalName, PATHINFO_FILENAME);
                $title = Str::substr(preg_replace('/[^\p{L}\p{N}_\-\s]/u', '', $title), 0, 150) ?: 'Document';
                $size = $file->getSize();

                $storedPath = $file->store('knowledge_bases/'.$kb->id, 'local');

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
                    'knowledge_base_id' => $kb->id,
                    'title' => $title,
                    'source_type' => 'file',
                    'file_path' => $storedPath,
                    'file_type' => $extension,
                    'file_size' => $size,
                    'status' => 'processing',
                ]);

                $chunks = $ragService->processAndIndexDocument($doc, $content);
                $totalChunks += $chunks;
                $indexedFiles++;
            }
        }

        $message = "Knowledge Base '{$kb->name}' created successfully.";
        if ($indexedFiles > 0) {
            $message .= " {$indexedFiles} file(s) uploaded & indexed into {$totalChunks} chunk(s).";
        }

        session()->flash('success', $message);

        return $this->redirect(route('admin.knowledge-bases.show', $kb), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.knowledge-bases.create', [
            'businesses' => Business::where('status', 'active')->orderBy('name')->get(),
        ]);
    }
}
