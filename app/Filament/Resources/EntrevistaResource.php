<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EntrevistaResource\Pages;
use App\Models\Entrevista;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EntrevistaResource extends Resource
{
    protected static ?string $model = Entrevista::class;
    protected static ?string $navigationIcon = 'heroicon-o-video-camera';
    protected static ?string $navigationLabel = 'Entrevistas';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('candidato_id')
                ->label('Candidato')
                ->relationship('candidato', 'nombre')
                ->required()
                ->searchable()
                ->preload(),
            Forms\Components\TextInput::make('titulo')
                ->label('Título')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('youtube_url')
                ->label('URL de YouTube')
                ->required()
                ->maxLength(255)
                ->url()
                ->helperText('Ejemplo: https://www.youtube.com/watch?v=XXXXX'),
            Forms\Components\DatePicker::make('fecha')
                ->label('Fecha')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->selectable(false)
            ->columns([
                Tables\Columns\TextColumn::make('candidato.nombre')
                    ->label('Candidato')
                    ->sortable()
                    ->weight('medium'),
                Tables\Columns\TextColumn::make('candidato.partido')
                    ->label('Partido')
                    ->badge()
                    ->color('primary'),
                Tables\Columns\ColorColumn::make('candidato.color_hex')
                    ->label('Color'),
                Tables\Columns\TextColumn::make('titulo')
                    ->label('Título')
                    ->searchable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('youtube_url')
                    ->label('Enlace')
                    ->url(fn ($record) => $record->youtube_url)
                    ->openUrlInNewTab()
                    ->limit(30),
                Tables\Columns\TextColumn::make('fecha')
                    ->label('Fecha')
                    ->date()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEntrevistas::route('/'),
            'create' => Pages\CreateEntrevista::route('/create'),
            'edit' => Pages\EditEntrevista::route('/{record}/edit'),
        ];
    }
}
