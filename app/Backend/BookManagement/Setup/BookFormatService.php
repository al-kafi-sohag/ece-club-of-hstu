<?php

namespace App\Services\Backend\BookManagement\Setup;

use App\Models\BookFormat;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class BookFormatService
{
    public function getAllBookFormats(): Collection
    {
        return BookFormat::with(['created_admin', 'updated_admin'])
            ->latest()
            ->get();
    }

    public function getActiveBookFormats(): Collection
    {
        return BookFormat::where('status', 1)
            ->orderBy('title')
            ->get();
    }

    public function createBookFormat(array $data): BookFormat
    {
        $bookFormatInsert = DB::transaction(function () use ($data): BookFormat {
            $bookFormat = new BookFormat();
            $bookFormat->title = $data['title'];
            $bookFormat->status = $data['status'] ?? BookFormat::STATUS_ACTIVE;
            $bookFormat->created_by = admin()?->id;
            $bookFormat->save();

            return $bookFormat;
        }, config('app.db_transaction_attemps', 3));

        return $bookFormatInsert;
    }

    public function updateBookFormat(BookFormat $bookFormat, array $data): BookFormat
    {
        $bookFormatUpdated = DB::transaction(function () use ($bookFormat, $data): BookFormat {
            $bookFormat->title = $data['title'];
            $bookFormat->status = $data['status'] ?? $bookFormat->status;
            $bookFormat->updated_by = admin()?->id;
            $bookFormat->save();

            return $bookFormat;
        }, config('app.db_transaction_attemps', 3));

        return $bookFormatUpdated;
    }

    public function deleteBookFormat(BookFormat $bookFormat): void
    {
        DB::transaction(function () use ($bookFormat) {
            $bookFormat->deleted_by = admin()?->id;
            $bookFormat->delete();
        }, config('app.db_transaction_attemps', 3));
    }

    public function updateBookFormatStatus(BookFormat $bookFormat, int $status): BookFormat
    {
        $bookFormat->status = $status;
        $bookFormat->updated_by = admin()?->id;
        $bookFormat->save();

        return $bookFormat;
    }
}


