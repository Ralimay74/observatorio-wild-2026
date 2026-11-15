<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PropuestaResource\Pages;
use App\Models\Propuesta;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;

class PropuestaResource extends Resource
{
    protected static ?string $model = Propuesta::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Propuestas';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('candidato_id')
                ->label('Candidato')
                ->relationship('candidato', 'nombre')
                ->required()
                ->searchable()
                ->preload(),
            Forms\Components\TextInput::make('tema')
                ->label('Tema')
                ->required()
                ->maxLength(255)
                ->helperText('Ej: Vivienda, Sanidad, Economía, Empleo, Inmigración...'),
            Forms\Components\TextInput::make('titulo')
                ->label('Título')
                ->required()
                ->maxLength(255),
            Forms\Components\Textarea::make('descripcion')
                ->label('Descripción')
                ->required()
                ->rows(4),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->selectable(false)
            ->defaultGroup('candidato.nombre')
            ->columns([
                Tables\Columns\ColorColumn::make('candidato.color_hex')
                    ->label('Color'),
                Tables\Columns\TextColumn::make('tema')
                    ->label('Tema')
                    ->badge()
                    ->color('success')
                    ->searchable(),
                Tables\Columns\TextColumn::make('titulo')
                    ->label('Título')
                    ->searchable()
                    ->weight('medium'),
                Tables\Columns\TextColumn::make('descripcion')
                    ->label('Descripción')
                    ->limit(50),
            ])
            ->actions([
                Action::make('comparar')
                    ->label('Comparar')
                    ->icon('heroicon-o-arrows-right-left')
                    ->color('warning')
                    ->modalHeading(fn ($record) => "Comparativa: {$record->tema}")
                    ->modalContent(fn ($record) => view('filament.comparativa', ['propuesta' => $record]))
                    ->modalWidth('4xl'),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPropuestas::route('/'),
            'create' => Pages\CreatePropuesta::route('/create'),
            'edit' => Pages\EditPropuesta::route('/{record}/edit'),
        ];
    }
}
