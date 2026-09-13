<?php

namespace App\Filament\Resources\OrderTransactions\Pages;

use App\Filament\Resources\OrderTransactions\OrderTransactionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditOrderTransaction extends EditRecord
{
    protected static string $resource = OrderTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
