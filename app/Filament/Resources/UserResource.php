<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon   = 'heroicon-o-users';
    protected static ?string $navigationLabel  = 'المستخدمين';
    protected static ?string $modelLabel       = 'مستخدم';
    protected static ?string $pluralModelLabel = 'المستخدمين';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('البيانات الشخصية')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('الاسم')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                            ->label('البريد الإلكتروني')
                            ->email()
                            ->required()
                            ->unique(User::class, 'email', ignoreRecord: true)
                            ->maxLength(255),

                        Forms\Components\TextInput::make('password')
                            ->label('كلمة المرور')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $operation) => $operation === 'create')
                            ->minLength(8)
                            ->maxLength(255),

                        Forms\Components\DateTimePicker::make('email_verified_at')
                            ->label('تاريخ التحقق من الإيميل')
                            ->nullable(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('البريد الإلكتروني')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\IconColumn::make('email_verified_at')
                    ->label('تم التحقق')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->getStateUsing(fn ($record) => !is_null($record->email_verified_at)),

                Tables\Columns\TextColumn::make('orders_count')
                    ->label('الطلبات')
                    ->counts('orders')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ التسجيل')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('email_verified')
                    ->label('تم التحقق من الإيميل')
                    ->query(fn ($query) => $query->whereNotNull('email_verified_at')),

                Tables\Filters\Filter::make('email_not_verified')
                    ->label('لم يتم التحقق')
                    ->query(fn ($query) => $query->whereNull('email_verified_at')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),

                // ─── تأكيد الإيميل يدوياً ─────────────────
                Tables\Actions\Action::make('verifyEmail')
                    ->label('تأكيد الإيميل')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn ($record) => is_null($record->email_verified_at))
                    ->requiresConfirmation()
                    ->modalHeading('تأكيد الإيميل')
                    ->modalDescription('هل تريد تأكيد إيميل هذا المستخدم يدوياً؟')
                    ->modalSubmitActionLabel('نعم، تأكيد')
                    ->action(function ($record) {
                        $record->update(['email_verified_at' => now()]);

                        \Filament\Notifications\Notification::make()
                            ->title('تم تأكيد الإيميل بنجاح')
                            ->success()
                            ->send();
                    }),

                // ─── حذف المستخدم ─────────────────────────
                Tables\Actions\DeleteAction::make()
                    ->label('حذف')
                    ->modalHeading('حذف المستخدم')
                    ->modalDescription('هل أنت متأكد؟ سيتم حذف جميع بيانات المستخدم.')
                    ->modalSubmitActionLabel('نعم، احذف')
                    ->successNotificationTitle('تم حذف المستخدم بنجاح'),
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
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view'   => Pages\ViewUser::route('/{record}'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}