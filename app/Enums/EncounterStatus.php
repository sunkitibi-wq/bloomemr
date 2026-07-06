<?php

namespace App\Enums;

enum EncounterStatus: string
{
    case Draft = 'draft';
    case Signed = 'signed';
    case Amended = 'amended';
    case CoSignPending = 'co_sign_pending';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Signed => 'Signed',
            self::Amended => 'Amended',
            self::CoSignPending => 'Co-Sign Pending',
        };
    }
}
