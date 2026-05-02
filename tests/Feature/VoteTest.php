<?php

namespace Tests\Feature;

use App\Models\Fighter;
use App\Models\Vote;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VoteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 投票が正常に記録される
     *
     * @test
     */
    public function vote_is_recorded_successfully()
    {
        $fighter = Fighter::factory()->create();

        $response = $this->postJson('/api/votes', [
            'fighter_id' => $fighter->id,
            'vote_type' => 'strong',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('votes', [
            'fighter_id' => $fighter->id,
            'vote_type' => 'strong',
        ]);
    }

    /**
     * 投票統計が正しく計算される
     *
     * @test
     */
    public function vote_stats_are_calculated_correctly()
    {
        $fighter = Fighter::factory()->create();
        Vote::factory()->count(10)->create(['fighter_id' => $fighter->id, 'vote_type' => 'strong']);
        Vote::factory()->count(5)->create(['fighter_id' => $fighter->id, 'vote_type' => 'weak']);

        // FighterモデルのgetVoteStats()メソッドを直接テスト
        $stats = $fighter->getVoteStats();

        $this->assertEquals(10, $stats['strong_count']);
        $this->assertEquals(5, $stats['weak_count']);
        $this->assertEquals(15, $stats['total_count']);
        $this->assertEquals(66.7, $stats['strong_percentage']);
        $this->assertEquals(33.3, $stats['weak_percentage']);
    }

    /**
     * 不正なfighter_idでエラーが返される
     *
     * @test
     */
    public function invalid_fighter_id_returns_error()
    {
        $response = $this->postJson('/api/votes', [
            'fighter_id' => 9999, // 存在しないID
            'vote_type' => 'strong',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('fighter_id');
    }

    /**
     * コメントが投稿される
     *
     * @test
     */
    public function comment_is_posted_successfully()
    {
        $fighter = Fighter::factory()->create();

        $response = $this->postJson('/api/comments', [
            'fighter_id' => $fighter->id,
            'content' => 'これは素晴らしいコメントです。',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('comments', [
            'fighter_id' => $fighter->id,
            'content' => 'これは素晴らしいコメントです。',
        ]);
    }

    /**
     * スパム防止が機能する (同一IP 1分以内の重複投稿が拒否される)
     *
     * @test
     */
    public function spam_prevention_works_for_comments()
    {
        $fighter = Fighter::factory()->create();
        $ipAddress = '127.0.0.1';

        // 1回目の投稿
        $this->postJson('/api/comments', [
            'fighter_id' => $fighter->id,
            'content' => '最初のコメント',
        ], ['X-Forwarded-For' => $ipAddress])->assertStatus(200);

        // 2回目の投稿 (1分以内)
        $response = $this->postJson('/api/comments', [
            'fighter_id' => $fighter->id,
            'content' => '2回目のコメント',
        ], ['X-Forwarded-For' => $ipAddress]);

        $response->assertStatus(429)
            ->assertJson(['success' => false, 'message' => 'しばらく待ってからコメントしてください。']);
    }

    /** @test */
    public function people_vote_page_returns_ok(): void
    {
        $fighter = Fighter::factory()->create(['name' => 'テスト選手']);

        $response = $this->get(route('people.vote', ['fighterName' => $fighter->name]));

        $response->assertOk();
    }

    /** @test */
    public function people_result_page_returns_ok(): void
    {
        $fighter = Fighter::factory()->create(['name' => '結果ページ選手']);

        $response = $this->get(route('people.result', ['fighterName' => $fighter->name]));

        $response->assertOk();
    }

    /** @test */
    public function legacy_query_id_redirects_to_people_vote(): void
    {
        $fighter = Fighter::factory()->create(['name' => 'リダイレクト選手']);

        $response = $this->get('/?id='.$fighter->id);

        $response->assertRedirect(route('people.vote', ['fighterName' => $fighter->name]));
        $response->assertStatus(301);
    }

    /** @test */
    public function legacy_query_id_with_show_bbs_preserves_query(): void
    {
        $fighter = Fighter::factory()->create(['name' => '掲示板リダイレクト']);

        $response = $this->get('/?id='.$fighter->id.'&show_bbs=1');

        $response->assertRedirect(route('people.vote', ['fighterName' => $fighter->name]).'?show_bbs=1');
        $response->assertStatus(301);
    }

    /** @test */
    public function legacy_fighter_path_redirects_to_people_result(): void
    {
        $fighter = Fighter::factory()->create(['name' => '旧パス選手']);

        $response = $this->get('/fighter/'.$fighter->id);

        $response->assertRedirect(route('people.result', ['fighterName' => $fighter->name]));
        $response->assertStatus(301);
    }
}
