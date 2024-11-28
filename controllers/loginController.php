<?php 

	if (isset($_SESSION['app-stacionL'])) {
		header("location: panel");
	}	
	
	// variables para la vista
	$vars = ["MSG_ERROR" => ""];

	// carga la vista
	$tpl = new Motor("login");
	
	if (isset($_POST['btn_login'])) {
		unset($_POST['btn_login']);

		$usuario= new Users();

		$response = ($usuario->login($_POST));

		if ($response["errno"]==200) {
			header("location: panel");
		}

		$vars = ["MSG_ERROR" => $response["error"]];
	}


	// reemplaza las variables de la vista
	$tpl->setVars($vars);

	// imprime la vista en la página
	$tpl->print();

 ?>