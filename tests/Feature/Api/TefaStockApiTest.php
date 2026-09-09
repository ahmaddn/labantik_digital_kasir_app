<?php

namespace Tests\Feature\Api;

use App\Models\Merchant;
use App\Models\Product;
use App\Models\TefaApiKey;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TefaStockApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        TefaApiKey::create([
            'name' => 'Dompet App Key',
            'api_key' => 'tefa_test_key_123456789',
            'is_active' => true,
        ]);
    }

    public function test_api_key_authentication_required(): void
    {
        $response = $this->getJson('/api/v1/tefa/merchants');

        $response->assertStatus(401)
                 ->assertJson(['status' => 'error']);
    }

    public function test_api_key_authentication_success(): void
    {
        $response = $this->withHeaders([
            'X-API-Key' => 'tefa_test_key_123456789',
        ])->getJson('/api/v1/tefa/merchants');

        $response->assertStatus(200)
                 ->assertJson(['status' => 'success']);
    }
}
