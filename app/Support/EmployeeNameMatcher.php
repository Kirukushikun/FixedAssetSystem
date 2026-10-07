<?php

namespace App\Support;

use Illuminate\Support\Collection;

/**
 * Suggests which employee a free-text name refers to, for a person to confirm.
 * Order, case, punctuation and initials are ignored, so "Josue Blanco" can match
 * "BLANCO, JOSUE R.". Never decides on its own: ties produce no suggestion.
 */
class EmployeeNameMatcher
{
    /** @var array<int, array{employee: object, tokens: string[]}> */
    private array $candidates;

    public function __construct(Collection $employees)
    {
        $this->candidates = $employees
            ->map(fn ($e) => ['employee' => $e, 'tokens' => self::tokens($e->employee_name)])
            ->all();
    }

    /**
     * @return array{employee: ?object, confidence: string, candidates: int}
     *   confidence: exact | close | partial | ambiguous | none
     */
    public function suggest(string $name): array
    {
        $wanted = self::tokens($name);
        if (!$wanted) {
            return ['employee' => null, 'confidence' => 'none', 'candidates' => 0];
        }

        $best = [];
        $bestScore = 0;

        foreach ($this->candidates as $c) {
            $score = $this->score($wanted, $c['tokens']);
            if ($score['matched'] < min(2, count($wanted))) {
                continue;
            }
            $rank = $score['matched'] * 10 + $score['exact'];
            if ($rank > $bestScore) {
                $best = [[$c['employee'], $score]];
                $bestScore = $rank;
            } elseif ($rank === $bestScore) {
                $best[] = [$c['employee'], $score];
            }
        }

        if (!$best) {
            return ['employee' => null, 'confidence' => 'none', 'candidates' => 0];
        }

        if (count($best) > 1) {
            return ['employee' => null, 'confidence' => 'ambiguous', 'candidates' => count($best)];
        }

        [$employee, $score] = $best[0];
        $confidence = match (true) {
            $score['matched'] < count($wanted)   => 'partial',
            $score['exact'] === count($wanted)   => 'exact',
            default                              => 'close',
        };

        return ['employee' => $employee, 'confidence' => $confidence, 'candidates' => 1];
    }

    /** @return array{matched: int, exact: int} */
    private function score(array $wanted, array $have): array
    {
        $matched = 0;
        $exact = 0;

        foreach ($wanted as $w) {
            if (in_array($w, $have, true)) {
                $matched++;
                $exact++;
                continue;
            }
            foreach ($have as $h) {
                if (strlen($w) >= 5 && levenshtein($w, $h) <= 1) {
                    $matched++;
                    break;
                }
            }
        }

        return ['matched' => $matched, 'exact' => $exact];
    }

    /** Lowercased name parts, ignoring punctuation and single-letter initials. */
    public static function tokens(?string $name): array
    {
        $clean = preg_replace('/[^a-z\s]/', ' ', mb_strtolower((string) $name));

        return array_values(array_unique(array_filter(
            preg_split('/\s+/', $clean),
            fn ($t) => strlen($t) > 1
        )));
    }
}
