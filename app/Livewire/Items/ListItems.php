<?php

namespace App\Livewire\Items;

use App\Models\Item;
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
use Livewire\Component;

class ListItems extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Item::query())
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('sku')->label('SKU')->searchable()->sortable(),
                TextColumn::make('price')->money('EUR', locale: 'fr')->searchable()->sortable(),
                TextColumn::make('status')->badge()->searchable()->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                //                Action::make('edit')
                //                    ->url(fn (Item $record): string => route('items.edit', $record))
                //                    ->openUrlInNewTab(),

                Action::make('delete')
                    ->requiresConfirmation()
                    ->action(fn (Item $record) => $record->delete())
                    ->color('danger')
                    ->successNotification(
                        notification: Notification::make()
                            ->title('Item deleted successfully')
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
        return view('livewire.items.list-items');
    }
}
