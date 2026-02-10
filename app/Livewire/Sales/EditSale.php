<?php

namespace App\Livewire\Sales;

use App\Models\Sale;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class EditSale extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public Sale $record;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill($this->record->attributesToArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Edit Sale')
                    ->description('Use this section to update the Sale record')
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
                Section::make('Order Items')
                    ->columns(1)
                    ->schema([
                        Repeater::make('saleItems')
                            ->relationship()
                            ->columns(3)
                            ->schema([
                                Select::make('item_id')
                                    ->relationship('item', 'name'),
                                TextInput::make('quantity')->numeric()->readOnly(),
                                TextInput::make('price')->numeric()
                                    ->readOnly(),
                            ])
                            ->orderColumn('sort')

                            ->disabled(),
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
            ->title('Sale updated!')
            ->body("Sale N°\"{$this->record->id}\" has been updated successfully.")
            ->success()
            ->send();
    }

    public function render(): View
    {
        return view('livewire.sales.edit-sale');
    }
}
