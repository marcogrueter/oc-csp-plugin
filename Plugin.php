<?php namespace OFFLINE\CSP;

use Backend\Facades\Backend;
use Event;
use Illuminate\Contracts\Http\Kernel;
use OFFLINE\CSP\Classes\CSPMiddleware;
use OFFLINE\CSP\Classes\NonceInjector;
use OFFLINE\CSP\Console\DisableCSPPlugin;
use OFFLINE\CSP\Models\CSPSettings;
use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    const REPORT_URI = '/_csp/report-uri';

    public function boot()
    {
        // Register the CSP middleware if it is enabled.
        if ((bool)CSPSettings::get('enabled')) {
            $this->app[Kernel::class]->pushMiddleware(CSPMiddleware::class);
        }

        if (CSPSettings::get('inject_nonce')) {
            // Automatically inject the nonce attribute into each script and style tag.
            Event::listen('cms.page.postprocess', function ($controller, $url, $page, $dataHolder) {
                $dataHolder->content = NonceInjector::withNonce(app('csp-nonce'))->inject($dataHolder->content);
            });
        }
    }

    public function register()
    {
        $this->registerConsoleCommand('csp.disable', DisableCSPPlugin::class);
    }

    public function registerMarkupTags()
    {
        return [
            'functions' => [
                'csp_nonce' => function () {
                    return app('csp-nonce');
                },
            ],
        ];
    }

    public function registerSettings()
    {
        return [
            'csp' => [
                'label' => 'CSP',
                'description' => 'offline.csp::lang.settings.description',
                'category' => 'CSP',
                'icon' => 'icon-lock',
                'class' => CSPSettings::class,
                'order' => 600,
                'keywords' => 'csp security content policy',
                'permissions' => ['offline.csp.manage_settings'],
            ],
            'csp_logs' => [
                'label' => 'offline.csp::lang.log.label',
                'description' => 'offline.csp::lang.log.description',
                'category' => 'CSP',
                'icon' => 'icon-list',
                'url' => Backend::url('offline/csp/csplogs'),
                'order' => 601,
                'keywords' => 'csp security content policy',
                'permissions' => ['offline.csp.manage_settings'],
            ],
        ];
    }
}