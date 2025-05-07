<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

function extractYearsByStrategy($digits) {
    $validYears = [];
    $minYear = 1900;
    $maxYear = 2099;

    // Separate single-digit and two-digit values
    $singleDigits = array_filter($digits, fn($d) => $d < 10);
    $doubleDigits = array_filter($digits, fn($d) => $d >= 10);

    // 1. Four single-digit numbers
    foreach (permutations($singleDigits, 4) as $perm) {
        $year = intval(implode('', $perm));
        if ($year >= $minYear && $year <= $maxYear) {
            $validYears[] = $year;
        }
    }

    // 2. Two 2-digit numbers
    foreach ($doubleDigits as $i => $first) {
        foreach ($doubleDigits as $j => $second) {
            if ($i === $j) continue;
            $year = intval($first . $second);
            if ($year >= $minYear && $year <= $maxYear) {
                $validYears[] = $year;
            }
        }
    }

    // 3. One 2-digit + two single digits (try all positions)
    foreach ($doubleDigits as $twoDigit) {
        foreach (permutations($singleDigits, 2) as $pair) {
            // Three ways to insert 2-digit number into the 4-digit string
            $year1 = intval($twoDigit . $pair[0] . $pair[1]);
            $year2 = intval($pair[0] . $twoDigit . $pair[1]);
            $year3 = intval($pair[0] . $pair[1] . $twoDigit);

            foreach ([$year1, $year2, $year3] as $y) {
                if ($y >= $minYear && $y <= $maxYear) {
                    $validYears[] = $y;
                }
            }
        }
    }

    // Remove duplicates and sort
    $validYears = array_unique($validYears);

    return $validYears;
}

// --- Helper function ---
function permutations($items, $size) {
    if ($size === 1) return array_map(fn($i) => [$i], $items);

    $result = [];
    foreach ($items as $i => $item) {
        $remaining = $items;
        unset($remaining[$i]);
        foreach (permutations(array_values($remaining), $size - 1) as $perm) {
            $result[] = array_merge([$item], $perm);
        }
    }
    return $result;
}

Route::get('/count-occurence', function () {
    $input = [26,18,13,13,17,22,24,22,1,9,3,14,2,8,12,12,14];
    $days = 0;
    $months = 0;
    $years = 0;
    

    // Count days and months directly
    foreach ($input as $value) {
        if ($value >= 1 && $value <= 12) {
            $months++;
            $days++; // 1–12 are valid as both months and days
        } elseif ($value >= 13 && $value <= 31) {
            $days++;
        }
    }

    $years = extractYearsByStrategy(array_unique($input));

    return response()->json([
        'days' => $days,
        'months' => $months,
        'years' => count($years),
        'years_values' => $years,
    ]);
});
