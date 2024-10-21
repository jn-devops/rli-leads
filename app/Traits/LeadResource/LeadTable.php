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
use Filament\Tables\Grouping\Group;
use Illuminate\Database\Eloquent\Builder;

trait LeadTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->defaultGroup(
                Group::make('meta->checkin->body->campaign->name')
                    ->label('Campaign')
                    ->getTitleFromRecordUsing(fn (Lead $record): string => ucfirst($record->campaign))
                    ->collapsible()
            )
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
                            ->searchable(
                                query: function (Builder $query, string $search): Builder {
                                    return $query->whereRaw("JSON_EXTRACT(meta, '$.checkin.body.data.fieldsExtracted.fullName') LIKE ?", ["%{$search}%"]);
                                }
                            ),
                        TextColumn::make('address')
                            ->searchable(
                                query: function (Builder $query, string $search): Builder {
                                    return $query->whereRaw("JSON_EXTRACT(meta, '$.checkin.body.data.fieldsExtracted.address') LIKE ?", ["%{$search}%"]);
                                }
                            ),
                        TextColumn::make('birthdate')
                            ->formatStateUsing(fn (string $state): string => str_replace('ago', 'old', Carbon::parse($state)->diffForHumans())),
                        TextColumn::make('mobile')
                            ->formatStateUsing(fn (string $state): string => preg_replace('/(.*) (.*) (.*)/', '($1) $2-$3', phone($state, 'PH', 2)))
                            ->searchable(
                                query: function (Builder $query, string $search): Builder {
                                    return $query->whereRaw("JSON_EXTRACT(meta, '$.checkin.body.inputs.mobile') LIKE ?", ["%{$search}%"]);
                                }
                            )
                    ]),
                ])->from('md'),
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
