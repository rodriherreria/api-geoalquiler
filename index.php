<?php 

require 'vendor/autoload.php';
require 'Models/User.php';
require 'Models/Anuncios.php';
require 'Models/Favoritos.php';

function simple_encrypt($text,$salt){  
   return trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $salt, $text, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));
}
 
function simple_decrypt($text,$salt){  
    return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $salt, base64_decode($text), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)));
}

$app = new \Slim\Slim();

$app->enc_key = '1234567891234567';

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
	$users = $db->table('users')->select('id', 'name', 'email', 'tipous')->get();

	$app->render(200,array('data' => $users));
});

//Login 

$app->post('/login', function () use ($app) {
	$input = $app->request->getBody();

	$email = $input['email'];
	if(empty($email)){
		$app->render(500,array(
			'error' => TRUE,
            'msg'   => 'Se requiere el Email',
        ));
	}
	$password = $input['password'];
	if(empty($password)){
		$app->render(500,array(
			'error' => TRUE,
            'msg'   => 'Se requiere la Contraseña',
        ));
	}
	$db = $app->db->getConnection();
	$user = $db->table('users')->select()->where('email', $email)->first();
	if(empty($user)){
		$app->render(500,array(
			'error' => TRUE,
            'msg'   => 'El usuario no existe',
        ));
	}
	if($user->password != $password){
		$app->render(500,array(
			'error' => TRUE,
            'msg'   => 'La password no coincide',
        ));
	}

	$token = simple_encrypt($user->id, $app->enc_key);

	$app->render(200,array('token' => $token));
});

$app->get('/me', function () use ($app) {

	$token = $app->request->headers->get('auth-token');

	if(empty($token)){
		$app->render(500,array(
			'error' => TRUE,
            'msg'   => 'Not logged1',
        ));
	}
	
	$id_user_token = simple_decrypt($token, $app->enc_key);

	$user = User::find($id_user_token);
	if(empty($user)){
		$app->render(500,array(
			'error' => TRUE,
            'msg'   => 'Not logged2',
        ));
	}
	$app->render(200,array('data' => $user->toArray()));
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

	$telefono = $input['telefono'];
	if(empty($telefono)){
		$app->render(500,array(
			'error' => TRUE,
            'msg'   => 'telefono is required',
        ));
	}


    $user = new User();
    $user->name = $name;
    $user->password = $password;
    $user->email = $email;
    $user->telefono = $telefono;
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
	$telefono = $input['telefono'];
	if(empty($telefono)){
		$app->render(500,array(
			'error' => TRUE,
            'msg'   => 'telefono is required',
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
    $user->telefono = $telefono;
    $user->email = $email;
    $user->save();
    $app->render(200,array('data' => $user->toArray()));
});

//Editar Tipo-Usuario
$app->put('/usuariostipo/:id', function ($id) use ($app) {
  $input = $app->request->getBody();

	$tipo = $input['tipo'];
	if(empty($tipo)){
		$app->render(500,array(
			'error' => TRUE,
            'msg'   => 'tipo es requerido',
        ));
	}
	
	$user = User::find($id);
	if(empty($user)){
		$app->render(404,array(
			'error' => TRUE,
            'msg'   => 'user not found',
        ));
	}
    $user->tipous = $tipo;
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


////////////////////////////////////////////////////////


//Conexion con la tabla anuncio

$app->get('/anuncios', function () use ($app) {
	$db = $app->db->getConnection();
	$anuncios = $db->table('anuncios')->select('id', 'inmueble', 'titulo', 'precio', 'descripcion', 'foto', 'barrio', 'usersid', 'tipo', 'longitud', 'latitud', 'habitaciones', 'banios', 'suptotal', 'garage', 'balcon', 'living', 'comedor', 'created_at')->get();

	$app->render(200,array('data' => $anuncios));
});


//Conexion con la tabla barrio

$app->get('/barrios', function () use ($app) {
	$db = $app->db->getConnection();
	$barrios = $db->table('barrios')->select('idbarrios', 'nombres')->get();

	$app->render(200,array('data' => $barrios));
});

//Insertar Anuncio

$app->post('/anuncios', function () use ($app) {

  $token = $app->request->headers->get('auth-token');
	if(empty($token)){
		$app->render(500,array(
			'error' => TRUE,
            'msg'   => 'Not logged',
        ));
	}
	$id_user_token = simple_decrypt($token, $app->enc_key);

	$user = User::find($id_user_token);
	if(empty($user)){
		$app->render(500,array(
			'error' => TRUE,
            'msg'   => 'Not logged',
        ));
	}

  $input = $app->request->getBody();
  $inmueble = $input['inmueble'];
	if(empty($inmueble)){
		$app->render(500,array(
			'error' => TRUE,
            'msg'   => 'inmueble is required',
        ));
	}
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

	$habitaciones = $input['habitaciones'];
	if(empty($habitaciones)){
		$app->render(500,array(
			'error' => TRUE,
            'msg'   => 'habitaciones is required',
        ));
	}

	$banios = $input['banios'];
	if(empty($banios)){
		$app->render(500,array(
			'error' => TRUE,
            'msg'   => 'banios is required',
        ));
	}

	$direccion = $input['direccion'];
	if(empty($direccion)){
		$app->render(500,array(
			'error' => TRUE,
            'msg'   => 'direccion is required',
        ));
	}

	$barrio = $input['barrio'];
	if(empty($barrio)){
		$app->render(500,array(
			'error' => TRUE,
            'msg'   => 'barrio is required',
        ));
	}

	$suptotal = $input['suptotal'];
	if(empty($suptotal)){
		$app->render(500,array(
			'error' => TRUE,
            'msg'   => 'suptotal is required',
        ));
	}

	$longitud = $input['longitud'];
	if(empty($longitud)){
		$app->render(500,array(
			'error' => TRUE,
            'msg'   => 'longitud is required',
        ));
	}

	$latitud = $input['latitud'];
	if(empty($latitud)){
		$app->render(500,array(
			'error' => TRUE,
            'msg'   => 'latitud is required',
        ));
	}

	$tipo = $input['tipo'];
	if(empty($tipo)){
		$app->render(500,array(
			'error' => TRUE,
            'msg'   => 'tipo is required',
        ));
	}

	$garage = $input['garage'];
	
	$balcon = $input['balcon'];
	
	$living = $input['living'];
	
	$comedor = $input['comedor'];
	

    $anuncio = new Anuncio();
    $anuncio->inmueble = $inmueble;
    $anuncio->titulo = $titulo;
    $anuncio->descripcion = $descripcion;
    $anuncio->precio = $precio;
    $anuncio->habitaciones = $habitaciones;
    $anuncio->banios = $banios;
    $anuncio->direccion = $direccion;
    $anuncio->barrio = $barrio;
    $anuncio->longitud = $longitud;
    $anuncio->latitud = $latitud;
    $anuncio->suptotal = $suptotal;
    $anuncio->tipo = $tipo;
    $anuncio->garage = $garage;
    $anuncio->balcon = $balcon;
    $anuncio->living = $living;
    $anuncio->comedor = $comedor;
	$anuncio->usersid = $user->id;
    $anuncio->save();
    $app->render(200,array('data' => $anuncio->toArray()));
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
	$anuncio = Anuncio::find($id);
	if(empty($anuncio)){
		$app->render(404,array(
			'error' => TRUE,
            'msg'   => 'user not found',
        ));
	}
    $anuncio->titulo = $titulo;
    $anuncio->descripcion = $descripcion;
    $anuncio->precio = $precio;
    $anuncio->save();
    $app->render(200,array('data' => $anuncio->toArray()));
});

//Buscar Anuncio

$app->get('/anuncios/:id', function ($id) use ($app) {
	$anuncio = Anuncio::find($id);
	if(empty($anuncio)){
		$app->render(404,array(
			'error' => TRUE,
            'msg'   => 'Anuncio not found',
        ));
	}

	$anuncio->user = User::find($anuncio->usersid);
	
	$app->render(200,array('data' => $anuncio->toArray()));
});

// Borrar Anuncio

$app->delete('/anuncios/:id', function ($id) use ($app) {
	$anuncio = Anuncio::find($id);
	if(empty($anuncio)){
		$app->render(404,array(
			'error' => TRUE,
            'msg'   => 'user not found',
        ));
	}

	$anuncio->delete();
	$app->render(200);
});
// traer un anuncio//
$app->get('/misanuncios', function () use ($app) {
	
		$token = $app->request->headers->get('auth-token');
		if(empty($token)){
			$app->render(500,array(
				'error' => TRUE,
				'msg'   => 'Not logged',
			));
		}
		$id_user_token = simple_decrypt($token, $app->enc_key);
		$anuncio = User::find($id_user_token);
		if(empty($anuncio)){
			$app->render(500,array(
				'error' => TRUE,
				'msg'   => 'Not logged',
			));
		}
		
	$db = $app->db->getConnection();
	$anuncios = $db->table('anuncios')->select('id', 'usersid', 'titulo', 'precio', 'descripcion', 'barrio', 'foto')->where('usersid', $anuncio->id)->get();
	$app->render(200,array('data' => $anuncios));
});

//Conexion con la tabla barrio

$app->get('/fav', function () use ($app) {
	$db = $app->db->getConnection();
	$fav = $db->table('favoritos')->select('idfavoritos', 'idanuncios', 'idusers')->get();

	$app->render(200,array('data' => $fav));
});

// agregar favoritos

$app->post('/favoritos', function () use ($app) {
  $token = $app->request->headers->get('auth-token');
	if(empty($token)){
		$app->render(500,array(
			'error' => TRUE,
            'msg'   => 'Not logged',
        ));
	}
	$id_user_token = simple_decrypt($token, $app->enc_key);
	$user = User::find($id_user_token);
	if(empty($user)){
		$app->render(500,array(
			'error' => TRUE,
            'msg'   => 'Not logged',
        ));
	}
	
  $input = $app->request->getBody();
  
  $idanuncio = $input['idanuncios'];
	if(empty($idanuncio)){
		$app->render(500,array(
			'error' => TRUE,
            'msg'   => 'Id anuncio is required',
        ));
	}
	
	$favorito = new Favorito();
    $favorito->idanuncios = $idanuncio;
    $favorito->idusers = $user->id;
    $favorito->save();
    $app->render(200,array('data' => $favorito->toArray()));
});

// Borrar favortios

$app->post('/misfavoritos', function () use ($app) {
	
   $token = $app->request->headers->get('auth-token');
	if(empty($token)){
		$app->render(500,array(
			'error' => TRUE,
            'msg'   => 'Not logged 1',
        ));
	}

	$id_user_token = simple_decrypt($token, $app->enc_key);
	$user = User::find($id_user_token);
	if(empty($user)){
		$app->render(500,array(
			'error' => TRUE,
            'msg'   => 'Not logged 2',
        ));
	}
	
	
	$input = $app->request->getBody();
  
	  $idanuncio = $input['idanuncio'];
		if(empty($idanuncio)){
			$app->render(500,array(
				'error' => TRUE,
				'msg'   => 'Id anuncio is required',
			));
		}
	
	$db = $app->db->getConnection();
	
	$favoritos = $db->table('favoritos')->select('idfavoritos', 'idusers', 'idanuncios')->where('idusers', $user->id)->where('idanuncios', $idanuncio)->get();
	
	$app->render(200,array('data' => $favoritos));
});

// listar mis favoritos

$app->get('/misfavoritoslist', function () use ($app) {
	
	$token = $app->request->headers->get('auth-token');
	if(empty($token)){
		$app->render(500,array(
			'error' => TRUE,
            'msg'   => 'Not logged',
        ));
	}
	$id_user_token = simple_decrypt($token, $app->enc_key);
	$user = User::find($id_user_token);
	if(empty($user)){
		$app->render(500,array(
			'error' => TRUE,
            'msg'   => 'Not logged',
        ));
	}
	
	
	$db = $app->db->getConnection();
	
	$favoritos = $db->table('favoritos')->select('idfavoritos', 'idusers', 'idanuncios')->where('idusers', $user->id)->get();
	
	$anunciosfav = $db->table('anuncios')->select('id', 'inmueble', 'titulo', 'precio', 'descripcion', 'foto', 'barrio', 'usersid', 'tipo', 'longitud', 'latitud', 'habitaciones', 'banios', 'suptotal', 'garage', 'balcon', 'living', 'comedor')->where('id', $favoritos->idanuncios)->get();
	
	$app->render(200,array('data' => $anunciosfav));
});


$app->run();


?>
