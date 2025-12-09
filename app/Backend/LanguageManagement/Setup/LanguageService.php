<?php

namespace App\Services\Backend\LanguageManagement\Setup;

use App\Models\Language;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class LanguageService
{
    public function getAllLanguages(): Collection
    {
        return Language::with(['created_admin', 'updated_admin'])
            ->latest()
            ->get();
    }

    public function getActiveLanguages(): Collection
    {
        return Language::where('status', 1)
            ->orderBy('title')
            ->get();
    }

    public function createLanguage(array $data, $flagImage = null): Language
    {
        $languageInsert = DB::transaction(function () use ($data, $flagImage): Language {
            $language = new Language();
            $language->title = $data['title'];
            $language->locale = $data['locale'];
            $language->status = $data['status'] ?? Language::STATUS_ACTIVE;
            $language->created_by = admin()?->id;
            $language->save();

            // Handle flag image upload using Spatie Media Library
            if ($flagImage) {
                try {
                    $language->addMedia($flagImage)
                        ->withCustomProperties(['field_name' => $language->id])
                        ->toMediaCollection('flag-image');
                } catch (\Exception $e) {
                    \Log::error('Language flag image upload failed: ' . $e->getMessage());
                    throw new \Exception('Could not upload flag image: ' . $e->getMessage());
                }
            }

            return $language;
        }, config('app.db_transaction_attemps', 3));

        return $languageInsert;
    }

    public function updateLanguage(Language $language, array $data, $flagImage = null): Language
    {
        $languageUpdated = DB::transaction(function () use ($language, $data, $flagImage): Language {
            $language->title = $data['title'];
            $language->locale = $data['locale'];
            $language->status = $data['status'] ?? $language->status;
            $language->updated_by = admin()?->id;
            $language->save();

            // Handle flag image upload using Spatie Media Library
            if ($flagImage) {
                try {
                    $language->replaceMedia($language->id, 'flag-image', $flagImage);
                } catch (\Exception $e) {
                    \Log::error('Language flag image update failed: ' . $e->getMessage());
                    throw new \Exception('Could not update flag image: ' . $e->getMessage());
                }
            }

            return $language;
        }, config('app.db_transaction_attemps', 3));

        return $languageUpdated;
    }

    public function deleteLanguage(Language $language): void
    {
        DB::transaction(function () use ($language) {
            $language->deleted_by = admin()?->id;
            $language->delete();
        }, config('app.db_transaction_attemps', 3));
    }

    public function updateLanguageStatus(Language $language, int $status): Language
    {
        $language->status = $status;
        $language->updated_by = admin()?->id;
        $language->save();

        return $language;
    }
}

