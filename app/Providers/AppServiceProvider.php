<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                $settings = \App\Models\Setting::all()->pluck('value', 'key');

                if ($settings->isNotEmpty()) {
                    $config = [];

                    if ($settings->has('mail_mailer')) {
                        $config['mail.default'] = $settings->get('mail_mailer');
                        $config['mail.mailers.smtp.transport'] = $settings->get('mail_mailer');
                    }

                    if ($settings->has('mail_host')) {
                        $config['mail.mailers.smtp.host'] = $settings->get('mail_host');
                    }

                    if ($settings->has('mail_port')) {
                        $config['mail.mailers.smtp.port'] = $settings->get('mail_port');
                    }

                    if ($settings->has('mail_encryption')) {
                        $config['mail.mailers.smtp.encryption'] = $settings->get('mail_encryption');
                    }

                    if ($settings->has('mail_username')) {
                        $config['mail.mailers.smtp.username'] = $settings->get('mail_username');
                    }

                    if ($settings->has('mail_password')) {
                        $config['mail.mailers.smtp.password'] = $settings->get('mail_password');
                    }

                    if ($settings->has('mail_from_address')) {
                        $config['mail.from.address'] = $settings->get('mail_from_address');
                    }

                    if ($settings->has('mail_from_name')) {
                        $config['mail.from.name'] = $settings->get('mail_from_name');
                    }

                    config($config);
                }
            }
        }
        catch (\Exception $e) {
        // Failsafe if DB not ready
        }
    }
}
