<?php

namespace App\Livewire\Items;

use App\Models\Item;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Config;
use Livewire\Component;

class EditItem extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public Item $record;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill($this->record->attributesToArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Edit "Item" record')
                    ->description('update the current Item record')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->minLength(3)
                            ->maxLength(255),
                        TextInput::make('sku')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->minLength(3)
                            ->maxLength(255),
                        TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->inputMode('decimal')
                            ->prefix(Config::get('app.currency')),
                        ToggleButtons::make('status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                            ])
                            ->grouped(),
                    ]),
            ])
            ->statePath('data')
            ->model($this->record);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $this->record->update($data);

        Notification::make()
            ->title('Item updated!')
            ->body("Item {$this->record->name} has been updated successfully.")
            ->success()
            ->send();
    }

    public function render(): View
    {
        return view('livewire.items.edit-item');
    }
}
