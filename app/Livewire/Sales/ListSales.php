<?php

namespace App\Livewire\Sales;

use App\Models\PaymentMethod;
use App\Models\Sale;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Notifications\Notification;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Config;
use Livewire\Component;

class ListSales extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithTable;
    use InteractsWithSchemas;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Sale::query())
            ->columns([
                TextColumn::make('customer.name')
                    ->label('Customers')
                    ->searchable(),
                TextColumn::make('saleItems.item.name')
                    ->label('Items')
                    ->bulleted()
                    ->limitList(2)
                    ->expandableLimitedList(),
                TextColumn::make('paymentMethod.name')
                    ->searchable(),
                TextColumn::make('total')
                    ->money(Config::get('app.currency'), locale: 'en')
                    ->sortable(),
                TextColumn::make('paid_amount')
                    ->money(Config::get('app.currency'), locale: 'en')
                    ->sortable(),
                TextColumn::make('discount')
                    ->money(Config::get('app.currency'), locale: 'en')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Action::make('create')
                    ->label('Add New')
                    ->url(fn (): string => route('sales.create')),
            ])
            ->recordActions([
                Action::make('edit')
                    ->url(fn (Sale $record): string => route('sales.edit', $record)),

                Action::make('delete')
                    ->requiresConfirmation()
                    ->action(fn (Sale $record) => $record->delete())
                    ->color('danger')
                    ->successNotification(
                        notification: Notification::make()
                            ->title('Sale deleted successfully')
                            ->success()
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }

    public function render(): View
    {
        return view('livewire.sales.list-sales');
    }
}
