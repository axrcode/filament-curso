<?php

namespace App\Filament\Personal\Resources\TimesheetResource\Pages;

use App\Filament\Personal\Resources\TimesheetResource;
use App\Models\Timesheet;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListTimesheets extends ListRecords
{
    protected static string $resource = TimesheetResource::class;

    protected function getHeaderActions(): array
    {
        $lastTimesheet = Timesheet::where('user_id', auth()->user()->id)->orderBy('id', 'desc')->first();

        if ( is_null($lastTimesheet) ) {

            return [
                Action::make('inwork')
                    ->label('In Work')
                    ->color('success')
                    ->keyBindings(['command+s', 'ctrl+s'])
                    ->requiresConfirmation()
                    ->action( function () {
                        $timesheet = new Timesheet();
                        $timesheet->calendar_id = 1;
                        $timesheet->user_id = auth()->user()->id;
                        $timesheet->day_in = Carbon::now();
                        $timesheet->type = 'work';
                        $timesheet->save();
                    }),
                Actions\CreateAction::make(),
            ];
        }

        return [
            Action::make('inwork')
                ->label('In Work')
                ->color('success')
                ->visible( !$lastTimesheet->day_out==null )
                ->disabled( $lastTimesheet->day_out==null )
                ->keyBindings(['command+s', 'ctrl+s'])
                ->requiresConfirmation()
                ->action( function () {
                    $timesheet = new Timesheet();
                    $timesheet->calendar_id = 1;
                    $timesheet->user_id = auth()->user()->id;
                    $timesheet->day_in = Carbon::now();
                    $timesheet->type = 'work';
                    $timesheet->save();
                }),
            Action::make('stopwork')
                ->label('Stop Work')
                ->color('success')
                ->visible( $lastTimesheet->day_out==null && $lastTimesheet->type!='pause' )
                ->disabled( !$lastTimesheet->day_out==null )
                ->requiresConfirmation()
                ->action( function () use ($lastTimesheet) {
                    $lastTimesheet->day_out = Carbon::now();
                    $lastTimesheet->update();
                }),
            Action::make('inpause')
                ->label('In Pause')
                ->color('warning')
                ->visible( $lastTimesheet->day_out==null && $lastTimesheet->type!='pause' )
                ->disabled( !$lastTimesheet->day_out==null )
                ->requiresConfirmation()
                ->action( function () use ($lastTimesheet) {
                    $lastTimesheet->day_out = Carbon::now();
                    $lastTimesheet->update();

                    $timesheet = new Timesheet();
                    $timesheet->calendar_id = 1;
                    $timesheet->user_id = auth()->user()->id;
                    $timesheet->day_in = Carbon::now();
                    $timesheet->type = 'pause';
                    $timesheet->save();
                }),
            Action::make('stoppause')
                ->label('Stop Pause')
                ->color('warning')
                ->visible( $lastTimesheet->day_out==null && $lastTimesheet->type=='pause' )
                ->disabled( !$lastTimesheet->day_out==null )
                ->requiresConfirmation()
                ->action( function () use ($lastTimesheet) {
                    $lastTimesheet->day_out = Carbon::now();
                    $lastTimesheet->update();

                    $timesheet = new Timesheet();
                    $timesheet->calendar_id = 1;
                    $timesheet->user_id = auth()->user()->id;
                    $timesheet->day_in = Carbon::now();
                    $timesheet->type = 'work';
                    $timesheet->save();
                }),
            Actions\CreateAction::make(),
        ];
    }
}
