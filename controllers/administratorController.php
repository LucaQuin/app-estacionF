<?php 

	if (!isset($_SESSION['app-stacionL'])) {
		header("location: panel");
	}	

	if ($_SESSION['app-stacionL']->email!="admin-estacion@gmail.com" || $_SESSION['app-stacionL']->contraseña!="c93ccd78b2076528346216b3b2f701e6") {
		header("location: panel");
	}
	
	// carga la vista
	$tpl = new Motor("administrator");

	// imprime la vista en la página
	$tpl->print();

 ?>