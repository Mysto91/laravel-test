<?php

namespace Tests\Feature\Article;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ArticleGetByIdTest extends TestCase
{
    use RefreshDatabase;

    private $url = '/api/articles';

    public function getUrl($id, $apiToken)
    {
        return $this->url . '/' . $id  . '?api_token=' . $apiToken;
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testIfGetWorks()
    {
        $user = $this->getUser();

        $article = Article::factory()->create();

        $response = $this->get($this->getUrl($article->id, $user->api_token));

        $response->assertStatus(200);

        $responseBody = $response->decodeResponseJson();

        $data = $responseBody['data'];

        $this->assertEquals($article->id, $data['id']);
        $this->assertStringContainsString($this->url . '/' . $article->id, $data['_links'][0]['href']);
        $this->assertStringContainsString($this->url, $data['_links'][1]['href']);
    }

    public function testIfGetWithoutNotAuthenticatedNotWork()
    {
        $article = Article::factory()->create();

        $response = $this->get($this->getUrl($article->id, 1234));
        $response->assertStatus(302);
    }

    public function testIfGetWithNotExistingArticleNotWork()
    {
        $user = $this->getUser();

        $article = Article::factory()->create();

        $response = $this->get($this->getUrl(9999, $user->api_token));

        $response->assertStatus(404);
    }
}
