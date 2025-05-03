<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'عملاءوموردين';
    protected static ?string $navigationLabel = 'العملاء';
    protected static ?string $label = 'عميل';
    protected static ?string $pluralLabel = 'العملاء';
    protected static ?string $slug = 'customers';
    protected static ?string $recordTitleAttribute = 'name';        
    protected static ?string $modelLabel = 'العملاء';
    protected static ?string $pluralModelLabel = 'العملاء';



    

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
                    Tables\Actions\ViewAction::make()->visible(auth()->user()->hasPermissionTo('view_customer')),
                    Tables\Actions\EditAction::make()->visible(auth()->user()->hasPermissionTo('update_customer')),
                    Tables\Actions\DeleteAction::make()->visible(auth()->user()->hasPermissionTo('delete_customer')),
                    Tables\Actions\ForceDeleteAction::make()->visible(auth()->user()->hasPermissionTo('force_delete_customer')),
                    Tables\Actions\RestoreAction::make()->visible(auth()->user()->hasPermissionTo('restore_customer')),
                ]), 
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                
                    Tables\Actions\ForceDeleteBulkAction::make()->visible(auth()->user()->hasPermissionTo('force_delete_customer')),
                    Tables\Actions\RestoreBulkAction::make()->visible(auth()->user()->hasPermissionTo('restore_customer')),
                    Tables\Actions\DeleteBulkAction::make()->visible(auth()->user()->hasPermissionTo('delete_customer')),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageCustomers::route('/'),
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
        return auth()->user()->hasPermissionTo('create_customer');
    }

    public static function canViewany(): bool
    {
        return auth()->user()->hasPermissionTo('view_customer');
    
}

}
