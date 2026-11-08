<?php

namespace Tests\Feature;

use App\Models\PropertyType;
use App\Models\User;
use Database\Seeders\PropertyTypeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use App\Models\Property;
use PHPUnit\Framework\Attributes\DataProvider;

class PropertyStoreTest extends TestCase
{
    use RefreshDatabase;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PropertyTypeSeeder::class);   // o banco de teste começa vazio
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'title'            => 'Apartamento no centro',
            'property_type_id' => PropertyType::where('slug', 'apartment')->value('id'),
            'city'             => 'Rio de Janeiro',
            'state'            => 'RJ',
        ], $overrides);
    }

    public function test_usuario_autenticado_cria_imovel_valido(): void
    {
        //Arrange
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        // Act
        $propertyData = $this->validPayload([
            'external_code' => 'IMV-001',
        ]);

        $response = $this->postJson('/api/properties', $propertyData);

        // Assert
        $response->assertCreated()
                ->assertJsonFragment(['external_code' => 'IMV-001']);

        $this->assertDatabaseHas('properties', [
            'external_code' => 'IMV-001', 
            'created_by' => $user->id  
        ]);
    }

    public function test_status_e_available_quando_nao_enviado(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $propertyData = $this->validPayload([
            'external_code' => 'IMV-002',
        ]);

        $response = $this->postJson('/api/properties', $propertyData);

        $response->assertCreated();

        $this->assertDatabaseHas('properties', [
            'external_code' => 'IMV-002',
            'status' => 'available', 
            'created_by' => $user->id  
        ]);
    }

    public function test_nao_aceita_source_e_created_by_enviado_pelo_cliente(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();          // outra pessoa
        Sanctum::actingAs($user);

        $propertyData = $this->validPayload([
            'external_code' => 'IMV-003',
            'source' => 'api', 
            'created_by' => $other->id, 
        ]);

        $response = $this->postJson('/api/properties', $propertyData);

        $response->assertCreated();

        $this->assertDatabaseHas('properties', [
            'external_code' => 'IMV-003',
            'source' => 'manual', 
            'created_by' => $user->id  
        ]);

        $this->assertDatabaseMissing('properties', [
            'external_code' => 'IMV-003',
            'created_by' => $other->id,
        ]);
    }

    public function test_titulo_e_cidade_sao_obrigatorios(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $typeId = PropertyType::where('slug', 'apartment')->value('id');

        $propertyData = [
            'external_code' => 'IMV-004',
            'property_type_id' => $typeId,
            'state' => 'RJ',
        ];

        $response = $this->postJson('/api/properties', $propertyData);

        $response->assertUnprocessable()
                 ->assertJsonValidationErrors(['title', 'city']);
    }

    public function test_rejeita_status_invalido(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $propertyData = $this->validPayload([
            'external_code' => 'IMV-005',
            'status' => 'invalid_status', 
        ]);

        $response = $this->postJson('/api/properties', $propertyData);

        $response->assertUnprocessable()
                 ->assertJsonValidationErrors(['status']);
    }

    public function test_rejeita_tipo_de_imovel_inexistente(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $propertyData = $this->validPayload([
            'external_code' => 'IMV-006',
            'property_type_id' => 999, 
        ]);
        
        $response = $this->postJson('/api/properties', $propertyData);

        $response->assertUnprocessable()
                 ->assertJsonValidationErrors(['property_type_id']);
    }

    public function test_rejeita_quartos_acima_do_limite(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $propertyData = $this->validPayload([
            'external_code' => 'IMV-007',
            'bedrooms' => 260, 
        ]);

        $response = $this->postJson('/api/properties', $propertyData);

        $response->assertUnprocessable()
                 ->assertJsonValidationErrors(['bedrooms']);
    }

    public function test_rejeita_codigo_externo_repetida(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $propertyData2 = $this->validPayload([
            'external_code' => 'IMV-008', 
            'title' => 'Casa na praia',
            'city' => 'Niterói',
        ]);

        Property::factory()->create(['external_code' => 'IMV-008']);   // Arrange

        $response = $this->postJson('/api/properties', $propertyData2);

        $response->assertUnprocessable()
                 ->assertJsonValidationErrors(['external_code']);
    }

    public function test_exige_autenticacao(): void
    {
        $propertyData = $this->validPayload([
            'external_code' => 'IMV-009',
        ]);

        $response = $this->postJson('/api/properties', $propertyData);

        $response->assertUnauthorized();
    }

    public function test_aceita_status_sold_e_grava_no_banco(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $propertyData = $this->validPayload([
            'external_code' => 'IMV-010',
            'status' => 'sold', 
        ]);

        $response = $this->postJson('/api/properties', $propertyData);

        $response->assertCreated();

        $this->assertDatabaseHas('properties', [
            'external_code' => 'IMV-010',
            'status' => 'sold', 
            'created_by' => $user->id  
        ]);
    }

    public function test_price_aceita_null_e_grava_no_banco(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $propertyData = $this->validPayload([
            'external_code' => 'IMV-011',
            'price' => null, 
        ]);

        $response = $this->postJson('/api/properties', $propertyData);

        $response->assertCreated();

        $this->assertDatabaseHas('properties', [
            'external_code' => 'IMV-011',
            'price' => null, 
            'created_by' => $user->id  
        ]);
    }

    public function test_grava_o_preco_enviado(): void
    {
        // Arrange
        Sanctum::actingAs(User::factory()->create());

        // Act
        $this->postJson('/api/properties', $this->validPayload([
            'external_code' => 'IMV-012',
            'price'         => 250000.50,
        ]))->assertCreated();

        // Assert
        $this->assertDatabaseHas('properties', [
            'external_code' => 'IMV-012',
            'price'         => 250000.50,
        ]);
    }

    public static function limitesDeQuartos(): array
    {
        return [
            'limite exato (255)' => [255, 201],
            'um acima (256)'     => [256, 422],
        ];
    }

    #[DataProvider('limitesDeQuartos')]
    public function test_limite_de_quartos(int $bedrooms, int $statusEsperado): void
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson('/api/properties', $this->validPayload([
            'external_code' => 'IMV-LIM',
            'bedrooms'      => $bedrooms,
        ]));

        $response->assertStatus($statusEsperado);
    }

}
