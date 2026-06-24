<?php

namespace App\Filament\Pages;

use App\Models\Setting as SettingsModel;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class Settings extends Page
{
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'Settings';

    protected static string | UnitEnum | null $navigationGroup = 'System';

    protected static ?int $navigationSort = 1;

    public string $business_name = '';

    public $business_logo = null;

    public string $currency = 'USD';

    public string $phone = '';

    public string $email = '';

    public string $address = '';

    public function mount(): void
    {
        $settings = SettingsModel::get();
        $this->business_name = $settings->business_name;
        $this->business_logo = $settings->business_logo;
        $this->currency = $settings->currency;
        $this->phone = $settings->phone ?? '';
        $this->email = $settings->email ?? '';
        $this->address = $settings->address ?? '';
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Business Information')
                    ->columns(2)
                    ->schema([
                        TextInput::make('business_name')
                            ->label('Business Name')
                            ->required()
                            ->maxLength(255),
                        FileUpload::make('business_logo')
                            ->label('Logo')
                            ->image()
                            ->disk('public')
                            ->directory('business')
                            ->maxSize(1024),
                        Select::make('currency')
                            ->options([
                                'USD' => 'USD ($)',
                                'EUR' => 'EUR (€)',
                                'GBP' => 'GBP (£)',
                                'ILS' => 'ILS (₪)',
                                'JOD' => 'JOD (د.ا)',
                            ])
                            ->required(),
                        TextInput::make('phone')
                            ->label('Phone')
                            ->tel()
                            ->maxLength(30),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255),
                        Textarea::make('address')
                            ->label('Address')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Settings')
                ->action('save'),
        ];
    }

    public function save(): void
    {
        $settings = SettingsModel::get();
        $settings->update([
            'business_name' => $this->business_name,
            'business_logo' => $this->business_logo,
            'currency' => $this->currency,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
        ]);

        Notification::make()
            ->title('Settings saved successfully')
            ->success()
            ->send();
    }
}
