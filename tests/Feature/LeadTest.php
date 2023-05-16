<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LeadTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testCreateLeadValid()
    {

        // Crear un usuario autenticado con el rol de 'manager'
        $user = User::factory()->create([
            'username' => fake()->unique()->userName(),
            'password' => Hash::make('PASSWORD'),
            'is_active' => true,
            'role' => 'manager'
        ]);

        // Generar un token de autenticación para el usuario
        $token = auth()->login($user);

        // Simular una solicitud con parametros
        $requestData = array(
            "name" => "Mi candidato Prueba",
            "source" => "Fotocasa Prueba",
            "owner" => 1
        );

        // Ejecutar la función create()
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->postJson('/api/lead', $requestData);

        // Verificar que la respuesta sea exitosa
        $response->assertStatus(200);

    }

    public function testCreateLeadInsufficientPermissions()
    {

        // Crear un usuario autenticado con el rol de 'agent'
        $user = User::factory()->create([
            'username' => fake()->unique()->userName(),
            'password' => Hash::make('PASSWORD'),
            'is_active' => true,
            'role' => 'agent'
        ]);

        // Generar un token de autenticación para el usuario
        $token = auth()->login($user);

        // Simular una solicitud con parametros
        $requestData = array(
            "name" => "Mi candidato Prueba 2",
            "source" => "Fotocasa Prueba 2",
            "owner" => 2
        );

        // Ejecutar la función create()
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->postJson('/api/lead', $requestData);

        // Verificar que la respuesta no exitosa por permisos
        $response->assertStatus(500);

    }

    public function testCreateLeadInvalid()
    {

        // Crear un usuario autenticado con el rol de 'manager' o 'agent'
        $user = User::factory()->create([
            'username' => fake()->unique()->userName(),
            'password' => Hash::make('PASSWORD'),
            'is_active' => true,
            'role' => fake()->randomElement(['manager', 'agent'])
        ]);

        // Generar un token de autenticación para el usuario
        $token = auth()->login($user);

        // Simular una solicitud con parametros
        $requestData = array(
            "name" => "Mi candidato Prueba 3",
            "source" => "Fotocasa Prueba 3",
            "owner" => "a3"
        );

        // Ejecutar la función create()
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->postJson('/api/lead', $requestData);

        // Verificar que la respuesta sea no exitosa, por falta de campos
        $response->assertStatus(422);

    }

    public function testGetLeadSuccess()
    {
        // Crear un usuario autenticado con el rol de 'manager'
        $user = User::factory()->create([
            'username' => fake()->unique()->userName(),
            'password' => Hash::make('PASSWORD'),
            'is_active' => true,
            'role' => 'manager'
        ]);

        // Generar un token de autenticación para el usuario
        $token = auth()->login($user);

        // Ejecutar la función create()
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->get('/api/lead/1');

        // Verificar que la respuesta sea exitosa
        $response->assertStatus(200);
    }

    public function testGetLeadInsufficientPermissions()
    {
        // Crear un usuario autenticado con el rol de 'agent'
        $user = User::factory()->create([
            'username' => fake()->unique()->userName(),
            'password' => Hash::make('PASSWORD'),
            'is_active' => true,
            'role' => 'agent'
        ]);

        // Generar un token de autenticación para el usuario
        $token = auth()->login($user);

        // Ejecutar la función create()
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->get('/api/lead/1');

        // Verificar que la respuesta sea exitosa
        $response->assertStatus(500);
    }

    public function testGetLeadInvalid()
    {
        // Crear un usuario autenticado con el rol de 'manager'
        $user = User::factory()->create([
            'username' => fake()->unique()->userName(),
            'password' => Hash::make('PASSWORD'),
            'is_active' => true,
            'role' => 'manager'
        ]);

        // Generar un token de autenticación para el usuario
        $token = auth()->login($user);

        // Ejecutar la función create()
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->get('/api/lead/1000');

        // Verificar que la respuesta sea exitosa
        $response->assertStatus(500);
    }

    public function testAllLeadsSuccessManager()
    {
        // Crear un usuario autenticado con el rol de 'manager'
        $user = User::factory()->create([
            'username' => fake()->unique()->userName(),
            'password' => Hash::make('PASSWORD'),
            'is_active' => true,
            'role' => 'manager'
        ]);

        // Generar un token de autenticación para el usuario
        $token = auth()->login($user);

        // Ejecutar la función create()
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->get('/api/leads');

        // Verificar que la respuesta sea exitosa
        $response->assertStatus(200);
    }

    public function testAllLeadsSuccessAgent()
    {
        // Crear un usuario autenticado con el rol de 'agent'
        $user = User::factory()->create([
            'username' => fake()->unique()->userName(),
            'password' => Hash::make('PASSWORD'),
            'is_active' => true,
            'role' => 'agent'
        ]);

        // Generar un token de autenticación para el usuario
        $token = auth()->login($user);

        // Ejecutar la función create()
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->get('/api/leads');

        // Verificar que la respuesta sea exitosa
        $response->assertStatus(200);
    }
}
