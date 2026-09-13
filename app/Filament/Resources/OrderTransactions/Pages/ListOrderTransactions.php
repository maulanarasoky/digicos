<?php

namespace App\Filament\Resources\OrderTransactions\Pages;

use App\Filament\Resources\OrderTransactions\OrderTransactionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOrderTransactions extends ListRecords
{
    protected static string $resource = OrderTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
