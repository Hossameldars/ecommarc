<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Category;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationLabel = 'المنتجات';

    protected static ?string $modelLabel = 'منتج';

    protected static ?string $pluralModelLabel = 'المنتجات';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('اسم المنتج')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('catagory_id')
                            ->label('التصنيف')
                            ->options(Category::all()->pluck('name', 'id'))
                            ->searchable()
                            ->required(),

                        Forms\Components\TextInput::make('price')
                            ->label('السعر')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->default(0)
                            ->minValue(0),

                        Forms\Components\FileUpload::make('image')
                            ->label('الصورة')
                            ->image()
                            ->imageEditor()
                            ->directory('products')
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('description')
                            ->label('الوصف')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('الصورة')
                    ->square(),

                Tables\Columns\TextColumn::make('name')
                    ->label('اسم المنتج')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('التصنيف')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('price')
                    ->label('السعر')
                    ->money('USD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('description')
                    ->label('الوصف')
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإضافة')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('catagory_id')
                    ->label('التصنيف')
                    ->options(Category::all()->pluck('name', 'id')),

                Tables\Filters\Filter::make('price')
                    ->label('نطاق السعر')
                    ->form([
                        Forms\Components\TextInput::make('price_from')
                            ->label('من')
                            ->numeric()
                            ->prefix('$'),
                        Forms\Components\TextInput::make('price_until')
                            ->label('إلى')
                            ->numeric()
                            ->prefix('$'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['price_from'],
                                fn (Builder $query, $price) => $query->where('price', '>=', $price),
                            )
                            ->when(
                                $data['price_until'],
                                fn (Builder $query, $price) => $query->where('price', '<=', $price),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'view'   => Pages\ViewProduct::route('/{record}'),
            'edit'   => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}