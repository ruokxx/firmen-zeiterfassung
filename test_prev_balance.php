<?php

require 'vendor/autoload.php';

use Carbon\Carbon;

function calculatePreviousMonthBalance($year, $month)
{
    echo "Calculating previous month balance for report of $year-$month\n";

    $currentStart = Carbon::createFromDate($year, $month, 1);
    $prevDate = $currentStart->copy()->subMonth();
    $prevYear = $prevDate->year;
    $prevMonth = $prevDate->month;

    echo "Previous Month: $prevYear-$prevMonth\n";

    // 1. Calculate Target Hours for Previous Month
    $daysInPrevMonth = $prevDate->daysInMonth;
    $prevTargetHours = 0;

    for ($d = 1; $d <= $daysInPrevMonth; $d++) {
        $date = Carbon::createFromDate($prevYear, $prevMonth, $d);
        if (!$date->isWeekend()) {
            $prevTargetHours += 8;
        }
    }

    echo "Previous Month Target Hours: $prevTargetHours\n";

    // Mocking Actual Hours for testing logic availability
    // In a real scenario, this comes from DB
    $mockActualHours = 170;
    echo "Mock Actual Hours: $mockActualHours\n";

    $balance = $mockActualHours - $prevTargetHours;
    echo "Calculated Balance: $balance\n";

    if ($balance > 0)
        echo "Status: OVERTIME (Green)\n";
    else if ($balance < 0)
        echo "Status: DEFICIT (Red)\n";
    else
        echo "Status: BALANCED\n";
    echo "-----------------------------\n";
}

ob_start();

// Test Report for Feb 2026 (Prev: Jan 2026)
// Jan 2026: 22 weekdays * 8 = 176h target
// Mock Actual: 170h
// Expected Balance: -6h
calculatePreviousMonthBalance(2026, 2);

// Test Report for March 2026 (Prev: Feb 2026)
// Feb 2026: 20 weekdays * 8 = 160h target
// Mock Actual: 170h
// Expected Balance: +10h
calculatePreviousMonthBalance(2026, 3);

// Test Report for Jan 2026 (Prev: Dec 2025)
// Dec 2025 (starts Monday): 31 days. 
// Weekends: 6,7, 13,14, 20,21, 27,28 = 8 days
// Weekdays: 23 days * 8 = 184h target
// Mock Actual: 170h
// Expected Balance: -14h
calculatePreviousMonthBalance(2026, 1);

file_put_contents('test_prev_balance.txt', ob_get_clean());
