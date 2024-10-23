<?php

namespace App\Traits\LeadResource;

use Filament\Infolists\Components\{Actions\Action, Group, ImageEntry, Livewire, Section, TextEntry};
use Joaopaulolndev\FilamentPdfViewer\Infolists\Components\PdfViewerEntry;
use Filament\Support\Enums\FontWeight;
use Filament\Infolists\Infolist;
use App\Livewire\MapViewer;

trait LeadInfoList
{
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
                                TextEntry::make('lead.name')
                                    ->label('Name')
                                    ->weight(FontWeight::Bold)
                                    ->columnSpan([
                                        'sm' => 6,
                                        'xl' => 3,
                                        '2xl' => 3,
                                        'default' => 6
                                    ]),
                                TextEntry::make('lead.birthdate')
                                    ->label('BirthDate')
                                    ->weight(FontWeight::Bold)
                                    ->columnSpan([
                                        'sm' => 6,
                                        'xl' => 3,
                                        '2xl' => 3,
                                        'default' => 6
                                    ]),
                                TextEntry::make('lead.mobile')
                                    ->label('Mobile')
                                    ->weight(FontWeight::Bold)
                                    ->columnSpan([
                                        'sm' => 6,
                                        'xl' => 3,
                                        '2xl' => 3,
                                        'default' => 6
                                    ]),
                                TextEntry::make('lead.email')
                                    ->label('Email')
                                    ->weight(FontWeight::Bold)
                                    ->columnSpan([
                                        'sm' => 6,
                                        'xl' => 3,
                                        '2xl' => 3,
                                        'default' => 6
                                    ]),
                                TextEntry::make('lead.id_type')
                                    ->label('ID Type')
                                    ->weight(FontWeight::Bold)
                                    ->columnSpan([
                                        'sm' => 6,
                                        'xl' => 3,
                                        '2xl' => 3,
                                        'default' => 6
                                    ]),
                                TextEntry::make('lead.id_number')
                                    ->label('ID Number')
                                    ->weight(FontWeight::Bold)
                                    ->columnSpan([
                                        'sm' => 6,
                                        'xl' => 3,
                                        '2xl' => 3,
                                        'default' => 6
                                    ]),
                                TextEntry::make('lead.identifier')
                                    ->label('Identifier')
                                    ->weight(FontWeight::Bold)
                                    ->columnSpan([
                                        'sm' => 6,
                                        'xl' => 3,
                                        '2xl' => 3,
                                        'default' => 6
                                    ]),
                                TextEntry::make('lead.code')
                                    ->label('code')
                                    ->weight(FontWeight::Bold)
                                    ->columnSpan([
                                        'sm' => 6,
                                        'xl' => 3,
                                        '2xl' => 3,
                                        'default' => 6
                                    ]),
                                TextEntry::make('lead.choice')
                                    ->label('Stock Keeping Unit')
                                    ->weight(FontWeight::Bold)
                                    ->columnSpan([
                                        'sm' => 6,
                                        'xl' => 3,
                                        '2xl' => 3,
                                        'default' => 6
                                    ]),
                                TextEntry::make('lead.address')
                                    ->label('Address')
                                    ->weight(FontWeight::Bold)
                                    ->columnSpan(6),
                                TextEntry::make('lead.answer')
                                    ->label('Answer')
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
                        ImageEntry::make('lead.selfie_image_url')
                            ->width(250)
                            ->visibility('private')
                            ->label('Selfie'),
                        ImageEntry::make('lead.id_image_url')
                            ->width(250)
                            ->visibility('private')
                            ->label('ID'),
                        PdfViewerEntry::make('file')
                            ->label('View the PDF')
                            ->minHeight('40svh')
                            ->hintAction(
                                Action::make('view_document')
                                    ->label('Open in New Tab')
                                    ->url('/mailmerge/converted_pdf/test.pdf')
                                    ->openUrlInNewTab()
                            )
                            ->fileUrl('/mailmerge/converted_pdf/test.pdf')
                            ->columnSpanFull()
                    ])
                    ->columnSpan(1),

            ])->columns(3);

    }
}
