<?php

namespace App\Services\Backend;

use App\Models\FooterZoneItems;
use Illuminate\Support\Facades\DB;

class FooterZoneItemService
{
    public function getAllFooterZoneItemss()
    {
        return FooterZoneItems::with(['created_admin', 'updated_admin','parent'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getAllForOrders()
    {
        return FooterZoneItems::orderBy('order')->get();
    }

    public function createFooterZoneItems(array $data): FooterZoneItems
    {
        $footer_zone_itemInsert = DB::transaction(function () use ($data): FooterZoneItems {
            $footer_zone_item = new FooterZoneItems();
            $footer_zone_item->title = $data['title'];
            $footer_zone_item->footer_zone_title_id = $data['footer_zone_title_id'];
            $footer_zone_item->url = $data['url'];
            $footer_zone_item->order = $data['order'];
            $footer_zone_item->status = $data['status'];
            $footer_zone_item->created_by = admin()?->id;

            // Handle order
            rearrangeOrder(FooterZoneItems::class, $data['order']);

            $footer_zone_item->save();

            return $footer_zone_item;
        }, config('app.db_transaction_attemps', 3));

        return $footer_zone_itemInsert;
    }

    public function updateFooterZoneItems(FooterZoneItems $footer_zone_item, array $data): FooterZoneItems
    {
        $footer_zone_itemUpdated = DB::transaction(function () use ($footer_zone_item, $data): FooterZoneItems {
            $footer_zone_item->title = $data['title'];
            $footer_zone_item->footer_zone_title_id = $data['footer_zone_title_id'];
            $footer_zone_item->url = $data['url'];
            $footer_zone_item->status = $data['status'];
            $footer_zone_item->updated_by = admin()?->id;

            // Handle order changes
            $oldOrder = $footer_zone_item->order;
            if (isset($data['order']) && $data['order'] != $oldOrder) {
                rearrangeOrder(FooterZoneItems::class,$data['order'], $oldOrder);
                $footer_zone_item->order = $data['order'];
            }
            $footer_zone_item->save();

            return $footer_zone_item;
        }, config('app.db_transaction_attemps', 3));

        return $footer_zone_itemUpdated;
    }

    public function deleteFooterZoneItems(FooterZoneItems $footer_zone_item)
    {
        DB::transaction(function () use ($footer_zone_item) {
            $footer_zone_item->deleted_by = admin()?->id;
            $footer_zone_item->delete();
        }, config('app.db_transaction_attemps', 3));
    }
}

