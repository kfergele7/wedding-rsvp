<?php

namespace App\Support;

use App\Models\Party;
use Illuminate\Support\Carbon;

class InvitationTiming
{
    public static function normalizeEveningArrivalTimeForStorage(mixed $value): ?string
    {
        $trimmed = trim((string) $value);
        if ($trimmed === '') {
            return null;
        }

        if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $trimmed)) {
            $trimmed = substr($trimmed, 0, 5);
        }

        if (! preg_match('/^\d{2}:\d{2}$/', $trimmed)) {
            return null;
        }

        try {
            $time = Carbon::createFromFormat('H:i', $trimmed);
            $formatted = $time->format('H:i');

            return $formatted === $trimmed ? $formatted : null;
        } catch (\Throwable) {
            return null;
        }
    }

    public static function formatEveningArrivalTime(mixed $value): ?string
    {
        $normalised = self::normalizeEveningArrivalTimeForStorage($value);
        if (! $normalised) {
            return null;
        }

        try {
            return Carbon::createFromFormat('H:i', $normalised)->format('g:i a');
        } catch (\Throwable) {
            return null;
        }
    }

    public static function eveningArrivalTimeForGuestType(?string $guestType, mixed $value): ?string
    {
        if (($guestType ?: Party::GUEST_TYPE_DAY) !== Party::GUEST_TYPE_EVENING) {
            return null;
        }

        return self::formatEveningArrivalTime($value);
    }

    public static function eveningArrivalSentence(?string $formattedTime): ?string
    {
        $formattedTime = trim((string) $formattedTime);
        if ($formattedTime === '') {
            return null;
        }

        return "Please arrive from {$formattedTime} for the evening celebration.";
    }
}
