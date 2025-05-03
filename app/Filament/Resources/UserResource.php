<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TrashedFilter;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use App\Filament\Resources\UserResource\RelationManagers;
use BezhanSalleh\FilamentShield\Traits\HasShieldFormComponents;

class UserResource extends Resource
{
    use HasShieldFormComponents;
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Filament Shield';
    protected static ?string $navigationLabel = '  Users';
    protected static ?string $label = 'User';
    protected static ?string $pluralLabel = 'Users';
    protected static ?string $slug = 'users';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('email')
                ->required()
                ->email()
                ->maxLength(255),
            Forms\Components\TextInput::make('password')
                ->password()
                ->required()
                ->confirmed()
                ->maxLength(255)
                ->dehydrated(fn ($state) => (bool) $state),
  
            Forms\Components\TextInput::make('password_confirmation')
                ->password()
                ->maxLength(255)
                ->dehydrated(false),
  

            Forms\Components\Select::make('roles')
            ->relationship('roles', 'name')
            ->required()
            ->preload()
            ->searchable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('role')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()->visible(auth()->user()->hasPermissionTo('update_user')),
                    Tables\Actions\DeleteAction::make()->visible(auth()->user()->hasPermissionTo('delete_user')),
                    Tables\Actions\ForceDeleteAction::make()->visible(auth()->user()->hasPermissionTo('force_delete_user')),
                    Tables\Actions\RestoreAction::make()->visible(auth()->user()->hasPermissionTo('restore_user')),
                ]),
            ])
            ->bulkActions([

                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->visible(auth()->user()->hasPermissionTo('delete_user')),
                    Tables\Actions\ForceDeleteBulkAction::make()->visible(auth()->user()->hasPermissionTo('force_delete_user')),
                    Tables\Actions\RestoreBulkAction::make()->visible(auth()->user()->hasPermissionTo('restore_user')),
                ]),
            ]);
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
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

       // roles 
       public static function canCreate(): bool
       {
           return auth()->user()->hasPermissionTo('create_user');
       }

       public static function canViewany(): bool
       {
           return auth()->user()->hasPermissionTo('view_user');
       }
    }