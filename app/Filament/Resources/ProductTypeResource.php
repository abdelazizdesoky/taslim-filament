<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\ProductType;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Actions\Action;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProductTypeResource\Pages;
use App\Filament\Resources\ProductTypeResource\RelationManagers;

class ProductTypeResource extends Resource
{
    protected static ?string $model = ProductType::class;

   
    protected static ?string $navigationGroup = 'منتجات';
    protected static ?int $navigationSort = 3;
    protected static ?string $label = ' صنف المنتج';
    protected static ?string $pluralLabel = ' الأصناف';
    protected static ?string $slug = 'product-types';
    protected static ?string $recordTitleAttribute = 'product_type_name';
    protected static ?string $navigationLabel = 'منتجات  الأصناف';
    protected static ?string $modelLabel = 'Product Type';
    protected static ?string $pluralModelLabel = 'اصناف المنتجات';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('type_name')
                    ->required()
                    ->maxLength(255)
                    ->label('Product Type Name'),
                Forms\Components\Select::make('brand_id')
                    ->relationship('brand', 'brand_name')
                    ->required()
                    ->label('Brand')
                    ->suffixActions([
                        Action::make('addbrand')
                        ->icon('heroicon-o-plus')
                        ->tooltip(' إضافة  علامة تجارية')
                        ->action(function (array $data) {
                            return \App\Models\Brand::create($data);
                        })
                        ->form([
                            Forms\Components\TextInput::make('brand_name')
                                ->required()
                                ->maxLength(255)
                                ->label('Brand Name'),
                        ])
                        
                        ->openUrlInNewTab(),
                    ]),
              
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type_name')
                    ->sortable()
                    ->searchable()
                    ->label('Product Type Name'),
                Tables\Columns\TextColumn::make('brand.brand_name')
                    ->sortable()
                    ->searchable()
                    ->label('Brand'),
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
                Tables\Actions\ViewAction::make()->visible(auth()->user()->hasPermissionTo('view_product::type')),
                 Tables\Actions\EditAction::make()->visible(auth()->user()->hasPermissionTo('update_product::type')),
                 Tables\Actions\DeleteAction::make()->visible(auth()->user()->hasPermissionTo('delete_product::type')),
                Tables\Actions\ForceDeleteAction::make()->visible(auth()->user()->hasPermissionTo('force_delete_product::type')),
                Tables\Actions\RestoreAction::make()->visible(auth()->user()->hasPermissionTo('restore_product::type')),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->visible(auth()->user()->hasPermissionTo('delete_product::type')),
                    Tables\Actions\ForceDeleteBulkAction::make()->visible(auth()->user()->hasPermissionTo('force_delete_product::type')),
                    Tables\Actions\RestoreBulkAction::make()->visible(auth()->user()->hasPermissionTo('restore_product::type')),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageProductTypes::route('/'),
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
          return auth()->user()->hasPermissionTo('create_product::type');
      }

      public static function canViewany(): bool
      {
          return auth()->user()->hasPermissionTo('view_product::type');
      }
}
