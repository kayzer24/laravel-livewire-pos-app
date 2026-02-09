<?php

namespace App\Livewire\Management;

use App\Models\PaymentMethod;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class CreatePaymentMethod extends Component implements HasActions, HasSchemas
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
                Section::make('Edit Payment Method')
                    ->description('Use this section to update the payment method record')
                    ->columns(1)
                    ->schema([
                        TextInput::make('name')
                            ->placeholder('Bank Transfer')
                            ->required(),
                        Textarea::make('description')
                            ->placeholder('Payment method description in few words')
                            ->maxLength(3000),
                    ]),
            ])
            ->statePath('data')
            ->model(PaymentMethod::class);
    }

    public function create(): void
    {
        $data = $this->form->getState();

        $record = PaymentMethod::create($data);

        $this->form->model($record)->saveRelationships();

        Notification::make()
            ->title('Payment Method created!')
            ->body('Payment Method has been created successfully.')
            ->success()
            ->send();
    }

    public function render(): View
    {
        return view('livewire.management.create-payment-method');
    }
}
