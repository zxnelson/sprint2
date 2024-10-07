<?php

namespace App\Filament\Resources\FixedvaluecouponResource\Pages;

use App\Filament\Resources\FixedvaluecouponResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFixedvaluecoupons extends ListRecords
{
    protected static string $resource = FixedvaluecouponResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
