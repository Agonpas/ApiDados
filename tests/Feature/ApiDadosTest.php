<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Game;

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
    public function test_login_with_correct_credentials(): void
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
public function test_create_game(): void
{
    //creamos un usuario
    $user = User::factory()->create();

    //asignamos el rol de jugador al usuario creado
    $user->assignRole('player'); 

    // Simulamos la autenticación del jugador
    $this->actingAs($user, 'api');

    // Hacemos la solicitud POST a la ruta de creación de juego
    $response = $this->postJson("api/players/{$user->id}/games");

    //respuesta esperada si la creación del juego es correcta
    $response->assertStatus(201);
}
public function test_get_games_for_player(): void
{
    //creamos jugador
    $player = User::factory()->create();
    //asignamos el rol de jugador al usuario creado
    $player->assignRole('player'); 

    //simulación de autenticación
    $this->actingAs($player, 'api');

    //creamos juegos al jugador creado
    Game::factory()->count(3)->create(['user_id' => $player->id]);

    //comprobamos la ruta get para mostrar los juegos
    $response = $this->getJson("api/players/{$player->id}/games");

    //respuesta esperada si la visualización de juegos es correcta
    $response->assertStatus(200);

    //comprobar los campos que devuelve el json son los esperados
    $response->assertJsonStructure([
        '*' => [ 
            'id',
            'user_id',
            'dice1',
            'dice2',
            'won',
            'created_at',
            'updated_at'
        ]
    ]);

    //comprobamos que se han creado los 3 juegos
    $response->assertJsonCount(3); 
}
public function test_player_cannot_view_other_players_games(): void
{
    //generamos 2 jugadores
    $player1 = User::factory()->create();
    $player2 = User::factory()->create();

    //asignamos rol jugador
    $player1->assignRole('player');
    $player2->assignRole('player');

    //autenticamos el jugador1
    $this->actingAs($player1, 'api');

    //creamos juegos al jugador2
    Game::factory()->count(3)->create(['user_id' => $player2->id]);

    //intento de visualizar los juegos del jugador2
    $response = $this->getJson("api/players/{$player2->id}/games");

    //verificamos que el resultado sea de prohibido
    $response->assertStatus(403);
}

}
