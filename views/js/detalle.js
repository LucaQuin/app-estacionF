
	// Ejemplo de uso
	const parametro = getParameterByName('chipid');

	buscar(parametro).then( data => {
		let fecha = document.querySelector(".fecha");
		let ubicacion = document.querySelector(".ubicacion");

		data.forEach(estacion => {
			fecha.innerHTML +=`
				${estacion.fecha}
			`
			ubicacion.innerHTML +=`
				${estacion.ubicacion}
			`
		})	
	})

	function getParameterByName(name) {
	    const urlParams = new URLSearchParams(window.location.search);
	    return urlParams.get(name);
	}

	/**
     * 
     * @brief Realiza el logueo con el email y contraseña GET
     * @param string nombre del usuario
     * @param string pass contraseña del usuario
     * @return json respuesta del intento de logueo
     * 
     * */
    async function buscar(parametro){
        /*< consulta a la API */
        const response = await fetch("https://mattprofe.com.ar/proyectos/app-estacion/datos.php?chipid="+parametro+"&cant=1");
        /*< convierte la respuesta a formato json */
        const data = await response.json();

        return data;
    }


// variable que se usará para instanciar el gráfico
		let grafico = null

		// // vectores para almacenar los valores del gráfico
		let temperatura	= []
		let periodo = []
		let ffmc = []
		let humedad = []
		let presion = []
		let viento = []


		let cual2=""
		let aux=0


		function verificar(){
			if (cual2!="") {
				opcBotones(cual2)
			}else{
				opcBotones("temperatura")
			}
		}

		setInterval(verificar, 60000);			

		

		// // Una vez cargado todo el DOM 
		document.addEventListener("DOMContentLoaded", () => {
			if (aux==0) {
			opcBotones("temperatura")
			}
		})

		function opcBotones(cual){
			cual2=cual;
			aux=1
			console.log(cual)
			refreshDatos(parametro).then(data => {
				procesaDatos(data)
			})
		}


		// Toma los datos de un registro y los agrega a los vectores correspondientes, luego los agrega a los datos para generar el gráfico
		function procesaDatos(dato){
			// console.log(dato[0])

			// Muestra los datos en el monitor
			for (var i = dato.length - 1; i >= 0; i--) {
				pintaMonitor(dato[i])

				temperatura.push(dato[i].temperatura)
				periodo.push(dato[i].fecha)
				ffmc.push(dato[i].ffmc)
				humedad.push(dato[i].humedad)
				presion.push(dato[i].presion)
				viento.push(dato[i].viento)

				// console.log(periodo.length)

				if(periodo.length>6){
					temperatura.splice(0,1);
					periodo.splice(0,1);
					ffmc.splice(0,1);
					humedad.splice(0,1);
					presion.splice(0,1);
					viento.splice(0,1);
				}
			}
	
			// Agregamos el nuevo dato como una posición dentro del vector
			

			// Si ya acumulamos 6 elementos borramos el primero

		    switch (cual2) {
		    	case "temperatura":
				    valores = {
						labels: periodo,
						datasets: [{
							label: '', // detalle de la linea graficada
							backgroundColor: 'rgb(25, 174, 49)', // color circulo
							borderColor: 'rgb(25, 174, 49)', // color linea
							data: temperatura // valores a graficar
						}]
					}
					
					document.querySelector(".contenido-main").innerHTML = `
						<div class="tempe">
							<div class="temeperaturaT">${dato[0].temperatura}</div>
							<div class="tempeMaxi">${dato[0].maxtemperatura}</div>
							<div class="tempeMini">${dato[0].tempmin}</div>
						</div>
						<div class="sensacion">
							<div class="sensaT">${dato[0].sensacion}</div>
							<div class="sensacionMaxi">${dato[0].sensamax}</div>
							<div class="sensacionMini">${dato[0].sensamin}</div>
						</div>
					`
			  	break;
			  	case "ffmc":
			    	valores = {
						labels: periodo,
						datasets: [{
							label: '', // detalle de la linea graficada
							backgroundColor: 'rgb(25, 174, 49)', // color circulo
							borderColor: 'rgb(25, 174, 49)', // color linea
							data: ffmc // valores a graficar
						}]
					}
					
					document.querySelector(".contenido-main").innerHTML = `
						<div class="titulo-fuego">Fuego</div>
						<div class="fuego-contenido">
							<div class="fuego-ffmc">${dato[0].ffmc}</div>
							<div class="fuego-isi">${dato[0].isi}</div>
							<div class="fuego-dmc">${dato[0].dmc}</div>
							<div class="fuego-bui">${dato[0].bui}</div>
							<div class="fuego-dc">${dato[0].dc}</div>
							<div class="fuego-fwi">${dato[0].fwi}</div>
						</div>
					`
			    break;
			    case "humedad":
				    valores = {
						labels: periodo,
						datasets: [{
							label: '', // detalle de la linea graficada
							backgroundColor: 'rgb(25, 174, 49)', // color circulo
							borderColor: 'rgb(25, 174, 49)', // color linea
							data: humedad // valores a graficar
						}]
					}

					document.querySelector(".contenido-main").innerHTML = `
						<div class="titulo-humedad">humedad</div>
						<div class="procentaje-humedad">%${dato[0].humedad}</div>
					`
			    break;
			    case "presion":
				    valores = {
						labels: periodo,
						datasets: [{
							label: '', // detalle de la linea graficada
							backgroundColor: 'rgb(25, 174, 49)', // color circulo
							borderColor: 'rgb(25, 174, 49)', // color linea
							data: presion // valores a graficar
						}]
					}
					document.querySelector(".contenido-main").innerHTML = `
						<div class="titulo-presion">presion</div>
						<div class="procentaje-presion">${dato[0].presion}hPa</div>
					`
			    break;
			    case "viento":
				    valores = {
						labels: periodo,
						datasets: [{
							label: '', // detalle de la linea graficada
							backgroundColor: 'rgb(25, 174, 49)', // color circulo
							borderColor: 'rgb(25, 174, 49)', // color linea
							data: viento // valores a graficar
						}]
					}
					document.querySelector(".contenido-main").innerHTML = `
						<div class="titulo-viento">viento</div>
						<div class="procentaje-viento">${dato[0].viento}km/h</div>
						<div class="procentaje-viento">${dato[0].maxviento}km/h</div>
						<div class="procentaje-viento">${dato[0].veleta}km/h</div>
					`
			    break;
			  default:
			    valores = {
					labels: periodo,
					datasets: [{
						label: '', // detalle de la linea graficada
						backgroundColor: 'rgb(25, 174, 49)', // color circulo
						borderColor: 'rgb(25, 174, 49)', // color linea
						data: temperatura // valores a graficar
					}]
				}
				document.querySelector(".contenido-main").innerHTML = `
					<div class="tempe">
						<div class="temeperaturaT">${dato[0].temperatura}</div>
						<div class="tempeMaxi">${dato[0].maxtemperatura}</div>
						<div class="tempeMini">${dato[0].tempmin}</div>
					</div>
					<div class="sensacion">
						<div class="sensaT">${dato[0].sensacion}</div>
						<div class="sensacionMaxi">${dato[0].sensamax}</div>
						<div class="sensacionMini">${dato[0].sensamin}</div>
					</div>
				`
			}
			

			pintaGrafico(valores, 'Dolar histórico')
		}


		// muestra el gráfico
		function pintaGrafico(valores, titulo){
			// console.log(valores)

			// Opciones generales del gráfico
			const options = {
				indexAxis: 'x', // Orden de los ejes del gráfico
				plugins: {
					title: { 
						display: false, // Mostrar el título
						text: titulo, // Texto del título
						font: {
							size: 30 // Tamaño del título
						}
					}
				},
				animation: {
					duration: 0
				},
				responsive: true,
				responsiveAnimationDuration: 0,
			}

			// Información con la cual se genera el gráfico
			const config = {
				type: 'line',
				data: valores,
				options: options
			}
			
			// si el objeto gráfico ya esta instanciado se destruye para que se vuelva a crear limpio
			if(grafico!=null){
		        grafico.destroy();
		    }

			// console.log(document.querySelector("#tabla-Graficos"))
			// Crea el gráfico dentro del canvas
			grafico = new Chart(document.querySelector("#Grafico"), config)
		}


		// muestra los valores en el monitor
		function pintaMonitor(valores){

			// let periodo = document.querySelector("#periodo")
			// let  = document.querySelector("#oficial")
			// let blue = document.querySelector("#blue")

			// periodo.innerHTML = valores.periodo
			// oficial.innerHTML = "$" + valores.oficial
			// blue.innerHTML = "$" + valores.blue
		}

		// Hace la peticion asincrona al archivo php y recupera el Json con los datos
		// =================================
		async function refreshDatos(parametro){
			const response = await fetch("https://mattprofe.com.ar/proyectos/app-estacion/datos.php?chipid="+parametro+"&cant=6")
			const data = await response.json()
			return(data)
		}