<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VotoResource\Pages;
use App\Models\Voto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class VotoResource extends Resource
{
    protected static ?string $model = Voto::class;
    protected static ?string $navigationIcon = 'heroicon-o-check-circle';
    protected static ?string $navigationLabel = 'Votos';
    protected static ?string $navigationGroup = 'Estadísticas';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('candidato_id')
                ->relationship('candidato', 'nombre')
                ->disabled(),
            Forms\Components\TextInput::make('ip_address')->disabled(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->selectable(false)
            ->columns([
                Tables\Columns\TextColumn::make('candidato.nombre')->sortable()->weight('medium')->badge(),
                Tables\Columns\TextColumn::make('ip_address')->label('IP'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVotos::route('/'),
        ];
    }
}
