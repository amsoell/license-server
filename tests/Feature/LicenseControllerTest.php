<?php

use App\License;
use Laravel\Lumen\Testing\DatabaseMigrations;

class ExampleTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function it_approves_new_licenses()
    {
        $machine_id = uniqid();
        $route = sprintf('/api/v1/checkin/%s', $machine_id);
        $response = $this->post($route)->response;

        $this->assertEquals(200, $response->status());
    }

    /**
     * @test
     */
    public function it_approves_existing_licenses()
    {
        $license = factory(License::class)->create();
        $route = sprintf('/api/v1/checkin/%s', $license->machine_id);
        $response = $this->post($route)->response;

        $this->assertEquals(200, $response->status());
    }

    /**
     * @test
     */
    public function it_disapproves_invalidated_licenses()
    {
        $license = factory(License::class)->state('invalidated')->create();
        $route = sprintf('/api/v1/checkin/%s', $license->machine_id);
        $response = $this->post($route)->response;

        $this->assertEquals(403, $response->status());
    }

    /**
     * @test
     */
    public function it_logs_checkins()
    {
        $machine_id = uniqid();
        $route = sprintf('/api/v1/checkin/%s', $machine_id);
        $this->post($route);

        $this->seeInDatabase('licenses', [
            'machine_id' => $machine_id,
        ]);
        $this->seeInDatabase('checkins', [
            'machine_id' => $machine_id,
        ]);
    }
}
