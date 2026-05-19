<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon   = 'heroicon-o-shopping-cart';
    protected static ?string $navigationLabel  = 'الطلبات';
    protected static ?string $modelLabel       = 'طلب';
    protected static ?string $pluralModelLabel = 'الطلبات';

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('number')
                    ->label('رقم الطلب')
                    ->searchable()
                    ->copyable()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('المستخدم')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('products_count')
                    ->label('عدد المنتجات')
                    ->counts('products')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_price')
                    ->label('الإجمالي')
                    ->money('USD')
                    ->getStateUsing(fn ($record) => $record->total_price),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('حالة الطلب')
                    ->colors([
                        'warning' => 'Pending',
                        'success' => 'Completed',
                        'danger'  => 'Cancelled',
                    ])
                    ->formatStateUsing(fn ($state) => match($state) {
                        'Pending'   => 'قيد الانتظار',
                        'Completed' => 'مكتمل',
                        'Cancelled' => 'ملغي',
                    }),

                Tables\Columns\BadgeColumn::make('payment_status')
                    ->label('حالة الدفع')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'paid',
                        'danger'  => 'failed',
                    ])
                    ->formatStateUsing(fn ($state) => match($state) {
                        'pending' => 'قيد الانتظار',
                        'paid'    => 'مدفوع',
                        'failed'  => 'فشل',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الطلب')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('حالة الطلب')
                    ->options([
                        'Pending'   => 'قيد الانتظار',
                        'Completed' => 'مكتمل',
                        'Cancelled' => 'ملغي',
                    ]),

                Tables\Filters\SelectFilter::make('payment_status')
                    ->label('حالة الدفع')
                    ->options([
                        'pending' => 'قيد الانتظار',
                        'paid'    => 'مدفوع',
                        'failed'  => 'فشل',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),

                Tables\Actions\Action::make('changeStatus')
                    ->label('تغيير الحالة')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->form([
                        Forms\Components\Select::make('status')
                            ->label('حالة الطلب')
                            ->options([
                                'Pending'   => 'قيد الانتظار',
                                'Completed' => 'مكتمل',
                                'Cancelled' => 'ملغي',
                            ])
                            ->required(),

                        Forms\Components\Select::make('payment_status')
                            ->label('حالة الدفع')
                            ->options([
                                'pending' => 'قيد الانتظار',
                                'paid'    => 'مدفوع',
                                'failed'  => 'فشل',
                            ])
                            ->required(),
                    ])
                    ->fillForm(fn ($record) => [
                        'status'         => $record->status,
                        'payment_status' => $record->payment_status,
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'status'         => $data['status'],
                            'payment_status' => $data['payment_status'],
                        ]);

                        \Filament\Notifications\Notification::make()
                            ->title('تم تغيير الحالة بنجاح')
                            ->success()
                            ->send();
                    }),

                // ─── حذف الطلب ────────────────────────────
                Tables\Actions\DeleteAction::make()
                    ->label('حذف')
                    ->modalHeading('حذف الطلب')
                    ->modalDescription('هل أنت متأكد من حذف هذا الطلب؟ لا يمكن التراجع عن هذا الإجراء.')
                    ->modalSubmitActionLabel('نعم، احذف')
                    ->successNotificationTitle('تم حذف الطلب بنجاح'),
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
            'index' => Pages\ListOrders::route('/'),
            'view'  => Pages\ViewOrder::route('/{record}'),
            'edit'  => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
      public static function canview($record): bool
    {
        return false;
    }
}