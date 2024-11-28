<?php 

	if (isset($_SESSION['app-stacionL'])) {
		header("location: panel");
	}	
	
	// variables para la vista
	$vars = ["MSG_ERROR" => "", "REGISTRADO" => ""];

	// carga la vista
	$tpl = new Motor("recovery");
	
	if (isset($_POST['btn_recovery'])) {
		unset($_POST['btn_recovery']);

		$usuario= new Users();

		$response = ($usuario->recuperar($_POST));

		if ($response["errno"]==200) {
			header("location: panel");
		}

		$vars = ["MSG_ERROR" => $response["error"], "REGISTRADO" => ""];

		if ($response["errno"]==404) {
			$vars = ["MSG_ERROR" => $response["error"], "REGISTRADO" => "<a href=register>Quiere registrase?</a>"];
		}
	}


	// reemplaza las variables de la vista
	$tpl->setVars($vars);

	// imprime la vista en la página
	$tpl->print();

 ?>