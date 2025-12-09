<?php

namespace App\Services\Backend;

use App\Models\Book;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class BookService
{
    public function getAllItems(): Collection
    {
        return Book::with([
            'storyTypes',
            'ageRanges',
            'genders',
            'skinColors',
            'languages',
            'bookSize',
            'bookTags',
            'created_admin',
            'updated_admin'
        ])
            ->orderBy('order')
            ->get();
    }

    public function getAllForOrders(): Collection
    {
        return Book::orderBy('order')->get();
    }

    public function getActiveBooks(array $filters = []): Collection
    {
        $query = Book::query()->where('status', 1);

        if (isset($filters['featured']) && $filters['featured']) {
            $query->where('featured', true);
        }

        if (isset($filters['story_type'])) {
            $query->whereHas('storyTypes', function ($q) use ($filters) {
                $q->where('story_types.id', $filters['story_type']);
            });
        }

        if (isset($filters['gender'])) {
            $query->whereHas('genders', function ($q) use ($filters) {
                $q->where('genders.id', $filters['gender']);
            });
        }

        if (isset($filters['age'])) {
            $age = (int) $filters['age'];
            $query->whereHas('ageRanges', function ($q) use ($age) {
                // Assuming age_ranges table has min and max fields, adjust if different
                $q->where('age_ranges.min', '<=', $age)
                  ->where('age_ranges.max', '>=', $age);
            });
        }

        return $query->orderBy('order')->get();
    }

    public function getBookBySlug(string $slug): ?Book
    {
        return Book::with(['pages', 'coverPage'])
            ->where('slug', $slug)
            ->where('status', 1)
            ->first();
    }

    public function getRelatedBooks(Book $book, int $limit = 4): Collection
    {
        return Book::where('id', '!=', $book->id)
            ->when($book->storyTypes->isNotEmpty(), function ($q) use ($book) {
                $q->whereHas('storyTypes', function ($query) use ($book) {
                    $query->whereIn('story_types.id', $book->storyTypes->pluck('id'));
                });
            })
            ->where('status', 1)
            ->orderBy('order')
            ->limit($limit)
            ->get();
    }

    public function createItem(array $data, $thumbnailImage = null, array $galleryImages = []): Book
    {
        $bookInsert = DB::transaction(function () use ($data, $thumbnailImage, $galleryImages): Book {
            $book = new Book();
            $book->fill($data);
            $book->created_by = admin()?->id;

            if (isset($data['order'])) {
                rearrangeOrder(Book::class, $data['order']);
            }

            $book->save();

            // Sync relationships
            if (isset($data['story_type_id']) && is_array($data['story_type_id'])) {
                $book->storyTypes()->sync($data['story_type_id']);
            }

            if (isset($data['age_range_id']) && is_array($data['age_range_id'])) {
                $book->ageRanges()->sync($data['age_range_id']);
            }

            if (isset($data['gender_id']) && is_array($data['gender_id'])) {
                $book->genders()->sync($data['gender_id']);
            }

            if (isset($data['skin_color_id']) && is_array($data['skin_color_id'])) {
                $book->skinColors()->sync($data['skin_color_id']);
            }

            if (isset($data['language_id']) && is_array($data['language_id'])) {
                $book->languages()->sync($data['language_id']);
            }

            if (isset($data['book_tag_id']) && is_array($data['book_tag_id'])) {
                $book->bookTags()->sync($data['book_tag_id']);
            }

            if ($thumbnailImage) {
                try {
                    $book->addMedia($thumbnailImage)
                        ->toMediaCollection('thumbnail-image');
                } catch (\Exception $e) {
                    \Log::error('Book thumbnail upload failed: ' . $e->getMessage());
                    throw new \Exception('Could not upload thumbnail image: ' . $e->getMessage());
                }
            }

            if (!empty($galleryImages)) {
                try {
                    $book->clearMediaCollection('gallery-images');
                    foreach ($galleryImages as $index => $image) {
                        if ($image) {
                            $book->addMedia($image)
                                ->withCustomProperties(['page_number' => $index + 1])
                                ->toMediaCollection('gallery-images');
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error('Book gallery upload failed: ' . $e->getMessage());
                    throw new \Exception('Could not upload gallery images: ' . $e->getMessage());
                }
            }

            return $book;
        }, config('app.db_transaction_attemps', 3));

        return $bookInsert;
    }

    public function updateItem(Book $book, array $data, $thumbnailImage = null, array $galleryImages = []): Book
    {
        $bookUpdated = DB::transaction(function () use ($book, $data, $thumbnailImage, $galleryImages): Book {
            $oldOrder = $book->order;
            $book->fill($data);
            $book->updated_by = admin()?->id;

            if (isset($data['order']) && $data['order'] != $oldOrder) {
                rearrangeOrder(Book::class, $data['order'], $oldOrder);
                $book->order = $data['order'];
            }

            $book->save();

            // Sync relationships
            if (isset($data['story_type_id']) && is_array($data['story_type_id'])) {
                $book->storyTypes()->sync($data['story_type_id']);
            }

            if (isset($data['age_range_id']) && is_array($data['age_range_id'])) {
                $book->ageRanges()->sync($data['age_range_id']);
            }

            if (isset($data['gender_id']) && is_array($data['gender_id'])) {
                $book->genders()->sync($data['gender_id']);
            }

            if (isset($data['skin_color_id']) && is_array($data['skin_color_id'])) {
                $book->skinColors()->sync($data['skin_color_id']);
            }

            if (isset($data['language_id']) && is_array($data['language_id'])) {
                $book->languages()->sync($data['language_id']);
            }

            if (isset($data['book_tag_id']) && is_array($data['book_tag_id'])) {
                $book->bookTags()->sync($data['book_tag_id']);
            }

            if ($thumbnailImage) {
                try {
                    $book->clearMediaCollection('thumbnail-image');
                    $book->addMedia($thumbnailImage)->toMediaCollection('thumbnail-image');
                } catch (\Exception $e) {
                    \Log::error('Book thumbnail update failed: ' . $e->getMessage());
                    throw new \Exception('Could not update thumbnail image: ' . $e->getMessage());
                }
            }

            if (!empty($galleryImages)) {
                try {
                    $book->clearMediaCollection('gallery-images');
                    foreach ($galleryImages as $index => $image) {
                        if ($image) {
                            $book->addMedia($image)
                                ->withCustomProperties(['page_number' => $index + 1])
                                ->toMediaCollection('gallery-images');
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error('Book gallery update failed: ' . $e->getMessage());
                    throw new \Exception('Could not update gallery images: ' . $e->getMessage());
                }
            }

            return $book;
        }, config('app.db_transaction_attemps', 3));

        return $bookUpdated;
    }

    public function deleteItem(Book $book): void
    {
        DB::transaction(function () use ($book) {
            $book->deleted_by = admin()?->id;
            $book->delete();
        }, config('app.db_transaction_attemps', 3));
    }
}
