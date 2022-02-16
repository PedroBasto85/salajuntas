<?php
include('conexionlocal.php');
//include('conexion.php');
date_default_timezone_set('America/Bogota');
$conexion = new mysqli(db_Servidor, db_Usuario, db_Pass, db_BaseDatos);
$conexion->set_charset('utf8');

$sqlHorarios = "SELECT Etiqueta, Etiqueta24 FROM syshorario";
$datosHorarios = $conexion->query($sqlHorarios);

?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta viewport="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Reservar</title>
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/foundation.min.css">
    <link rel="stylesheet" href="css/app.css">
</head>
<body>

<div class="agenda">
    <div class="row">
        <div class="logo">
            <img src="img/logo.png">
        </div>
    </div>
    <div class="row">
        <div class="small-12 columns">
            <span>Agendar reservación en sala de juntas</span>
            <div class="linea"></div>
        </div>

    </div>
    <div class="row">
        <div class="small-12 medium-6 large-4 columns">
            <input type="text" id="txtTitulo" name="txtTitulo" class="campo" placeholder="TITULO" required>
        </div>
        <div class="small-6 medium-6 large-4 columns left">
            <input type="text" id="txtPersonas" name="txtPersonas" class="campo persona" placeholder="PERSONAS" required>
        </div>
        <div class="small-6 medium-6 large-4 columns">
            <input type="text" id="txtFecha" name="txtFecha" class="campo fecha" placeholder="FECHA" required>
        </div>
    </div>
    <div class="row">
        <div class="small-6 medium-6 large-4 columns">
            <select id="txtHoraInicio" name="txtHoraInicio" class="campo hora horaselect" placeholder="HORA INICIO">
                <option value="00:00">Hora Inicio</option>
                <?php
                    while ($filaHorario = $datosHorarios->fetch_assoc()){
                        $valor = $filaHorario['Etiqueta24'];
                        $texto = $filaHorario['Etiqueta'];
                        echo "<option value='$valor'>$texto</option>";
                    }
                ?>
            </select>
        </div>
        <div class="small-6 medium-6 large-4 columns">
            <select id="txtHoraTermino" name="txtHoraTermino" class="campo hora horaselect" placeholder="HORA TÉRMINO">
                <option value="00:00">Hora Término</option>
                <?php
                $datosHorarios->data_seek(0);
                while ($filaHorario = $datosHorarios->fetch_assoc()){
                    $valor = $filaHorario['Etiqueta24'];
                    $texto = $filaHorario['Etiqueta'];
                    echo "<option value='$valor'>$texto</option>";
                }
                ?>
            </select>
        </div>
        <div class="small-4 medium-4 large-4 columns">
            <button class="botonmin" id="btnIniciar">RESERVAR</button>
        </div>
    </div>
</div>

</body>
</html>