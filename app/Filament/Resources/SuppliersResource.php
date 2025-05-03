<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Supplier;
use Filament\Forms\Form;
use App\Models\Suppliers;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\SuppliersResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SuppliersResource\RelationManagers;

class SuppliersResource extends Resource
{
    protected static ?string $model = Supplier::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'عملاءوموردين';
    protected static ?string $navigationLabel = 'الموردين';
    protected static ?string $label = 'مورد';
    protected static ?string $pluralLabel = 'الموردين';
    protected static ?string $slug = 'suppliers';
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $modelLabel = 'الموردين';
    protected static ?string $pluralModelLabel = 'الموردين';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('address')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Toggle::make('status')
                    ->label('Status')
                    ->default(true)
                    ->onColor('success')
                    ->offColor('danger')
                    ->inline()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Code')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('address')
                    ->label('Address')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\BooleanColumn::make('status')
                    ->label('Status')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()->visible(auth()->user()->hasPermissionTo('view_suppliers')),
                    Tables\Actions\EditAction::make()->visible(auth()->user()->hasPermissionTo('update_suppliers')),
                    Tables\Actions\DeleteAction::make()->visible(auth()->user()->hasPermissionTo('delete_suppliers')),
                    Tables\Actions\ForceDeleteAction::make()->visible(auth()->user()->hasPermissionTo('force_delete_suppliers')),
                    Tables\Actions\RestoreAction::make()->visible(auth()->user()->hasPermissionTo('restore_suppliers')),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\ForceDeleteBulkAction::make()->visible(auth()->user()->hasPermissionTo('force_delete_suppliers')),
                    Tables\Actions\RestoreBulkAction::make()->visible(auth()->user()->hasPermissionTo('restore_suppliers')),
                    Tables\Actions\DeleteBulkAction::make()->visible(auth()->user()->hasPermissionTo('delete_suppliers')),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageSuppliers::route('/'),
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
        return auth()->user()->hasPermissionTo('create_suppliers');
    }

    public static function canViewany(): bool
    {
        return auth()->user()->hasPermissionTo('view_suppliers');
    }
}
