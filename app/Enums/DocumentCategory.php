<?php

namespace App\Enums;

enum DocumentCategory: string
{
    case OutsideRecords = 'outside_records';
    case IEP = 'iep';
    case BehavioralPlan = 'behavioral_plan';
    case SchoolReport = 'school_report';
    case PriorAuth = 'prior_auth';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::OutsideRecords => 'Outside Records',
            self::IEP => 'IEP',
            self::BehavioralPlan => 'Behavioral Plan',
            self::SchoolReport => 'School Report',
            self::PriorAuth => 'Prior Authorization',
            self::Other => 'Other',
        };
    }
}
