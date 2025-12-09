<?php

namespace App\Services\Backend;

use App\Models\Menu;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MenuService
{
    public function getAllMenus()
    {
        return Menu::with(['parent', 'created_admin', 'updated_admin'])
            ->orderBy('order')
            ->get();
    }

    public function getMenusForOrder()
    {
        return Menu::orderBy('order')->get();
    }

    public function getRootMenus()
    {
        return Menu::whereNull('parent_id')
            ->where('status', Menu::STATUS_ACTIVE)
            ->orderBy('order')
            ->get();
    }

    public function createMenu(array $data): Menu
    {

        $menu_insert = DB::transaction(function () use ($data): Menu {
            $menu = new Menu();
            $menu->name = $data['name'];
            $menu->parent_id = $data['parent_id'] ?? null;
            $menu->url = $data['url'];
            $menu->target = $data['target'] ?? 'self';
            $menu->status = $data['status'] ?? 1;
            $menu->created_by = admin()?->id;
            $menu->order = $data['order'];

            // Handle order
            $this->rearrangeMenuOrder($data['order']);

            $menu->save();

            return $menu;
        }, config('app.db_transaction_attemps'));

        return $menu_insert;
    }

    public function updateMenu(Menu $menu, array $data): Menu
    {
        $menu_updated = DB::transaction(function () use ($menu, $data): Menu {
            $oldOrder = $menu->order;
            $menu->name = $data['name'];
            $menu->parent_id = $data['parent_id'] ?? null;
            $menu->url = $data['url'];
            $menu->target = $data['target'] ?? 'self';
            $menu->status = $data['status'] ?? Menu::STATUS_ACTIVE;
            $menu->updated_by = admin()?->id;

            // Handle order changes
            if (isset($data['order']) && $data['order'] != $oldOrder) {
                $this->rearrangeMenuOrder($data['order'], $menu->order);
                $menu->order = $data['order'];
            }

            $menu->save();

            return $menu;
        }, config('app.db_transaction_attemps'));

        return $menu_updated;
    }

    public function deleteMenu(Menu $menu)
    {
        DB::transaction(function () use ($menu) {
            // Update order of remaining menus
            $this->rearrangeMenuOrder($menu->order, '-');
            $menu->deleted_by = admin()?->id;
            $menu->delete();
        }, config('app.db_transaction_attemps'));
    }

    public function updateMenuStatus(Menu $menu, $status): Menu
    {
        $menu->status = $status;
        $menu->updated_by = admin()?->id;
        $menu->save();

        return $menu;
    }

    private function rearrangeOrderSave(Collection $menus, $type)
    {

        if ($type == '+') {
            foreach ($menus as  $menu) {
                $menu->order = $menu->order + 1; // Shift by 1 order to make space
                $menu->save();
            }
        }

        if ($type == '-') {
            foreach ($menus as  $menu) {
                $menu->order = $menu->order - 1; // Shift by 1 order to make space
                $menu->save();
            }
        }
    }

    private function rearrangeMenuOrder(int $order_to, int|string $order_from = '+')
    {
        if ($order_from == '+') {
            $menus = Menu::where('order', '>=', $order_to)->get();
            $this->rearrangeOrderSave($menus, '+');
        } elseif ($order_from == '-') {
            $menus = Menu::where('order', '>=', $order_to)->get();
            $this->rearrangeOrderSave($menus, '-');
        } else {
            if ($order_from < $order_to) {
                $menus = Menu::whereBetween('order', [$order_from + 1, $order_to])
                    ->orderByDesc('order')
                    ->get();
                $this->rearrangeOrderSave($menus, '-');
            } else {
                $menus = Menu::whereBetween('order', [$order_to, $order_from - 1])
                    ->orderBy('order')
                    ->get();
                $this->rearrangeOrderSave($menus, '+');
            }
        }
    }
}
