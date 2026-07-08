<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Passport::routes();

        Passport::tokensCan([
            'patient.read' => 'Read patient demographics',
            'patient.write' => 'Write patient demographics',
            'encounter.read' => 'Read encounter data',
            'encounter.write' => 'Write encounter data',
            'observation.read' => 'Read lab results and observations',
            'medication.read' => 'Read medication requests',
            'medication.write' => 'Write medication requests',
            'document.read' => 'Read clinical documents',
            'document.write' => 'Write clinical documents',
            'patient.all' => 'Full access to Patient resources',
            'clinical.all' => 'Full access to all clinical data',
        ]);

        Passport::setDefaultScope([
            'patient.read',
            'encounter.read',
            'observation.read',
        ]);
    }
}
