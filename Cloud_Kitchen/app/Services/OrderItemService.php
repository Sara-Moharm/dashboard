<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\OrderItem;

class OrderItemService
{
    public function getAllOrderItems()
    {
        return OrderItem::with(['CategoryDish', 'Order'])->get();
    }

    public function createOrderItem(array $data)
    {
        return OrderItem::create($data)->load(['categoryDish', 'order']);
    }

    public function getOrderItemById(string $id)
    {
        $orderItem =  OrderItem::with(['categoryDish', 'order'])->findOrFail($id);
        return $orderItem;
    }

    public function updateOrderItem(string $id, array $data)
    {
        $orderItem = OrderItem::findOrFail($id);
        $orderItem->update($data);
        return $orderItem->load(['categoryDish', 'order']);
    }

    public function deleteOrderItem(string $id)
    {
        $orderItem = OrderItem::findOrFail($id);
        $orderItem->delete();
        return $orderItem->load(['categoryDish', 'order']);
    }

    protected function updateOrderItemStatus(string $id, string $status, string $staffStatus)
    {
        $orderItem = OrderItem::findOrFail($id);
        $staff = auth()->user()->staff;

        $orderItem->update([
            'status' => $status,
            'staff_id' => $staff->id,
        ]);

        $staff->update([
            'status' => $staffStatus,
        ]);

        return $orderItem->fresh();
    }

    public function markAsPending(string $id)
    {
        return $this->updateOrderItemStatus($id, 'pending', 'available');
    }

    public function markAsPreparing(string $id)
    {
        return $this->updateOrderItemStatus($id, 'preparing', 'busy');
    }

    public function markAsReady(string $id)
    {
        return $this->updateOrderItemStatus($id, 'ready', 'available');
    }

    private function isInShift($start, $end)
    {
         
        $now = Carbon::now();
        // \Log::info('Current time: ' . $now->toTimeString());
        // \Log::info('Shift start: ' . $start);
        // \Log::info('Shift end: ' . $end);
       
        $shiftStart = Carbon::createFromTimeString($start);
        $shiftEnd = Carbon::createFromTimeString($end);

        // If shift does NOT pass midnight
        if ($shiftStart <= $shiftEnd) {
            return $now->between($shiftStart, $shiftEnd);
        }

        // If shift passes midnight (e.g. 22:00:00 to 06:00:00)
        return $now->gte($shiftStart) || $now->lte($shiftEnd);
    }

    public function getPendingItemsForCurrentKitchenShift()
    {
        // $kitchen_staff = auth()->user()->staff;

        // if ($kitchen_staff->status !== 'available') {
        //     throw new \Exception("You are not available.");
        // }

        // if (!$this->isInShift($kitchen_staff->shift_start, $kitchen_staff->shift_end)){
        //     throw new \Exception("You are not within your shift hours.");
        // }

        return OrderItem::where('status', 'pending')->get()->makeHidden('staff_id');
    }

    public function getPreparingItemsForCurrentKitchenStaff()
    {
        $kitchen_staff = auth()->user()->staff;

        // if (!$this->isInShift($kitchen_staff->shift_start, $kitchen_staff->shift_end)){
        //     throw new \Exception("You are not within your shift hours.");
        // }

        return OrderItem::where('status', 'preparing')->where('staff_id', $kitchen_staff->id)->get()->makeHidden('staff_id');
    }
}
