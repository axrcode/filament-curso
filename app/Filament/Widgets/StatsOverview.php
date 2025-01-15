<?php

namespace App\Filament\Widgets;

use App\Models\Holiday;
use App\Models\Timesheet;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalEmployees = User::count();
        $totalHolidays = Holiday::where('type', 'pending')->count();
        $totalTimesheets = Timesheet::count();

        return [
            Stat::make('Employees', $totalEmployees),
            Stat::make('Holidays Pending', $totalHolidays),
            Stat::make('Timesheets', $totalTimesheets),
        ];
    }
}
