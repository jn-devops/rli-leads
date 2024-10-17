<?php

namespace App\Traits\LeadResource;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;

trait LeadForm
{
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name'),
                TextInput::make('address'),
            ]);
    }
}
