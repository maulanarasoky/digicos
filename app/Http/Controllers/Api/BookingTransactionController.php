<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingTransactionRequest;
use App\Http\Resources\Api\BookingTransactionApiResource;
use App\Models\BookingTransaction;
use App\Models\Cosmetic;
use Illuminate\Http\Request;

class BookingTransactionController extends Controller
{
    public function store(StoreBookingTransactionRequest $request)
    {
        try {
            $validateData = $request->validated();

            //Handle file upload
            if ($request->hasFile('proof')) {
                $filePath = $request->file('proof')->store('proofs', 'public');
                $validateData['proof'] = $filePath;
            }

            //Retrieve products, calculate quantities and prices
            $products = $request->input('cosmetic_ids');
            $totalQuantity = 0;
            $totalPrice = 0;

            $cosmeticIds = array_column($products, 'id');
            $cosmetics = Cosmetic::whereIn('id', $cosmeticIds)->get();

            foreach ($products as $product) {
                $cosmetic = $cosmetics->firstWhere('id', $product['id']);
                $totalQuantity += $product['quantity'];
                $totalPrice += $cosmetic->price * $product['quantity'];
            }

            $tax = 0.11 * $totalPrice;
            $grandTotal = $totalPrice + $tax;

            //Populate booking transaction data
            $validateData['booking_trx_id'] = BookingTransaction::generateUniqueTrxId();
            $validateData['total_amount'] = $grandTotal;
            $validateData['total_tax_amount'] = $tax;
            $validateData['sub_total_amount'] = $totalPrice;
            $validateData['is_paid'] = false;
            $validateData['quantity'] = $totalQuantity;

            $bookingTransaction = BookingTransaction::create($validateData);

            //Create transaction details for each product
            foreach ($products as $product) {
                $cosmetic = $cosmetics->firstWhere('id', $product['id']);
                $bookingTransaction->transactionDetails()->create([
                    'cosmetic_id' => $product['id'],
                    'quantity' => $product['quantity'],
                    'price' => $cosmetic->price,
                ]);
            }

            //Return booking transaction with details
            return new BookingTransactionApiResource($bookingTransaction->load('transactionDetails'));
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }
}
