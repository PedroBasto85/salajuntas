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

    $sqlUsuarios = "SELECT UsuarioID, Nombre FROM usuario WHERE Vigente = 1 and EmpresaID = '$idempresa' and UsuarioID <> '$ClaveUsuario' ";
    $datosUsuarios = $conexion->query($sqlUsuarios);

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

        .botongrid {
            background: none;
            border: none;
            outline: none;
            display: block;
            width: 100%;
        }

        .botoneditar {
            color: #0077ff;
            text-align: right;
        }

        .botonborrar {
            color: #ee0000;;
        }

        .fechas {
            margin-bottom: 0;
        }
        .titulos{
            float: left;
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

<div class="busqueda">
<div class="row">
    <div class="small-6 columns">
        <div class="fechas">
            <div class="row">
                <div class="small-12 columns">
                    <?php echo "<div class='nombreusuario'>" . $_SESSION['NombreUsuario'] . "</div>"; ?>
                    <h2 style="margin: 0;" class="colorgris">Administrador de <?php echo $empresa; ?></h2><br>
                    <h2 style="margin: 0;" class="colorgris">Cat??logo de Usuarios</h2>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<div class="listado">
    <div class="row">
        <div class="small-9 small-offset-3 medium-9 medium-offset-3 columns">
            <table class="tablas" id="tblUsuarios">
                <thead>
                <tr>
                    <td style="text-align:left;width:80%;" class="color4">Nombre</td>
                    <td></td>
                </tr>
                </thead>
                <tbody>
                <?php
                if ($datosUsuarios->num_rows > 0) {
                    while ($fila = $datosUsuarios->fetch_assoc()) {
                        $id = $fila['UsuarioID'];
                        $nombre = $fila['Nombre'];


                        /*echo "<tr>";
                        echo "<td>$nombre</td>";
                        echo "<td style='text-align: center'><button class='botongrid botoneditar' onclick='return editarUsuario($id)'><i class='fa fa-pencil fa-2x'></button></td>";
                        echo "<td style='text-align: center'><a href='usuariospost.php?campoOpcion=BORRAR&id=$id'><button class='botongrid botonborrar'><i class='fa fa-trash-o fa-2x'></button></a></td>";
                        echo "</tr>";*/

                        echo "<tr>";
                        echo "<td colspan='3'>";
                        echo "<div class='filaconfig'>";
                        echo "<div class='row'>";
                        echo "<div class='small-8 columns agendadoletra'>";
                        echo "<span>$nombre</span>";
                        echo "</div>";
                        echo "<div class='small-3 columns'>";
                        echo "<button class='botongrid botoneditar' onclick='return editarUsuario($id)'>Editar</button>";
                        echo "</div>";
                        echo "<div class='small-1 columns' style='padding-left: 0;'>";
                        echo "<a href='usuariospost.php?campoOpcion=BORRAR&id=$id'><button class='botongrid botonborrar'><img src='img/cerrar24.png' width='14px'></button></a>";
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";
                        echo "</td>";
                        echo "</tr>";

                        /*echo "<tr class='mensaje' title='$usuario $nombreestado $fecha'>";
                        echo "<td colspan='5'>";
                        echo "<div class='$clase filaestado'>";
                        echo "<div class='row'>";
                        echo "<div class='small-6 columns $claseletra'>";
                        echo "$titulo";
                        echo "</div>";
                        echo "<div class='small-2 columns center letragrid letraDe'>";
                        echo "$horaInicio";
                        echo "</div>";
                        echo "<div class='small-2 columns center letragrid color3'>";
                        echo "$horaTermino";
                        echo "</div>";
                        echo "<div class='small-1 columns center letragrid'>";
                        echo "$personas";
                        echo "</div>";
                        echo "<div class='small-1 columns center'>";
                        echo "<a class='borrar' onclick='return borrarCita($reservacionid)'><img src='img/cerrar24.png'></a>";
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";
                        echo "</td>";
                        echo "</tr>";*/
                    }
                } else {
                    echo "<tr style='text-align: center'><td colspan='3'>Sin Usuarios</td></tr>";
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row" style="margin-top: 25px">
        <div class="small-1 columns right">
            <div class="avatar" style="text-align: right">
                <a href="#" id="linkNuevo"><img src="img/suma32gris.png"></a>
            </div>
        </div>
    </div>
</div>
<div class="datosusuario">
    <div class="row">
        <div class="small-9 small-offset-3 medium-9 medium-offset-3 columns">
            <form action="usuariospost.php" method="post" name="form1" id="form1">
                <input type="hidden" name="campoOpcion" id="campoOpcion" value="">
                <input type="hidden" name="campoID" id="campoID" value="">
                <input type="hidden" name="campoEmpresa" id="campoEmpresa" value="<?php echo $idempresa; ?>">

                <div class="row">
                    <div class="small-12 medium-12 large-12 columns right">
                        <input type="text" id="txtNombre" name="txtNombre" class="campo" placeholder="Nombre"
                               required>
                    </div>
                    <div class="small-6 medium-6 large-6 columns">
                        <input type="text" id="txtUsuario" name="txtUsuario" class="campo" placeholder="Usuario"
                               required>
                    </div>
                    <div class="small-6 medium-6 large-6 columns">
                        <input type="password" id="txtPass" name="txtPass" class="campo" placeholder="Contrase??a"
                               required>
                    </div>
                </div>
                <div class="row">
                    <div class="small-12 medium-12 large-12 columns">
                        <button class="botonmin" id="btnAgregar">AGREGAR</button>
                    </div>
                </div>
            </form>
            <div class="row">
                <div class="small-1 columns right">
                    <div class="avatar">
                        <a href="#" id="linkCerrarNuevo"><img src="img/resta24.png"></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
</body>
</html>
