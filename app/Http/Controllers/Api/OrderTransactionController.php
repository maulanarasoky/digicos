<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderTransactionRequest;
use App\Http\Resources\Api\OrderTransactionApiResource;
use App\Models\OrderTransaction;
use App\Models\Cosmetic;
use Illuminate\Http\Request;

class OrderTransactionController extends Controller
{
    public function store(OrderTransactionRequest $request)
    {
        try {
            $validateData = $request->validated();

            //Handle file upload
            if ($request->hasFile('proof')) {
                $filePath = $request->file('proof')->store('proofs', 'local');
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

            //Populate order transaction data
            $validateData['order_trx_id'] = OrderTransaction::generateUniqueTrxId();
            $validateData['total_amount'] = $grandTotal;
            $validateData['total_tax_amount'] = $tax;
            $validateData['sub_total_amount'] = $totalPrice;
            $validateData['is_paid'] = false;
            $validateData['quantity'] = $totalQuantity;

            $orderTransaction = OrderTransaction::create($validateData);

            //Create transaction details for each product
            foreach ($products as $product) {
                $cosmetic = $cosmetics->firstWhere('id', $product['id']);
                $orderTransaction->transactionDetails()->create([
                    'cosmetic_id' => $product['id'],
                    'quantity' => $product['quantity'],
                    'price' => $cosmetic->price,
                ]);
            }

            //Return order transaction with details
            return new OrderTransactionApiResource($orderTransaction->load(['transactionDetails', 'transactionDetails.cosmetic']));
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }

    public function order_details(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'order_trx_id' => 'required|string',
        ]);

        $order = OrderTransaction::where('email', $request->email)->where('order_trx_id', $request->order_trx_id)->with([
            'transactionDetails',
            'transactionDetails.cosmetic',
        ])->first();

        if (!$order) {
            return response()->json(['message' => 'Order is not found'], 404);
        }

        return new OrderTransactionApiResource($order);
    }
}
