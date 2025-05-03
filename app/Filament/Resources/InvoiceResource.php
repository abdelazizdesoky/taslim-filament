<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Invoice;
use App\Models\Product;
use Filament\Forms\Form;
use Filament\Tables\Table;
use GuzzleHttp\Promise\Create;
use Illuminate\Validation\Rule;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\IconColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\InvoiceResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\InvoiceResource\RelationManagers;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
    return $form
        ->schema([

          Section::make()
                ->schema([
                    Section::make('بيانات الفاتورة')
                    ->description('إضافة بيانات الفاتورة')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('code')
                                    ->label('رقم الفاتورة')
                                    ->required()
                                    ->maxLength(255)
                                    ->rules(fn (string $context) => $context === 'create'
                                    ? [Rule::unique('invoices', 'code')]
                                    : [])
                                    ->disabled(fn (string $context) => $context === 'edit') ,
                            ]),
                Forms\Components\Select::make('invoice_type')
                    ->label('نوع الفاتورة')
                    ->placeholder('اختر نوع الفاتورة')
                    ->options([
                        1 => 'مرتجع',
                        2 => 'استلام',
                        3 => 'تسليم',
                    ])
                    ->required()
                    ->reactive(),
                Forms\Components\DatePicker::make('invoice_date')
                    ->label('تاريخ الفاتورة')
                    ->placeholder('اختر تاريخ الفاتورة')
                    ->required(),
                Forms\Components\Select::make('location_id')
                   ->label('اسم الموقع')
                    ->placeholder('اختر الموقع')
                    ->relationship('location', 'location_name')
                    ->required(),
                Forms\Components\Select::make('employee_id')
                    ->label('اسم الموظف')
                    ->placeholder('اختر الموظف')
                    ->relationship('user', 'name')
                    ->options(function () {
                        return \App\Models\User::all()->mapWithKeys(function ($user) {
                            return [$user->id => $user->name . ' - ' . $user->id];
                        });
                    })
                    
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('supplier_id')
                    ->label('اسم المورد')
                    ->placeholder('اختر المورد')
                    ->relationship('supplier', 'name')
                    ->visible(fn (Forms\Get $get) => $get('invoice_type') === '2'|| $get('invoice_type') === '1')
                    ->options(function () {
                        return \App\Models\Supplier::all()->mapWithKeys(function ($supplier) {
                            return [$supplier->id => $supplier->name . ' - ' . $supplier->code];
                        });
                    })
                    ->searchable(),
                Forms\Components\Select::make('customer_id')
                    ->label('اسم العميل')
                    ->placeholder('اختر العميل')
                    ->relationship('customer', 'name')
                    ->visible(fn (Forms\Get $get) => $get('invoice_type') === '3' || $get('invoice_type') === '1')
                    ->options(function () {
                        return \App\Models\Customer::all()->mapWithKeys(function ($customer) {
                            return [$customer->id => $customer->name . ' - ' . $customer->code];
                        });
                    })
                    ->searchable(),
                    Forms\Components\Select::make('created_by')
                    ->label('تم إنشاؤه بواسطة')

                    ->relationship('createdBy', 'name') 
                    ->default(auth()->id())
                    ->disabled(fn (string $context) => $context === 'create') 
                    ->required(fn (string $context) => $context === 'edit'), 
                Forms\Components\Select::make('invoice_status')
                    ->label('حالة ')
                    ->placeholder('اختر الحالة')
                    ->options([
                        1=> 'مفتوح',
                        2=> 'مغلق',
                        3=> 'مؤرشف',
                    ])->default(1)
                    ->required(),
                Forms\Components\Textarea::make('notes'),
            ])
            ->columns(3),

            Forms\Components\Section::make('منتجات الفاتورة')
            ->schema([
                Repeater::make('invoice_products') // تأكد أن الاسم مطابق للعلاقة
                    ->relationship('invoice_products') // ربط التكرار بالعلاقة hasMany
                    ->collapsible()
                    ->grid(2)
                    ->columns(2)
                    ->createItemButtonLabel('إضافة منتج')
                    ->schema([
                        Forms\Components\Select::make('product_id')
                            ->label('اسم المنتج')
                            ->placeholder('اختر المنتج')
                            ->options(fn () => \App\Models\Product::pluck('product_name', 'id')->toArray())
                            ->searchable()
                            ->required(),
        
                        Forms\Components\TextInput::make('quantity')
                            ->label('الكمية')
                            ->required()
                            ->numeric()
                            ->minValue(1),
                    ]),
        
                ]),
            
        ])
        ]);


    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                
                Tables\Columns\TextColumn::make('code')
                    ->label('رقم الفاتورة')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('invoice_type')
                    ->label('نوع الفاتورة')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn ($state) => [
                        1 => 'مرتجع',
                        2 => 'استلام',
                        3 => 'تسليم',
                    ][$state] ?? 'غير معروف')
                    ->color(fn ($record) => 
                    match ($record->invoice_type) {
                         1 => 'success',
                         2 => 'danger',
                         3 => 'warning',
                         default => 'secondary',
                     }),
                Tables\Columns\TextColumn::make('invoice_date')
                    ->label('تاريخ الفاتورة')
                    ->date()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('location.location_name')
                    ->label('اسم الموقع'),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('اسم الموظف'),
                Tables\Columns\TextColumn::make('supplier.name')
                    ->label('اسم المورد'),
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('اسم العميل'),
                Tables\Columns\TextColumn::make('createdBy.name')
                    ->label('تم إنشاؤه بواسطة'),
               Tables\Columns\IconColumn::make('invoice_status')
                    ->label('حالة الفاتورة')
               ->icon(fn ($record) => 
               match ($record->invoice_status) {
                    1 => 'heroicon-c-receipt-percent',
                    2 => 'heroicon-m-lock-closed',
                    3 => 'heroicon-o-check-circle',
                    default => 'heroicon-o-question-mark-circle',
                })
                ->color(fn ($record) => 
                match ($record->invoice_status) {
                     1 => 'success',
                     2 => 'danger',
                     3 => 'warning',
                     default => 'secondary',
                 })
                 
                    ->label('حالة الفاتورة')
                    ->tooltip(fn ($record) => $record->invoice_status == 1 ? 'مفتوح' : 'مغلق'),
           
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()->visible(auth()->user()->hasPermissionTo('view_invoice')),
                    Tables\Actions\EditAction::make()->visible(auth()->user()->hasPermissionTo('update_invoice')),
                    Tables\Actions\DeleteAction::make()->visible(auth()->user()->hasPermissionTo('delete_invoice')),
                    Tables\Actions\ForceDeleteAction::make()->visible(auth()->user()->hasPermissionTo('force_delete_invoice')),
                    Tables\Actions\RestoreAction::make()->visible(auth()->user()->hasPermissionTo('restore_invoice')),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->visible(auth()->user()->hasPermissionTo('delete_invoice')),
                    Tables\Actions\ForceDeleteBulkAction::make()->visible(auth()->user()->hasPermissionTo('force_delete_invoice')),
                    Tables\Actions\RestoreBulkAction::make()->visible(auth()->user()->hasPermissionTo('restore_invoice')),
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
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'view' => Pages\ViewInvoice::route('/{record}'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();

        return parent::getEloquentQuery()
            ->when(!$user->hasRole('admin') && !$user->hasRole('deliver'), function ($query) use ($user) {
                return $query->where('created_by', $user->id);
            })
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
               return auth()->user()->hasPermissionTo('create_invoice');
           }
    
           public static function canViewany(): bool
           {
               return auth()->user()->hasPermissionTo('view_invoice');
           }
}
