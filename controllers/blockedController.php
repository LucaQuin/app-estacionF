<?php 

	if (isset($_SESSION['app-stacionL'])) {
		header("location: panel");
	}	
	
	// carga la vista
	$tpl = new Motor("blocked");

	// imprime la vista en la página
	$tpl->print();

 ?>