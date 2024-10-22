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
                            ->visibility('private')
                            ->label('Selfie'),
                        ImageEntry::make('id_image_url')
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
