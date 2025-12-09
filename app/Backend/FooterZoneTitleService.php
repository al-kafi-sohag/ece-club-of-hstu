<?php

namespace App\Services\Backend;


use App\Models\FooterZoneTitle;
use Illuminate\Support\Facades\DB;

class FooterZoneTitleService
{
    public function getAllFooterZoneTitles()
    {
        return FooterZoneTitle::with(['created_admin', 'updated_admin'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getAllForOrders()
    {
        return FooterZoneTitle::orderBy('order')->get();
    }

    public function createFooterZoneTitle(array $data): FooterZoneTitle
    {
        $footer_zone_titleInsert = DB::transaction(function () use ($data): FooterZoneTitle {
            $footer_zone_title = new FooterZoneTitle();
            $footer_zone_title->title = $data['title'];
            $footer_zone_title->order = $data['order'];
            $footer_zone_title->status = $data['status'];
            $footer_zone_title->created_by = admin()?->id;

            // Handle order
            rearrangeOrder(FooterZoneTitle::class, $data['order']);

            $footer_zone_title->save();

            return $footer_zone_title;
        }, config('app.db_transaction_attemps', 3));

        return $footer_zone_titleInsert;
    }

    public function updateFooterZoneTitle(FooterZoneTitle $footer_zone_title, array $data): FooterZoneTitle
    {
        $footer_zone_titleUpdated = DB::transaction(function () use ($footer_zone_title, $data): FooterZoneTitle {
            $footer_zone_title->title = $data['title'];
            $footer_zone_title->status = $data['status'];
            $footer_zone_title->updated_by = admin()?->id;

            // Handle order changes
            $oldOrder = $footer_zone_title->order;
            if (isset($data['order']) && $data['order'] != $oldOrder) {
                rearrangeOrder(FooterZoneTitle::class,$data['order'], $oldOrder);
                $footer_zone_title->order = $data['order'];
            }
            $footer_zone_title->save();

            return $footer_zone_title;
        }, config('app.db_transaction_attemps', 3));

        return $footer_zone_titleUpdated;
    }

    public function deleteFooterZoneTitle(FooterZoneTitle $footer_zone_title)
    {
        DB::transaction(function () use ($footer_zone_title) {
            $footer_zone_title->deleted_by = admin()?->id;
            $footer_zone_title->delete();
        }, config('app.db_transaction_attemps', 3));
    }
}

