<?php

namespace App\Filament\Resources\FixedvaluecouponResource\Pages;

use App\Filament\Resources\FixedvaluecouponResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFixedvaluecoupon extends EditRecord
{
    protected static string $resource = FixedvaluecouponResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
