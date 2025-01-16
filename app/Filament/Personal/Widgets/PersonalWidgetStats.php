<?php

namespace App\Filament\Personal\Widgets;

use App\Models\Holiday;
use App\Models\Timesheet;
use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PersonalWidgetStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Holidays Pending', $this->getTotalHolidaysByUserAndType(auth()->user(), 'pending')),
            Stat::make('Holidays Approved', $this->getTotalHolidaysByUserAndType(auth()->user(), 'approved')),
            Stat::make('Total Work', $this->getTotalWork(auth()->user())),
        ];
    }

    protected function getTotalHolidaysByUserAndType(User $user, $type)
    {
        return Holiday::where('type', $type)
            ->where('user_id', $user->id)
            ->count();
    }

    protected function getTotalWork(User $user)
    {
        $timesheets = Timesheet::where('user_id', $user->id)
            ->where('type', 'work')
            ->get();

        $sumSeconds = 0;
        foreach ($timesheets as $timesheet) {
            $startTime = Carbon::parse($timesheet->day_in);
            $finalTime = Carbon::parse($timesheet->day_out);

            $totalDuration = $finalTime->diffInSeconds($startTime);
            $sumSeconds += $totalDuration;
        }

        return gmdate('H:i:s', $sumSeconds);
    }
}
