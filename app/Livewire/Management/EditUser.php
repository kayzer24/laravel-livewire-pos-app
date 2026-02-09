<?php

namespace App\Livewire\Management;

use App\Models\User;
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

class EditUser extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public User $record;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill($this->record->attributesToArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Edit User')
                    ->description('Use this section to update the user record')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required(),
                        TextInput::make('email')
                            ->unique(ignoreRecord: true)
                            ->label('Email address')
                            ->email()
                            ->required(),
                        Select::make('role')
                            ->options([
                                'cashier' => 'Cashier',
                                'admin' => 'Admin',
                            ])
                            ->native()
                            ->required()
                            ->default('cashier'),
                        TextInput::make('password')
                            ->password()
                            ->revealable()
                            ->unique(ignoreRecord: true),
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
            ->title('User updated!')
            ->body("User \"{$this->record->name}\" has been updated successfully.")
            ->success()
            ->send();
    }

    public function render(): View
    {
        return view('livewire.management.edit-user');
    }
}
