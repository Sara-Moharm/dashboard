<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrderPolicy
{
    /**
     * Determine whether the user can view any models.
     * Admins can view all orders.
     * Delivery personnel can view the orders they've worked on.
     * Customers can view their own orders.
     */
    public function viewAny(User $user): bool
    {
        return $user->isManager()
            || $user->isDelivery()
            || $user->isCustomer();
    }

    /**
     * Determine whether the user can view the model.
     * Admins can view 
     */
    public function view(User $user, Order $order): bool
    {
        if ($user->isManager()) {
            return true;
        }
        if ($user->isDelivery()) {
            return $order->status == 'ready'
            || ($order->status == 'delivering' && $order->delivery_staff_id === $user->id);
        }
        if ($user->isCustomer()) {
            return $order->customer_id === $user->id;
        }
        return false;
    }

    /**
     * Determine whether the user can create models.
     * Only customers can create orders.
     * Admins and staff cannot create orders directly; they can only manage existing ones.
     */
    public function create(User $user): bool
    {
        return $user->isCustomer();
    }

    /**
     * Determine whether the user can update the model.
     * Admins can update all orders.
     * Customers can update their own orders.
     */
    public function update(User $user, Order $order): bool
    {
        if ($user->isSuperAdmin() && in_array($order->status,['preparing','ready'])) 
            return true;
            
        
        return $user->isCustomer()
        && $order->customer_id === $user->id
        && in_array($order->status, ['pending']);
    }

        /**
     * Determine whether the user can change the status of the model.
     * Kitchen staff can change the status of orders they are working on.
     * Delivery staff can change the status of orders they are delivering.
     */
    public function changeStatus(User $user, Order $order): bool
    {
    if ($user->isCustomer()) {
        return $order->customer_id === $user->id && $order->status === 'pending';
    }

    if ($user->isDelivery()) {
        return $order->delivery_staff_id === $user->id && in_array($order->status, ['ready', 'delivering']);
    }

    if ($user->isSuperAdmin()) {
        return in_array($order->status,['preparing','ready','delivering']);
    }

    return false;
  }

    /**
     * Determine whether the user can delete the model.
     * Only admins can delete orders.
     * Customers cannot delete orders, but they can cancel them if the order is not completed.
     */
    public function delete(User $user, Order $order): bool
    {
        return $user->isSuperAdmin();
    }

    


   
}