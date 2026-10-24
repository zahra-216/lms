<?php

namespace App\Support;

/**
 * Picture for a faculty or course card: the uploaded image when there is one, otherwise a stock
 * picture that matches the subject (public/images/courses), otherwise the campus photo.
 */
class StockImages
{
    /** keyword (lower case) => file name without extension. First match wins, so the order matters. */
    private const KEYWORDS = [
        'artificial intelligence' => 'ai', ' ai' => 'ai', 'data science' => 'data-science', 'computer science' => 'computer-science',
        'forensic' => 'forensics', 'cyber' => 'cyber-security', 'mobile' => 'mobile', 'cloud' => 'cloud',
        'full stack' => 'web', 'web' => 'web', 'programming' => 'programming', 'software' => 'programming',
        'information technology' => 'it', 'network' => 'it', 'computing' => 'it',
        'interior' => 'interior', 'revit' => 'architecture', 'architect' => 'architecture', 'autocad' => 'cad', 'mep' => 'cad',
        'quantity' => 'qs', 'civil' => 'civil', 'construction' => 'construction',
        'graphic' => 'graphic', 'ui' => 'graphic', 'ux' => 'graphic',
        'accounting' => 'accounting', 'finance' => 'accounting', 'marketing' => 'marketing',
        'human resource' => 'hrm', 'hrm' => 'hrm', 'tourism' => 'hospitality', 'hotel' => 'hospitality', 'hospitality' => 'hospitality',
        'project' => 'project', 'business' => 'business', 'management' => 'business',
        'english' => 'english', 'language' => 'english', 'linguistic' => 'english',
        'psycholog' => 'psychology', 'counsel' => 'psychology', 'early childhood' => 'early-childhood', 'pre-school' => 'early-childhood',
        'preschool' => 'early-childhood', 'teacher' => 'early-childhood', 'beauty' => 'beauty', 'cosmetolog' => 'beauty',
        'education' => 'education',
    ];

    private const FACULTY = [
        'science' => 'science-technology', 'engineering' => 'engineering', 'commerce' => 'commerce',
        'management' => 'commerce', 'arts' => 'arts', 'language' => 'language-vocational', 'vocational' => 'language-vocational',
    ];

    public static function forCourse($course): string
    {
        return self::uploaded('storage/courses/', $course->image ?? null)
            ?? self::stock(self::match(' '.($course->name ?? ''), self::KEYWORDS))
            ?? self::stock(self::match($course->faculty->name ?? '', self::FACULTY))
            ?? asset('images/ttmc.jpeg');
    }

    public static function forFaculty($faculty): string
    {
        return self::uploaded('storage/faculty/', $faculty->image ?? null)
            ?? self::stock(self::match($faculty->name ?? '', self::FACULTY))
            ?? asset('images/ttmc.jpeg');
    }

    private static function uploaded(string $dir, ?string $file): ?string
    {
        return $file && file_exists(public_path($dir.$file)) ? asset($dir.$file) : null;
    }

    private static function stock(?string $key): ?string
    {
        return $key && file_exists(public_path("images/courses/{$key}.jpg")) ? asset("images/courses/{$key}.jpg") : null;
    }

    private static function match(string $text, array $map): ?string
    {
        $text = mb_strtolower($text);
        foreach ($map as $needle => $key) {
            if (str_contains($text, $needle)) {
                return $key;
            }
        }

        return null;
    }
}
