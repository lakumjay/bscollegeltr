<?php

namespace App\Services;

use App\Models\StudentCertificate;
use Closure;
use RuntimeException;

class ClcCertificateNumberGenerator
{
    private $randomNumber;

    public function __construct(?Closure $randomNumber = null)
    {
        $this->randomNumber = $randomNumber;
    }

    public function generate(?string $configuredPrefix, ?int $year = null, ?int $preferredSuffix = null, ?string $tag = null): string
    {
        $prefix = $this->normalizePrefix($configuredPrefix);
        $tag = $tag ? strtoupper(trim($tag)) : null;
        $year = $year ?: (int) now()->format('Y');

        if ($preferredSuffix !== null && $preferredSuffix >= 1 && $preferredSuffix <= 9999) {
            $number = $this->formatNumber($prefix, $tag, $year, $preferredSuffix);
            if (! StudentCertificate::where('certificate_number', $number)->exists()) {
                return $number;
            }
        }

        for ($attempt = 0; $attempt < 10000; $attempt++) {
            $suffix = ($this->randomNumber ?: static function (): int {
                return random_int(1, 9999);
            })();
            $number = $this->formatNumber($prefix, $tag, $year, $suffix);

            if (! StudentCertificate::where('certificate_number', $number)->exists()) {
                return $number;
            }
        }

        $scope = $prefix !== null ? ($tag ? "{$prefix}/{$tag}/{$year}" : "{$prefix}/{$year}") : ($tag ? "{$tag}/{$year}" : (string) $year);
        throw new RuntimeException("No available certificate number remains for {$scope}.");
    }

    private function formatNumber(?string $prefix, ?string $tag, int $year, int $suffix): string
    {
        $parts = [];
        if ($prefix !== null && $prefix !== '') {
            $parts[] = $prefix;
        }
        if ($tag !== null && $tag !== '') {
            $parts[] = $tag;
        }
        $parts[] = $year;
        $parts[] = sprintf('%04d', $suffix);

        return implode('/', $parts);
    }

    private function normalizePrefix(?string $configuredPrefix): ?string
    {
        $prefix = strtoupper(trim((string) $configuredPrefix));
        $prefix = preg_replace('/[^A-Z0-9_\-\/]/', '', $prefix);
        $prefix = trim(preg_replace('#/+#', '/', $prefix), '/');

        return ($prefix !== '' && $prefix !== null) ? $prefix : null;
    }
}
