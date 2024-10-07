<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CouponResource\Pages;
use App\Filament\Resources\CouponResource\RelationManagers;
use App\Models\Coupons\Coupon;
use App\Models\Coupons\FixedValueCoupon;
use App\Models\Coupons\PercentOffCoupon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationLabel = 'Cupones';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationGroup = 'Administrar pedido';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->label('Código')
                    ->required(), 
                Forms\Components\Select::make('couponable_id')
                    ->label('Valor Fijo')
                    ->relationship('couponable', 'value') 
                    ->visible(fn ($get) => $get('couponable_type') === 'App\Models\Coupons\FixedValueCoupon')
                    ->reactive() 
                    ->options(function () {
                        return \App\Models\Coupons\FixedValueCoupon::all()->pluck('value', 'id');
                    }),
                Forms\Components\Select::make('couponable_id')
                    ->label('Porcentaje de descuento')
                    ->relationship('couponable', 'percent_off')
                    ->visible(fn ($get) => $get('couponable_type') === 'App\Models\Coupons\PercentOffCoupon')
                    ->reactive() 
                    ->searchable()
                     ->preload()
                    ->options(function () {
                        return \App\Models\Coupons\PercentOffCoupon::all()->pluck('percent_off', 'id');
                    })->default(null),
    
                Forms\Components\Select::make('couponable_type')
                    ->label('Tipo de descuento')
                    ->options([
                        'App\Models\Coupons\PercentOffCoupon' => 'Porcentual',
                        'App\Models\Coupons\FixedValueCoupon' => 'Valor fijo',
                    ])
                    ->required() 
                    ->reactive() 
                    ->afterStateUpdated(fn ($set) => $set('couponable_id', null)) 
            ]);
    }
    
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')->label('Codigo'),
                Tables\Columns\TextColumn::make('couponable.percent_off')->label('Porcentaje (%)'),
                Tables\Columns\TextColumn::make('couponable.value')->label('Valor'),
                Tables\Columns\TextColumn::make('couponable_type')
                ->label('Tipo')
                ->formatStateUsing(function ($state) {
                return match ($state) {
                'App\Models\Coupons\PercentOffCoupon' => 'Porcentual',
                'App\Models\Coupons\FixedValueCoupon' => 'Valor fijo',
                 default => 'Desconocido',
                 };
            }),

                Tables\Columns\TextColumn::make('created_at')->label('Fecha de creacion')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                /*Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),*/
            ])->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->button()->label(''),
                Tables\Actions\DeleteAction::make()->button()->label(''),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
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
            'index' => Pages\ListCoupons::route('/'),
            'create' => Pages\CreateCoupon::route('/create'),
            'edit' => Pages\EditCoupon::route('/{record}/edit'),
        ];
    }  
    
    public static function getPluralModelLabel(): string
    {
        return __('Cupones');
    }
}
