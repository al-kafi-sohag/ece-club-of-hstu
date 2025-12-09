<?php

namespace App\Services\Backend;

use App\Models\PartnerCompany;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class PartnerService
{
    public function getAllPartners()
    {
        return PartnerCompany::with(['created_admin', 'updated_admin'])
            ->orderBy('order')
            ->get();
    }

    public function getAllForOrders()
    {
        return PartnerCompany::orderBy('order')->get();
    }

    public function createPartner(array $data, Request $request): PartnerCompany
    {
        $partner_insert = DB::transaction(function () use ($data, $request): PartnerCompany {
            $partner = new PartnerCompany();
            $partner->addMediaFromRequest('image')->toMediaCollection('partner');
            $partner->status = $data['status'] ?? PartnerCompany::STATUS_ACTIVE;
            $partner->order = $data['order'];
            $partner->created_by = admin()?->id;

            // Handle order
            rearrangeOrder(PartnerCompany::class, $data['order']);

            $partner->save();

            return $partner;
        }, config('app.db_transaction_attemps', 3));

        return $partner_insert;
    }

    public function updatePartner(PartnerCompany $partner, array $data, Request $request): PartnerCompany
    {
        $partner_updated = DB::transaction(function () use ($partner, $data, $request): PartnerCompany {

            $partner->status = $data['status'] ?? $partner->status;
            $partner->updated_by = admin()?->id;

            // Handle order changes
            $oldOrder = $partner->order;
            if (isset($data['order']) && $data['order'] != $oldOrder) {
                rearrangeOrder(PartnerCompany::class,$data['order'], $oldOrder);
                $partner->order = $data['order'];
            }

            $partner->save();

            // Handle image upload using MediaLibrary replaceMedia method
            if ($request->hasFile('image')) {
                try {
                    $partner->replaceMedia('image', 'partner', $request->file('image'));
                } catch (\Exception $e) {
                    \Log::error('Partner image upload failed: ' . $e->getMessage());
                    throw new \Exception('Could not upload partner image: ' . $e->getMessage());
                }
            }

            return $partner;
        }, config('app.db_transaction_attemps', 3));

        return $partner_updated;
    }

    public function deletePartner(PartnerCompany $partner)
    {
        DB::transaction(function () use ($partner) {
            // MediaLibrary automatically handles media deletion
            $partner->deleted_by = admin()?->id;
            $partner->delete();
        }, config('app.db_transaction_attemps', 3));
    }

    public function updatePartnerStatus(PartnerCompany $partner, $status): PartnerCompany
    {
        $partner->status = $status;
        $partner->updated_by = admin()?->id;
        $partner->save();

        return $partner;
    }
}
