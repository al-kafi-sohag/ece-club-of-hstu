<?php

namespace App\Services\Backend;

use App\Models\FAQ;
use Illuminate\Support\Facades\DB;

class FAQService
{
    public function getAllFAQs()
    {
        return FAQ::with(['created_admin', 'updated_admin'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getAllForOrders()
    {
        return FAQ::orderBy('order')->get();
    }

    public function createFAQ(array $data): FAQ
    {
        $faqInsert = DB::transaction(function () use ($data): FAQ {
            $faq = new FAQ();
            $faq->question = $data['question'];
            $faq->answer = $data['answer'];
            $faq->order = $data['order'];
            $faq->status = $data['status'];
            $faq->created_by = admin()?->id;

            // Handle order
            rearrangeOrder(FAQ::class, $data['order']);

            $faq->save();

            return $faq;
        }, config('app.db_transaction_attemps', 3));

        return $faqInsert;
    }

    public function updateFAQ(FAQ $faq, array $data): FAQ
    {
        $faqUpdated = DB::transaction(function () use ($faq, $data): FAQ {
            $faq->question = $data['question'];
            $faq->answer = $data['answer'];
            $faq->status = $data['status'];
            $faq->updated_by = admin()?->id;

            // Handle order changes
            $oldOrder = $faq->order;
            if (isset($data['order']) && $data['order'] != $oldOrder) {
                rearrangeOrder(FAQ::class,$data['order'], $oldOrder);
                $faq->order = $data['order'];
            }
            $faq->save();

            return $faq;
        }, config('app.db_transaction_attemps', 3));

        return $faqUpdated;
    }

    public function deleteFAQ(FAQ $faq)
    {
        DB::transaction(function () use ($faq) {
            $faq->deleted_by = admin()?->id;
            $faq->delete();
        }, config('app.db_transaction_attemps', 3));
    }
}

