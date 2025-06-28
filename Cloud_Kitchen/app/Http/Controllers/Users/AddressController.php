<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customerId = auth()->user()->customer->id;
        $addresses = Address::where('customer_id', $customerId)->get();
        return response()->json($addresses);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'city' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'street_address' => 'required|string|max:255',
        ]);

        $address = new Address();
        $address->customer_id = auth()->user()->customer->id;
        $address->city = $request->input('city');
        $address->district = $request->input('district');
        $address->street_address = $request->input('street_address');
        $address->save();

        return response()->json($address, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $address = Address::findOrFail($id);
        if ($address->customer_id !== auth()->user()->customer->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        return response()->json($address);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'city' => 'sometimes|string|max:255',
            'district' => 'sometimes|string|max:255',
            'street_address' => 'sometimes|string|max:255',
        ]);

        $address = Address::findOrFail($id);
        if ($address->customer_id !== auth()->user()->customer->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $address->city = $request->input('city');
        $address->district = $request->input('district');
        $address->street_address = $request->input('street_address');
        $address->save();

        return response()->json($address);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $address = Address::findOrFail($id);
        if ($address->customer_id !== auth()->user()->customer->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $address->delete();
        return response()->json(['message' => 'Address deleted successfully']);
    }
}
