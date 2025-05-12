<?php

namespace App\Filament\Resources;

use Closure;
use Filament\Forms;
use Filament\Tables;
use App\Models\Product;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\ProductType;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Actions\Action;
use App\Filament\Resources\ProductResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProductResource\RelationManagers;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'منتجات';
    protected static ?int $navigationSort = 1;
    protected static ?string $label = 'منتجات';
    protected static ?string $pluralLabel = 'المنتجات';
    protected static ?string $slug = 'products';
    protected static ?string $recordTitleAttribute = 'product_name';
    protected static ?string $navigationLabel = 'المنتجات';
    protected static ?string $modelLabel = 'Product';
    protected static ?string $pluralModelLabel = 'منتجات';
   


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('product_name')
                    ->required()
                    ->maxLength(255)
                    ->label('Product Name'),
              
                Forms\Components\TextInput::make('detail_name')
                    ->required()
                    ->maxLength(255)
                    ->label('detail_name'),
                Forms\Components\TextInput::make('product_code')
                    ->label('product_code')
                    ->required()
                    ->maxLength(255),

                    Select::make('type_id')
                    ->label('Brand and Type')
                    ->options(function () {
                        return \App\Models\ProductType::with('brand')->get()->mapWithKeys(function ($type) {
                            return [$type->id => $type->brand->brand_name . ' - ' . $type->type_name];
                        });
                    })
                    ->searchable()
                    ->required()
                    ->suffixActions([
                        Action::make('addProductType')
                        ->icon('heroicon-o-plus')
                        ->tooltip('إضافة نوع')
                        ->url(route('filament.admin.resources.product-types.index'))
                        ->openUrlInNewTab(),
                    ]),
                
                    
            
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product_name')
                    ->sortable()
                    ->searchable()
                    ->label('Product Name'),
                Tables\Columns\TextColumn::make('detail_name')
                    ->sortable()
                    ->searchable()
                    ->label('Detail Name'),
                Tables\Columns\TextColumn::make('product_code')
                    ->sortable()
                    ->searchable()
                    ->label('Product Code'),
        
                Tables\Columns\TextColumn::make('product_type.brand.brand_name')
                    ->sortable()
                    ->searchable()
                    ->label('Brand'),
                Tables\Columns\TextColumn::make('product_type.type_name')
                    ->sortable()
                    ->searchable()
                    ->label('Type'),
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
                    Tables\Actions\ViewAction::make()->visible(auth()->user()->hasPermissionTo('view_product')),
                    Tables\Actions\EditAction::make()->visible(auth()->user()->hasPermissionTo('update_product')),
                    Tables\Actions\DeleteAction::make()->visible(auth()->user()->hasPermissionTo('delete_product')),
                    Tables\Actions\ForceDeleteAction::make()->visible(auth()->user()->hasPermissionTo('force_delete_product')),
                    Tables\Actions\RestoreAction::make()->visible(auth()->user()->hasPermissionTo('restore_product')),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->visible(auth()->user()->hasPermissionTo('delete_product')),
                    Tables\Actions\ForceDeleteBulkAction::make()->visible(auth()->user()->hasPermissionTo('force_delete_product')),
                    Tables\Actions\RestoreBulkAction::make()->visible(auth()->user()->hasPermissionTo('restore_product')),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
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
              return auth()->user()->hasPermissionTo('create_product');
          }
   
          public static function canViewany(): bool
          {
              return auth()->user()->hasPermissionTo('view_product');
          }
}
