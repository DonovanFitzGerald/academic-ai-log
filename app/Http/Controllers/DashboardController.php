<?php

namespace App\Http\Controllers;

use App\Models\UseLogCase;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $cases = UseLogCase::query()->get(['input_type', 'output_type', 'assistant_role']);

        $inputCounts = [];
        $outputCounts = [];
        $roleCounts = [];

        foreach ($cases as $c) {
            foreach ((array) $c->input_type as $t) {
                $inputCounts[$t] = ($inputCounts[$t] ?? 0) + 1;
            }
            foreach ((array) $c->output_type as $t) {
                $outputCounts[$t] = ($outputCounts[$t] ?? 0) + 1;
            }
            $r = (string) $c->assistant_role;
            if ($r !== '') {
                $roleCounts[$r] = ($roleCounts[$r] ?? 0) + 1;
            }
        }

        arsort($inputCounts);
        arsort($outputCounts);
        arsort($roleCounts);

        return Inertia::render('dashboard', [
            'chartCounts' => [
                'inputs' => $this->topNPlusOther($inputCounts, 6),
                'outputs' => $this->topNPlusOther($outputCounts, 6),
                'roles' => $this->topNPlusOther($roleCounts, 10),
            ],
        ]);
    }

    private function topNPlusOther(array $counts, int $n): array
    {
        $labels = array_keys($counts);
        $values = array_values($counts);

        $topLabels = array_slice($labels, 0, $n);
        $topValues = array_slice($values, 0, $n);

        $other = array_sum(array_slice($values, $n));
        if ($other > 0) {
            $topLabels[] = 'Other';
            $topValues[] = $other;
        }

        return ['labels' => $topLabels, 'values' => $topValues];
    }
}