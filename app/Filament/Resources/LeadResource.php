<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeadResource\Pages;
use App\Filament\Resources\LeadResource\RelationManagers;
use App\Livewire\MapComponent;
use App\Livewire\MapViewer;
use Carbon\Carbon;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Actions\Action;
use Homeful\KwYCCheck\Models\Lead;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Homeful\Mailmerge\Mailmerge;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;

use Filament\Infolists\Infolist;
use Filament\Infolists\Components\ImageEntry;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Joaopaulolndev\FilamentPdfViewer\Infolists\Components\PdfViewerEntry;
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
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;
use App\Actions\Disburse;
use Propaganistas\LaravelPhone\PhoneNumber;

class LeadResource extends Resource
{
    protected static ?string $model = Lead::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $recordTitleAttribute = 'meta->checkin->body->data->fieldsExtracted->fullName';

    protected static int $globalSearchResultsLimit = 20;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

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
                            ->formatStateUsing(fn (string $state): string => str_replace('ago', 'old', Carbon::parse($state)->diffForHumans()))
                            ->searchable(),
                        TextColumn::make('mobile')
                            ->formatStateUsing(fn (string $state): string => preg_replace('/(.*) (.*) (.*)/', '($1) $2-$3', phone($state, 'PH', 2)))
                            ->searchable()
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
                    }),
                Action::make('disburse')
                    ->form([
                        TextInput::make('amount')
                            ->integer()
                            ->required(),
                    ])
                    ->action(function (Lead $record, array $data) {
                        Disburse::dispatch($record, $data['amount']);
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
        $mailmerge = new \Homeful\Mailmerge\Mailmerge();
        $filePath = storage_path('Documents/test.docx');
        $converted_path =$mailmerge->generateDocument($filePath, ['buyer_name' => 'sample name'], 'test', 'public', false);
        return $infolist
            ->inlineLabel(true)
            ->schema([
                Group::make()
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
                            ->columnSpanFull(),
                        Section::make('Map')
                            ->icon('heroicon-c-map-pin')
                            ->schema([
                                Livewire::make(MapViewer::class),
                            ])->columnSpanFull(),
                    ])->columnSpan(2),

                Section::make('Uploaded Images and Documents')
                    ->inlineLabel(false)
                    ->icon('heroicon-s-arrow-up-on-square')
                    ->schema([
                        ImageEntry::make('selfie_image_url')
                            ->width(250)
                            ->label('Selfie'),
                        ImageEntry::make('id_image_url')
                            ->width(250)
                            ->label('ID'),
                        PdfViewerEntry::make('file')
                            ->label('View the PDF')
                            ->minHeight('40svh')
                            ->fileUrl('/mailmerge/converted_pdf/test.pdf')
                            ->columnSpanFull()
                    ])
                    ->columnSpan(1),

            ])->columns(3);

    }

    public static function getGlobalSearchResultTitle(Model $record): string | Htmlable
    {
        return Str::title($record->name);
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Birthdate' => $record->birthdate,
            'Address' => $record->address
        ];
    }

    public static function getGlobalSearchResultUrl(Model $record): string
    {
        return LeadResource::getUrl('view', ['record' => $record]);
    }
}
