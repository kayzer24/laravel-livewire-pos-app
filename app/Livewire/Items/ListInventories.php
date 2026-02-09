<?php

namespace App\Livewire\Items;

use App\Models\Inventory;
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

class ListInventories extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Inventory::query())
            ->columns([
                TextColumn::make('item.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('quantity')
                    ->sortable()
                    ->badge(),
                TextColumn::make('created_at')
                    ->dateTime(format: 'd M Y - H:m')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Action::make('create')
                    ->label('Add New')
                    ->url(fn (): string => route('inventories.create')),
            ])
            ->recordActions([
                Action::make('edit')
                    ->url(fn (Inventory $record): string => route('inventories.edit', $record)),

                Action::make('delete')
                    ->requiresConfirmation()
                    ->action(fn (Inventory $record) => $record->delete())
                    ->color('danger')
                    ->successNotification(
                        notification: Notification::make()
                            ->title('Inventory deleted successfully')
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
        return view('livewire.items.list-inventories');
    }
}
