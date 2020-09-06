<?php

namespace Canvas\Tests\Http\Middleware;

use Canvas\Models\User;
use Canvas\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Class AdminTest.
 *
 * @covers \Canvas\Http\Middleware\Admin
 */
class AdminTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array
     */
    public function protectedRoutesProvider(): array
    {
        return [
            // Tag routes...
            ['GET', 'canvas/api/tags'],
            ['GET', 'canvas/api/tags/create'],
            ['POST', 'canvas/api/tags/not-a-tag'],
            ['DELETE', 'canvas/api/tags/not-a-tag'],

            // Topic routes...
            ['GET', 'canvas/api/topics'],
            ['GET', 'canvas/api/topics/create'],
            ['POST', 'canvas/api/topics/not-a-topic'],
            ['DELETE', 'canvas/api/topics/not-a-topic'],

            // User routes...
            ['GET', 'canvas/api/users'],
            ['GET', 'canvas/api/users/create'],
            ['POST', 'canvas/api/users/not-a-user'],
            ['DELETE', 'canvas/api/users/not-a-user'],

            // Search routes...
            ['GET', 'canvas/api/search/tags'],
            ['GET', 'canvas/api/search/topics'],
            ['GET', 'canvas/api/search/users'],
        ];
    }

    /**
     * @test
     * @dataProvider protectedRoutesProvider
     * @param $method
     * @param $endpoint
     */
    public function it_restricts_contributors_access($method, $endpoint)
    {
        $user = factory(User::class)->create([
            'role' => User::CONTRIBUTOR,
        ]);

        $this->actingAs($user, 'canvas')->call($method, $endpoint)->assertForbidden();
    }

    /**
     * @test
     * @dataProvider protectedRoutesProvider
     * @param $method
     * @param $endpoint
     */
    public function it_restricts_editors_access($method, $endpoint)
    {
        $user = factory(User::class)->create([
            'role' => User::EDITOR,
        ]);

        $this->assertAuthenticatedAs($user)->call($method, $endpoint)->assertForbidden();
    }
}
