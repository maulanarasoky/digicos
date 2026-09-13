<?php

namespace App\Filament\Resources\OrderTransactions\Pages;

use App\Filament\Resources\OrderTransactions\OrderTransactionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOrderTransaction extends CreateRecord
{
    protected static string $resource = OrderTransactionResource::class;
}
