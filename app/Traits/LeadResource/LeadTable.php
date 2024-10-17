<?php

namespace App\Traits\LeadResource;

use Filament\Tables\Actions\{BulkActionGroup, DeleteBulkAction};
use Filament\Tables\Columns\{ImageColumn, TextColumn};
use Filament\Tables\Actions\{BulkAction, ViewAction};
use Filament\Tables\Columns\Layout\{Split, Stack};
use Illuminate\Database\Eloquent\Collection;
use Filament\Forms\Components\TextInput;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\Action;
use App\Notifications\Adhoc;
use Filament\Tables\Table;
use App\Actions\Disburse;
use App\Models\Lead;
use Carbon\Carbon;

trait LeadTable
{
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
                ViewAction::make(),
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
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
}
