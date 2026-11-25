<?php

namespace App\Support;

/**
 * Finds a student's registration number inside the name someone joined Zoom with, for example
 * "(Afsa) -TTMC/ML/UG/CSE-O/26/ 13" or "ttmc ml dip hrm-o 24 02 Kamal Perera".
 *
 * Two passes, the safe one first:
 *  1. exact: with capitals, spaces, dashes and slashes ignored, the whole registration number is in the name
 *     (the longest number wins, and "...02" is never taken from "...021").
 *  2. tolerant: the front part of the number is in the name and the last number is written without its
 *     leading zeros ("26/ 13" for "26/013"), or the letter O was typed for a zero. If more than one student
 *     fits, nothing is matched (the admin decides).
 */
class RegistrationMatcher
{
    /** normalized number => student id, longest first */
    private array $exact = [];

    /** @var array<int, array{0: string, 1: string, 2: int}> [front part, last number, student id], longest front first */
    private array $loose = [];

    /** @param  iterable<object>  $students  anything with ->id and ->registration_no */
    public function __construct(iterable $students)
    {
        foreach ($students as $s) {
            $reg = (string) $s->registration_no;
            $key = self::normalize($reg);

            if (strlen($key) < 6) {
                continue;
            }

            $this->exact[$key] = (int) $s->id;

            // front part and last number, taken from the number as written: "TTMC/ML/UG/CSE-O/26/013"
            if (preg_match('/^(.*\D)(\d+)\s*$/s', $reg, $m)) {
                $front = self::zeroForO(self::normalize($m[1]));

                if (strlen($front) >= 4 && ltrim($m[2], '0') !== '') {
                    $this->loose[] = [$front, $m[2], (int) $s->id];
                }
            }
        }

        uksort($this->exact, fn ($a, $b) => strlen($b) <=> strlen($a));
        usort($this->loose, fn ($a, $b) => strlen($b[0]) <=> strlen($a[0]));
    }

    /** Letters and digits only, capitals. */
    public static function normalize(string $text): string
    {
        return strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $text));
    }

    private static function zeroForO(string $normalized): string
    {
        return str_replace('O', '0', $normalized);
    }

    public function match(string $name): ?int
    {
        $text = self::normalize($name);

        if ($text === '') {
            return null;
        }

        // 1. the whole number is there
        foreach ($this->exact as $reg => $id) {
            $at = strpos($text, (string) $reg);

            while ($at !== false) {
                $next = $text[$at + strlen($reg)] ?? '';

                // "...2402" inside "...24021" is a different (longer) number, not a match.
                if (! (ctype_digit(substr($reg, -1)) && ctype_digit($next))) {
                    return $id;
                }

                $at = strpos($text, (string) $reg, $at + 1);
            }
        }

        // 2. the front part is there and the last number is short of zeros (or has an O for a zero)
        $loose = self::zeroForO($text);
        $found = [];

        foreach ($this->loose as [$front, $last, $id]) {
            $at = strpos($loose, $front);

            while ($at !== false) {
                if (preg_match('/^\d+/', substr($loose, $at + strlen($front)), $m)) {
                    $typed = $m[0];

                    if (strlen($typed) <= strlen($last) && ltrim($typed, '0') === ltrim($last, '0')) {
                        $found[$id] = true;
                    }
                }

                $at = strpos($loose, $front, $at + 1);
            }
        }

        return count($found) === 1 ? array_key_first($found) : null;
    }
}
