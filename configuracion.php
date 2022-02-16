<?php
require_once('seguridad.php');
include('conexion.php');

if ($_SESSION['RolID'] != 1) {
    header('location: agenda.php');
} else {
    $conexion = new mysqli(db_Servidor, db_Usuario, db_Pass, db_BaseDatos);
    $conexion->set_charset('utf8');
    $ClaveUsuario = $_SESSION['ClaveUsuario'];

    $sqlEmpresa = "SELECT usuario.EmpresaID, sysempresa.Nombre AS Empresa FROM usuario INNER JOIN sysempresa ON usuario.EmpresaID = sysempresa.EmpresaID WHERE usuario.UsuarioID = '$ClaveUsuario'";
    $datosEmpresa = $conexion->query($sqlEmpresa);
    $filaEmpresa = $datosEmpresa->fetch_assoc();
    $idempresa = $filaEmpresa['EmpresaID'];
    $empresa = $filaEmpresa['Empresa'];
}
?>

<!doctype html>
<html lang="es" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="UTF-8">
    <meta viewport="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Agenda</title>
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/foundation.min.css">
    <link rel="stylesheet" href="css/fontawesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/sweetalert2.css">
    <link rel="stylesheet" href="css/tooltipster.css">
    <link rel="stylesheet" href="css/tooltipster-shadow.css">
    <link rel="stylesheet" href="css/jquery.custom-scrollbar.css">
    <link rel="stylesheet" href="css/app.css">
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript" src="js/sweetalert2.min.js"></script>
    <script type="text/javascript" src="js/jquery.tooltipster.min.js"></script>
    <script type="text/javascript" src="js/jquery.custom-scrollbar.js"></script>
    <script type="text/javascript" src="js/app2.js"></script>
    <link rel="short icon" href="favicon.png">

    <style>
        body {
            background-color: #0B0B0B;
            color: #fff;
        }


        .fechas {
            margin-bottom: 0;
        }

        .tablas tbody tr {
            border-bottom: 1px solid #0077ff;
        }

    </style>
</head>
<body>
<div class="overlay">
</div>
<div class="modal">
    <div class="tabla" style="height:100%;">
        <div class="fila" style="height:100%;">
            <div class="celda" style="height:100%;">
                Cargando...
            </div>
        </div>
    </div>
</div>
<div class="icono">
    <div class="row">
        <!--        <div class="small-12 columns">-->
        <div class="tabla tabla1">
            <div class="celda">
                <img src="img/meergegris.png" class="imagencelda2">
            </div>
        </div>
        <!--        </div>-->
    </div>
</div>
<div class="iconosalir">
    <div class="row">
        <div class="small-1 columns right">
            <div class="avatar" style="height: 100px; line-height: 100px">
                <a href="logout.php" class="asalir" title="SALIR"><img src="img/resta24.png" style="margin-top: -2px;"></a>
            </div>
        </div>
    </div>
</div>
<div class="header">
    <div class="row">
        <div class="row" style="width: 330px; margin: auto">
            <div class="iconomenu">
                <div class="avatar" style="margin-top: 22px">
                    <a href="usuarios.php" id="linkUsuarios" class="ocultar"><img src="img/usuarios32Gris.png"></a>
                </div>
            </div>
            <div class="iconomenu">
                <div class="avatar" style="margin-top: 22px">
                    <a href="empresas.php" id="linkEmpresas" class="ocultar"><img src="img/empresas32gris.png"></a>
                </div>
            </div>
            <div class="iconomenu">
                <div class="avatar" style="margin-top: 22px">
                    <a href="agenda.php" id="linkAgenda" class="ocultar"><img src="img/calendario32Gris.png"></a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="small-6 columns">
        <div class="fechas">
            <div class="row">
                <div class="small-12 columns">
                    <?php echo "<div class='nombreusuario'>" . $_SESSION['NombreUsuario'] . "</div>"; ?>
                    <h2 style="margin: 0;" class="colorgris">Administrador de <?php echo $empresa; ?> </h2>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="listado">
    <div class="row">
        <div class="small-12 columns">
            <h1 style="margin-bottom: 5px; color: #4C2EF3">CONFIGURACIÓN</h1>
            <h2 class="colorgris" style="margin-top: 0">Seleccione una de las opciones de arriba</h2>
        </div>

    </div>
</div>
</body>
</html>
