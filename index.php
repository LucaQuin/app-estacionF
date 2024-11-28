<?php 
	// carga de modelos para que esten disponibles en todos los controladores
	// incluimos la variables de entorno
	include_once 'env.php';

	// inicia o continua la sesión
	// session_unset();
	// session_destroy();


	include_once 'lib/php-mailer/Mailer/src/PHPMailer.php';
	include_once 'lib/php-mailer/Mailer/src/SMTP.php';
	include_once 'lib/php-mailer/Mailer/src/Exception.php';

	// Carga del motor de plantillas
	include_once 'lib/Motor/Motor.php';
	include_once 'models/Users.php';

	session_start();

	// por defecto seccion es landing
	$seccion = "landing";


	// si existe slug entonces la sección es su contenido
	if($_GET['slug']!=""){
		$seccion = $_GET['slug'];
	}

	// verificamos que exista el controlador
	if(!file_exists('controllers/'.$seccion.'Controller.php')){
		// si no existe el controlador lo llevamos al controlador de error 404
		$seccion = "error404";
	}

	$aux=0;

	// var_dump($_SESSION);

	// listas de acceso por tipo de usuario
	$seccion_deslogue = ["landing", "panel", "detalle", "prueba", "login", "register", "validate", "blocked", "recovery", "reset"];
	$seccion_logue = ["landing", "detalle", "panel", "logout", "administrator", "map"];

	// recorro la lista de secciones permitidas

	if (isset($_SESSION['app-stacionL'])) {
		foreach ($seccion_logue as $key => $value) {
			if($value==$seccion){
				$aux++;
			}
		}
	}else{
		foreach ($seccion_deslogue as $key => $value) {
			if($value==$seccion){
				$aux++;
			}
		}
	}

	

	// 
	if ($aux==0) {
		$seccion = "error404";
	}

	// Carga del controlador
	include_once 'controllers/'.$seccion.'Controller.php';
 ?>