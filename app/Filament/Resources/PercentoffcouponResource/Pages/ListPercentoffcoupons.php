<?php

namespace App\Filament\Resources\PercentoffcouponResource\Pages;

use App\Filament\Resources\PercentoffcouponResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPercentoffcoupons extends ListRecords
{
    protected static string $resource = PercentoffcouponResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
