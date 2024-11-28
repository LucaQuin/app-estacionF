	<?php
	
	// Incluimos la clase que conecta a la base de datos
	include_once 'DBAbstract.php';

	/*< incluimos la clase para enviar correo electrónico*/
	include_once 'Mailer.php';
	date_default_timezone_set('America/Argentina/Buenos_Aires');
	
	
	/**
	 * 
	 * Clase para trabajar con la tabla de usuarios
	 * 
	 * */
	class Users extends DBAbstract{


		public $attributes = array();

		/**
		 * 
		 * @brief Al instanciar hace autocarga de atributos
		 * 
		 * ejectuta el constructor de DBAbstract
		 * Realiza auto creación de atributos en la clase en base a la tabla
		 * 
		 * */

		function __construct(){
			parent::__construct();

			/**< Obtiene información de la tabla */
		 	$request = $this->query("DESCRIBE appEstacion__usuarios");

		 	$request = $request->fetch_all(MYSQLI_ASSOC);

			foreach ($request as $key => $value) {

				$var = $value["Field"];

				//Guarda los nombres de la columna en un vector
				$this->attibutes[]= $var;

				//Crea el atributo con el nombre de la columna
				$this->$var = "";
			}
		}

		// /**
		//  * 
		//  * Verifica el token enviado al email para validar al usuario
		//  * @brief valida el token email
		//  * @param array $form [token]
		//  * @return array [error, errno]
		//  * 
		//  * */
		function verify($token_active){

			$token_active= $_GET['token_active'];

			if ($token_active=="") {
				return ["errno" => 406, "error" => "No mandaron el token_active"];
			}

			/*< consulta para buscar el usuario por medio de su token*/
			$ssql = "SELECT * FROM `appEstacion__usuarios` WHERE token_action = '$token_active'; ";

			/*< ejecuta la consulta*/
			$response = $this->query($ssql)->fetch_all(MYSQLI_ASSOC);

			/*< si se encontro el usuario*/
			if(count($response)>0){

				if ($response[0]["activo"]!="1") {

					$fecha_hora = date("Y-m-d H:i:s");

					/*< activa el usuario y borra el token email*/
					$ssql = "UPDATE `appEstacion__usuarios` SET activo = '1', token_action = '', active_date = '$fecha_hora'  WHERE token_action = '$token_active'; ";

					/*< ejecuta la consulta*/
					$this->query($ssql);

					$tpl = new Motor("email/avisarActivo");

					/*< crea el objeto para enviar el email*/
					$mailer = new Mailer();

					/*< motivo del email*/
					$asunto = "Usuario activado";

					/*< carga las variables de la plantilla de email*/
					$tpl->setVars(["PROJECT_NAME" => $_ENV["PROJECT_NAME"]]);

					/*< la plantilla se pasa a una variable para que se imprima en el email*/
					$correo = $tpl->buffer;


					$email= $response[0]["email"];

					/*< envia el email de validación*/
					$response = $mailer->send($email, $asunto, $correo);

					return ["errno" => 200, "error" => "Usuario activado"];
				}
				return ["errno" => 403, "error" => "Este usuario ya esta activado"];

			}
				/*< el token no existe o no está relacionado a ningún usuario*/
			return ["errno" => 404, "error" => "El token no corresponde a un usuario"];

		}

		//  * 
		//  * Verifica el token enviado al email para validar al usuario
		//  * @brief valida el token email
		//  * @param array $form [token]
		//  * @return array [error, errno]
		//  * 
		//  * */
		function bloquear($token){

			$token= $_GET['token'];

			if ($token=="") {
				return ["errno" => 406, "error" => "No mandaron el token"];
			}

			/*< consulta para buscar el usuario por medio de su token*/
			$ssql = "SELECT * FROM `appEstacion__usuarios` WHERE token = '$token'; ";

			/*< ejecuta la consulta*/
			$response = $this->query($ssql)->fetch_all(MYSQLI_ASSOC);

			/*< si se encontro el usuario*/
			if(count($response)>0){

				if ($response[0]["bloqueado"]=="0") {
				
					$fecha_hora = date("Y-m-d H:i:s");

					$active_token = md5($_ENV['TOKEN_APP'].date("YmdHis").mt_rand(0,1000));

					/*< activa el usuario y borra el token email*/
					$ssql = "UPDATE `appEstacion__usuarios` SET bloqueado = '1', token_action = '$active_token', blocked_date = '$fecha_hora'  WHERE token = '$token'; ";

					/*< ejecuta la consulta*/
					$this->query($ssql);

					$tpl = new Motor("email/bloqueado");

					/*< crea el objeto para enviar el email*/
					$mailer = new Mailer();

					/*< motivo del email*/
					$asunto = "Usuario bloquedo";

					/*< carga las variables de la plantilla de email*/
					$tpl->setVars(["PROJECT_NAME" => $_ENV["PROJECT_NAME"],  "PROJECT_WEB" => $_ENV["PROJECT_WEB"], "EMAIL_TOKEN" => $response[0]["token_action"]]);

					/*< la plantilla se pasa a una variable para que se imprima en el email*/
					$correo = $tpl->buffer;


					$email= $response[0]["email"];

					/*< envia el email de validación*/
					$response = $mailer->send($email, $asunto, $correo);

					return ["errno" => 200, "error" => "Usuario bloqueado, revise su correo electrónico"];
				}

				return ["errno" => 300, "error" => "Esta cuenta ya ha sido bloqueada"];

			}
				/*< el token no existe o no está relacionado a ningún usuario*/
			return ["errno" => 404, "error" => "El token no corresponde a un usuario"];

		}


		function tracker($form){

			/*< valida que el métod http sea GET*/
			if($_SERVER["REQUEST_METHOD"]!="GET"){
				return ["errno" => 405, "error" => "Metodo incorrecto"];
			}

			$accion= $form["accion"];

			if ($accion=="list-clients-location") {
				$ssql = "SELECT `ip`,`latitud`,`longitud`, COUNT(`ip`) AS cantidad FROM `appEstacion__tracker` GROUP BY `ip`; ";

				$response = $this->query($ssql)->fetch_all(MYSQLI_ASSOC);
				return ["errno" => 200, "error" => $response];
			}
			return ["errno" => 404, "error" => "Parametro incorrecto"];

		}


		function devolceNRegis(){
			$sql = "SELECT COUNT(`ID_TRACEKR`) FROM `appEstacion__tracker`;";

			$response = $this->query($sql)->fetch_all(MYSQLI_ASSOC);

			return $response;
		}

		function devolceRegis(){
			$sql = "SELECT COUNT(`token`) FROM `appEstacion__usuarios`;";

			$response = $this->query($sql)->fetch_all(MYSQLI_ASSOC);

			return $response;
		}


		function verToken($form){

			/*< valida que el métod http sea GET*/
			if($_SERVER["REQUEST_METHOD"]!="GET"){
				return ["errno" => 405, "error" => "Metodo incorrecto"];
			}

			$token_action=$form["token_action"]; 

			if ($token_action=="" || $token_action=="null") {
				return ["errno" => 404, "error" => "No se envio ningun token"];
			}

			$sql = "SELECT * FROM `appEstacion__usuarios` WHERE token_action = '$token_action'; ";

			$response = $this->query($sql)->fetch_all(MYSQLI_ASSOC);

			if(count($response)==0){
				return ["errno" => 404, "error" => "Token invalido"];
			}

				return ["errno" => 200, "error" => "Token valido"];

		}


		function agregarTracker($datos){

			$ip=$datos["ip"];
			$latitud=$datos["latitude"];
			$longitud=$datos["longitude"];
			$country=$datos["country"];
	

			$token = md5($_ENV['TOKEN_APP'].date("YmdHis").mt_rand(0,1000));
			$sis_Opera=shell_exec("uname -a");
			$navegador=$_SERVER['HTTP_USER_AGENT'];
			$fecha_actual = date('Y-m-d H:i:s');


			$sql = "INSERT INTO `appEstacion__tracker` (`ID_TRACEKR`, `token`, `ip`, `latitud`, `longitud`, `pais`, `navegador`, `sistema`, `add_date`) VALUES (NULL, '$token', '$ip', '$latitud', '$longitud', '$country', '$navegador', '$sis_Opera', '$fecha_actual');";

			$result =$this->query($sql);

		}

		

				/**
		 * 
		 * Valida el usuario y contraseña
		 * @param array $form formulario de logueo sin el botón
		 * @return array errno and error
		 * 
		 * */
		function resetearContra($form){

			/*< valida que el métod http sea GET*/
			if($_SERVER["REQUEST_METHOD"]!="POST"){
				return ["errno" => 405, "error" => "Metodo incorrecto"];
			}

			/*< valida que el usuario ya haya iniciado sesión*/
			if(isset($_SESSION['app-stacionL'])){
				return ["errno" => 406, "error" => "Ya esta con la cuenta iniciada no puede cambiar la contraseña ahora (para hacerlo cerrar sesion)"];
			}
			
			$contra = $form["txt_contra_reset"];
			$contra2 = $form["txt_contra_reset2"];
			$token_action = $form["txt_token_active"]; 

			if ($contra!=$contra2) {
				return ["errno" => 304, "error" => "Las contraseñas no son iguales"];
			}

			
			$pass = md5($form["txt_contra_reset"]);

			$sql = "SELECT * FROM `appEstacion__usuarios` WHERE token_action = '$token_action'; ";
			$response = $this->query($sql)->fetch_all(MYSQLI_ASSOC);


			$ssql = "UPDATE `appEstacion__usuarios` SET bloqueado = '0', recupero = '0', contraseña = '$pass', token_action = '' WHERE token_action = '$token_action'; ";

			// $this->query($ssql);

			$tpl = new Motor("email/resetearComtra");

			/*< crea el objeto para enviar el email*/
			$mailer = new Mailer();

			/*< motivo del email*/
			$asunto = "Contraseña cambiada";
			

			$fecha_actual = date('Y-m-d H:i:s');

			$sis_Opera=shell_exec("uname -a");

			$email=$response[0]["email"];

			/*< carga las variables de la plantilla de email*/
			$tpl->setVars(["PROJECT_NAME" => $_ENV["PROJECT_NAME"], "PROJECT_WEB" => $_ENV["PROJECT_WEB"], "EMAIL_TOKEN" => $response[0]["token"], "HORA" => $fecha_actual, "IP" => $_SERVER['REMOTE_ADDR'], "SOPE" => $sis_Opera, "NAVEGADOR" => $_SERVER['HTTP_USER_AGENT']]);

			/*< la plantilla se pasa a una variable para que se imprima en el email*/
			$correo = $tpl->buffer;

			/*< envia el email de validación*/
			$response = $mailer->send($email, $asunto, $correo);

			return ["errno" => 200, "error" => "Cambio realizado y email enviado"];
		}


		/**
		 * 
		 * Valida el usuario y contraseña
		 * @param array $form formulario de logueo sin el botón
		 * @return array errno and error
		 * 
		 * */
		function recuperar($form){

			/*< valida que el métod http sea GET*/
			if($_SERVER["REQUEST_METHOD"]!="POST"){
				return ["errno" => 405, "error" => "Metodo incorrecto"];
			}

			/*< valida que el usuario ya haya iniciado sesión*/
			if(isset($_SESSION['app-stacionL'])){
				return ["errno" => 406, "error" => "Ya esta logueado no puede volver a loguearse"];
			}
			
			$email = $form["txt_emial_recovery"];

			// averigua si el email existe en la tabla de users
			$sql = "SELECT * FROM `appEstacion__usuarios` WHERE email = '$email'; ";

			$result =$this->query($sql);

			$result = $result->fetch_all(MYSQLI_ASSOC);

			// si no hay filas
			if(count($result)==0){
				return ["errno" => 404, "error" => "Usuario no registrado"];
			}

			if($result[0]["recupero"]=="1"){
				return ["errno" => 407, "error" => "Este usuario ya esta en proceso de restablecimiento de contraseña"];
			}


			$fecha_hora = date("Y-m-d H:i:s");

			$active_token = md5($_ENV['TOKEN_APP'].date("YmdHis").mt_rand(0,1000));
			
			$ssql = "UPDATE `appEstacion__usuarios` SET recupero = '1', token_action = '$active_token', recover_date = '$fecha_hora'  WHERE email = '$email'; ";

			$this->query($ssql);

			$tpl = new Motor("email/recuperar");

			/*< crea el objeto para enviar el email*/
			$mailer = new Mailer();

			/*< motivo del email*/
			$asunto = "Recuperar contraseña";
			

			$fecha_actual = date('Y-m-d H:i:s');

			$sis_Opera=shell_exec("uname -a");

			/*< carga las variables de la plantilla de email*/
			$tpl->setVars(["PROJECT_NAME" => $_ENV["PROJECT_NAME"], "PROJECT_WEB" => $_ENV["PROJECT_WEB"],"EMAIL_TOKEN" => $active_token, "EMAIL" => $result[0]["email"]]);

			/*< la plantilla se pasa a una variable para que se imprima en el email*/
			$correo = $tpl->buffer;

			/*< envia el email de validación*/
			$response = $mailer->send($email, $asunto, $correo);

			return ["errno" => 200, "error" => "Logueo valido"];
		}

		/**
		 * 
		 * Valida el usuario y contraseña
		 * @param array $form formulario de logueo sin el botón
		 * @return array errno and error
		 * 
		 * */
		function login($form){

			/*< valida que el métod http sea GET*/
			if($_SERVER["REQUEST_METHOD"]!="POST"){
				return ["errno" => 405, "error" => "Metodo incorrecto"];
			}

			/*< valida que el usuario ya haya iniciado sesión*/
			if(isset($_SESSION['app-stacionL'])){
				return ["errno" => 406, "error" => "Ya esta logueado no puede volver a loguearse"];
			}
			
			$email = $form["txt_emial_login"];
			// encripta la contraseña con md5
			
			// si existe el elemento cifrado la contraseña proporcionada no se cifra
			$pass = md5($form["txt_contra_login"]);

			// averigua si el email existe en la tabla de users
			$sql = "SELECT * FROM `appEstacion__usuarios` WHERE email = '$email'";

			$result =$this->query($sql);

			$result = $result->fetch_all(MYSQLI_ASSOC);

			// si no hay filas
			if(count($result)==0){
				return ["errno" => 404, "error" => "Usuario no registrado"];
			}

			if($result[0]["activo"]=="0"){
				return ["errno" => 404, "error" => "Su usuario aún no se ha validado, revise su casilla de correo"];
			}

			if($result[0]["bloqueado"]=="1"){
				return ["errno" => 404, "error" => "Este usuario esta bloqueado"];
			}

			if($result[0]["recupero"]=="1"){
				return ["errno" => 404, "error" => "A este usuario lo quieren cambiar la contraseña"];
			}

			// si la contraseña coincide

			if($result[0]["contraseña"]==$pass){

				// autocarga de valores en los atributos
				foreach ($this->attibutes as $key => $attribute) {
					// menos la contraseña
					$this->$attribute = $result[0][$attribute];
				}

				

			$_SESSION['app-stacionL'] = $this;

			$tpl = new Motor("email/accesoLogrado");

			/*< crea el objeto para enviar el email*/
			$mailer = new Mailer();

			/*< motivo del email*/
			$asunto = "Sesion iniciada";
			

			$fecha_actual = date('Y-m-d H:i:s');

			$sis_Opera=shell_exec("uname -a");

			/*< carga las variables de la plantilla de email*/
			$tpl->setVars(["PROJECT_NAME" => $_ENV["PROJECT_NAME"], "PROJECT_WEB" => $_ENV["PROJECT_WEB"],"EMAIL_TOKEN" => $result[0]["token"], "HORA" => $fecha_actual, "IP" => $_SERVER['REMOTE_ADDR'], "SOPE" => $sis_Opera, "NAVEGADOR" => $_SERVER['HTTP_USER_AGENT']]);

			/*< la plantilla se pasa a una variable para que se imprima en el email*/
			$correo = $tpl->buffer;

			/*< envia el email de validación*/
			$response = $mailer->send($email, $asunto, $correo);

				return ["errno" => 200, "error" => "Logueo valido"];
			}

			$tpl = new Motor("email/intentoDeAcceso");

			/*< crea el objeto para enviar el email*/
			$mailer = new Mailer();

			/*< motivo del email*/
			$asunto = "Inteno de acceso fallido";
			

			$fecha_actual = date('Y-m-d H:i:s');

			$sis_Opera=shell_exec("uname -a");

			/*< carga las variables de la plantilla de email*/
			$tpl->setVars(["PROJECT_NAME" => $_ENV["PROJECT_NAME"], "PROJECT_WEB" => $_ENV["PROJECT_WEB"],"EMAIL_TOKEN" => $result[0]["token"], "HORA" => $fecha_actual, "IP" => $_SERVER['REMOTE_ADDR'], "SOPE" => $sis_Opera, "NAVEGADOR" => $_SERVER['HTTP_USER_AGENT']]);

			/*< la plantilla se pasa a una variable para que se imprima en el email*/
			$correo = $tpl->buffer;

			/*< envia el email de validación*/
			$response = $mailer->send($email, $asunto, $correo);

			return ["errno" => 400, "error" => "Contraseña incorrecta"];
		}

		// /**
		//  * 
		//  * Actualiza los datos del usuario
		//  * @param array $form formulario sin el botón
		//  * @return array errno and error
		//  * 
		//  * */
		// function update($form){

		// 	$nombre = $form["txt_nombre"];
		// 	$apellido = $form["txt_apellido"];
		// 	$id = $this->ID_USER;

		// 	// actualiza el nombre
		// 	$sql = "CALL `users_update`('$nombre','$apellido',$id)";

		// 	// ejecuta la consulta
		// 	$this->query($sql);

		// 	// reemplaza el atributo nombre con el valor nuevo
		// 	$this->nombre = $nombre;
		// 	$this->apellido = $apellido;

		// 	// retorna el mensaje de que esta todo bien
		// 	return ["errno" => 200, "error" => "Se actualizaron los datos"];
			
		// }

		// /**
		//  * 
		//  * Agrega un nuevo usuario si no existe en la tabla de usuarios el correo
		//  * @param array $form formulario sin el botón
		//  * @return array errno and error
		//  * 
		//  * */
		function register($form){

			$email = $form["txt_email_register"];
			$contra = $form["txt_contra_register"];
			$contra2 = $form["txt_contra_register2"];


			$sql = "SELECT * FROM `appEstacion__usuarios` WHERE email = '$email'";
			$result =$this->query($sql);
			$result = $result->fetch_all(MYSQLI_ASSOC);


			if(count($result)>=1){
				return ["errno" => 400, "error" => "Email ya registrado"];
			}

			if ($contra!=$contra2) {
				return ["errno" => 401, "error" => "Las contraseñas no son iguales"];

			}

			$email_token = md5($_ENV['TOKEN_APP'].date("YmdHis").mt_rand(0,1000));
			$active_token = md5($_ENV['TOKEN_APP'].date("YmdHis").mt_rand(0,1000));
			$fecha_hora = date("Y-m-d H:i:s");


			$pass = md5($contra);
			
			$ssql = "INSERT INTO `appEstacion__usuarios` (`ID_USUARIO`, `token`, `email`, `nombres`, `contraseña`, `activo`, `bloqueado`, `recupero`, `token_action`, `add_date`, `update_date`, `delete_date`, `active_date`, `blocked_date`, `recover_date`) VALUES (NULL, '$email_token', '$email', '', '$pass', '', '', '', '$active_token', '$fecha_hora', '0', '0', '0', '0', '0')";
			
			$this->query($ssql);


			$tpl = new Motor("email/validation");

			/*< crea el objeto para enviar el email*/
			$mailer = new Mailer();

			/*< motivo del email*/
			$asunto = "Verificar cuenta";

			/*< carga las variables de la plantilla de email*/
			$tpl->setVars(["PROJECT_NAME" => $_ENV["PROJECT_NAME"], "PROJECT_WEB" => $_ENV["PROJECT_WEB"],"EMAIL_TOKEN" => $active_token]);

			/*< la plantilla se pasa a una variable para que se imprima en el email*/
			$correo = $tpl->buffer;

			/*< envia el email de validación*/
			$response = $mailer->send($email, $asunto, $correo);


			return ["errno" => 200, "error" => "Usuario registrado, revise su email"];
		}


		// /**
		//  * 
		//  * Busca un usuario por medio de su email
		//  * @param string $email correo electrónico del usuario
		//  * @return array datos con datos del usuario
		//  * 
		//  * */
		// function getByEmail($email){

		// 	// Busca el email
		// 	$result = $this->query("SELECT * FROM users WHERE email = '$email'");

		// 	// Ponemos los arrays en forma asocaitiva
		// 	$result = $result->fetch_all(MYSQLI_ASSOC);

		// 	// carca el atributo nombre con el nombre del usuario
		// 	$this->nombre = $result[0]["first_name"];

		// 	return $result;
		// }

		// /**
		//  * 
		//  * Cantidad total de usuarios
		//  * @return int cantidad de usuarios
		//  * 
		//  * */
		// function cant(){
		// 	$this->query("SELECT * FROM app-stacionL__usuarios");
		// 	return $this->db->affected_rows;
		// }

		// function cantTienda(){
		// 	$this->query("SELECT * FROM store_x__tienda");
		// 	return $this->db->affected_rows;
		// }

		// function mostrarTiendas(){
		// 	$this->query("SELECT * FROM store_x__tienda ");
		// }

		// /**
		//  * 
		//  * @brief Lista de usuarios limitada GET
		//  * @param array $params [inicio]int [cantidad]int
		//  * @return array listado de usuarios
		//  *
		//  * */
		// function getAll($params){

		// 	if($_SERVER["REQUEST_METHOD"]!="GET"){
		// 		return ["errno" => 405, "error" => "Metodo incorrecto"];
		// 	}

		// 	$inicio = $params["inicio"];
		// 	$cantidad = $params["cantidad"];

		// 	// var_dump($cantidad);

		// 	$result = $this->consultar("SELECT * FROM store_x__usuarios LIMIT  $inicio,$cantidad")->fetch_all(MYSQLI_ASSOC);

		// 	$result = ["errno" => 200, "error" => "Listado correctamente", "info" => $result];

		// 	return $result;
		// }

		// function recuperar($form){
		// 	$email = $form["txt_email"];

		// 	// Averigua si el usuario ya esta en la tabla de store_x__usuarios
		// 	$result1= $this->query("SELECT * FROM store_x__usuarios WHERE email = '$email'");

		// 	// Ponemos los arrays en forma asocaitiva
		// 	$result1 = $result1->fetch_all(MYSQLI_ASSOC);

		// 	if(count($result1)!=0){
		// 		/*< genera el token para enviar en el email y validar el usuario*/
		// 		$email_token = md5($_ENV['TOKEN_APP'].date("YmdHis").mt_rand(0,1000));
		// 		$fecha = date("YmdHis");

		// 		/*< carga la plantilla de email de validación*/
		// 		$tpl = new Motor("email/validationPassword");

		// 		/*< crea el objeto para enviar el email*/
		// 		$mailer = new Mailer();

		// 		/*< motivo del email*/
		// 		$asunto = "Recuperación de contraseña";

		// 		/*< carga las variables de la plantilla de email*/
		// 		$tpl->setVars(["EMAIL_TOKEN" => $email_token]);

		// 		/*< la plantilla se pasa a una variable para que se imprima en el email*/
		// 		$correo = $tpl->buffer;

		// 		/*< envia el email de validación*/
		// 		$response = $mailer->send($email, $asunto, $correo);
		// 		// var_dump($response);

		// 		// mensaje de exito al agregar
		// 		return ["errno" => 200, "error" => "gmail enviado"];
		// 	}

		// 	// mensaje de usuario ya registrado
		// 	return ["errno" => 203, "error" => "gmail no existente"];

		// }

		// function cambiar($form){
		// 	$pass1 = $form["txt_pass1"];
		// 	$pass2 = $form["txt_pass2"];
		// 	/*< recupera el token del array*/
		// 	$token = $form["token"];

		// 	if($pass1==$pass2){
		// 		/*< activa el usuario y borra el token email*/
		// 		$ssql = "UPDATE store_x__usuarios SET contraseña = $pass1 WHERE email_token = '$token'";
		// 		// mensaje de usuario ya registrado
		// 		return ["errno" => 200, "error" => "La contraseña a sido cambiada"];
		// 	}
		// 	// mensaje de usuario ya registrado
		// 	return ["errno" => 203, "error" => "Las contraseñas ingresadas son diferentes"];
		// }
	}

 ?>