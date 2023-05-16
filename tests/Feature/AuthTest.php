<?php

namespace Tests\Feature;

use Tests\TestCase;

class AuthTest extends TestCase
{

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testAuthWithValidCredentials()
    {
        // Simular una solicitud con credenciales válidas
        $requestData = array(
            'username' => 'agent',
            'password' => 'PASSWORD',
        );
        // Ejecutar la función auth()
        $response = $this->postJson('/api/auth', $requestData);

        // Verificar que la respuesta sea exitosa
        $response->assertStatus(200);
    }

    public function testAuthWithInvalidCredentials()
    {
        // Simular una solicitud con credenciales inválidas
        $requestData = [
            'username' => 'usuario_prueba',
            'password' => 'PASSWORD_FALLA',
        ];

        // Ejecutar la función auth()
        $response = $this->postJson('/api/auth', $requestData);

        // Verificar que la respuesta sea un error de autenticación
        $response->assertStatus(401);
    }
}
