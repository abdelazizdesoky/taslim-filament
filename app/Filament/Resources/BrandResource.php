<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BrandResource\Pages;
use App\Filament\Resources\BrandResource\RelationManagers;
use App\Models\Brand;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BrandResource extends Resource
{
    protected static ?string $model = Brand::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'منتجات';
    protected static ?int $navigationSort = 2;
    protected static ?string $label = 'ماركة';
    protected static ?string $pluralLabel = 'الماركات';
    protected static ?string $slug = 'brands';
    protected static ?string $recordTitleAttribute = 'brand_name';
    protected static ?string $navigationLabel = 'الماركات';
    protected static ?string $modelLabel = 'Brand';
    protected static ?string $pluralModelLabel = 'الماركات';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make(__('brand_name'))
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
           
                Tables\Columns\TextColumn::make('brand_name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Created At'),
               
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                Tables\Actions\ViewAction::make()->visible(auth()->user()->hasPermissionTo('view_brand')),
                 Tables\Actions\EditAction::make()->visible(auth()->user()->hasPermissionTo('update_brand')),
                 Tables\Actions\DeleteAction::make()->visible(auth()->user()->hasPermissionTo('delete_brand')),
                Tables\Actions\ForceDeleteAction::make()->visible(auth()->user()->hasPermissionTo('force_delete_brand')),
                Tables\Actions\RestoreAction::make()->visible(auth()->user()->hasPermissionTo('restore_brand')),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->visible(auth()->user()->hasPermissionTo('delete_brand')),
                    Tables\Actions\ForceDeleteBulkAction::make()->visible(auth()->user()->hasPermissionTo('force_delete_brand')),
                    Tables\Actions\RestoreBulkAction::make()->visible(auth()->user()->hasPermissionTo('restore_brand')),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageBrands::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
          // roles 
          public static function canCreate(): bool
          {
              return auth()->user()->hasPermissionTo('create_brand');
          }
   
          public static function canViewany(): bool
          {
              return auth()->user()->hasPermissionTo('view_brand');
          }
}
