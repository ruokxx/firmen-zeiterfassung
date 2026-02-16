<?php

require 'vendor/autoload.php';

use Carbon\Carbon;

function calculateTargetHours($year, $month)
{
    echo "Calculating target hours for $year-$month\n";
    $date = Carbon::createFromDate($year, $month, 1);
    $daysInMonth = $date->daysInMonth;
    $targetHours = 0;

    for ($d = 1; $d <= $daysInMonth; $d++) {
        $currentDate = Carbon::createFromDate($year, $month, $d);
        if (!$currentDate->isWeekend()) {
            $targetHours += 8;
        }
    }

    echo "Days in month: $daysInMonth\n";
    echo "Target Hours: $targetHours\n";
    echo "-----------------------------\n";
}

ob_start();

// Test Feb 2026 (28 days, starts Sunday)
// Weekends: 1, 7, 8, 14, 15, 21, 22, 28 = 8 days
// Weekdays: 20 days
// Expected: 160 hours
calculateTargetHours(2026, 2);

// Test Jan 2026 (31 days, starts Thursday)
// Weekends: 3, 4, 10, 11, 17, 18, 24, 25, 31 = 9 days
// Weekdays: 22 days
// Expected: 176 hours
calculateTargetHours(2026, 1);

file_put_contents('target_hours_result.txt', ob_get_clean());
