<?php

namespace App\Services\Backend;

use App\Models\PromiseSection;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class PromiseSectionService
{
    public function getAllPromiseSections()
    {
        return PromiseSection::with(['created_admin', 'updated_admin'])
            ->orderBy('order')
            ->get();
    }

    public function getPromiseSectionsForOrder()
    {
        return PromiseSection::orderBy('order')->get();
    }

    public function createPromiseSection(array $data, $file): PromiseSection
    {
        $promiseSectionInsert = DB::transaction(function () use ($data, $file): PromiseSection {
            $promiseSection = new PromiseSection();
            $promiseSection->icon_bg_color = $data['icon_bg_color'];
            $promiseSection->title = $data['title'];
            $promiseSection->description = $data['description'];
            $promiseSection->order = $data['order'];
            $promiseSection->status = $data['status'];
            $promiseSection->created_by = admin()?->id;

            // Handle order
            $this->rearrangePromiseSectionOrder($data['order']);

            $promiseSection->save();

             // Handle icon image upload using Spatie Media Library
             if ($file) {
                try {
                    $media = $promiseSection->addMedia($file)
                        ->withCustomProperties(['field_name' => $promiseSection->id])
                        ->toMediaCollection('promise-icon');

                } catch (\Exception $e) {
                    \Log::error('Hero Section Icon image upload failed: ' . $e->getMessage());
                    throw new \Exception('Could not upload icon image: ' . $e->getMessage());
                }
            }

            return $promiseSection;
        }, config('app.db_transaction_attemps', 3));

        return $promiseSectionInsert;
    }

    public function updatePromiseSection(PromiseSection $promiseSection, array $data, $file): PromiseSection
    {
        $promiseSectionUpdated = DB::transaction(function () use ($promiseSection, $data, $file): PromiseSection {
            $oldOrder = $promiseSection->order;
            $promiseSection->icon_bg_color = $data['icon_bg_color'];
            $promiseSection->title = $data['title'];
            $promiseSection->description = $data['description'];
            $promiseSection->status = $data['status'];
            $promiseSection->updated_by = admin()?->id;

            // Handle order changes
            if (isset($data['order']) && $data['order'] != $oldOrder) {
                $this->rearrangePromiseSectionOrder($data['order'], $oldOrder);
                $promiseSection->order = $data['order'];
            }

            $promiseSection->save();

             // Handle icon image upload using MediaLibrary replaceMedia method
             if ($file) {
                try {
                    $promiseSection->replaceMedia($promiseSection->id, 'promise-icon', $file);
                } catch (\Exception $e) {
                    \Log::error('Promise Section Icon image upload failed: ' . $e->getMessage());
                    throw new \Exception('Could not upload icon image: ' . $e->getMessage());
                }
            }

            return $promiseSection;
        }, config('app.db_transaction_attemps', 3));

        return $promiseSectionUpdated;
    }

    public function deletePromiseSection(PromiseSection $promiseSection)
    {
        DB::transaction(function () use ($promiseSection) {
            // Update order of remaining sections
            $this->rearrangePromiseSectionOrder($promiseSection->order, '-');
            $promiseSection->deleted_by = admin()?->id;
            $promiseSection->delete();
        }, config('app.db_transaction_attemps', 3));
    }

    private function rearrangeOrderSave(Collection $promiseSections, $type)
    {
        if ($type == '+') {
            foreach ($promiseSections as $promiseSection) {
                $promiseSection->order = $promiseSection->order + 1; // Shift by 1 order to make space
                $promiseSection->save();
            }
        }

        if ($type == '-') {
            foreach ($promiseSections as $promiseSection) {
                $promiseSection->order = $promiseSection->order - 1; // Shift by 1 order
                $promiseSection->save();
            }
        }
    }

    private function rearrangePromiseSectionOrder(int $order_to, int|string $order_from = '+')
    {
        if ($order_from == '+') {
            $promiseSections = PromiseSection::where('order', '>=', $order_to)->get();
            $this->rearrangeOrderSave($promiseSections, '+');
        } elseif ($order_from == '-') {
            $promiseSections = PromiseSection::where('order', '>=', $order_to)->get();
            $this->rearrangeOrderSave($promiseSections, '-');
        } else {
            if ($order_from < $order_to) {
                $promiseSections = PromiseSection::whereBetween('order', [$order_from + 1, $order_to])
                    ->orderByDesc('order')
                    ->get();
                $this->rearrangeOrderSave($promiseSections, '-');
            } else {
                $promiseSections = PromiseSection::whereBetween('order', [$order_to, $order_from - 1])
                    ->orderBy('order')
                    ->get();
                $this->rearrangeOrderSave($promiseSections, '+');
            }
        }
    }
}

