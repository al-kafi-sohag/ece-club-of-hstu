<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use App\Models\ApplicationSetting;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Collection;

function get_app_setting($key)
{
    if (Cache::has($key)) {
        return Cache::get($key);
    }
    $setting = ApplicationSetting::where('key', $key)->first();
    if ($setting && $setting->value) {
        Cache::put($key, $setting->value);
        return $setting->value;
    }
    return null;
}

function timeFormate($time)
{
    $dateFormat = config('app.date_format', 'd-M-Y');
    $timeFormat = config('app.time_format', 'H:i A');
    return date($dateFormat . " " . $timeFormat, strtotime($time));
}


function admin(){
   return auth('admin')->user();
}

function str_limit($string, $limit = 25) {
    return Str::limit($string, $limit);
}
