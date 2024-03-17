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

        //para no borrar manualmente el usuario de pruebas en cada test lo busco por su correo y lo borramos
        User::where('email', 'test@test.com')->delete();
        
    }
    public function test_update_user_name(): void
{
    //creamos un usuario
    $user = User::factory()->create();

    //esto simula la autenticación
    $this->actingAs($user, 'api');

    //vamos a la ruta put y damos un nuevo nombre y un nuevo nickname
    $response = $this->putJson("api/players/{$user->id}", [
        'name' => 'New Name',
        'nickname' => 'New Nickname'
    ]);

    //respuesta esperada si la actualización es correcta
    $response->assertStatus(200);
    //eliminamos al usuario al finalizar el test
    $user->delete();   
}
public function test_logout_user(): void
{
    //creamos un usuario
    $user = User::factory()->create();

    //esto simula la autenticación
    $this->actingAs($user, 'api');

    ////vamos a la ruta post logout y pedimos el cierre de sesión
    $response = $this->postJson('api/logout');

    //respuesta esperada si el cierre de sesión es correcto
    $response->assertStatus(200);

    //lo que sigue comprueba que el token se haya eliminado
    $this->assertNull($user->tokens()->latest()->first());

    //eliminamos al usuario al finalizar el test
    $user->delete(); 
}

}
