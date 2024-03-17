<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class ApiDadosTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
    public function test_register_new_player(): void

    {   $this->withoutExceptionHandling();
        //creamos un nuevo usuario
        $response = $this->post('api/players', [
            'name' => 'ApiDados Test',
            'nickname' => 'Test',
            'email' => 'test@test.com',
            'password' => 'password'
        ]);
        //respuesta esperada si la creación es correcta
        $response->assertStatus(201);
    }
    public function test_login_with_correct_credentials()
    {   //probamos que el usuario creado se pueda logear (prueba doble: logeo y que el crear se hizo correcto)
        $response = $this->post('api/login', [
            'email' => 'test@test.com',
            'password' => 'password'
        ]);
        //respuesta esperada si la creación es correcta
        $response->assertStatus(200);
        //para no borrar manualmente el usuario de pruebas en cada test lo busco por correo y lo borramos
        User::where('email', 'test@test.com')->delete();
    }
    
}
