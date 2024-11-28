<?php 

	if (isset($_SESSION['app-stacionL'])) {
		header("location: panel");
	}	
	
	// variables para la vista
	$vars = ["MSG_ERROR" => ""];

	// carga la vista
	$tpl = new Motor("reset");
	
	if (isset($_POST['btn_reset'])) {
		unset($_POST['btn_reset']);

		$usuario= new Users();

		$response = ($usuario->resetearContra($_POST));

		if ($response["errno"]==200) {
			// header("location: panel");
		}

		$vars = ["MSG_ERROR" => $response["error"]];

		if ($response["errno"]==404) {
			$vars = ["MSG_ERROR" => $response["error"]];
		}
	}


	// reemplaza las variables de la vista
	$tpl->setVars($vars);

	// imprime la vista en la página
	$tpl->print();

 ?>