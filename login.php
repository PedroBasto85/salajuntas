<?php
include("conexion.php");

$Error = 0;
$Usuario = "";
$Contrasenia = "";

if (isset($_POST['txtUsuario'])) {
    $Usuario = $_POST['txtUsuario'];
} else {
    $Usuario = "";
}

if (isset($_POST['txtPass'])) {
    $Contrasenia = $_POST['txtPass'];
} else {
    $Contrasenia = "";
}

if (($Usuario != "") && ($Contrasenia != "")) {
    $conexion = new mysqli(db_Servidor, db_Usuario, db_Pass, db_BaseDatos);
    $conexion->set_charset("utf8");
    $consulta = $conexion->query("SELECT UsuarioID, Nombre, RolID, Avatar, EmpresaID FROM usuario WHERE Usuario = '$Usuario' AND Contrasenia = '$Contrasenia' and Vigente=1");
    if ($consulta->num_rows > 0) {
        $fila = $consulta->fetch_assoc();
        session_name("loginUsuario");
        session_start();
        $_SESSION["Autentificado"] = "SI";
        $_SESSION["ClaveUsuario"] = $fila['UsuarioID'];
        $_SESSION["NombreUsuario"] = $fila['Nombre'];
        $_SESSION["RolID"] = $fila['RolID'];
        $_SESSION["EmpresaUsuario"] = $fila['EmpresaID'];
        $_SESSION["Avatar"] = $fila['Avatar'];
        header("Location: agenda.php");
    } else {
        $Error = 2;
    }

} else {
    $Error = 1;
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta viewport="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Agenda</title>
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/foundation.min.css">
    <link rel="stylesheet" href="css/app.css">
    <link rel="short icon" href="favicon.png">
    <style>
        body {
            background-color: #161E83;
        }

        /**************************/
        ::-webkit-input-placeholder {
            color: #0077ff;
        }
        :-moz-placeholder {
            /* Firefox 18- */

            color: #0077ff;
        }
        ::-moz-placeholder {
            /* Firefox 19+ */

            color: #0077ff;
        }
        :-ms-input-placeholder {
            color: #0077ff;
        }
        /**************************/

        .linea{
            position: relative;
            border-bottom: 1px solid #0077ff;
            height: 1px;
            margin: 10px auto 0px auto;
        }

        .titulo2{
            color: #ffffff!important;
            opacity: 1.0;
        }


    </style>
</head>
<body>

<div class="login">
    <div class="row">
        <div class="logo">
            <img src="img/meerge.png">
        </div>
    </div>
    <div class="row">
        <div class="small-12 columns">
            <span class="degradado titulo2">Ingresa usuario y contraseña</span>

            <div class="linea"></div>
        </div>

    </div>
    <input type="hidden" value="<?php echo $Error; ?>" id="campoerror">
    <?php
    if ($Error == 2) {
        echo "<div class='row'><div class='small-12 columns text-center'>Datos Incorrectos</div></div>";
    }
    ?>
    <form action="login.php" method="post">
        <div class="row">
            <div class="small-6 medium-4 large-4 columns">
                <input type="text" id="txtUsuario" name="txtUsuario" class="campo campologin" placeholder="USUARIO">
            </div>
            <div class="small-6 medium-4 large-4 columns">
                <input type="password" id="txtPass" name="txtPass" class="campo campologin" placeholder="CONTRASEÑA">
            </div>
            <div class="small-12 medium-4 large-4 columns">
                <button class="botonmin" id="btnIniciar">INICIAR SESIÓN</button>
            </div>
        </div>
    </form>
</div>
<div class="ubicacion">
    <div class="row">
        <div class="small-12 columns center">
            <img src="img/lugar32.png" style="opacity: 0.4">
        </div>
    </div>
    <div class="row" style="margin-top: 10px">
        <div class="small-12 columns center">
            <span class="degradado">Florencia #385&nbsp;&nbsp;&nbsp;&nbsp;Chetumal, Quintana Roo. México</span>
        </div>
    </div>

</div>
<div class="firmas">
    <div class="row">
        <div class="small-6 columns center">
            <div class="row" style="margin-bottom: 25px">
                <div class="small-12 columns center">
                    <img src="img/ynbadi.png" class="imagenpie">
                </div>
            </div>
            <div class="row">
                <div class="small-12 columns center">
                    <div class="degradado">email&nbsp;&nbsp;&nbsp;contacto@ynbadi.com</div>
                    <div class="degradado">web&nbsp;&nbsp;&nbsp;www.ynbadi.com</div>
                </div>
            </div>
        </div>
        <div class="small-6 columns center">
            <div class="row" style="margin-bottom: 20px">
                <div class="small-12 columns center">
                    <img class="imagenpie" src="img/idihub.png" style="margin-top: -8px;">
                </div>
            </div>
            <div class="row">
                <div class="small-12 columns center">
                    <div class="degradado">email&nbsp;&nbsp;&nbsp;innovacion@idihub.com</div>
                    <div class="degradado">web&nbsp;&nbsp;&nbsp;www.idihub.com</div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>