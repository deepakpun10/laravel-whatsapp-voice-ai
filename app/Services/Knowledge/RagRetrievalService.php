<?php

namespace App\Services\Knowledge;

use App\Models\AiAgent;
use App\Models\Business;
use App\Models\KbChunk;
use App\Models\KbDocument;
use App\Models\KnowledgeBase;
use Illuminate\Support\Str;

class RagRetrievalService
{
    /**
     * Search relevant knowledge chunks strictly scoped to the target business and optional agent
     *
     * Isolation Rules:
     * - Business A MUST NEVER retrieve documents of Business B.
     * - Only chunks belonging to knowledge bases assigned to the business (and agent if specified) are returned.
     */
    public function retrieve(string $query, int $businessId, ?int $agentId = null, int $limit = 4): array
    {
        $cleanQuery = Str::lower(trim($query));
        $terms = array_filter(explode(' ', preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $cleanQuery)));

        if (empty($terms)) {
            return [];
        }

        // 1. Get eligible Knowledge Base IDs for this business (must be active)
        $kbIdsQuery = KnowledgeBase::where('business_id', $businessId)
            ->whereNotIn('status', ['inactive', 'disabled']);

        if ($agentId) {
            $agent = AiAgent::find($agentId);
            if ($agent && $agent->knowledgeBases()->exists()) {
                $assignedKbIds = $agent->knowledgeBases()->pluck('knowledge_bases.id')->toArray();
                $kbIdsQuery->whereIn('id', $assignedKbIds);
            }
        }

        $eligibleKbIds = $kbIdsQuery->pluck('id')->toArray();

        if (empty($eligibleKbIds)) {
            return [];
        }

        // 2. Query chunks strictly matching eligible KBs
        $chunks = KbChunk::with(['document.knowledgeBase'])
            ->whereHas('document', function ($q) use ($eligibleKbIds) {
                $q->whereIn('knowledge_base_id', $eligibleKbIds)
                    ->where('status', 'indexed');
            })
            ->get();

        $scoredChunks = [];

        foreach ($chunks as $chunk) {
            $contentLower = Str::lower($chunk->content);
            $score = 0;

            foreach ($terms as $term) {
                if (strlen($term) < 2) {
                    continue;
                }
                $count = substr_count($contentLower, $term);
                if ($count > 0) {
                    $score += ($count * 1.5);
                }
            }

            if ($score > 0) {
                $scoredChunks[] = [
                    'id' => $chunk->id,
                    'document_title' => $chunk->document->title ?? 'Document',
                    'kb_name' => $chunk->document->knowledgeBase->name ?? 'KB',
                    'content' => $chunk->content,
                    'score' => round(min($score / 5.0, 1.0), 2),
                ];
            }
        }

        // Sort descending by relevance score
        usort($scoredChunks, fn ($a, $b) => $b['score'] <=> $a['score']);

        return array_slice($scoredChunks, 0, $limit);
    }

    /**
     * Index text into chunks for a document
     */
    public function processAndIndexDocument(KbDocument $document, string $rawText): int
    {
        // Simple paragraph and token-aware chunking (approx 250 words per chunk with 30-word overlap)
        $paragraphs = preg_split('/\n\s*\n/', trim($rawText));
        $chunks = [];
        $currentChunk = '';

        foreach ($paragraphs as $para) {
            $para = trim($para);
            if (empty($para)) {
                continue;
            }

            if (str_word_count($currentChunk.' '.$para) > 200 && ! empty($currentChunk)) {
                $chunks[] = trim($currentChunk);
                $currentChunk = $para;
            } else {
                $currentChunk .= (empty($currentChunk) ? '' : "\n\n").$para;
            }
        }

        if (! empty($currentChunk)) {
            $chunks[] = trim($currentChunk);
        }

        // Remove old chunks if reprocessing
        $document->chunks()->delete();

        foreach ($chunks as $index => $content) {
            KbChunk::create([
                'kb_document_id' => $document->id,
                'chunk_index' => $index + 1,
                'content' => $content,
                'token_count' => (int) (str_word_count($content) * 1.3),
            ]);
        }

        $document->update([
            'chunk_count' => count($chunks),
            'status' => 'indexed',
            'indexed_at' => now(),
        ]);

        return count($chunks);
    }
}
