<?php

namespace App\Services\AI;

use App\Models\Contact;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Str;

class ConversationMemoryService
{
    /**
     * Build standard multi-turn LLM message payload including system prompt,
     * persistent facts, summary, and chronological history.
     */
    public function buildMessagesPayload(
        Conversation $conversation,
        string $baseSystemPrompt,
        string $latestIncomingMessage,
        int $maxRecentMessages = 10
    ): array {
        $messagesPayload = [];

        // 1. Build enriched System Prompt with Persistent Memory & Extracted Facts
        $systemContent = $baseSystemPrompt;

        $facts = $conversation->extracted_facts ?? [];
        if (! empty($facts)) {
            $systemContent .= "\n\n=== PERSISTENT CUSTOMER FACTS (ALREADY VERIFIED - DO NOT RE-ASK) ===";
            if (! empty($facts['service'])) {
                $systemContent .= "\n- Requested Service/Product: {$facts['service']}";
            }
            if (! empty($facts['budget'])) {
                $systemContent .= "\n- Customer Stated Budget: {$facts['budget']}";
            }
            if (! empty($facts['business_type'])) {
                $systemContent .= "\n- Customer Business Type: {$facts['business_type']}";
            }
            if (! empty($facts['location'])) {
                $systemContent .= "\n- Location/Market: {$facts['location']}";
            }
            if (! empty($facts['language'])) {
                $systemContent .= "\n- Preferred Language: {$facts['language']}";
            }
            if (! empty($facts['key_notes'])) {
                $systemContent .= "\n- Specific Notes: {$facts['key_notes']}";
            }
            $systemContent .= "\nCRITICAL RULE: The customer has ALREADY provided these facts. Never ask for them again. Always build upon them.";
        }

        if (! empty($conversation->summary)) {
            $systemContent .= "\n\n=== CONVERSATION SUMMARY SO FAR ===\n".$conversation->summary;
        }

        $messagesPayload[] = [
            'role' => 'system',
            'content' => $systemContent,
        ];

        // 2. Fetch past chronological messages (isolated strictly to this conversation)
        $pastMessages = Message::where('conversation_id', $conversation->id)
            ->whereIn('sender_type', ['contact', 'ai', 'human'])
            ->whereNotNull('content')
            ->where('content', '!=', '')
            ->orderBy('created_at', 'asc')
            ->get();

        // Slice to the most recent messages to fit token window
        if ($pastMessages->count() > $maxRecentMessages) {
            $pastMessages = $pastMessages->slice(-$maxRecentMessages);
        }

        foreach ($pastMessages as $msg) {
            if ($msg->sender_type === 'contact') {
                $messagesPayload[] = [
                    'role' => 'user',
                    'content' => $msg->content,
                ];
            } elseif ($msg->sender_type === 'human') {
                // Human agent takeover message: preserved as assistant with clear rep attribution
                $messagesPayload[] = [
                    'role' => 'assistant',
                    'content' => '[Sales Representative]: '.$msg->content,
                ];
            } else {
                $messagesPayload[] = [
                    'role' => 'assistant',
                    'content' => $msg->content,
                ];
            }
        }

        // 3. Ensure the current incoming message is the final user turn if not already appended
        $lastRole = end($messagesPayload)['role'] ?? null;
        $lastContent = end($messagesPayload)['content'] ?? null;

        if ($lastRole !== 'user' || trim($lastContent ?? '') !== trim($latestIncomingMessage)) {
            $messagesPayload[] = [
                'role' => 'user',
                'content' => $latestIncomingMessage,
            ];
        }

        return $messagesPayload;
    }

    /**
     * Extract structured customer facts (service, budget, business, language) from text
     */
    public function extractFacts(string $text, array $existingFacts = []): array
    {
        $facts = $existingFacts;
        $lower = Str::lower($text);

        // 1. Detect Requested Service / Product
        if (empty($facts['service'])) {
            if (preg_match('/\b(website|web|web design|store|ecommerce|e-commerce|web app|landing page|वेबसाइट)\b/u', $lower)) {
                $facts['service'] = 'Website Development';
            } elseif (preg_match('/\b(app|mobile app|android|ios|एप्लिकेशन)\b/u', $lower)) {
                $facts['service'] = 'Mobile App Development';
            } elseif (preg_match('/\b(software|saas|crm|erp|सॉफ्टवेयर)\b/u', $lower)) {
                $facts['service'] = 'Custom Software Development';
            } elseif (preg_match('/\b(seo|digital marketing|marketing|रैंकिंग)\b/u', $lower)) {
                $facts['service'] = 'SEO & Digital Marketing';
            } elseif (preg_match('/\b(garlic|lahsun|cloves|लहसुन|मंडी)\b/u', $lower)) {
                $facts['service'] = 'Himachal Garlic Wholesale';
            }
        }

        // 2. Detect Budget Mentions (e.g., 15000, 15k, ₹20,000, 25 hazar, 1.5 lakh)
        if (preg_match('/(?:budget|₹|rs\.?|inr|बजट|लगभग|under|around|upto)\s*[:=]?\s*([0-9]+(?:,[0-9]+)*(?:\s*(?:k|thousand|lakh|hazar|हज़ार))?)/iu', $text, $m)) {
            $facts['budget'] = trim($m[1]);
        } elseif (preg_match('/\b([0-9]{4,6})\b/', $text, $m) && (str_contains($lower, 'budget') || str_contains($lower, 'rupee') || str_contains($lower, 'rs') || str_contains($lower, 'खर्च') || str_contains($lower, 'de sakta') || str_contains($lower, 'around'))) {
            $facts['budget'] = trim($m[1]);
        } elseif (preg_match('/\b([0-9]{1,3}\s*k)\b/i', $text, $m)) {
            $facts['budget'] = trim($m[1]);
        }

        // 3. Detect Language Preference
        if (preg_match('/[\x{0900}-\x{097F}]/u', $text)) {
            $facts['language'] = 'Hindi';
        } elseif (preg_match('/\b(kya|hai|chahiye|kitna|hoga|banao|bhai|sir|namaste|karo|kaise|sakte)\b/i', $lower)) {
            $facts['language'] = 'Hinglish';
        } elseif (empty($facts['language'])) {
            $facts['language'] = 'English';
        }

        // 4. Detect Business Type / Category
        if (empty($facts['business_type'])) {
            if (preg_match('/\b(gym|clinic|doctor|lawyer|retail|shop|store|restaurant|hotel|cafe|agency|school|coaching|real estate)\b/i', $text, $m)) {
                $facts['business_type'] = ucfirst(trim($m[1]));
            } elseif (preg_match('/\b(mandi|trader|vyapari|व्यापारी|दुकान|wholesale)\b/u', $lower, $m)) {
                $facts['business_type'] = 'Mandi / Wholesale Business';
            }
        }

        // 5. Detect Location
        if (empty($facts['location'])) {
            if (preg_match('/\b(delhi|mumbai|bangalore|ncr|chandigarh|shimla|jaipur|punjab|haryana|up|lucknow|solan|kullu)\b/i', $text, $m)) {
                $facts['location'] = ucfirst(trim($m[1]));
            }
        }

        return $facts;
    }

    /**
     * Update persistent summary and extracted facts in database after every interaction
     */
    public function updateConversationMemory(Conversation $conversation, string $incomingText, string $aiReply): void
    {
        // 1. Extract and merge facts
        $currentFacts = $conversation->extracted_facts ?? [];
        $updatedFacts = $this->extractFacts($incomingText, $currentFacts);

        // 2. Generate concise persistent summary
        $summaryParts = [];
        if (! empty($updatedFacts['service'])) {
            $summaryParts[] = 'Service: '.$updatedFacts['service'];
        }
        if (! empty($updatedFacts['budget'])) {
            $summaryParts[] = 'Budget: '.$updatedFacts['budget'];
        }
        if (! empty($updatedFacts['business_type'])) {
            $summaryParts[] = 'Business: '.$updatedFacts['business_type'];
        }
        if (! empty($updatedFacts['location'])) {
            $summaryParts[] = 'Location: '.$updatedFacts['location'];
        }

        $summaryText = ! empty($summaryParts)
            ? 'Customer Inquired for '.implode(', ', $summaryParts).'.'
            : ($conversation->summary ?: 'Customer started sales enquiry.');

        // 3. Persist memory to Conversation record
        $conversation->update([
            'extracted_facts' => $updatedFacts,
            'summary' => $summaryText,
        ]);

        // 4. Sync extracted facts to Contact record (CRM integration)
        $contact = $conversation->contact;
        if ($contact) {
            $contactUpdates = [];
            if (! empty($updatedFacts['service']) && empty($contact->requirement)) {
                $contactUpdates['requirement'] = $updatedFacts['service'];
            }
            if (! empty($updatedFacts['budget']) && empty($contact->budget)) {
                $contactUpdates['budget'] = $updatedFacts['budget'];
            }
            if (! empty($updatedFacts['location']) && empty($contact->location)) {
                $contactUpdates['location'] = $updatedFacts['location'];
            }
            if (! empty($updatedFacts['budget']) || ! empty($updatedFacts['service'])) {
                if ($contact->lead_status === 'new') {
                    $contactUpdates['lead_status'] = 'interested';
                }
            }

            if (! empty($contactUpdates)) {
                $contact->update($contactUpdates);
            }
        }
    }
}
