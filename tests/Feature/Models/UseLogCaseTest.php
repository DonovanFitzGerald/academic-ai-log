<?php

namespace Tests\Feature\Models;

use App\Models\UseLog;
use App\Models\UseLogCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UseLogCaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_mass_assigns_attributes()
    {
        $useLog = UseLog::factory()->create();

        $useLogCase = UseLogCase::create([
            'use_log_id' => $useLog->id,
            'position' => 1,
            'label' => 'Generation',
            'evidence' => 'Generated text',
            'input_type' => ['text'],
            'output_type' => ['text'],
            'assistant_role' => 'tutor',
            'confidence' => 90,
        ]);

        $this->assertEquals($useLog->id, $useLogCase->use_log_id);
        $this->assertEquals(1, $useLogCase->position);
        $this->assertEquals('Generation', $useLogCase->label);
        $this->assertEquals('Generated text', $useLogCase->evidence);
        $this->assertEquals('tutor', $useLogCase->assistant_role);
        $this->assertEquals(90, $useLogCase->confidence);
    }

    public function test_it_belongs_to_use_log()
    {
        $useLog = UseLog::factory()->create();
        $useLogCase = UseLogCase::factory()->create(['use_log_id' => $useLog->id]);

        $this->assertInstanceOf(UseLog::class, $useLogCase->useLog);
        $this->assertEquals($useLog->id, $useLogCase->useLog->id);
    }

    public function test_it_casts_arrays()
    {
        $useLog = UseLog::factory()->create();

        $useLogCase = UseLogCase::create([
            'use_log_id' => $useLog->id,
            'position' => 1,
            'label' => 'Generation',
            'evidence' => 'Generated text',
            'input_type' => ['text', 'code'],
            'output_type' => ['text'],
            'assistant_role' => 'tutor',
            'confidence' => 90,
        ]);

        $useLogCase->refresh();

        $this->assertIsArray($useLogCase->input_type);
        $this->assertEquals(['text', 'code'], $useLogCase->input_type);

        $this->assertIsArray($useLogCase->output_type);
        $this->assertEquals(['text'], $useLogCase->output_type);
    }
}
