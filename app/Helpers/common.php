<?php 
use App\Models\GeneralSettings;

/**
 * Site Information
 * **/ 
if(!function_exists('settings')){
    function settings(){
        $settings = GeneralSettings::take(1)->first();

        if(!is_null($settings)){return $settings;}
    }
}

?>