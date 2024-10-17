<?php

namespace App\Filament\Resources;

use App\Traits\LeadResource\{LeadInfoList, LeadForm, LeadTable};
use App\Filament\Resources\LeadResource\Pages;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Resource;
use Illuminate\Support\Str;
use App\Models\Lead;

class LeadResource extends Resource
{
    use LeadForm, LeadTable, LeadInfoList;

    protected static ?string $model = Lead::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $recordTitleAttribute = 'meta->checkin->body->data->fieldsExtracted->fullName';

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
            'index' => Pages\ListLeads::route('/'),
            'view' => Pages\ViewLead::route('/{record}'),
            'edit' => Pages\EditLead::route('/{record}/edit'),
        ];
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
