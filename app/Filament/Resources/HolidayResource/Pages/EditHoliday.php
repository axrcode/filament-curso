<?php

namespace App\Filament\Resources\HolidayResource\Pages;

use App\Filament\Resources\HolidayResource;
use App\Mail\HolidayApproved;
use App\Mail\HolidayDecline;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;

class EditHoliday extends EditRecord
{
    protected static string $resource = HolidayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);

        $user = User::find($record->user_id);

        $dataToSend = [
            'day' => $record->day,
            'name' => $user->name,
            'email' => $user->email,
        ];

        if ( $record->type == 'approved' ) {
            Mail::to($user)->send(new HolidayApproved($dataToSend));
        }
        else if ( $record->type == 'decline' ) {
            Mail::to($user)->send(new HolidayDecline($dataToSend));
        }

        return $record;
    }
}
