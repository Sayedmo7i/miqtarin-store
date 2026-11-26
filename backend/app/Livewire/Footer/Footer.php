<?php

namespace App\Livewire\Footer;
use App\Services\SettingService;
use App\Services\StoreService;
use Livewire\Component;

class Footer extends Component
{

    public function render()
    {
        $settings = app(SettingService::class)->getSettings('web_settings',true);
        $settings = json_decode($settings);
        $store_settings = app(StoreService::class)->getStoreSettings();
        $footer_style = getFooterStyle($store_settings);
        return view($footer_style,[
            'settings'=>$settings,
            'store_settings'=>$store_settings,
        ]);
    }
}
