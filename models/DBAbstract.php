
	<?php
	
	/**
	 * 
	 * Colocamos las creedenciales para la base de datos
	 * 
	 * */


	// Desactivamos el reporte de errores de mysqli
	mysqli_report(MYSQLI_REPORT_OFF);

	/**
	 * 
	 * Clase para heredar la conexión a la base de datos
	 * 
	 * */
	class DBAbstract{

		public $db;

		/**
		 * 
		 * Genera la conexión a la base de datos
		 * 
		 * */
		function __construct(){
			
			$this->connect();
		}

		/**
		 * 
		 * Realiza la conexión con la base de datos
		 * 
		 * */
		function connect(){
			// instancia la clase mysqli
			$this->db = @new mysqli($_ENV['HOST'], $_ENV['USER'], $_ENV['PASS'], $_ENV['DB']);
			
			// en caso de error de conección
			if($this->db->connect_errno){
				
				echo "Hubo un error en la conexion a la base de datos<br>";
				echo "codigo de error (".$this->db->connect_errno.") ".$this->db->connect_error;
				exit();
			}
		}

		/**
		 * 
		 * Realiza una consulta a la base de datos
		 * @param string $sql query
		 * @return object resultado de la query
		 * 
		 * */
		function query($sql){

			$this->connect();

			// ejecuta la consulta a la db
			$result = $this->db->query($sql);

			// en caso de error de consulta
			if($this->db->errno){

				echo "Hubo en error en la consulta: (".$this->db->errno.") ".$this->db->error;
				exit();
			}
			return $result;
		}


		/**
		 * 
		 * realiza una consulta a la base de datos tipo DML
		 * 
		 * @param string $sql consulta en formato SQL
		 * @return array|bool lista indexada de forma asociativa (SELECT)|true (INSERT,UPDATE,DELETE)
		 * 
		 * */
		function consultar($sql){

			$this->connect();
			
			$response = $this->db->query($sql);

			if($this->db->errno){
				echo "Error de consulta: ".$this->db->error;
				exit();
			}
			
			return $response;
		}
	}
?>