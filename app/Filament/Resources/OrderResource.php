<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'Administrar pedido';

    protected static ?string $navigationLabel = 'Pedidos';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')->label('Cliente')
                    ->relationship('user', 'name'),
               /* Forms\Components\TextInput::make('confirmation_number')
                    ->maxLength(255),*/
                Forms\Components\TextInput::make('billing_email')->label('Correo')
                    ->email()
                    ->maxLength(255),
                /*Forms\Components\TextInput::make('billing_name')
                    ->maxLength(255),
                Forms\Components\TextInput::make('billing_name_on_card')
                    ->maxLength(255),*/
                Forms\Components\TextInput::make('billing_address')->label('Direccion')
                    ->maxLength(255),
                Forms\Components\TextInput::make('billing_city')->label('Ciudad')
                    ->maxLength(255),
                /*Forms\Components\TextInput::make('billing_state')
                    ->maxLength(255),
                Forms\Components\TextInput::make('billing_zip_code')
                    ->maxLength(255),*/
                Forms\Components\TextInput::make('billing_discount')->label('Descuento')
                    ->required()
                    ->numeric()
                    ->default(0),
                /*Forms\Components\TextInput::make('billing_discount_code')
                    ->maxLength(255),*/
                Forms\Components\TextInput::make('billing_subtotal')->label('Subtotal')
                    ->numeric(),
                Forms\Components\TextInput::make('billing_tax')->label('Impuesto')
                    ->numeric(),
                Forms\Components\TextInput::make('billing_total')->label('Total')
                    ->numeric(),
                Forms\Components\Toggle::make('shipped')->label('Envio')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->label('Cliente')
                    ->numeric()
                    ->sortable(),
                /*Tables\Columns\TextColumn::make('confirmation_number')
                    ->searchable(),*/
                Tables\Columns\TextColumn::make('billing_email')->label('Correo')
                    ->searchable(),
                /*Tables\Columns\TextColumn::make('billing_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('billing_name_on_card')
                    ->searchable(),*/
                Tables\Columns\TextColumn::make('billing_address')->label('Direccion')
                    ->searchable(),
                Tables\Columns\TextColumn::make('billing_city')->label('Ciudad')
                    ->searchable(),
                /*Tables\Columns\TextColumn::make('billing_state')
                    ->searchable(),
                Tables\Columns\TextColumn::make('billing_zip_code')
                    ->searchable(),*/
                Tables\Columns\TextColumn::make('billing_discount')->label('Descuento')
                    ->numeric()
                    ->sortable(),
                /*Tables\Columns\TextColumn::make('billing_discount_code')
                    ->searchable(),*/
                Tables\Columns\TextColumn::make('billing_subtotal')->label('Subtotal')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('billing_tax')->label('Impuesto')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('billing_total')->label('Total')
                    ->numeric()
                    ->sortable()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                        ->money(),
                    ]),
                Tables\Columns\IconColumn::make('shipped')->label('Envio')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')->label('Fecha de creacion')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->button()->label('')->color('info'),
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    } 
    
    public static function getPluralModelLabel(): string
    {
        return __('Pedidos');
    }
}
