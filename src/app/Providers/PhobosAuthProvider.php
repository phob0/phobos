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
        \App\PhobosUser::class => \Phobos\Framework\App\Policies\UserPolicy::class,
        \Phobos\Framework\App\Models\Setting::class => \Phobos\Framework\App\Policies\SettingPolicy::class,
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
