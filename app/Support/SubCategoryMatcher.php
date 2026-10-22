<?php

namespace App\Support;

use Illuminate\Support\Collection;

/**
 * Suggests an existing sub-category for a name the system doesn't have, for a person
 * to confirm. "Gigabit Switch" → Switch, "Air Condtioner" → Air Conditioner,
 * "Plastic Chairs" → Chair. Never decides on its own: ties and weak matches give no suggestion.
 */
class SubCategoryMatcher
{
    private const MIN_SCORE = 75;

    /** @var array<int, array{sub: object, tokens: string[], text: string, category: string}> */
    private array $candidates;

    /** @param Collection $subCategories SubCategory models with ->category loaded */
    public function __construct(Collection $subCategories)
    {
        $this->candidates = $subCategories
            ->map(fn ($s) => [
                'sub'      => $s,
                'tokens'   => self::tokens($s->name),
                'text'     => implode(' ', self::tokens($s->name)),
                'category' => mb_strtolower((string) $s->category?->name),
            ])
            ->filter(fn ($c) => $c['tokens'])
            ->all();
    }

    public function suggest(string $name, string $fileCategory = ''): ?object
    {
        $tokens = self::tokens($name);
        if (!$tokens) {
            return null;
        }

        $text = implode(' ', $tokens);
        $fileCategory = mb_strtolower(trim($fileCategory));

        $best = [];
        $bestScore = 0;

        foreach ($this->candidates as $c) {
            $score = $this->score($tokens, $text, $c);
            if ($score > 0 && $fileCategory !== '' && $c['category'] === $fileCategory) {
                $score += 10;
            }

            if ($score > $bestScore) {
                $best = [$c['sub']];
                $bestScore = $score;
            } elseif ($score === $bestScore) {
                $best[] = $c['sub'];
            }
        }

        return $bestScore >= self::MIN_SCORE && count($best) === 1 ? $best[0] : null;
    }

    /**
     * 90+  every word of the sub-category appears in the name ("Gigabit Switch" → Switch)
     * 85+  near-identical text, i.e. a typo ("Air Condtioner" → Air Conditioner)
     * 75   same last word, the noun that names the thing ("Wooden Cabinet" → Filing Cabinet)
     * 0    otherwise — loose character similarity on short words is noise ("Pliers" ≠ Amplifier)
     */
    private function score(array $tokens, string $text, array $c): int
    {
        $covered = count(array_filter($c['tokens'], fn ($t) => $this->hasToken($tokens, $t)));
        if ($covered === count($c['tokens'])) {
            return 85 + 5 * min($covered, 3);
        }

        similar_text($text, $c['text'], $percent);
        if ($percent >= 85) {
            return (int) round($percent);
        }

        if ($this->hasToken([end($tokens)], end($c['tokens']))) {
            return 75;
        }

        return 0;
    }

    private function hasToken(array $tokens, string $wanted): bool
    {
        foreach ($tokens as $t) {
            if ($t === $wanted || (strlen($wanted) >= 5 && levenshtein($t, $wanted) <= 1)) {
                return true;
            }
        }

        return false;
    }

    /** Lowercased words, simple plurals folded ("chairs" → "chair"). */
    public static function tokens(?string $name): array
    {
        $words = preg_split('/[^a-z0-9]+/', mb_strtolower((string) $name), -1, PREG_SPLIT_NO_EMPTY);

        return array_values(array_unique(array_map(
            fn ($w) => strlen($w) > 3 && str_ends_with($w, 's') && !str_ends_with($w, 'ss') ? substr($w, 0, -1) : $w,
            $words
        )));
    }
}
