<?php

namespace App\Filament\Resources\OrderTransactions\Tables;

use App\Models\OrderTransaction;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class OrderTransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('name')->searchable(),
                TextColumn::make('Order_trx_id')->searchable()->label('Order ID'),
                TextColumn::make('created_at'),
                IconColumn::make('is_paid')->boolean()->trueColor('success')->falseColor('danger')->trueIcon('heroicon-o-check-circle')->falseIcon('heroicon-o-x-circle')->label('Terverifikasi'),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('approve')->label('Approve')->action(function (OrderTransaction $record) {
                    $record->is_paid = true;
                    $record->save();

                    Notification::make()->title('Order Approved')->success()->body('The order has been approved successfully')->send();
                })->color('success')->requiresConfirmation()->visible(fn(OrderTransaction $record) => !$record->is_paid),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
