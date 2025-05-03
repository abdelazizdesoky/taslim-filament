<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LocationResource\Pages;
use App\Filament\Resources\LocationResource\RelationManagers;
use App\Models\Location;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LocationResource extends Resource
{
    protected static ?string $model = Location::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('location_name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('location_name')
                    ->label('Location Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime()
                    ->sortable(),
      
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()->visible(auth()->user()->hasPermissionTo('view_location')),
                    Tables\Actions\EditAction::make()->visible(auth()->user()->hasPermissionTo('update_location')),
                    Tables\Actions\DeleteAction::make()->visible(auth()->user()->hasPermissionTo('delete_location')),
                    Tables\Actions\ForceDeleteAction::make()->visible(auth()->user()->hasPermissionTo('force_delete_location')),
                    Tables\Actions\RestoreAction::make()->visible(auth()->user()->hasPermissionTo('restore_location')),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\ForceDeleteBulkAction::make()->visible(auth()->user()->hasPermissionTo('force_delete_location')),
                    Tables\Actions\RestoreBulkAction::make()->visible(auth()->user()->hasPermissionTo('restore_location')),
                    Tables\Actions\DeleteBulkAction::make()->visible(auth()->user()->hasPermissionTo('delete_location')),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageLocations::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
    public static function getNavigationBadge(): ?string
{
    return static::getModel()::count();
}

        //role permission
        public static function canCreate(): bool
        {
            return auth()->user()->hasPermissionTo('create_location');
        }
    
        public static function canViewany(): bool
        {
            return auth()->user()->hasPermissionTo('view_location');
        }
}
