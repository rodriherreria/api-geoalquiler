<?php

header("Access-Control-Allow-Origin: *");


require 'vendor/autoload.php';
require 'Models/User.php';

$app = new \Slim\Slim();

$app->config('databases', [
    'default' => [
        'driver'    => 'mysql',
        'host'      => 'localhost',
        'database'  => 'geo',
        'username'  => 'Geo',
        'password'  => 'geoalquiler',
        'charset'   => 'utf8',
        'collation' => 'utf8_general_ci',
        'prefix'    => ''
    ]
]);
$app->add(new Zeuxisoo\Laravel\Database\Eloquent\ModelMiddleware);

$app->view(new \JsonApiView());
$app->add(new \JsonApiMiddleware());
$app->add(new \Slim\Middleware\ContentTypes());

$app->get('/', function () use ($app) {
$app->render(200,array('msg' =>'API INDEX'));
});

//Mostrar

$app->get('/usuarios', function () use ($app) {
	$db = $app->db->getConnection();
	$users = $db->table('users')->select('id', 'name', 'email')->get();

	$app->render(200,array('data' => $users));
});

//Insertar

$app->post('/usuarios', function () use ($app) {
  $input = $app->request->getBody();
	$name = $input['name'];
	if(empty($name)){
		$app->render(500,array(
			'error' => TRUE,
            'msg'   => 'name is required',
        ));
	}
	$password = $input['password'];
	if(empty($password)){
		$app->render(500,array(
			'error' => TRUE,
            'msg'   => 'password is required',
        ));
	}
	$email = $input['email'];
	if(empty($email)){
		$app->render(500,array(
			'error' => TRUE,
            'msg'   => 'email is required',
        ));
	}
    $user = new User();
    $user->name = $name;
    $user->password = $password;
    $user->email = $email;
    $user->save();
    $app->render(200,array('data' => $user->toArray()));
});

//Editar

$app->put('/usuarios/:id', function ($id) use ($app) {
  $input = $app->request->getBody();

	$name = $input['name'];
	if(empty($name)){
		$app->render(500,array(
			'error' => TRUE,
            'msg'   => 'name is required',
        ));
	}
	$password = $input['password'];
	if(empty($password)){
		$app->render(500,array(
			'error' => TRUE,
            'msg'   => 'password is required',
        ));
	}
	$email = $input['email'];
	if(empty($email)){
		$app->render(500,array(
			'error' => TRUE,
            'msg'   => 'email is required',
        ));
	}
	$user = User::find($id);
	if(empty($user)){
		$app->render(404,array(
			'error' => TRUE,
            'msg'   => 'user not found',
        ));
	}
    $user->name = $name;
    $user->password = $password;
    $user->email = $email;
    $user->save();
    $app->render(200,array('data' => $user->toArray()));
});

//Buscar por ID

$app->get('/usuarios/:id', function ($id) use ($app) {
	$user = User::find($id);
	if(empty($user)){
		$app->render(404,array(
			'error' => TRUE,
            'msg'   => 'user not found',
        ));
	}
	$app->render(200,array('data' => $user->toArray()));
});

//Eliminar

$app->delete('/usuarios/:id', function ($id) use ($app) {
	$user = User::find($id);
	if(empty($user)){
		$app->render(404,array(
			'error' => TRUE,
            'msg'   => 'user not found',
        ));
	}

	$user->delete();
	$app->render(200);
});

//Conexion con la tabla anuncio

$app->get('/anuncios', function () use ($app) {
	$db = $app->db->getConnection();
	$users = $db->table('anuncios')->select('id', 'titulo', 'precio', 'descripcion', 'foto')->get();

	$app->render(200,array('data' => $users));
});

//Insertar Anuncio

$app->post('/anuncios', function () use ($app) {

  $input = $app->request->getBody();
	$titulo = $input['titulo'];
	if(empty($name)){
		$app->render(500,array(
			'error' => TRUE,
            'msg'   => 'name is required',
        ));
	}
	$descripcion = $input['descripcion'];
	if(empty($password)){
		$app->render(500,array(
			'error' => TRUE,
            'msg'   => 'password is required',
        ));
	}
	$precio = $input['precio'];
	if(empty($email)){
		$app->render(500,array(
			'error' => TRUE,
            'msg'   => 'email is required',
        ));
	}
    $user = new Anuncio();
    $user->titulo = $titulo;
    $user->descripcion = $descripcion;
    $user->precio = $precio;
    $user->save();
    $app->render(200,array('data' => $user->toArray()));
});


//Editar Anuncio

$app->put('/anuncios/:id', function ($id) use ($app) {
  $input = $app->request->getBody();

	$titulo = $input['titulo'];
	if(empty($titulo)){
		$app->render(500,array(
			'error' => TRUE,
            'msg'   => 'name is required',
        ));
	}
	$descripcion = $input['descripcion'];
	if(empty($descripcion)){
		$app->render(500,array(
			'error' => TRUE,
            'msg'   => 'password is required',
        ));
	}
	$precio = $input['precio'];
	if(empty($precio)){
		$app->render(500,array(
			'error' => TRUE,
            'msg'   => 'email is required',
        ));
	}
	$user = Anuncio::find($id);
	if(empty($user)){
		$app->render(404,array(
			'error' => TRUE,
            'msg'   => 'user not found',
        ));
	}
    $user->titulo = $titulo;
    $user->descripcion = $descripcion;
    $user->precio = $precio;
    $user->save();
    $app->render(200,array('data' => $user->toArray()));
});

//Buscar Anuncio

$app->get('/anuncios/:id', function ($id) use ($app) {
	$user = Anuncio::find($id);
	if(empty($user)){
		$app->render(404,array(
			'error' => TRUE,
            'msg'   => 'Anuncio not found',
        ));
	}
	$app->render(200,array('data' => $user->toArray()));
});

// Borrar Anuncio

$app->delete('/anuncios/:id', function ($id) use ($app) {
	$user = Anuncio::find($id);
	if(empty($user)){
		$app->render(404,array(
			'error' => TRUE,
            'msg'   => 'user not found',
        ));
	}

	$user->delete();
	$app->render(200);
});

$app->run();
?>
