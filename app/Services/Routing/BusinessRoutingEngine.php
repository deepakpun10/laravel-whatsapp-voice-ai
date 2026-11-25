<?php

namespace App\Services\Routing;

use App\Models\Business;
use App\Models\BusinessRoutingRule;
use App\Models\Contact;
use App\Models\RoutingLog;
use App\Models\WhatsappAccount;
use Illuminate\Support\Str;

class BusinessRoutingEngine
{
    /**
     * Route an incoming message or enquiry string.
     *
     * Routing Priority:
     * 1. Check whether contact already has an assigned business.
     * 2. Evaluate configured business keywords and phrases.
     * 3. Calculate match score and confidence per business.
     * 4. Check confidence threshold.
     * 5. Check for ambiguity (conflicting businesses with high match).
     * 6. Safe fallback: unassigned review.
     */
    public function route(string $messageText, ?Contact $contact = null, ?WhatsappAccount $whatsappAccount = null): RoutingResult
    {
        $cleanText = Str::lower(trim($messageText));

        // 1. Check existing contact assignment
        if ($contact && $contact->business_id && $contact->business) {
            $result = new RoutingResult(
                business: $contact->business,
                confidence: 1.0,
                status: 'routed',
                reason: 'Existing confirmed business assignment for contact '.$contact->name,
                matchedKeywords: ['known_contact'],
                details: ['contact_id' => $contact->id]
            );

            $this->logRouting($result, $cleanText, $whatsappAccount, $contact);

            return $result;
        }

        // 2. Fetch all active business routing rules
        $rules = BusinessRoutingRule::with('business')
            ->where('is_active', true)
            ->get();

        $businessScores = [];
        $businessMatches = [];

        foreach ($rules as $rule) {
            $bizId = $rule->business_id;
            if (! isset($businessScores[$bizId])) {
                $businessScores[$bizId] = 0;
                $businessMatches[$bizId] = [];
            }

            $weight = match ($rule->priority) {
                'high' => 1.5,
                'low' => 0.7,
                default => 1.0,
            };

            foreach ($rule->keywords as $keyword) {
                $kw = Str::lower(trim($keyword));
                if (empty($kw)) {
                    continue;
                }

                // Substring or word match
                if (str_contains($cleanText, $kw)) {
                    $businessScores[$bizId] += (1.0 * $weight);
                    $businessMatches[$bizId][] = $kw;
                }
            }
        }

        // Filter businesses that had matches
        $matchedBizIds = array_keys(array_filter($businessScores, fn ($s) => $s > 0));

        // Scenario A: No matches at all
        if (empty($matchedBizIds)) {
            $result = new RoutingResult(
                business: null,
                confidence: 0.0,
                status: 'unassigned_review',
                reason: 'No configured keywords matched the enquiry. Sent to Unassigned Review.',
                matchedKeywords: []
            );

            $this->logRouting($result, $cleanText, $whatsappAccount, $contact);

            return $result;
        }

        // Scenario B: Ambiguous / multiple businesses matched
        if (count($matchedBizIds) > 1) {
            arsort($businessScores);
            $scores = array_values($businessScores);

            // If top score is not significantly higher than second (within 1.0 score difference), mark ambiguous
            if (($scores[0] - $scores[1]) < 1.0) {
                $competingNames = Business::whereIn('id', $matchedBizIds)->pluck('name')->implode(', ');
                $result = new RoutingResult(
                    business: null,
                    confidence: 0.50,
                    status: 'ambiguous',
                    reason: "Ambiguous intent: multiple businesses matched ({$competingNames}). Safe fallback to Unassigned Review.",
                    matchedKeywords: array_merge(...array_values($businessMatches))
                );

                $this->logRouting($result, $cleanText, $whatsappAccount, $contact);

                return $result;
            }
        }

        // Scenario C: Clear winner
        arsort($businessScores);
        $topBizId = (int) array_key_first($businessScores);
        $topScore = $businessScores[$topBizId];
        $matchedKeywords = array_unique($businessMatches[$topBizId]);

        // Base confidence starts at 0.85 for 1 keyword, 0.95 for 2+, capped at 0.99
        $confidence = min(0.85 + (count($matchedKeywords) - 1) * 0.05, 0.98);

        // Fetch winning business rule's min_confidence
        $topRule = $rules->where('business_id', $topBizId)->sortByDesc('min_confidence')->first();
        $minConfidence = $topRule ? (float) $topRule->min_confidence : 0.80;

        $winningBusiness = Business::find($topBizId);

        if ($confidence >= $minConfidence && $winningBusiness) {
            $result = new RoutingResult(
                business: $winningBusiness,
                confidence: $confidence,
                status: 'routed',
                reason: "Successfully matched business '{$winningBusiness->name}' with ".count($matchedKeywords).' keyword(s).',
                matchedKeywords: $matchedKeywords,
                details: ['score' => $topScore]
            );
        } else {
            $result = new RoutingResult(
                business: null,
                confidence: $confidence,
                status: 'unassigned_review',
                reason: "Confidence ({$confidence}) below required threshold ({$minConfidence}). Routed to Unassigned Review.",
                matchedKeywords: $matchedKeywords
            );
        }

        $this->logRouting($result, $cleanText, $whatsappAccount, $contact);

        return $result;
    }

    protected function logRouting(RoutingResult $result, string $incomingText, ?WhatsappAccount $account, ?Contact $contact): void
    {
        RoutingLog::create([
            'whatsapp_account_id' => $account?->id,
            'contact_id' => $contact?->id,
            'incoming_text' => $incomingText,
            'matched_business_id' => $result->business?->id,
            'confidence' => $result->confidence,
            'status' => $result->status,
            'reason' => $result->reason,
            'details' => [
                'keywords' => $result->matchedKeywords,
                'details' => $result->details,
            ],
        ]);
    }
}
