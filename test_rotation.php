<?php

use Carbon\Carbon;
use Illuminate\Support\Collection;

// Mocking Carbon and Collection behavior since we don't have the full app loaded
require 'vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

function testMonthRotation($year, $month)
{
    $startOfMonth = Carbon::createFromDate($year, $month, 1);
    $daysInMonth = $startOfMonth->daysInMonth;

    echo "Testing rotation for $year-$month\n";
    echo "Today is: " . Carbon::today()->format('Y-m-d') . "\n";
    echo "Start of month: " . $startOfMonth->format('Y-m-d') . "\n";

    // Generate all days of the month
    $calendarDays = collect();
    for ($day = 1; $day <= $daysInMonth; $day++) {
        $calendarDays->push($startOfMonth->copy()->addDays($day - 1));
    }

    // If it's the current month, rotate the collection so today is first
    if ($startOfMonth->isCurrentMonth()) {
        $today = Carbon::today();
        // Find the index of today (day of month - 1)
        $dayIndex = $today->day - 1;

        // Rotate: slice from today to end, then slice from start to today
        $part1 = $calendarDays->slice($dayIndex);
        $part2 = $calendarDays->slice(0, $dayIndex);
        $calendarDays = $part1->merge($part2);
        echo "Rotation applied.\n";
    }
    else {
        echo "No rotation needed.\n";
    }

    echo "First day in list: " . $calendarDays->first()->format('Y-m-d') . "\n";
    echo "Last day in list: " . $calendarDays->last()->format('Y-m-d') . "\n";
    echo "--------------------------------------------------\n";
}

ob_start();

// Test Case 1: Current Month
testMonthRotation(Carbon::now()->year, Carbon::now()->month);

// Test Case 2: Past Month
testMonthRotation(2025, 1);

// Test Case 3: Future Month
testMonthRotation(2028, 1);

$output = ob_get_clean();
file_put_contents('result_utf8.txt', $output);
echo "Done.";
