<?php

namespace App\Services\Backend\BookManagement\Setup;

use App\Models\BookTag;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class BookTagService
{
    public function getAllBookTags(): Collection
    {
        return BookTag::with(['created_admin', 'updated_admin'])
            ->latest()
            ->get();
    }

    public function getActiveBookTags(): Collection
    {
        return BookTag::where('status', 1)
            ->orderBy('title')
            ->get();
    }

    public function createBookTag(array $data): BookTag
    {
        $bookTagInsert = DB::transaction(function () use ($data): BookTag {
            $bookTag = new BookTag();
            $bookTag->title = $data['title'];
            $bookTag->status = $data['status'] ?? BookTag::STATUS_ACTIVE;
            $bookTag->created_by = admin()?->id;
            $bookTag->save();

            return $bookTag;
        }, config('app.db_transaction_attemps', 3));

        return $bookTagInsert;
    }

    public function updateBookTag(BookTag $bookTag, array $data): BookTag
    {
        $bookTagUpdated = DB::transaction(function () use ($bookTag, $data): BookTag {
            $bookTag->title = $data['title'];
            $bookTag->status = $data['status'] ?? $bookTag->status;
            $bookTag->updated_by = admin()?->id;
            $bookTag->save();

            return $bookTag;
        }, config('app.db_transaction_attemps', 3));

        return $bookTagUpdated;
    }

    public function deleteBookTag(BookTag $bookTag): void
    {
        DB::transaction(function () use ($bookTag) {
            $bookTag->deleted_by = admin()?->id;
            $bookTag->delete();
        }, config('app.db_transaction_attemps', 3));
    }

    public function updateBookTagStatus(BookTag $bookTag, int $status): BookTag
    {
        $bookTag->status = $status;
        $bookTag->updated_by = admin()?->id;
        $bookTag->save();

        return $bookTag;
    }
}

