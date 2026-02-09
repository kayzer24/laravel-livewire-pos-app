<?php

namespace App\Livewire\Customer;

use App\Models\Customer;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class CreateCustomer extends Component implements HasActions, HasSchemas
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
                Section::make('Edit Customer')
                    ->description('Use this section to update the customer record')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->placeholder('John Doe')
                            ->required(),
                        TextInput::make('email')
                            ->placeholder('john-doe@email.com')
                            ->label('Email address')
                            ->email(),
                        TextInput::make('phone')
                            ->placeholder('+33612345678')
                            ->tel(),
                    ]),
            ])
            ->statePath('data')
            ->model(Customer::class);
    }

    public function create(): void
    {
        $data = $this->form->getState();

        $record = Customer::create($data);

        $this->form->model($record)->saveRelationships();

        Notification::make()
            ->title('Customer created!')
            ->body('Customer has been created successfully.')
            ->success()
            ->send();
    }

    public function render(): View
    {
        return view('livewire.customer.create-customer');
    }
}
