<?php

namespace App\Providers;

use App\Repositories\PatientRepository;
use App\Services\ConsultationService;
use App\Services\InvoiceService;
use App\Services\NumberGeneratorService;
use App\Services\PatientService;
use App\Services\PharmacyService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PatientRepository::class, fn () => new PatientRepository(new \App\Models\Patient));
        $this->app->singleton(NumberGeneratorService::class);
        $this->app->singleton(PatientService::class);
        $this->app->singleton(ConsultationService::class);
        $this->app->singleton(PharmacyService::class);
        $this->app->singleton(InvoiceService::class);
    }

    public function boot(): void
    {
        // if (app()->environment('local')) {
        //     URL::forceScheme('https');
        // }
    }
}
