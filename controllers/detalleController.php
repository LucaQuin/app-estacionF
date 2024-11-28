<?php 

	if (!isset($_SESSION['app-stacionL'])) {
		header("location: login");
	}	

	// Carga la vista
	$tpl = new Motor("detalle");

	// imprime la vista en la página
	$tpl->print();

 ?>