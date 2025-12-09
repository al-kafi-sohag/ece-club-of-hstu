<?php

namespace App\Services\Backend;

use App\Models\Book;
use App\Models\BookPage;
use Illuminate\Support\Facades\DB;

class BookPageService
{
    public function createPage(Book $book, int $pageNumber, bool $isCover = false): BookPage
    {
        return DB::transaction(function () use ($book, $pageNumber, $isCover): BookPage {
            if ($isCover) {
                BookPage::where('book_id', $book->id)->update(['is_cover' => false]);
            }

            $page = new BookPage();
            $page->book_id = $book->id;
            $page->page_number = $pageNumber;
            $page->is_cover = $isCover;
            $page->order = $pageNumber;
            $page->save();

            return $page;
        }, config('app.db_transaction_attemps', 3));
    }

    public function updatePage(BookPage $page, array $data, $backgroundImage = null): BookPage
    {
        return DB::transaction(function () use ($page, $data, $backgroundImage): BookPage {
            if (!empty($data['is_cover'])) {
                BookPage::where('book_id', $page->book_id)->update(['is_cover' => false]);
                $page->is_cover = true;
            }

            if (isset($data['page_number']) && $data['page_number'] != $page->page_number) {
                $page->page_number = $data['page_number'];
                $page->order = $data['page_number'];
            }

            if (isset($data['text_elements'])) {
                $page->text_elements = $data['text_elements'];
            }

            $page->save();

            if ($backgroundImage) {
                try {
                    $page->clearMediaCollection('background-image');
                    $page->addMedia($backgroundImage)
                        ->withCustomProperties(['field_name' => 'background_image'])
                        ->toMediaCollection('background-image');
                } catch (\Exception $e) {
                    \Log::error('Book page background upload failed: ' . $e->getMessage());
                    throw new \Exception('Could not upload background image: ' . $e->getMessage());
                }
            }

            return $page;
        }, config('app.db_transaction_attemps', 3));
    }

    public function deletePage(BookPage $page): void
    {
        DB::transaction(function () use ($page) {
            $bookId = $page->book_id;
            $pageNumber = $page->page_number;
            $page->delete();

            BookPage::where('book_id', $bookId)
                ->where('page_number', '>', $pageNumber)
                ->orderBy('page_number')
                ->get()
                ->each(function (BookPage $p) {
                    $p->page_number = $p->page_number - 1;
                    $p->order = $p->page_number;
                    $p->save();
                });
        }, config('app.db_transaction_attemps', 3));
    }

    public function setCoverPage(BookPage $page): BookPage
    {
        return DB::transaction(function () use ($page): BookPage {
            BookPage::where('book_id', $page->book_id)->update(['is_cover' => false]);
            $page->is_cover = true;
            $page->save();

            return $page;
        }, config('app.db_transaction_attemps', 3));
    }
}


