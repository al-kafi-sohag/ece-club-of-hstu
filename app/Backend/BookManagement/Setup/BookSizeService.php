<?php

namespace App\Services\Backend\BookManagement\Setup;

use App\Models\BookSize;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class BookSizeService
{
    public function getAllBookSizes(): Collection
    {
        return BookSize::with(['created_admin', 'updated_admin'])
            ->latest()
            ->get();
    }

    public function getActiveBookSizes(): Collection
    {
        return BookSize::where('status', 1)
            ->orderBy('title')
            ->get();
    }

    public function createBookSize(array $data): BookSize
    {
        $bookSizeInsert = DB::transaction(function () use ($data): BookSize {
            $bookSize = new BookSize();
            $bookSize->title = $data['title'];
            $bookSize->width = $data['width'];
            $bookSize->height = $data['height'];
            $bookSize->status = $data['status'] ?? BookSize::STATUS_ACTIVE;
            $bookSize->created_by = admin()?->id;
            $bookSize->save();

            return $bookSize;
        }, config('app.db_transaction_attemps', 3));

        return $bookSizeInsert;
    }

    public function updateBookSize(BookSize $bookSize, array $data): BookSize
    {
        $bookSizeUpdated = DB::transaction(function () use ($bookSize, $data): BookSize {
            $bookSize->title = $data['title'];
            $bookSize->width = $data['width'];
            $bookSize->height = $data['height'];
            $bookSize->status = $data['status'] ?? $bookSize->status;
            $bookSize->updated_by = admin()?->id;
            $bookSize->save();

            return $bookSize;
        }, config('app.db_transaction_attemps', 3));

        return $bookSizeUpdated;
    }

    public function deleteBookSize(BookSize $bookSize): void
    {
        DB::transaction(function () use ($bookSize) {
            $bookSize->deleted_by = admin()?->id;
            $bookSize->delete();
        }, config('app.db_transaction_attemps', 3));
    }

    public function updateBookSizeStatus(BookSize $bookSize, int $status): BookSize
    {
        $bookSize->status = $status;
        $bookSize->updated_by = admin()?->id;
        $bookSize->save();

        return $bookSize;
    }
}

