<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeadResource\Pages;
use App\Filament\Resources\LeadResource\RelationManagers;
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
                TextColumn::make('name'),
                TextColumn::make('address'),
                TextColumn::make('birthdate'),
                TextColumn::make('mobile'),
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
                    ->action(function (Model $record, array $data) {
                        SMS::channel('engagespark')->from('TXTCMDR')->to($record->mobile)->content($data['message'])->send();
                    })
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'create' => Pages\CreateLead::route('/create'),
            'view' => Pages\ViewLead::route('/{record}'),
            'edit' => Pages\EditLead::route('/{record}/edit'),
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('name'),
                TextEntry::make('address'),
                TextEntry::make('birthdate'),
                TextEntry::make('mobile'),
                TextEntry::make('email'),
                TextEntry::make('location'),
                TextEntry::make('identifier'),
                TextEntry::make('code'),
                TextEntry::make('choice'),
                TextEntry::make('answer'),
                TextEntry::make('id_type'),
                TextEntry::make('id_number'),
                ImageEntry::make('selfie_image_url'),
                ImageEntry::make('id_image_url'),
            ]);
    }
}
