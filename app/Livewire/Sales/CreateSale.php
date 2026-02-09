<?php

namespace App\Livewire\Sales;

use App\Models\Sale;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class CreateSale extends Component implements HasActions, HasSchemas
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
                Section::make('Create new Sale')
                    ->description('use the form bellow to create a new Sale')
                    ->columns(2)
                    ->schema([
                        Select::make('customer_id')
                            ->relationship('customer', 'name'),
                        Select::make('payment_method_id')
                            ->relationship('paymentMethod', 'name'),
                        TextInput::make('total')
                            ->required()
                            ->numeric(),
                        TextInput::make('paid_amount')
                            ->required()
                            ->numeric(),
                        TextInput::make('discount')
                            ->required()
                            ->numeric()
                            ->default(0),
                    ]),

            ])
            ->statePath('data')
            ->model(Sale::class);
    }

    public function create(): void
    {
        $data = $this->form->getState();

        $record = Sale::create($data);

        $this->form->model($record)->saveRelationships();

        Notification::make()
            ->title('Sale created!')
            ->body('Sale has been created successfully.')
            ->success()
            ->send();
    }

    public function render(): View
    {
        return view('livewire.sales.create-sale');
    }
}
