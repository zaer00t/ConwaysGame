#!/usr/bin/php -q
<?php
#==================================================#
#     coded by: Moises E                           #
#     nick: zaer00t                                #
#    e-mail: zaer00t@gmail.com                     #
#==================================================#
if(php_sapi_name() != 'cli')
	throw new Exception('Este script php solo corre bajo la linea de comandos.');
$nom_script = $argv[0];

function comoUsar($nom_script)
{
	echo "\nPara iniciar este script es necesario proporcionarle la cantidad de columnas y filas\n";
	echo "Por ejemplo, para crear un tablero de 5 columnas por 5 filas\n";
	echo "\tmodo de uso:\t\033[1;97m".$nom_script."\033[0;0m \033[1;91m5\033[0;0m \033[1;91m5\n\033[0;0m\n";
	exit(1);
}
if((count($argv)==1)) comoUsar($argv[0]);

$titulo = "Juego de la vida (Conway's Game of Life)";
$pid = getmypid();// cachamos el pid

if(!cli_set_process_title($titulo)) {
    echo "No es posible ponerle titulo al PID $pid...\n";
    exit(1);
} else {
    echo "Inicio el '$titulo' con PID $pid!\n";
    sleep(3);
}

// Dimensiones del tablero
$filas = intval($argv[1]);
$columnas = intval($argv[2]);

// Inicialización del tablero
$tablero = array();
for ($i = 0; $i < $filas; $i++)
{
	$tablero[$i] = array();
	for ($j = 0; $j < $columnas; $j++)
	{
		// Cada celda se inicializa aleatoriamente con 0 o 1
		$tablero[$i][$j] = rand(0, 1);
	}
}

// Función para imprimir el tablero
function imprimirTablero($tablero)
{
	for ($i = 0; $i < count($tablero); $i++)
	{
		for ($j = 0; $j < count($tablero[$i]); $j++)
		{
			echo $tablero[$i][$j] ? "\033[1;92m■ " : "\033[00;00m□ ";
		}
		echo "\n";
	}
	echo "\n";
}

// Función para obtener el número de vecinos vivos de una celda
function obtenerVecinosVivos($tablero, $fila, $columna)
{
	$vecinosVivos = 0;
	$vecinos = array(
		array(-1,-1),array(-1,0),
		array(-1, 1),array(0,-1),
		array(0,1),array(1,-1),
		array(1,0),array(1,1)
	);

	foreach ($vecinos as $vecino)
	{
		$filaVecino = $fila + $vecino[0];
		$columnaVecino = $columna + $vecino[1];

        if(
			$filaVecino >= 0 && $filaVecino < count($tablero) &&
			$columnaVecino >= 0 && $columnaVecino < count($tablero[$filaVecino]) &&
			$tablero[$filaVecino][$columnaVecino])
		{
			$vecinosVivos++;
		}
	}
	return $vecinosVivos;
}

// Función para aplicar una iteración del juego de la vida
function iterarJuegoVida(&$tablero)
{
	$nuevoTablero = array();

	for ($i = 0; $i < count($tablero); $i++)
	{
		$nuevoTablero[$i] = array();
		for ($j = 0; $j < count($tablero[$i]); $j++)
		{
			$vecinosVivos = obtenerVecinosVivos($tablero, $i, $j);
			$celulaActual = $tablero[$i][$j];

			// Aplicar las reglas del juego de la vida
			if($celulaActual && ($vecinosVivos == 2 || $vecinosVivos == 3))
			{
				// Una célula viva con 2 o 3 vecinos vivos sigue viva
				$nuevoTablero[$i][$j] = 1;
			}
			elseif(!$celulaActual && $vecinosVivos == 3)
			{
				// Una célula muerta con exactamente 3 vecinos vivos revive
				$nuevoTablero[$i][$j] = 1;
			}
			else
			{
				// En cualquier otro caso, la célula muere o permanece muerta
				$nuevoTablero[$i][$j] = 0;
			}
		}
	}
	$tablero = $nuevoTablero;
}

// Ejecutar el juego de la vida durante n iteraciones
for ($k = 1; $k <= 1000; $k++)
{
	echo "Iteración: $k\n";
	imprimirTablero($tablero);
	usleep(90000);
	iterarJuegoVida($tablero);
	system('clear');
}
?>
