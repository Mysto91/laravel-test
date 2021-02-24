<?php

namespace Tests\Feature\Article;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ArticlePutTest extends TestCase
{
    use RefreshDatabase;

    private $url = '/api/articles';

    public function getUrl($id, $apiToken)
    {
        return $this->url . '/' . $id . '?api_token=' . $apiToken;
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testIfPutWorks()
    {
        $user = $this->getUser();

        $body = [
            'title' => 'nouveau titre',
            'description' => 'nouvelle description',
            'format' => 'blu-ray',
            'rate' => '4.5'
        ];

        $article = Article::factory()->create();

        $response = $this->json('PUT', $this->getUrl($article->id, $user->api_token), $body);

        $responseBody = $response->decodeResponseJson();

        $data = $responseBody['data'];

        $response->assertStatus(201);
        $this->assertDatabaseHas('articles', $body);
        $this->assertEquals($body['title'], $data['title']);
        $this->assertEquals($body['description'], $data['description']);
        $this->assertEquals($body['format'], $data['format']);
        $this->assertEquals($body['rate'], $data['rate']);
    }

    public function testIfPutWithWrongBodyNotWork()
    {
        $user = $this->getUser();

        $body = [
            'description' => 'ma description'
        ];

        $article = Article::factory()->create();

        $response = $this->json('PUT', $this->getUrl($article->id, $user->api_token), $body);

        $expected = [
            "title" => [
                "The title field is required."
            ],
            "format" => [
                "The format field must be present."
            ],
            "rate" => [
                "The rate field must be present."
            ]
        ];

        $this->assertEquals(json_encode($expected), $response->getContent());
    }

    public function testIfPutWithNotExistingArticleNotWork()
    {
        $user = $this->getUser();

        $body = [
            'title' => 'nouveau titre',
            'description' => 'nouvelle description'
        ];

        $response = $this->json('PUT', $this->getUrl(999999, $user->api_token), $body);

        $response->assertStatus(404);
    }

    public function testIfPutWithoutNotAuthenticatedNotWork()
    {
        $article = Article::factory()->create();

        $response = $this->json('PUT', $this->getUrl($article->id, 123456), []);
        $response->assertStatus(401);
    }
}
