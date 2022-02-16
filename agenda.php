<?php
require_once('seguridad.php');
include('conexion.php');
date_default_timezone_set('America/Bogota');
$conexion = new mysqli(db_Servidor, db_Usuario, db_Pass, db_BaseDatos);
$conexion->set_charset('utf8');

$diaactual = date('Y-m-d');
$horaactual = date('H:i:s');
$fechaMostrar = date('d/m/Y');
$ClaveUsuario = $_SESSION['ClaveUsuario'];
$RolID = $_SESSION['RolID'];

$sqlDato = "Select EmpresaID From usuario Where UsuarioID = '$ClaveUsuario'";
$dato = $conexion->query($sqlDato);
$fila = $dato->fetch_assoc();
$empresaID = $fila['EmpresaID'];

$sqlupdateestado = "Update reservacion Set EstadoID = 3 Where Fecha = '$diaactual' and '$horaactual' BETWEEN HoraInicio and HoraTermino and EstadoID <> 2";
$conexion->query($sqlupdateestado);

$sqlupdateestado = "Update reservacion Set EstadoID = 4 Where Fecha < '$diaactual' and EstadoID <> 2";
$conexion->query($sqlupdateestado);

$sqlupdateestado = "Update reservacion Set EstadoID = 4 Where Fecha = '$diaactual' and HoraTermino < '$horaactual' and EstadoID <> 2";
$conexion->query($sqlupdateestado);

$sqlagendahoy = "SELECT ReservacionID, CASE WHEN CHAR_LENGTH(Titulo) > 30 THEN CONCAT(SUBSTR(Titulo,1,30),'...') ELSE Titulo END AS Titulo, Personas, Fecha,TIME_FORMAT(HoraInicio,'%h:%i%p') as HoraInicio, TIME_FORMAT(HoraTermino,'%h:%i%p') as HoraTermino, EstadoID, usuario.Nombre, DATE_FORMAT(FechaUM,'%d/%m/%Y %h:%i%p') AS UM, ".
    "CASE EstadoID WHEN 2 THEN 'CANCELADO' ELSE 'CREADO' END AS NombreEstado ".
    "FROM reservacion " .
    "INNER JOIN usuario on reservacion.UsuarioID = usuario.UsuarioID " .
    "WHERE Fecha = '$diaactual'";
$datosagendahoy = $conexion->query($sqlagendahoy);


$sqlHorarios = "SELECT Etiqueta, Etiqueta24 FROM syshorario";
$datosHorarios = $conexion->query($sqlHorarios);

$sqlContactos = "SELECT ContactoID, Concat_ws(' - ', Nombre, Correo) as Contacto From usuariocontacto Where UsuarioID = '$ClaveUsuario'";
$datosContactos = $conexion->query($sqlContactos);

$sqlGrupos = "SELECT GrupoID, Nombre FROM grupo WHERE EmpresaID = 0 UNION SELECT GrupoID, Nombre FROM grupo WHERE EmpresaID = '$empresaID' AND Activo = 1";
$datosGrupos = $conexion->query($sqlGrupos);

$sqlCuantos = "SELECT count(ReservacionID) as Cuantos From reservacion Where UsuarioID = '$ClaveUsuario' and EstadoID = 1";
$datosCuantos = $conexion->query($sqlCuantos);
$filaCuantos = $datosCuantos->fetch_assoc();
$citasProgramadas = $filaCuantos['Cuantos'];

$sqlEmpresas = "SELECT RazonSocialID, Nombre FROM razonsocial WHERE Activo = 1 AND EmpresaID = '$empresaID'";
$datoEmpresas = $conexion->query($sqlEmpresas);

?>
<!doctype html>
<html lang="es" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="UTF-8">
    <meta viewport="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Agenda</title>
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/foundation.min.css">
    <link rel="stylesheet" href="css/jquery-ui.min.css">
    <link rel="stylesheet" href="css/fontawesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/sweetalert2.css">
    <link rel="stylesheet" href="css/tooltipster.css">
    <link rel="stylesheet" href="css/tooltipster-shadow.css">
<!--    <link rel="stylesheet" href="css/jquery.custom-scrollbar.css">-->
<!--    <link rel="stylesheet" href="css/jquery.mCustomScrollbar.min.css" />-->
    <link rel="stylesheet" href="css/app.css">
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript" src="js/jquery-ui.min.js"></script>
    <script type="text/javascript" src="js/sweetalert2.min.js"></script>
    <script type="text/javascript" src="js/jquery.tooltipster.min.js"></script>
<!--    <script type="text/javascript" src="js/jquery.mCustomScrollbar.concat.min.js"></script>-->
<!--    <script type="text/javascript" src="js/jquery.custom-scrollbar.js"></script>-->
    <script type="text/javascript" src="js/app.js"></script>
    <link rel="short icon" href="favicon.png">

    <style>
        body {
            background-color: #0B0B0B;
            color: #fff;
        }

        #btnReservar {
            padding: 0px;
        }

        .borrar {
            background: none;
            border: none;
            outline: none;
            color: #ee0000;
            display: none;
        }

        .borrarGrupo {
            background: none;
            border: none;
            outline: none;
            color: #ee0000;
            display: none;
        }

        .borrar img{
            margin-top: -3px;
            margin-left: -10px;
        }

        .ui-datepicker{
            z-index: 1000000000 !important;
        }

        .config{
            width: 24px;
            height: auto;
            margin-left: 5px;
            margin-top: -10px;
        }

        #flechas{
            height: 24px;
        }

        input[type=checkbox] {
            visibility: hidden;
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

<div class="busqueda">
    <!--<div class="row">
        <div class="small-12 columns">-->
            <div class="fechas">
                <div class="row">
                    <div class="small-12 columns">
                        <?php echo "<div class='nombreusuario'>" . $_SESSION['NombreUsuario'] . "</div>"; ?>
                        <h2 style="margin: 0; display: inline-block;" class="colorgris">SALA DE JUNTAS</h2>
                    </div>
                </div>
                <div class="row">
                    <div class="small-12 columns">
                        <div class="numero">
                            <h1 class="colorgris" id="h1numero"><?php echo $citasProgramadas; ?></h1>
                                <span class="degradado colorgris"
                                      style="font-size: 1.0rem">Reuniones<br>Programadas</span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="small-12 columns">
                        <a href="#" id="fechaSelect"><span id="fechaActual"><?php echo $fechaMostrar; ?></span>
                            <input type="hidden" id="dp">
                            <img src="img/search24.png" style="width: 20px; margin-left: 10px;margin-top:-5px">
                        </a>
                        <div></div>
                    </div>
                </div>
                <div class="row" style="margin-top: 100px;">
                    <div class="small-12 columns">
                        <?php
                        if ($RolID == 1){
                            echo "<a href='configuracion.php' class='nombreusuario'>Configuración</a>";
                        }
                        ?>
                    </div>
                </div>
            </div>
       <!-- </div>
    </div>-->
</div>

<div class="header">
    <div class="row">
        <div class="small-8 small-offset-3 medium-8 medium-offset-3 columns">
            <div class="avatar" style="left: -12px;margin-top: 22px">
                <a href="#" id="linkReservar" class="ocultar"><img src="img/suma32gris.png"></a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="small-8 small-offset-3 medium-8 medium-offset-3 columns">
                <table class="tablas" style="margin-top: -5px">
                    <thead>
                    <tr>
                        <td style="text-align: left; width:48%; padding-left:0.9375em;" class="color4 ocultar">Titulo</td>
                        <td style="text-align: left; width:17%; padding-left: 1.3em;" class="color4 ocultar">De</td>
                        <td style="text-align: left; width:17%; padding-left: 1.0em;" class="color4 ocultar">A</td>
                        <td style="text-align: left; width:9%;  padding-left: 0.9em; " class="color4 ocultar">Para</td>
                        <td  style="width:9%;"></td>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
        </div>
    </div>
</div>
<!--FIN Header-->
<div class="contenedor">
    <div class="row">
        <div class="small-8 small-offset-3 medium-8 medium-offset-3 columns">
            <div id="flechas">
            <a href="#" class="down" id="down"><img src="img/abajo.png" width="24"></a>
                <a href="#" class="up" id="up" style="float: right"><img src="img/arriba.png" width="24"></a>
            </div>
            <div class="agendadiaria">
                <!--<div class="row borde">
                    <div class="small-12 medium-12 columns">-->
                        <table class="tablas" id="tblAgendaDiaria">
                           <thead style="height: 10px">
                            <tr style="height: 30px">
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            if ($datosagendahoy->num_rows > 0) {
                                while ($fila = $datosagendahoy->fetch_assoc()) {
                                    $reservacionid = $fila['ReservacionID'];
                                    $titulo = $fila['Titulo'];
                                    $horaInicio = $fila['HoraInicio'];
                                    $horaTermino = $fila['HoraTermino'];
                                    $personas = $fila['Personas'];
                                    $estadoid = $fila['EstadoID'];
                                    $usuario = $fila['Nombre'];
                                    $nombreestado = $fila['NombreEstado'];
                                    $fecha = $fila['UM'];

                                    if ($estadoid == 1) {
                                        $clase = 'agendado';
                                        $claseletra = 'agendadoletra';
                                    } else if ($estadoid == 2) {
                                        $clase = 'cancelado';
                                        $claseletra = 'canceladoletra';
                                    } else if ($estadoid == 3) {
                                        $clase = 'encurso';
                                        $claseletra = 'encursoletra';
                                    } else {
                                        $clase = 'finalizado';
                                        $claseletra = 'finalizadoletra';
                                    }
                                    echo "<tr class='mensaje' title='$usuario $nombreestado $fecha'>";
                                    echo "<td colspan='5'>";
                                    echo "<div class='$clase filaestado'>";
                                    echo "<div class='row'>";
                                    echo "<div class='small-6 columns letraTitulo $claseletra'>";
                                    echo "<a href='listas.php?id=$reservacionid' target='_blank' class='$claseletra listasa'>$titulo</a>";
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
                                    echo "<a class='borrar' onclick='return borrarPrevio($reservacionid)'><img src='img/cerrar24.png'></a>";
                                    echo "</div>";
                                    echo "</div>";
                                    echo "</div>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr style='text-align: center'><td colspan='6'>No hay reservaciones programadas</td></tr>";
                            }
                            ?>
                            </tbody>
                        </table>
                    <!--</div>
                </div>-->
            </div>
        </div>
    </div>
</div>

<div class="x">
    <input type="hidden" name="hiddenRol" id="hiddenRol" value="<?php echo $_SESSION['RolID'];?>">
    <input type="hidden" name="hiddenEmpresa" id="hiddenEmpresa" value="<?php echo $empresaID;?>">
    <input type="hidden" name="hiddenGrupo" id="hiddenGrupo" value="0">
    <div class="agenda" style="top: 100px;">
        <div class="row">
            <div class="small-10 columns">
                <span class="titulo">Agendar reservación en sala de juntas</span>
            </div>
            <div class="small-2 columns" style="text-align: right">
                <a id="asalir" class="cancelar">Cancelar</a>
            </div>
        </div>
        <div class="row">
            <div class="small-12 columns">
                <div class="linea"></div>
            </div>
        </div>
        <form action="agendapost.php" method="post" id="formAgendar" name="formAgendar">
            <input type="hidden" value="<?php echo $_SESSION['ClaveUsuario']; ?>" name="campoUsuarioID"
                   id="campoUsuarioID">
            <input type="hidden" value="" name="campoReservacionID" id="campoReservacionID">

            <div class="row">
                <div class="small-12 medium-12 large-4 columns">
                    <input type="text" id="txtTitulo" name="txtTitulo" class="campo" placeholder="TITULO"
                           required>
                </div>
                <div class="small-6 medium-6 large-4 columns left">
                    <input type="text" id="txtPersonas" name="txtPersonas" class="campo persona"
                           placeholder="PERSONAS"
                           required>
                </div>
                <div class="small-6 medium-6 large-4 columns">
                    <input type="text" id="txtFecha" name="txtFecha" class="campo fecha" placeholder="FECHA"
                           required>
                </div>
                <div class="small-6 medium-6 large-4 columns">
                    <select id="txtHoraInicio" name="txtHoraInicio" class="campo hora horaselect"
                            placeholder="HORA INICIO">
                        <option value="00:00">Hora Inicio</option>
                        <?php
                        while ($filaHorario = $datosHorarios->fetch_assoc()) {
                            $valor = $filaHorario['Etiqueta24'];
                            $texto = $filaHorario['Etiqueta'];
                            echo "<option value='$valor'>$texto</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="small-6 medium-6 large-4 columns">
                    <select id="txtHoraTermino" name="txtHoraTermino" class="campo hora horaselect"
                            placeholder="HORA FINAL">
                        <option value="00:00">Hora Final</option>
                        <?php
                        $datosHorarios->data_seek(0);
                        while ($filaHorario = $datosHorarios->fetch_assoc()) {
                            $valor = $filaHorario['Etiqueta24'];
                            $texto = $filaHorario['Etiqueta'];
                            echo "<option value='$valor'>$texto</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="small-12 medium-12 large-4 columns">
                    <input type="text" id="txtComentarios" name="txtComentarios" class="campo"
                           placeholder="COMENTARIO">
                </div>
                <div class="small-12 medium-12 large-12 columns">
                    <select id="txtEmpresa" name="txtEmpresa" class="campo horaselect"
                            placeholder="INVITA">
                        <option value="0">INVITA</option>
                        <?php
                        while($filaEmpresas = $datoEmpresas->fetch_assoc()){
                            $id = $filaEmpresas['RazonSocialID'];
                            $nombre = $filaEmpresas['Nombre'];
                            echo "<option value='$id'>$nombre</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="small-12 medium-12 large-12 columns">
                    <button class="botonmin" id="btnGuardar" onclick="return agendarCita();">AGENDAR</button>
                </div>
            </div>
            <div class="row">
                <div class="small-12 large-12 columns">
                    <div class="error">FALTAN DATOS</div>
                </div>
            </div>
            <input type="hidden" name="campoTemp" id="campoTemp" value="0">
        </form>
        <div class="asistentes">
            <div class="lista">
                <div class="row">
                    <div class="small-12 columns">
                        <div class="row">
                            <div class="small-12 columns">
                                <div class="titAsistentes">
                                    Asistentes
                                </div>
                            </div>
                        </div>
                        <div class="row collapse rowListas">
                            <div class="small-4 columns">
                                <div class="row">
                                    <div class="small-12 columns">
                                       <div class="titEncabezado">
                                           <span>Grupos</span>
                                           <a href="#" id="linkMasGrupos"><img src="img/suma32gris.png" width="16px" style="float: right;"></a>
                                       </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="small-12 columns">
                                       <ul class="listas" id="listaGrupo">
                                           <?php
                                             while ($filaGrupos = $datosGrupos->fetch_assoc()){
                                                 $id = $filaGrupos['GrupoID'];
                                                 $nombre = $filaGrupos['Nombre'];
                                                 if ($id == 1){
                                                     echo "<li value='$id'><label id='$id'>$nombre</label></li>";
                                                 }else{
                                                     echo "<li value='$id'><label id='$id'>$nombre</label><a class='borrarGrupo' style='float: right' onclick='return borrarGrupo($id)'><img src='img/cerrar24.png' width='16px'></a></li>";
                                                 }
                                             }
                                           ?>
                                       </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="small-8 columns">
                                <div class="row">
                                    <div class="small-12 columns">
                                        <div class="titEncabezado">
                                            <span>Contactos</span>
                                            <a href="#" id="linkAgregar"><img src="img/suma32gris.png" width="16px" style="float: right;"></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="small-12 columns">
                                        <ul class="listas" id="listaContacto">
                                            <li>
                                                <ul class="listaContactos" id="listaContactoDet">
                                                    <li style="width: 40%; text-align: left"><label>-</label></li>
                                                    <li style="width: 40%; text-align: center"><label>-</label></li>
                                                    <li><div class="roundedOne">
                                                            <input type="checkbox" value="None" id="roundedOne" name="checkContacto" />
                                                            <label for="roundedOne"></label>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="small-12 medium-12 large-12 columns">
                        <!--<button class="botonmin" id="btnInvitar" onclick="return invitar();">INVITAR</button>-->
                    </div>
                </div>
            </div>
            <div class="nuevo">
                <div class="row">
                    <div class="small-6 medium-6 large-6 columns">
                        <input type="text" id="txtNombre" name="txtNombre" class="campo" placeholder="Nombre"
                               required>
                    </div>
                    <div class="small-6 medium-6 large-6 columns">
                        <input type="text" id="txtCorreo" name="txtCorreo" class="campo" placeholder="Correo"
                               required>
                    </div>
                    <div class="small-12 medium-12 large-12 columns">
                        <select id="comboGrupo" name="comboGrupo" class="campo horaselect">
                            <option value="-1">Seleccione Grupo</option>
                            <?php
                            $datosGrupos->data_seek(0);
                            while ($filaGrupos = $datosGrupos->fetch_assoc()){
                                $id = $filaGrupos['GrupoID'];
                                $nombre = $filaGrupos['Nombre'];
                                echo "<option value='$id'>$nombre</option>";                            }
                            ?>
                        </select>
                    </div>
                    <div class="small-12 medium-12 large-12 columns">
                        <input type="text" id="txtEmpresaContacto" name="txtEmpresaContacto" class="campo" placeholder="Institución/Empresa"
                               required>
                    </div>
                    <div class="small-12 medium-12 large-12 columns">
                        <button class="botonmin" id="btnInvitar" onclick="return agregarContacto();">AGREGAR</button>
                    </div>
                </div>
                <div class="row">
                    <div class="small-1 columns right">
                        <div class="avatar">
                            <a href="#" id="linkCerrarAgregar"><img src="img/resta24.png"></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="nuevogrupo">
                <div class="row">
                    <div class="small-12 medium-12 large-12 columns">
                        <input type="text" id="txtNombreGrupo" name="txtNombreGrupo" class="campo" placeholder="Nombre Grupo"
                               required>
                    </div>
                    <div class="small-12 medium-12 large-12 columns">
                        <button class="botonmin" id="btnAgregarGrupo" onclick="return agregarGrupo();">AGREGAR</button>
                    </div>
                </div>
                <div class="row">
                    <div class="small-1 columns right">
                        <div class="avatar">
                            <a href="#" id="linkCerrarGrupo"><img src="img/resta24.png"></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="cancelargrid" id="divCancelar">
    <div class="row" style="margin-bottom: 10px">
        <div class="small-12 columns">
            <a href="#" onclick="return cerrarCancelar();"><img src="img/cerrar24.png"></a>
        </div>
    </div>
    <div class="row" style="margin-bottom: 10px">
        <div class="small-12 columns">
            <textarea class="campo" placeholder="Comentario" id="txtCancelacion" name="txtCancelacion" rows="2"></textarea>
        </div>
    </div>
    <div class="row">
        <div class="small-12 columns">
            <button class="botonmin" style="margin: 0;" id="btnCancelarReunion" onclick="return borrarCita();">CANCELAR REUNIÓN</button>
        </div>
    </div>
</div>

<!--<script>

    (function($){
        $(window).load(function(){
           $(".rowListas").mCustomScrollbar();

        });
    })(jQuery);
</script>-->

<script type="text/javascript">
    var step = 25;
    var scrolling = false;

    // Wire up events for the 'scrollUp' link:
    $("#up").bind("click", function(event) {
        event.preventDefault();
        // Animates the scrollTop property by the specified
        // step.
        $(".agendadiaria").animate({
            scrollTop: "-=" + step + "px"
        });
    });/*.bind("mouseover", function(event) {
        scrolling = true;
        scrollContent("up");
    }).bind("mouseout", function(event) {
        // Cancel scrolling continuously:
        scrolling = false;
    });*/


    $("#down").bind("click", function(event) {
        event.preventDefault();
        $(".agendadiaria").animate({
            scrollTop: "+=" + step + "px"
        });
    });/*.bind("mouseover", function(event) {
        scrolling = true;
        scrollContent("down");
    }).bind("mouseout", function(event) {
        scrolling = false;
    });*/

    function scrollContent(direction) {
        var amount = (direction === "up" ? "-=1px" : "+=1px");
        $(".agendadiaria").animate({
            scrollTop: amount
        }, 1, function() {
            if (scrolling) {
                // If we want to keep scrolling, call the scrollContent function again:
                scrollContent(direction);
            }
        });
    }
</script>
<!--<script>
    /*$(document).ready(function(){
     $('#fechas').datepicker({
     dateFormat: 'dd-mm-yyyy',
     inline: true,
     minDate: new Date(2010, 1 - 1, 1),
     maxDate:new Date(2020, 12 - 1, 31),
     onSelect: function(){
     var day1 = $("#fechas").datepicker('getDate').getDate();
     var month1 = $("#fechas").datepicker('getDate').getMonth() + 1;
     var year1 = $("#fechas").datepicker('getDate').getFullYear();
     var fullDate = year1 + "-" + month1 + "-" + day1;
     alert(fullDate);
     }
     });
     });
     */
</script>-->
</body>
</html>
