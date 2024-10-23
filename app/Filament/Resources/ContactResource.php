<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactResource\Pages;
use App\Filament\Resources\ContactResource\RelationManagers;
use App\Models\Contact;
use App\Traits\ContactTable;
use App\Traits\LeadResource\LeadForm;
use App\Traits\LeadResource\LeadInfoList;
use App\Traits\LeadResource\LeadTable;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class ContactResource extends Resource
{
    use LeadForm, LeadTable, LeadInfoList;
    protected static ?string $model = Contact::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $recordTitleAttribute = 'name';

	protected static ?string $navigationLabel='Leads';

	protected static ?string $label='Leads';

    protected static int $globalSearchResultsLimit = 20;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
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
            'index' => Pages\ListContacts::route('/'),
//            'create' => Pages\CreateContact::route('/create'),
//            'edit' => Pages\EditContact::route('/{record}/edit'),
            'view'=>Pages\ContactView::route('/{record}'),
        ];
    }
    public static function getGlobalSearchResultTitle(Model $record): string | Htmlable
    {
        return Str::title($record->name);
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Birthdate' => $record->lead->birthdate,
            'Address' => $record->lead->address
        ];
    }

    public static function getGlobalSearchResultUrl(Model $record): string
    {
        return ContactResource::getUrl('view', ['record' => $record]);
    }
}
