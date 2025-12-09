<?php

namespace  App\Services\Backend;

use App\Models\ApplicationSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class ApplicationSettingsService
{
    protected ApplicationSetting $baseSetting;

    public function __construct()
    {
        $this->baseSetting = ApplicationSetting::first();
    }

    public function updateSettings(array $settings): void
    {
        foreach ($settings as $key => $value) {
            $setting = ApplicationSetting::where('key', $key)->first();
            if (!$setting) {
                continue;
            }

            if ($this->isFileUpload($key)) {
                $this->handleFileUpload($setting, $key);
                continue;
            }

            $this->updateSettingValue($setting, $value);
        }
    }

    protected function isFileUpload(string $key): bool
    {
        return in_array($key, ['logo', 'favicon']) && request()->hasFile($key);
    }

    protected function updateSettingValue(ApplicationSetting $setting, $value): void
    {
        $value = $value ?? '';

        if ($setting->value !== $value) {
            $setting->update(['value' => $value]);

            if (!empty($setting->env_key)) {
                $this->updateEnvFile($setting->env_key, $value);
            }

            if (!empty($setting->cache_key)) {
            $this->updateCachedSettings($setting->cache_key, $value);
            }
        }
    }

    protected function updateEnvFile(string $key, string $value): void
    {
        try {
            $envPath = base_path('.env');
            if (!File::exists($envPath)) return;

            $safeValue = '"' . str_replace('"', '\"', $value) . '"';
            $envContent = File::get($envPath);
            $pattern = "/^{$key}=.*/m";

            if (preg_match($pattern, $envContent)) {
                $envContent = preg_replace($pattern, "{$key}={$safeValue}", $envContent);
            } else {
                $envContent .= "\n{$key}={$safeValue}";
            }

            File::put($envPath, $envContent);
        } catch (\Exception $e) {
            Log::warning("Could not update .env key {$key}: " . $e->getMessage());
        }
    }

    protected function updateCachedSettings(string $key, string $value): void
    {
        Cache::forget($key);
        Cache::put($key, $value);
    }

    protected function handleFileUpload(ApplicationSetting $setting, string $key): void
    {
        $this->baseSetting->clearMediaCollection($key);
        $media = $this->baseSetting->addMediaFromRequest($key)->toMediaCollection($key);
        $fileUrl = $media->getUrl();

        $this->updateSettingValue($setting, $fileUrl);
    }

    public function purgeCache(): void
    {
        Cache::flush();
    }
}
