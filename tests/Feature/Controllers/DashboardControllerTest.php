<?php

namespace Tests\Feature\Controllers;

use App\Models\UseLogCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_requires_authentication()
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect('/login');
    }

    public function test_dashboard_renders_inertia_view_with_chart_counts()
    {
        $user = User::factory()->create();

        UseLogCase::factory()->create([
            'input_type' => ['text'],
            'output_type' => ['text'],
            'assistant_role' => 'tutor',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertInertia(
            fn(Assert $page) => $page
                ->component('dashboard')
                ->has('chartCounts')
                ->has('chartCounts.inputs.labels')
                ->has('chartCounts.inputs.values')
                ->has('chartCounts.outputs.labels')
                ->has('chartCounts.outputs.values')
                ->has('chartCounts.roles.labels')
                ->has('chartCounts.roles.values')
        );
    }
}