<?php

namespace App\Services\Backend;

use App\Models\WhyChooseUs;
use Illuminate\Support\Facades\DB;

class WhyChooseUsService
{
    public function getAllWhyChooseUs()
    {
        return WhyChooseUs::with(['created_admin', 'updated_admin'])
            ->orderBy('order')
            ->get();
    }

    public function getAllForOrders()
    {
        return WhyChooseUs::orderBy('order')->get();
    }

    public function createWhyChooseUs(array $data, $file): WhyChooseUs
    {
        $whyChooseUsInsert = DB::transaction(function () use ($data, $file): WhyChooseUs {
            $whyChooseUs = new WhyChooseUs();
            $whyChooseUs->title = $data['title'];
            $whyChooseUs->order = $data['order'];
            $whyChooseUs->status = $data['status'];
            $whyChooseUs->description = $data['description'];
            $whyChooseUs->created_by = admin()?->id;

            // Handle order
            rearrangeOrder(WhyChooseUs::class, $data['order']);

            $whyChooseUs->save();


            // Handle icon image upload using Spatie Media Library
            if ($file) {
                try {
                    $media = $whyChooseUs->addMedia($file)
                        ->withCustomProperties(['field_name' => $whyChooseUs->id])
                        ->toMediaCollection('why-us');
                } catch (\Exception $e) {
                    \Log::error('Hero Section Icon image upload failed: ' . $e->getMessage());
                    throw new \Exception('Could not upload icon image: ' . $e->getMessage());
                }
            }

            return $whyChooseUs;
        }, config('app.db_transaction_attemps', 3));

        return $whyChooseUsInsert;
    }

    public function updateWhyChooseUs(WhyChooseUs $whyChooseUs, array $data, $file): WhyChooseUs
    {
        $whyChooseUsUpdated = DB::transaction(function () use ($whyChooseUs, $data, $file): WhyChooseUs {
            $whyChooseUs->title = $data['title'];
            $whyChooseUs->description = $data['description'];
            $whyChooseUs->updated_by = admin()?->id;
            $whyChooseUs->status = $data['status'];

            // Handle order changes
            $oldOrder = $whyChooseUs->order;
            if (isset($data['order']) && $data['order'] != $oldOrder) {
                rearrangeOrder(WhyChooseUs::class,$data['order'], $oldOrder);
                $whyChooseUs->order = $data['order'];
            }

            $whyChooseUs->save();

            // Handle icon image upload using MediaLibrary replaceMedia method
            if ($file) {
                try {
                    $whyChooseUs->replaceMedia($whyChooseUs->id, 'why-us', $file);
                } catch (\Exception $e) {
                    \Log::error('Why Choose Us Section Icon image upload failed: ' . $e->getMessage());
                    throw new \Exception('Could not upload icon image: ' . $e->getMessage());
                }
            }

            return $whyChooseUs;
        }, config('app.db_transaction_attemps', 3));

        return $whyChooseUsUpdated;
    }

    public function deleteWhyChooseUs(WhyChooseUs $whyChooseUs)
    {
        DB::transaction(function () use ($whyChooseUs) {
            $whyChooseUs->deleted_by = admin()?->id;
            $whyChooseUs->delete();
        }, config('app.db_transaction_attemps', 3));
    }
}
