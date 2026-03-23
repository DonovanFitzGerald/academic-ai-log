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
                'inputs' => $this->formatLabels($inputCounts),
                'outputs' => $this->formatLabels($outputCounts),
                'roles' => $this->formatLabels($roleCounts),
            ],
        ]);
    }

    private function formatLabels(array $counts): array
    {
        $labels = array_keys($counts);
        $values = array_values($counts);
        return ['labels' => $labels, 'values' => $values];
    }
}