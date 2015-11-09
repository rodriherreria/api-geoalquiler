<?php

require 'vendor/autoload.php';
require 'Models/User.php';

$app = new \Slim\Slim();

$app->config('databases', [
    'default' => [
        'driver'    => 'mysql',
        'host'      => 'us-cdbr-iron-east-03.cleardb.net',
        'database'  => 'heroku_cdaa68b6cbc8edb',
        'username'  => 'b5d870bc08264e',
        'password'  => '121369b6',
        'charset'   => 'utf8',
        'collation' => 'utf8_general_ci',
        'prefix'    => ''
    ]
]);
$app->add(new Zeuxisoo\Laravel\Database\Eloquent\ModelMiddleware);

$app->view(new \JsonApiView());
$app->add(new \JsonApiMiddleware());
$app->add(new \Slim\Middleware\ContentTypes());

$app->options('/(:name+)', function() use ($app) {
    $app->render(200,array('msg' => 'API Index'));
});

$app->get('/', function () use ($app) {
$app->render(200,array('msg' =>'API INDEX'));
});

//Mostrar

$app->get('/usuarios', function () use ($app) {
	$db = $app->db->getConnection();
	$users = $db->table('users')->select('id', 'name', 'email')->get();

	$app->render(200,array('data' => $users));
});

/*login

$app->post('/login', function() use ($app) {
    $req = $app->request;

    $user= $req->params('user');
    $pass = $req->params('pass');

    try {
        $query = $app->db->prepare("SELECT user, password FROM users
                              WHERE user = :user AND password = :pass
                              LIMIT 1");
        $query->execute(
            array(
                ':user' => $user,
                ':pass' => md5($pass)
            )
        );

        $result = $query->fetch();
    }

    catch (PDOException $e) {
        $app->flash('error', 'db error');
    }


    if ( empty($result) ) {
        $app->flash('error', 'wrong user or pass');
        $app->redirect('/login');
    }

    $app->redirect('/');

})->name('login');


//Login2
$app->post('/login', function () use ($app) {

	$input = $app->request->getBody();
	$errors = array();
	
	$email = $input['usuario'];
    $password = $input['password'];
	
    $errors = array();
    if ($email != "puntsdasdasdo@gmail.com") {
        $errors['usuario'] = "Email is not found.";
    } else if ($password != "123") {
        $app->flash('email', $email);
        $errors['password'] = "Password does not match.";
    }
	
	  if (count($errors) > 0) {
        $app->flash('errors', $errors);
        $app->render(200,array('error' => FALSE, 'msg'   => 'Logeado exitosamente',
        ));
    }
	
});

/* Login 3
$app->post('/login', function () use ($app) {

	$input = $app->request->getBody();
	
	$user = $input['usuario'];
	$pass = $input['password'];
	
	if ($user == "Fran Meriles" && $pass == "1234") { 
			
			$app->render(200,array('data' => $user->toArray()));	
	}
		
	$app->render(404,array(
			'error' => TRUE,
            'msg'   => 'user not found',
        ));
	
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
*/

/*

$app->post('/login', function() use ($app) {
   $input = $app->request->getBody();
	
	$user = $input['usuario'];
    $pass = $input['password'];

    try {
        $query = $app->db->prepare("SELECT user, password FROM users
                              WHERE user = :user AND password = :pass
                              LIMIT 1");
        $query->execute(
            array(
                ':user' => $user,
                ':pass' => $pass
            )
        );

        $result = $query->fetch();
    }

    catch (PDOException $e) {
        $app->flash('error', 'db error');
    }


    if ( empty($result) ) {
        $app->flash('error', 'wrong user or pass');
        $app->redirect('/login');
    }

    $app->redirect('/');

})->name('login');
*/


//Login2
$app->post('/login', function () use ($app) {

	$input = $app->request->getBody();
	$errors = array();
	
	$email = $input['usuario'];
    $password = $input['password'];
	
    $errors = array();
    if ($email != "puntsdasdasdo@gmail.com") {
        $errors['usuario'] = "Email is not found.";
    } else if ($password != "123") {
        $app->flash('email', $email);
        $errors['password'] = "Password does not match.";
    }
	
	  if (count($errors) > 0) {
        $app->flash('errors', $errors);
        $app->render(200,array('error' => FALSE, 'msg'   => 'Logeado exitosamente',
        ));
    }else if (count($errors) < 0){
        $app->flash('errors', $errors);
        $app->render(300,array('error' => TRUE, 'msg'   => 'Fallo en Login',
        ));

    }
	
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
	if(empty($titulo)){
		$app->render(500,array(
			'error' => TRUE,
            'msg'   => 'titulo is required',
        ));
	}
	$descripcion = $input['descripcion'];
	if(empty($descripcion)){
		$app->render(500,array(
			'error' => TRUE,
            'msg'   => 'descripcion is required',
        ));
	}
	$precio = $input['precio'];
	if(empty($precio)){
		$app->render(500,array(
			'error' => TRUE,
            'msg'   => 'precio is required',
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
