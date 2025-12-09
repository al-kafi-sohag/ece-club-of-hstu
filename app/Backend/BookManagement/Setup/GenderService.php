<?php

namespace App\Services\Backend\BookManagement\Setup;

use App\Models\Gender;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class GenderService
{
    public function getAllGenders(): Collection
    {
        return Gender::with(['created_admin', 'updated_admin'])
            ->latest()
            ->get();
    }

    public function getActiveGenders(): Collection
    {
        return Gender::where('status', 1)
            ->orderBy('title')
            ->get();
    }

    public function createGender(array $data): Gender
    {
        $genderInsert = DB::transaction(function () use ($data): Gender {
            $gender = new Gender();
            $gender->title = $data['title'];
            $gender->status = $data['status'] ?? Gender::STATUS_ACTIVE;
            $gender->created_by = admin()?->id;
            $gender->save();

            return $gender;
        }, config('app.db_transaction_attemps', 3));

        return $genderInsert;
    }

    public function updateGender(Gender $gender, array $data): Gender
    {
        $genderUpdated = DB::transaction(function () use ($gender, $data): Gender {
            $gender->title = $data['title'];
            $gender->status = $data['status'] ?? $gender->status;
            $gender->updated_by = admin()?->id;
            $gender->save();

            return $gender;
        }, config('app.db_transaction_attemps', 3));

        return $genderUpdated;
    }

    public function deleteGender(Gender $gender): void
    {
        DB::transaction(function () use ($gender) {
            $gender->deleted_by = admin()?->id;
            $gender->delete();
        }, config('app.db_transaction_attemps', 3));
    }

    public function updateGenderStatus(Gender $gender, int $status): Gender
    {
        $gender->status = $status;
        $gender->updated_by = admin()?->id;
        $gender->save();

        return $gender;
    }
}

