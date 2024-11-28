<?php 

	// carga la vista
	$tpl = new Motor("panel");

	$usuario= new Users();

	// $vars = ["MSG_ERROR" => $response];

	// $tpl->setVars($vars);


	// imprime la vista en la página
	$tpl->print();

 ?>