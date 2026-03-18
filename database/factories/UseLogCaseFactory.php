<?php

namespace Database\Factories;

use App\Models\UseLog;
use App\Models\UseLogCase;
use Illuminate\Database\Eloquent\Factories\Factory;

class UseLogCaseFactory extends Factory
{
    protected $model = UseLogCase::class;

    private const INPUT_TYPES = [
        'question',
        'instructions',
        'draft text',
        'outline',
        'code',
        'data',
        'image prompt',
        'error message',
        'reflection notes',
        'research topic',
        'citation request',
        'mixed',
    ];

    private const OUTPUT_TYPES = [
        'explanation',
        'summary',
        'rewrite',
        'ideas',
        'outline',
        'code',
        'debugging help',
        'examples',
        'feedback',
        'grammar correction',
        'structure advice',
        'citations',
        'plan',
        'mixed',
    ];

    private const ROLES = [
        'tutor',
        'editor',
        'brainstorm partner',
        'coding assistant',
        'research assistant',
        'formatter',
        'reviewer',
    ];

    private const CONFIDENCE = ['High', 'Medium', 'Low'];

    public function definition(): array
    {
        $input = $this->faker->randomElements(self::INPUT_TYPES, $this->faker->numberBetween(1, 2));
        $output = $this->faker->randomElements(self::OUTPUT_TYPES, $this->faker->numberBetween(1, 2));

        return [
            'use_log_id' => UseLog::factory(),
            'position' => 1,
            'label' => $this->faker->sentence(6),
            'evidence' => $this->faker->sentence(16),
            'input_type' => array_values($input),
            'output_type' => array_values($output),
            'assistant_role' => $this->faker->randomElement(self::ROLES),
            'confidence' => $this->faker->randomElement(self::CONFIDENCE),
        ];
    }
}