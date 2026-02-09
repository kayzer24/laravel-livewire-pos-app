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
use Livewire\Component;

class CreateItem extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Create new Item')
                    ->description('Create a brand new Item using the form bellow')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->placeholder('Test Item')
                            ->required(),
                        TextInput::make('sku')
                            ->unique(ignoreRecord: true)
                            ->placeholder('ABC123')
                            ->label('SKU')
                            ->required(),
                        TextInput::make('price')
                            ->required()
                            ->placeholder('129.99')
                            ->numeric()
                            ->prefix('€'),
                        ToggleButtons::make('status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                            ])
                            ->default('active')
                            ->grouped(),
                    ]),
            ])
            ->statePath('data')
            ->model(Item::class);
    }

    public function create(): void
    {
        $data = $this->form->getState();

        $record = Item::create($data);

        $this->form->model($record)->saveRelationships();

        Notification::make()
            ->title('Item created!')
            ->body('Item has been created successfully.')
            ->success()
            ->send();
    }

    public function render(): View
    {
        return view('livewire.items.create-item');
    }
}
