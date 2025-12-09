<?php

namespace App\Services\Backend\BookManagement\Setup;

use App\Models\StoryType;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class StoryTypeService
{
    public function getAllStoryTypes(): Collection
    {
        return StoryType::with(['created_admin', 'updated_admin'])
            ->latest()
            ->get();
    }

    public function getActiveStoryTypes(): Collection
    {
        return StoryType::where('status', 1)
            ->orderBy('title')
            ->get();
    }

    public function createStoryType(array $data): StoryType
    {
        $storyTypeInsert = DB::transaction(function () use ($data): StoryType {
            $storyType = new StoryType();
            $storyType->title = $data['title'];
            $storyType->status = $data['status'] ?? StoryType::STATUS_ACTIVE;
            $storyType->created_by = admin()?->id;
            $storyType->save();

            return $storyType;
        }, config('app.db_transaction_attemps', 3));

        return $storyTypeInsert;
    }

    public function updateStoryType(StoryType $storyType, array $data): StoryType
    {
        $storyTypeUpdated = DB::transaction(function () use ($storyType, $data): StoryType {
            $storyType->title = $data['title'];
            $storyType->status = $data['status'] ?? $storyType->status;
            $storyType->updated_by = admin()?->id;
            $storyType->save();

            return $storyType;
        }, config('app.db_transaction_attemps', 3));

        return $storyTypeUpdated;
    }

    public function deleteStoryType(StoryType $storyType): void
    {
        DB::transaction(function () use ($storyType) {
            $storyType->deleted_by = admin()?->id;
            $storyType->delete();
        }, config('app.db_transaction_attemps', 3));
    }

    public function updateStoryTypeStatus(StoryType $storyType, int $status): StoryType
    {
        $storyType->status = $status;
        $storyType->updated_by = admin()?->id;
        $storyType->save();

        return $storyType;
    }
}

