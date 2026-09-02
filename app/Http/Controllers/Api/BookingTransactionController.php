<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingTransactionRequest;
use Illuminate\Http\Request;

class BookingTransactionController extends Controller
{
    public function store(StoreBookingTransactionRequest $request) {
        try {
            $validateData = $request->validated();

            if($request->hasFile('proof')) {
                $filePath = $request->file('proof')->store('proofs', 'public');
                $validateData['proof'] = $filePath;
            }
        } catch(\Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }
}
