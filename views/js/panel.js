let main = document.querySelector("main");

		buscar().then( data => {
			let aux=0;
			data.forEach(estacion => {
				main.innerHTML +=`
					<a href="detalle?chipid=${estacion.chipid}">
						<div class="estacion">
							<div class="nombre">${estacion.apodo}</div>
							<div class="ubicacion">${estacion.ubicacion}</div>
							<div class="vistas">${estacion.visitas}</div>
							<div class="activo a${aux}">"Inactive"</div>
						</div>
					</a>
				`

				if (estacion.dias_inactivo==0) {
					let act = document.querySelector(`.a${aux}`);

					act.style.display = "none";
				}
				aux++;
			})		
		})

		/**
         * 
         * @brief Realiza el logueo con el email y contraseña GET
         * @param string nombre del usuario
         * @param string pass contraseña del usuario
         * @return json respuesta del intento de logueo
         * 
         * */
        async function buscar(){
            /*< consulta a la API */
            const response = await fetch('https://mattprofe.com.ar/proyectos/app-estacion/datos.php?mode=list-stations');
            /*< convierte la respuesta a formato json */
            const data = await response.json();

            return data;
        }