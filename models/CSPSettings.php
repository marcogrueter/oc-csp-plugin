<?php

namespace OFFLINE\CSP\Models;

use Model;

class CSPSettings extends Model
{
    public $implement = ['System.Behaviors.SettingsModel'];

    public $settingsCode = 'offline_csp_settings';

    public $settingsFields = 'fields.yaml';


    public function initSettingsData()
    {
        $this->enabled = true;
        $this->report_only = true;
        $this->report_mode = 'internal';
        $this->default_src = ['self'];
        $this->require_trusted_types = ["'script'"];
        $this->script_src = ['nonce', 'unsafe-inline'];
        $this->style_src = ['self', 'nonce', 'unsafe-inline'];
        $this->object_src = ['none'];
        $this->base_uri = ['none'];
        $this->inject_nonce = true;
        $this->block_all_mixed_content = true;
    }
}