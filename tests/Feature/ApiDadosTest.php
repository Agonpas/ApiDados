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
    public function test_login_with_wrong_credentials(): void
    {   //probamos que el usuario que acabamos de borrar no puede logearse
        $response = $this->post('api/login', [
            'email' => 'test@test.com',
            'password' => 'password'
        ]);
        //respuesta esperada si el intento de login es fallido
        $response->assertStatus(401);
        
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

        //vamos a la ruta post logout y pedimos el cierre de sesión
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

        //autenticamos al jugador
        $this->actingAs($user, 'api');

        //usamo la ruta para crear juego al jugador
        $response = $this->postJson("api/players/{$user->id}/games");

        //respuesta esperada si la creación del juego es correcta
        $response->assertStatus(201);
    }
    public function test_player_cannot_create_other_players_games(): void
    {
        //creamos usuarios
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        //asignamos roles de jugador
        $user1->assignRole('player');
        $user2->assignRole('player');

        //autenticamos al jugador 1
        $this->actingAs($user1, 'api');

        //intentamos crear juego para el jugador 2
        $response = $this->postJson("api/players/{$user2->id}/games");

        //verificamos que el resultado sea de prohibido
        $response->assertStatus(403);
    }
    public function test_get_games_for_player(): void
    {
        //creamos jugador
        $user = User::factory()->create();
        //asignamos el rol de jugador al usuario creado
        $user->assignRole('player'); 

        //simulación de autenticación
        $this->actingAs($user, 'api');

        //creamos juegos al jugador creado
        Game::factory()->count(3)->create(['user_id' => $user->id]);

        //comprobamos la ruta get para mostrar los juegos
        $response = $this->getJson("api/players/{$user->id}/games");

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
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        //asignamos rol jugador
        $user1->assignRole('player');
        $user2->assignRole('player');

        //autenticamos el jugador1
        $this->actingAs($user1, 'api');

        //creamos juegos al jugador2
        Game::factory()->count(3)->create(['user_id' => $user2->id]);

        //intento de visualizar los juegos del jugador2
        $response = $this->getJson("api/players/{$user2->id}/games");

        //verificamos que el resultado sea de prohibido
        $response->assertStatus(403);
    }
    public function test_player_can_delete_games(): void
    {
        //creamos jugador
        $user = User::factory()->create();

        //asignamos el rol de jugador al usuario creado
        $user->assignRole('player'); 

        //simulación de autenticación
        $this->actingAs($user, 'api');

        //creamos juegos al jugador creado
        Game::factory()->count(3)->create(['user_id' => $user->id]);
        
        //usamos la ruta para borrar todos los juegos del jugador
        $response = $this->deleteJson("api/players/{$user->id}/games");
        
        //respuesta esperada si el borrado de los juegos es correcta
        $response->assertStatus(200);
        
        //comprobamos que se hayan borrado los juegos
        $this->assertEquals(0, Game::where('user_id', $user->id)->count());
    }
    public function test_player_cannot_delete_other_players_games(): void
    {
        //generamos 2 jugadores
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        //asignamos rol jugadores
        $user1->assignRole('player');
        $user2->assignRole('player');

        //autenticamos el jugador1
        $this->actingAs($user1, 'api');

        //creamos juegos al jugador2
        Game::factory()->count(3)->create(['user_id' => $user2->id]);

        //intento de borrar los juegos del jugador2
        $response = $this->deleteJson("api/players/{$user2->id}/games");

        //verificamos que el resultado sea de prohibido
        $response->assertStatus(403);
    }
    public function test_admin_can_access_all_users_win_percentage(): void
    {
        //creamos administrador
        $user = User::factory()->create();

        //asignamos el rol de administrador al usuario creado
        $user->assignRole('admin'); 

        //simulación de autenticación
        $this->actingAs($user, 'api');
        
        //usamos la ruta par acceder a todos los usuarios con sus porcentajes
        $response = $this->getJson('/api/players');
        
        //respuesta esperada si el acceso a los jugadores es correct
        $response->assertStatus(200);
        
        //comprobar los campos que devuelve el json son los esperados
        $response->assertJsonStructure([
            'users' => [
                '*' => [
                    'user' => [
                        'id',
                        'name',
                        'nickname',
                        'email',
                        'email_verified_at',
                        'created_at',
                        'updated_at'
                    ],
                    'win_percentage'
                ]
            ]
        ]);
    }
    public function test_admin_can_access_ranking_users_with_win_percentage(): void
    {
        //creamos administrador
        $user = User::factory()->create();

        //asignamos el rol de administrador al usuario creado
        $user->assignRole('admin'); 

        //simulación de autenticación
        $this->actingAs($user, 'api');
        
        //usamos la ruta par acceder al ranking de jugadores con sus porcentajes
        $response = $this->getJson('/api/players/ranking');
        
        //respuesta esperada si el acceso a los jugadores es correct
        $response->assertStatus(200);
        
        //comprobar los campos que devuelve el json son los esperados
        $response->assertJsonStructure([
            'users' => [
                '*' => [
                    'user' => [
                        'id',
                        'name',
                        'nickname',
                        'email',
                        'email_verified_at',
                        'created_at',
                        'updated_at'
                    ],
                    'win_percentage'
                ]
            ]
        ]);
    }
    public function test_admin_can_acces_winners(): void
    {
        //creamos administrador
        $user = User::factory()->create();

        //asignamos el rol de adminstrador al usuario creado
        $user->assignRole('admin'); 

        //simulación de autenticación
        $this->actingAs($user, 'api');
        
        //usamos la ruta par acceder a los ganadores
        $response = $this->getJson('/api/players/ranking/winner');
        
        //respuesta esperada si el acceso a los jugadores es correct
        $response->assertStatus(200);
        
        //comprobar los campos que devuelve el json son los esperados
        $response->assertJsonStructure([
            'winners' => [
                '*' => [
                    'id',
                    'name',
                    'nickname',
                    'email',
                    'email_verified_at',
                    'created_at',
                    'updated_at'
                ]
            ],
            'win_percentage'
        ]);
    }
    public function test_admin_can_acces_losers(): void
    {
        //creamos administrador
        $user = User::factory()->create();

        //asignamos el rol de adminstrador al usuario creado
        $user->assignRole('admin'); 

        //simulación de autenticación
        $this->actingAs($user, 'api');
        
        //usamos la ruta par acceder a los perdedores
        $response = $this->getJson('/api/players/ranking/loser');
        
        //respuesta esperada si el acceso a los jugadores es correct
        $response->assertStatus(200);
        
        //comprobar los campos que devuelve el json son los esperados
        $response->assertJsonStructure([
            'losers' => [
                '*' => [
                    'id',
                    'name',
                    'nickname',
                    'email',
                    'email_verified_at',
                    'created_at',
                    'updated_at'
                ]
            ],
            'win_percentage'
        ]);
    }
    public function test_player_cannot_access_admin_functions(): void
    {
        //creamos jugador
        $user = User::factory()->create();

        //asignamos el rol de jugador al usuario creado
        $user->assignRole('player'); 

        //simulación de autenticación
        $this->actingAs($user, 'api');

        //intento de acceder a función de administrador
        $response = $this->getJson('/api/players'); 

        //verificamos que el resultado sea de prohibido
        $response->assertStatus(403);
    }
    public function test_admin_cannot_access_player_functions(): void
    {
        //creamos administrador
        $user = User::factory()->create();

        //asignamos el rol de administrador al usuario creado
        $user->assignRole('admin'); 

        //simulación de autenticación
        $this->actingAs($user, 'api');

        //intento de acceder a función de player
        $response = $this->postJson("api/players/{$user->id}/games");

        //verificamos que el resultado sea de prohibido
        $response->assertStatus(403);
    }
}
