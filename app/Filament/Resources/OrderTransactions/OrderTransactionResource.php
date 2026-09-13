<?php

namespace App\Filament\Resources\OrderTransactions;

use App\Filament\Resources\OrderTransactions\Pages\CreateOrderTransaction;
use App\Filament\Resources\OrderTransactions\Pages\EditOrderTransaction;
use App\Filament\Resources\OrderTransactions\Pages\ListOrderTransactions;
use App\Filament\Resources\OrderTransactions\Schemas\OrderTransactionForm;
use App\Filament\Resources\OrderTransactions\Tables\OrderTransactionsTable;
use App\Models\OrderTransaction;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class OrderTransactionResource extends Resource
{
    protected static ?string $model = OrderTransaction::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::CreditCard;

    protected static string|UnitEnum|null $navigationGroup = 'Customer';

    public static function form(Schema $schema): Schema
    {
        return OrderTransactionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OrderTransactionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrderTransactions::route('/'),
            'create' => CreateOrderTransaction::route('/create'),
            'edit' => EditOrderTransaction::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
