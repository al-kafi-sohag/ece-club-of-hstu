<?php

namespace App\Services\Backend\BookManagement\Setup;

use App\Models\SkinColor;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class SkinColorService
{
    public function getAllSkinColors(): Collection
    {
        return SkinColor::with(['created_admin', 'updated_admin'])
            ->latest()
            ->get();
    }

    public function getActiveSkinColors(): Collection
    {
        return SkinColor::where('status', 1)
            ->orderBy('title')
            ->get();
    }

    public function createSkinColor(array $data): SkinColor
    {
        $skinColorInsert = DB::transaction(function () use ($data): SkinColor {
            $skinColor = new SkinColor();
            $skinColor->title = $data['title'];
            $skinColor->code = $data['code'];
            $skinColor->status = $data['status'] ?? SkinColor::STATUS_ACTIVE;
            $skinColor->created_by = admin()?->id;
            $skinColor->save();

            return $skinColor;
        }, config('app.db_transaction_attemps', 3));

        return $skinColorInsert;
    }

    public function updateSkinColor(SkinColor $skinColor, array $data): SkinColor
    {
        $skinColorUpdated = DB::transaction(function () use ($skinColor, $data): SkinColor {
            $skinColor->title = $data['title'];
            $skinColor->code = $data['code'];
            $skinColor->status = $data['status'] ?? $skinColor->status;
            $skinColor->updated_by = admin()?->id;
            $skinColor->save();

            return $skinColor;
        }, config('app.db_transaction_attemps', 3));

        return $skinColorUpdated;
    }

    public function deleteSkinColor(SkinColor $skinColor): void
    {
        DB::transaction(function () use ($skinColor) {
            $skinColor->deleted_by = admin()?->id;
            $skinColor->delete();
        }, config('app.db_transaction_attemps', 3));
    }

    public function updateSkinColorStatus(SkinColor $skinColor, int $status): SkinColor
    {
        $skinColor->status = $status;
        $skinColor->updated_by = admin()?->id;
        $skinColor->save();

        return $skinColor;
    }
}

