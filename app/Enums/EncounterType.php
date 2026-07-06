<?php

namespace App\Enums;

enum EncounterType: string
{
    case SOAP = 'soap';
    case DAP = 'dap';
    case Narrative = 'narrative';
    case Intake = 'intake';
    case Custom = 'custom';

    public function label(): string
    {
        return match ($this) {
            self::SOAP => 'SOAP Note',
            self::DAP => 'DAP Note',
            self::Narrative => 'Narrative',
            self::Intake => 'Intake / Evaluation',
            self::Custom => 'Custom Template',
        };
    }
}
