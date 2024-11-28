<?php 

	if (isset($_SESSION['app-stacionL'])) {
		header("location: panel");
	}	
	
	// carga la vista
	$tpl = new Motor("validate");

	// imprime la vista en la página
	$tpl->print();

 ?>