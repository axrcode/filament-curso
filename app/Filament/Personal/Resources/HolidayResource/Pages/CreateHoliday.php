<?php

namespace App\Filament\Personal\Resources\HolidayResource\Pages;

use App\Filament\Personal\Resources\HolidayResource;
use App\Mail\HolidayPending;
use App\Models\User;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Mail;

class CreateHoliday extends CreateRecord
{
    protected static string $resource = HolidayResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->user()->id;
        $data['type'] = 'pending';

        $user = User::first();

        $dataToSend = [
            'day' => $data['day'],
            'name' => User::find($data['user_id'])->name,
            'email' => User::find($data['user_id'])->email,
        ];

        Mail::to($user)->send(new HolidayPending($dataToSend));

        $recipient = auth()->user();

        Notification::make()
            ->title('Vacation Request')
            ->body('On '.$data['day'].' it is pending approval')
            ->sendToDatabase($recipient);

        return $data;
    }
}
