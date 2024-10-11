<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeadResource\Pages;
use App\Filament\Resources\LeadResource\RelationManagers;
use App\Livewire\MapComponent;
use Carbon\Carbon;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Actions\Action;
use Homeful\KwYCCheck\Models\Lead;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\ImageEntry;
use LBHurtado\SMS\Facades\SMS;
use Filament\Forms\Components\RichEditor;
use Illuminate\Database\Eloquent\Collection;
use Filament\Infolists\Components\Section;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Infolists\Components\Livewire;
use App\Notifications\Adhoc;

class LeadResource extends Resource
{
    protected static ?string $model = Lead::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name'),
                TextInput::make('address'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Split::make([
                    ImageColumn::make('selfie_image_url')
                        ->grow(false)
                        ->width(70)
                        ->height(70)
                        ->circular(),
                    Stack::make([
                        TextColumn::make('name')
                            ->weight(FontWeight::Bold)
                            ->searchable(),
                        TextColumn::make('address')
                            ->searchable(),
                        TextColumn::make('birthdate')
                            ->searchable(),
                        TextColumn::make('mobile')
                            ->searchable(),
                    ]),
                ])->from('md')
                // TextColumn::make('name'),
                // TextColumn::make('mobile'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Action::make('send')
                    ->form([
                        TextInput::make('message')->required(),
                    ])
                    ->action(function (Lead $record, array $data) {
                        $record->notify(new Adhoc($data['message']));
                    })
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    BulkAction::make('sms')
                        ->label('Send SMS')
                        ->form([
                            TextInput::make('message')->required(),
                        ])
                        ->action(function (Collection $records, array $data) {
                            $records->each(function(Lead $record) use($data) {
                                $record->notify(new Adhoc($data['message']));
                            });
                        })
                        ->deselectRecordsAfterCompletion()
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeads::route('/'),
            // 'create' => Pages\CreateLead::route('/create'),
            'view' => Pages\ViewLead::route('/{record}'),
            'edit' => Pages\EditLead::route('/{record}/edit'),
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->inlineLabel(true)
            ->schema([
                Section::make('Personal Data')
                    ->icon('heroicon-s-user-circle')
                    ->schema([
                        TextEntry::make('name')
                                ->weight(FontWeight::Bold)
                                ->columnSpan([
                                    'sm' => 6,
                                    'xl' => 3,
                                    '2xl' => 3,
                                    'default' => 6
                                ]),
                        TextEntry::make('birthdate')
                                ->weight(FontWeight::Bold)
                                ->columnSpan([
                                    'sm' => 6,
                                    'xl' => 3,
                                    '2xl' => 3,
                                    'default' => 6
                                ]),
                        TextEntry::make('mobile')
                                ->weight(FontWeight::Bold)
                                ->columnSpan([
                                    'sm' => 6,
                                    'xl' => 3,
                                    '2xl' => 3,
                                    'default' => 6
                                ]),
                        TextEntry::make('email')
                                ->weight(FontWeight::Bold)
                                ->columnSpan([
                                    'sm' => 6,
                                    'xl' => 3,
                                    '2xl' => 3,
                                    'default' => 6
                                ]),
                        TextEntry::make('id_type')
                                ->label('ID Type')
                                ->weight(FontWeight::Bold)
                                ->columnSpan([
                                    'sm' => 6,
                                    'xl' => 3,
                                    '2xl' => 3,
                                    'default' => 6
                                ]),
                        TextEntry::make('id_number')
                                ->label('ID Number')
                                ->weight(FontWeight::Bold)
                                ->columnSpan([
                                    'sm' => 6,
                                    'xl' => 3,
                                    '2xl' => 3,
                                    'default' => 6
                                ]),
                        TextEntry::make('identifier')
                                ->weight(FontWeight::Bold)
                                ->columnSpan([
                                    'sm' => 6,
                                    'xl' => 3,
                                    '2xl' => 3,
                                    'default' => 6
                                ]),
                        TextEntry::make('code')
                                ->weight(FontWeight::Bold)
                                ->columnSpan([
                                    'sm' => 6,
                                    'xl' => 3,
                                    '2xl' => 3,
                                    'default' => 6
                                ]),
                        TextEntry::make('choice')
                                ->label('Stock Keeping Unit')
                                ->weight(FontWeight::Bold)
                                ->columnSpan([
                                    'sm' => 6,
                                    'xl' => 3,
                                    '2xl' => 3,
                                    'default' => 6
                                ]),
                        TextEntry::make('address')
                                ->weight(FontWeight::Bold)
                                ->columnSpan(6),
                        TextEntry::make('answer')
                                ->weight(FontWeight::Bold)
                                ->columnSpan(6),
                    ])
                    ->columns(6)
                    ->columnSpan(2),
                Section::make('Uploaded Images')
                    ->inlineLabel(false)
                    ->icon('heroicon-s-arrow-up-on-square')
                    ->schema([
                        ImageEntry::make('selfie_image_url')
                            ->width(250)
                            ->label('Selfie'),
                        ImageEntry::make('id_image_url')
                            ->width(250)
                            ->label('ID'),
                    ])
                    ->columnSpan(1),
                Section::make('Map')
                    ->icon('heroicon-c-map-pin')
                    ->schema([
                        TextEntry::make('location')
                                ->columnSpan(3),
                       Livewire::make(MapComponent::class)
                            ->key(Carbon::now()->format('Y-m-d H:i:s'))
                            ->columnSpanFull(),
                    ]),
            ])->columns(3);

    }
}
