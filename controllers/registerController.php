<?php 

	if (isset($_SESSION['app-stacionL'])) {
		header("location: panel");
	}	
	
	// variables para la vista
	$vars = ["MSG_ERROR" => "", "REGISTRADO" => "", "REGISTRADO" =>""];

	// carga la vista
	$tpl = new Motor("register");
	
	if (isset($_POST['btn_register'])) {
		unset($_POST['btn_register']);

		$usuario= new Users();

		$response = ($usuario->register($_POST));

		if ($response["errno"]==200) {
			// header("location: ");
		}

		$vars = ["MSG_ERROR" => $response["error"], "REGISTRADO" => ""];

		if ($response["errno"]==400) {
			$vars = ["MSG_ERROR" => $response["error"], "REGISTRADO" => "<a href=login>Quiere iniciar sesion?</a>"];
		}
	}


	// reemplaza las variables de la vista
	$tpl->setVars($vars);

	// imprime la vista en la página
	$tpl->print();

 ?>