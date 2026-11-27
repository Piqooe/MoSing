<?php

namespace App\Support;

class Format
{
    public static function duration(?int $seconds): string
    {
        $seconds = max(0, (int) $seconds);

        return intdiv($seconds, 60).':'.str_pad((string) ($seconds % 60), 2, '0', STR_PAD_LEFT);
    }

    public static function minutes(?int $seconds): string
    {
        return (int) round(($seconds ?? 0) / 60).' min';
    }

    public static function totalDuration(?int $seconds, bool $short = false): string
    {
        $seconds = max(0, (int) $seconds);
        $h = intdiv($seconds, 3600);
        $m = intdiv($seconds % 3600, 60);
        $s = $seconds % 60;

        if ($short) {
            return $h ? "{$h}h {$m}min" : "{$m}min";
        }

        return $h ? "about {$h} hr {$m} min" : "{$m} min {$s} sec";
    }

    public static function dateAdded(?string $date): string
    {
        if (! $date) {
            return '';
        }

        $at = \Illuminate\Support\Carbon::parse($date);
        $days = (int) $at->copy()->startOfDay()->diffInDays(now()->startOfDay());

        return match (true) {
            $days <= 0 => 'Today',
            $days === 1 => 'Yesterday',
            $days < 7 => "{$days} days ago",
            default => $at->format('M j, Y'),
        };
    }

    public static function plays(?int $plays): string
    {
        return number_format((int) $plays);
    }

    public static function initials(string $name): string
    {
        return collect(preg_split('/\s+/', trim($name)))
            ->filter()
            ->take(2)
            ->map(fn ($word) => mb_strtoupper(mb_substr($word, 0, 1)))
            ->implode('');
    }

    public static function greeting(?\DateTimeInterface $now = null): string
    {
        $hour = (int) ($now ?? now())->format('G');

        return match (true) {
            $hour >= 5 && $hour < 12 => 'Good morning',
            $hour >= 12 && $hour < 18 => 'Good afternoon',
            default => 'Good evening',
        };
    }
}
