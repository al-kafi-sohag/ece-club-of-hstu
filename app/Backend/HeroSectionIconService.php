<?php

namespace App\Services\Backend;

use App\Models\HeroSectionIcon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class HeroSectionIconService
{
    public function getAllHeroSectionIcons()
    {
        return HeroSectionIcon::with(['created_admin', 'updated_admin'])
            ->orderBy('order')
            ->get();
    }

    public function getHeroSectionIconsForOrder()
    {
        return HeroSectionIcon::orderBy('order')->get();
    }

    public function createHeroSectionIcon(array $data, $file): HeroSectionIcon
    {
        $heroSectionIconInsert = DB::transaction(function () use ($data, $file): HeroSectionIcon {
            $heroSectionIcon = new HeroSectionIcon();
            $heroSectionIcon->icon_bg_color = $data['icon_bg_color'];
            $heroSectionIcon->title = $data['title'];
            $heroSectionIcon->description = $data['description'];
            $heroSectionIcon->order = $data['order'];
            $heroSectionIcon->status = $data['status'];
            $heroSectionIcon->created_by = admin()?->id;

            // Handle order
            $this->rearrangeHeroSectionIconOrder($data['order']);

            $heroSectionIcon->save();

            // Handle icon image upload using Spatie Media Library
            if ($file) {
                try {
                    $media = $heroSectionIcon->addMedia($file)
                        ->withCustomProperties(['field_name' => $heroSectionIcon->id])
                        ->toMediaCollection('hero-icon');

                } catch (\Exception $e) {
                    \Log::error('Hero Section Icon image upload failed: ' . $e->getMessage());
                    throw new \Exception('Could not upload icon image: ' . $e->getMessage());
                }
            }

            return $heroSectionIcon;
        }, config('app.db_transaction_attemps', 3));

        return $heroSectionIconInsert;
    }

    public function updateHeroSectionIcon(HeroSectionIcon $heroSectionIcon, array $data, $file): HeroSectionIcon
    {
        $heroSectionIconUpdated = DB::transaction(function () use ($heroSectionIcon, $data, $file): HeroSectionIcon {
            $heroSectionIcon->icon_bg_color = $data['icon_bg_color'];
            $heroSectionIcon->title = $data['title'];
            $heroSectionIcon->description = $data['description'];
            $heroSectionIcon->status = $data['status'];
            $heroSectionIcon->updated_by = admin()?->id;

            // Handle order changes
            $oldOrder = $heroSectionIcon->order;
            if (isset($data['order']) && $data['order'] != $oldOrder) {
                $this->rearrangeHeroSectionIconOrder($data['order'], $oldOrder);
                $heroSectionIcon->order = $data['order'];
            }

            $heroSectionIcon->save();

            // Handle icon image upload using MediaLibrary replaceMedia method
            if ($file) {
                try {
                    $heroSectionIcon->replaceMedia($heroSectionIcon->id, 'hero-icon', $file);
                } catch (\Exception $e) {
                    \Log::error('Hero Section Icon image upload failed: ' . $e->getMessage());
                    throw new \Exception('Could not upload icon image: ' . $e->getMessage());
                }
            }

            return $heroSectionIcon;
        }, config('app.db_transaction_attemps', 3));

        return $heroSectionIconUpdated;
    }

    public function deleteHeroSectionIcon(HeroSectionIcon $heroSectionIcon)
    {
        DB::transaction(function () use ($heroSectionIcon) {
            // Update order of remaining icons
            $this->rearrangeHeroSectionIconOrder($heroSectionIcon->order, '-');
            $heroSectionIcon->deleted_by = admin()?->id;
            $heroSectionIcon->delete();
        }, config('app.db_transaction_attemps', 3));
    }

    private function rearrangeOrderSave(Collection $heroSectionIcons, $type)
    {
        if ($type == '+') {
            foreach ($heroSectionIcons as $heroSectionIcon) {
                $heroSectionIcon->order = $heroSectionIcon->order + 1; // Shift by 1 order to make space
                $heroSectionIcon->save();
            }
        }

        if ($type == '-') {
            foreach ($heroSectionIcons as $heroSectionIcon) {
                $heroSectionIcon->order = $heroSectionIcon->order - 1; // Shift by 1 order
                $heroSectionIcon->save();
            }
        }
    }

    private function rearrangeHeroSectionIconOrder(int $order_to, int|string $order_from = '+')
    {
        if ($order_from == '+') {
            $heroSectionIcons = HeroSectionIcon::where('order', '>=', $order_to)->get();
            $this->rearrangeOrderSave($heroSectionIcons, '+');
        } elseif ($order_from == '-') {
            $heroSectionIcons = HeroSectionIcon::where('order', '>=', $order_to)->get();
            $this->rearrangeOrderSave($heroSectionIcons, '-');
        } else {
            if ($order_from < $order_to) {
                $heroSectionIcons = HeroSectionIcon::whereBetween('order', [$order_from + 1, $order_to])
                    ->orderByDesc('order')
                    ->get();
                $this->rearrangeOrderSave($heroSectionIcons, '-');
            } else {
                $heroSectionIcons = HeroSectionIcon::whereBetween('order', [$order_to, $order_from - 1])
                    ->orderBy('order')
                    ->get();
                $this->rearrangeOrderSave($heroSectionIcons, '+');
            }
        }
    }
}

