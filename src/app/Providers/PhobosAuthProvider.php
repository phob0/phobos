<?php

namespace Phobos\Framework\App\Providers;

use Laravel\Passport\Passport;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider;

class PhobosAuthProvider extends AuthServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Models\PhobosUser' => 'Phobos\Framework\App\Policies\UserPolicy',
        'Phobos\Framework\App\Models\Setting' => 'Phobos\Framework\App\Policies\SettingPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();
    }
}
