<?php

namespace App\Http\Services;

class NameParser
{
    public function splitNames(string $name): array
    {
        $names = [];

        if (preg_match('/^(Mr|Mrs|Ms|Dr|Prof|Miss|Mister)\s+and\s+(Mr|Mrs|Ms|Dr|Prof|Miss|Mister)\s+(.+)$/i', $name, $matches)) {
            $names[] = $matches[1] . ' ' . $matches[3];
            $names[] = $matches[2] . ' ' . $matches[3];
        } elseif (preg_match('/^(Mr|Mrs|Ms|Dr|Prof|Miss|Mister)\s+and\s+(Mrs|Ms|Dr|Prof|Miss|Mister)\s+([A-Z][a-z]+(?:\s+[A-Z][a-z]+)+)$/i', $name, $matches)) {
            $names[] = $matches[1] . ' ' . $matches[3];
            $names[] = $matches[2] . ' ' . $matches[3];
        } elseif (str_contains($name, ' and ')) {
            $parts = explode(' and ', $name);
            foreach ($parts as $part) {
                $names[] = trim($part);
            }
        } else {
            $names[] = $name;
        }

        return $names;
    }

    public function parseName(string $name): ?array
    {
        $name = trim(preg_replace('/\s+/', ' ', $name));

        if (empty($name) || str_word_count($name) < 2) {
            return null;
        }

        $parts = explode(' ', $name);

        $title = in_array($parts[0], ['Mr', 'Mrs', 'Ms', 'Dr', 'Prof', 'Miss', 'Mister']) ? array_shift($parts) : null;

        if (count($parts) === 0) {
            return null;
        }

        $last = array_pop($parts);
        $first = null;
        $initial = null;

        if (count($parts) === 1) {
            if (preg_match('/^[A-Z]\.?$/i', $parts[0])) {
                $initial = strtoupper(trim($parts[0], '.'));
            } else {
                $first = $parts[0];
            }
        } elseif (count($parts) >= 2) {
            $first = $parts[0];
            if (preg_match('/^[A-Z]\.?$/', $parts[1])) {
                $initial = strtoupper(trim($parts[1], '.'));
            }
        }

        return [
            'title' => $title,
            'first_name' => $first,
            'initial' => $initial,
            'last_name' => $last,
        ];
    }
}
