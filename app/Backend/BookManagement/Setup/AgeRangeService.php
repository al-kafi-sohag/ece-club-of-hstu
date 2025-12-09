<?php

namespace App\Services\Backend\BookManagement\Setup;

use App\Models\AgeRange;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class AgeRangeService
{
    public function getAllAgeRanges(): Collection
    {
        return AgeRange::with(['created_admin', 'updated_admin'])
            ->latest()
            ->get();
    }

    public function getActiveAgeRanges(): Collection
    {
        return AgeRange::where('status', 1)
            ->orderBy('title')
            ->get();
    }

    public function createAgeRange(array $data): AgeRange
    {
        $ageRangeInsert = DB::transaction(function () use ($data): AgeRange {
            $ageRange = new AgeRange();
            $ageRange->title = $data['title'];
            $ageRange->status = $data['status'] ?? AgeRange::STATUS_ACTIVE;
            $ageRange->created_by = admin()?->id;
            $ageRange->save();

            return $ageRange;
        }, config('app.db_transaction_attemps', 3));

        return $ageRangeInsert;
    }

    public function updateAgeRange(AgeRange $ageRange, array $data): AgeRange
    {
        $ageRangeUpdated = DB::transaction(function () use ($ageRange, $data): AgeRange {
            $ageRange->title = $data['title'];
            $ageRange->status = $data['status'] ?? $ageRange->status;
            $ageRange->updated_by = admin()?->id;
            $ageRange->save();

            return $ageRange;
        }, config('app.db_transaction_attemps', 3));

        return $ageRangeUpdated;
    }

    public function deleteAgeRange(AgeRange $ageRange): void
    {
        DB::transaction(function () use ($ageRange) {
            $ageRange->deleted_by = admin()?->id;
            $ageRange->delete();
        }, config('app.db_transaction_attemps', 3));
    }

    public function updateAgeRangeStatus(AgeRange $ageRange, int $status): AgeRange
    {
        $ageRange->status = $status;
        $ageRange->updated_by = admin()?->id;
        $ageRange->save();

        return $ageRange;
    }
}

