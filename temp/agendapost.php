<?php
include('seguridad.php');
include('conexion.php');
include('clases/phpmailer/PHPMailerAutoload.php');
include('smtp.php');

date_default_timezone_set('America/Bogota');
$conexion = new mysqli(db_Servidor, db_Usuario, db_Pass, db_BaseDatos);
$conexion->set_charset('utf8');

$operacion = $_POST['operacion'];
$diaactual = date('Y-m-d');
$horaactual = date('H:i:s');
$ClaveUsuario = $_SESSION['ClaveUsuario'];

$sqlupdateestado = "Update reservacion Set EstadoID = 3 Where EstadoID <> 2 AND Fecha = '$diaactual' and '$horaactual' BETWEEN HoraInicio and HoraTermino and EstadoID <> 2";
$conexion->query($sqlupdateestado);

$sqlupdateestado = "Update reservacion Set EstadoID = 4 Where EstadoID <> 2 AND Fecha < '$diaactual' and EstadoID <> 2";
$conexion->query($sqlupdateestado);

$sqlupdateestado = "Update reservacion Set EstadoID = 4 Where EstadoID <> 2 AND Fecha = '$diaactual' and HoraTermino < '$horaactual' and EstadoID <> 2";
$conexion->query($sqlupdateestado);

if ($operacion == 'ACTUALIZAR') {
    $datos = array();
    $fechafiltro = $_POST['fecha'];

    $sqlreservas = "SELECT ReservacionID, CASE WHEN CHAR_LENGTH(Titulo) > 30 THEN CONCAT(SUBSTR(Titulo,1,30),'...') ELSE Titulo END AS Titulo, Personas, Fecha, TIME_FORMAT(HoraInicio,'%h:%i%p') as HoraInicio, TIME_FORMAT(HoraTermino,'%h:%i%p') as HoraTermino, reservacion.UsuarioID, FechaUM, EstadoID, usuario.Nombre, " .
        "DATE_FORMAT(FechaUM,'%d/%m/%Y %h:%i%p') AS UM, CASE EstadoID WHEN 2 THEN 'CANCELADO' ELSE 'CREADO' END AS NombreEstado " .
        "FROM reservacion " .
        "INNER JOIN usuario on reservacion.UsuarioID = usuario.UsuarioID " .
        "WHERE Fecha = '$fechafiltro'";

    $datosreservas = $conexion->query($sqlreservas);
    if ($datosreservas->num_rows == 0) {
        $datos[0]['ReservacionID'] = 0;
    } else {
        while ($filareservas = $datosreservas->fetch_assoc()) {
            $datos[] = $filareservas;
        }
    }
    echo json_encode($datos);
}

if ($operacion == 'AGENDAR') {
    $datos = array();
    $usuarioid = $_POST['usuarioid'];
    $titulo = $_POST['titulo'];
    $personas = $_POST['personas'];
    $idtemp = $_POST['idtemp'];
    $comentario = $_POST['comentario'];
    $empresaid = $_POST['empresaid'];
    $empresanombre = $_POST['empresanombre'];

    $fechamostrar = $_POST['fecha'];
    $hora1 = $_POST['horainicio'];
    $hora2 = $_POST['horatermino'];

    $diahoy = date('d/m/Y');
    $horahoy = date('H:i');

    $fechas = explode('/', $_POST['fecha']);
    $fecha = new datetime($fechas[2] . '-' . $fechas[1] . '-' . $fechas[0]);
    $fechains = $fecha->format('Y-m-d');
    $horainicio = $_POST['horainicio'] . ':00';
    $horatermino = $_POST['horatermino'] . ':00';



    $sqlbuscar = "SELECT ReservacionID FROM reservacion WHERE Fecha = '$fechains' AND EstadoID <> 2 " .
        "AND ((HoraInicio > '$horainicio' and HoraInicio < '$horatermino') OR (HoraTermino > '$horainicio' and HoraTermino < '$horatermino') OR (HoraInicio = '$horainicio' and HoraTermino = '$horatermino'))";
    $datosbuscar = $conexion->query($sqlbuscar);

    $sqlbuscarSeparacion = "Select ReservacionID FROM reservacion WHERE Fecha = '$fechains' AND EstadoID <> 2 AND (HoraTermino = '$horainicio' OR HoraInicio = '$horatermino')";
    $datosSeparacion = $conexion->query($sqlbuscarSeparacion);

    if ($fechamostrar == $diahoy and $hora1 < $horahoy) {
        $valido = 0;
    } else {
        $valido = 1;
    }

    if ($hora1 <> $hora2){
        $valido = 1;
    }else{
        $valido = 0;
    }

    if ($valido == 1) {
        if (($datosbuscar->num_rows == 0) && ($datosSeparacion->num_rows == 0)) {
            $datos['Error'] = '';
            $sqlinsertar = "CALL pAgendaInsertar('$titulo', '$personas', '$fechains', '$horainicio', '$horatermino', '$usuarioid','$comentario','$empresaid')";
            $conexion->query($sqlinsertar);

            $sqlID = "Select Max(ReservacionID) as Ultimo From reservacion where UsuarioID = '$usuarioid'";
            $datosID = $conexion->query($sqlID);
            $filaID = $datosID->fetch_assoc();
            $UltimoID = $filaID['Ultimo'];

            $sqlInvitados = "Update reservacioninvitados SET ReservacionID = '$UltimoID' Where IDTemp = '$idtemp'";
            $conexion->query($sqlInvitados);

            $sqlInvitados = "Select contactos.Nombre, contactos.Correo From reservacioninvitados invitados INNER JOIN usuariocontacto contactos ON invitados.ContactoID = contactos.ContactoID " .
                "Where contactos.UsuarioID = '$ClaveUsuario' AND invitados.IDTemp = '$idtemp'";
            $datosInvitados = $conexion->query($sqlInvitados);

            $sqlLogo = "Select Logo from razonsocial Where RazonSocialID = '$empresaid'";
            $datosLogo = $conexion->query($sqlLogo);
            $filaLogo = $datosLogo->fetch_assoc();
            $logo = $filaLogo['Logo'];
            $dimensiones = getimagesize($logo);

            if ($_SESSION['EmpresaUsuario']==1){
                $NombreEmpresaUsuario = 'YNBADI';
            }else{
                $NombreEmpresaUsuario = 'IDIHUB';
            }

            /*$MensajeCompleto = "Hola...";*/


            $mail = new PHPMailer();
            $mail->IsSMTP();
            $mail->CharSet = "UTF-8";
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = correo_smtpsecure;
            $mail->Host = correo_host;
            $mail->Port = correo_port;
            $mail->Username = correo_username;
            $mail->Password = correo_password;
            $mail->isHTML(true); /*#5E00FF*/
            //"<img src='" . $logo . "' style='width: 325px; height:150px;max-width:325px;max-height:150px;'>".
            while ($filaInvitados = $datosInvitados->fetch_assoc()) {
                try {
                    $MensajeCompleto = "<br>".
                        "<div style='margin: 10px 0 20px 0;'>".
                        "<img src='" . $logo . "' width='". $dimensiones[0]."' height='". $dimensiones[1]."'>".
                        "</div><br><br>" .
                        "<div style='font-family: Arial;color:#303030;'>" .
                        "<span style='font-size:22px; color:#5E00FF'> Hola! </span><br>" .
                        "<span style='font-size:22px; color:#303030'>".$filaInvitados['Nombre']."</span><br><br>" .
                        "<span style='font-size:22px; color:#5E00FF'>Tu próxima reunión</span><br>" .
                        "<span style='font-size:22px; color: #303030;'>" . $fechamostrar . "</span><br><br>" .
                        "<span style='font-size:22px; margin: 20px 0;color: #5E00FF;'>Titulo</span><br>" .
                        "<span style='font-size:22px; margin: 20px 0;color: #303030;'>" . $titulo . "</span><br><br>" .
                        "<span style='font-size:22px; margin: 0;color: #5E00FF;'>Inicia</span>&nbsp;&nbsp;<span style='font-size:22px;color: #303030'>" . $hora1 . "hrs</span><br>" .
                        "<span style='font-size:22px; margin: 0;color: #5E00FF;'>Termina</span>&nbsp;&nbsp;<span style='font-size:22px;color: #303030'>" . $hora2 . "hrs</span><br><br>" .
                        "<span style='font-size:22px; margin: 20px 0;color: #5E00FF;'>Detalles</span><br>" .
                        "<span style='margin: 0; font-size:22px;'>" . $comentario . "</p>" .
                        "<br><br><br>" .
                        "<div style='margin: 100px 0 20px 0;'>" .
                        "<img src='http://www.meerge.com/rsc/logomail.png' style='width: 250px'>" .
                        "</div><br>" .
                        "<p style='font-size: 12px; color: #BCBCBC;'>" .
                        "Has recibido este correo electrónico por parte de meerge.com. Si no deseas recibir más correos puedes solicitarlo a la empresa responsable" .
                        "</p>" .
                        "</div><br>";
                    $mail->SetFrom(correo_username, 'Meerge Recordatorio');
                    $mail->Subject = "Agenda";
                    $mail->msgHTML($MensajeCompleto);
                    $address = $filaInvitados['Correo'];
                    $mail->AddAddress($address, $address);
                    if (!$mail->Send()) {
                        $datos['correo'] = $mail->ErrorInfo;
                    }
                } catch (Exception $e) {
                }
                $mail->clearAddresses();
            }
            $datos['Error'] = $conexion->error;

        } else {
            $datos['Error'] = 'Fecha y hora ocupada. 30 Minutos de Márgen';
        }
    } else {
        $datos['Error'] = 'Horario No Válido';
    }

    echo json_encode($datos);
}

if ($operacion == 'BORRAR') {
    $reservacionid = $_POST['reservacionid'];
    $comentario = $_POST['comentario'];
    $sqlborrar = "UPDATE reservacion SET EstadoID = 2, Comentario = '$comentario', FechaUM = Now() WHERE ReservacionID = '$reservacionid' ";
    $conexion->query($sqlborrar);

    $sqlTitulo = "SELECT Titulo, RazonSocialID From reservacion Where ReservacionID = '$reservacionid'";
    $datosTitulo = $conexion->query($sqlTitulo);
    $filaTitulo = $datosTitulo->fetch_assoc();
    $titulo = $filaTitulo['Titulo'];
    $razonsocial = $filaTitulo['RazonSocialID'];

    $sqlLogo = "Select Logo from razonsocial Where RazonSocialID = '$razonsocial'";
    $datosLogo = $conexion->query($sqlLogo);
    $filaLogo = $datosLogo->fetch_assoc();
    $logo = $filaLogo['Logo'];


    $sqlInvitados = "Select contactos.Nombre, contactos.Correo From reservacioninvitados invitados INNER JOIN usuariocontacto contactos ON invitados.ContactoID = contactos.ContactoID " .
        "Where contactos.UsuarioID = '$ClaveUsuario' AND invitados.ReservacionID = '$reservacionid'";
    $datosInvitados = $conexion->query($sqlInvitados);

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->CharSet = "UTF-8";
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = correo_smtpsecure;
    $mail->Host = correo_host;
    $mail->Port = correo_port;
    $mail->Username = correo_username;
    $mail->Password = correo_password;
    $mail->isHTML(true);
    while ($filaInvitados = $datosInvitados->fetch_assoc()) {
        try {
            $MensajeCompleto = "<br>".
                "<div style='margin: 10px 0 20px 0;'>".
                "<img src='" . $logo . "' style='width: 325px; height:150px;max-width:325px;max-height:150px;'>".
                "</div><br><br>" .
                "<div style='font-family: Arial;color:#303030;'>" .
                "<span style='font-size:22px; color:#5E00FF'> Hola! </span><br>" .
                "<span style='font-size:22px; color:#303030'>".$filaInvitados['Nombre']."</span><br><br>" .
                "<span style='font-size:22px; color:#5E00FF'>La reunión</span><br>" .
                "<span style='font-size:22px; margin: 20px 0; color: #303030;'>" . $titulo . "</span><br>" .
                "<span style='font-size:22px; margin: 20px 0;color: #ee0000;'>Ha sido&nbsp;<b>CANCELADA</b></span><br><br>" .
                "<span style='font-size:22px; margin: 20px 0;color: #5E00FF;'>Detalles</span><br>" .
                "<span style='margin: 0; font-size:22px;'>" . $comentario . "</p>" .
                "<br><br><br>" .
                "<div style='margin: 100px 0 20px 0;'>" .
                "<img src='http://www.meerge.com/rsc/logomail.png' style='width: 250px'>" .
                "</div><br>" .
                "<p style='font-size: 12px; color: #BCBCBC;'>" .
                "Has recibido este correo electrónico por parte de meerge.com. Si no deseas recibir más correos puedes solicitarlo a la empresa responsable" .
                "</p>" .
                "</div><br>";
            $mail->SetFrom(correo_username, 'Meerge Recordatorio');
            $mail->Subject = "Agenda - CANCELADA";
            $mail->msgHTML($MensajeCompleto);
            $address = $filaInvitados['Correo'];
            $mail->AddAddress($address, $address);
            if (!$mail->Send()) {
                $datos['correo'] = $mail->ErrorInfo;
            }
        } catch (Exception $e) {
        }
        $mail->clearAddresses();
    }


    $datos = array();
    $datos['Error'] = '';
    echo json_encode($datos);


}

if ($operacion == 'INVITAR') {
    $datos = array();
    $contactoid = $_POST['contacto'];
    $idtemp = $_POST['idtemp'];
    $estado = $_POST['estado'];
    if ($idtemp == '0') {
        $idtemp = date('His');
        $idtemp = $idtemp . $_SESSION['ClaveUsuario'];
    }

    if ($estado == 1){
        $sqlInvitados = "INSERT INTO reservacioninvitados(IDTemp, ContactoID) VALUES('$idtemp','$contactoid')";
    }else{
        $sqlInvitados = "DELETE FROM reservacioninvitados Where IDTemp = '$idtemp' AND ContactoID =  '$contactoid'";
    }
    $conexion->query($sqlInvitados);
    $datos['error'] = $conexion->error;

    $sqlCuantos = "Select count(RegistroID) as Cuantos From reservacioninvitados Where IDTemp = '$idtemp'";
    $datosCuantos = $conexion->query($sqlCuantos);
    $filaCuantos = $datosCuantos->fetch_assoc();


    $datos['idtemp'] = $idtemp;
    $datos['cuantos'] = $filaCuantos['Cuantos'];
    echo json_encode($datos);
}

if ($operacion == 'CONTACTO') {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $grupo = $_POST['grupo'];
    $empresacontacto = $_POST['empresacontacto'];
    $idusuario = $_POST['idusuario'];

    $sqlContacto = "INSERT INTO usuariocontacto(UsuarioID, Nombre, Correo, FechaUM, GrupoID, Activo, Empresa) VALUES('$idusuario', '$nombre', '$correo',Now(), '$grupo',1, '$empresacontacto')";
    $conexion->query($sqlContacto);

    $sqlID = "Select Max(ContactoID) as Ultimo From usuariocontacto where UsuarioID = '$idusuario'";
    $datosID = $conexion->query($sqlID);
    $filaID = $datosID->fetch_assoc();
    $ultimoid = $filaID['Ultimo'];

    $datos = array();
    $datos['error'] = $conexion->error;
    $datos['id'] = $ultimoid;
    echo json_encode($datos);
}

if ($operacion == 'GRUPOINSERTAR') {
    $nombre = $_POST['nombre'];
    $empresa = $_POST['empresa'];
    $idusuario = $_POST['idusuario'];

    $sqlGrupo = "INSERT INTO grupo(Nombre, EmpresaID,Activo) VALUES('$nombre', '$empresa','1')";
    $conexion->query($sqlGrupo);

    $sqlID = "Select Max(GrupoID) as Ultimo From grupo where EmpresaID = '$empresa'";
    $datosID = $conexion->query($sqlID);
    $filaID = $datosID->fetch_assoc();
    $ultimoid = $filaID['Ultimo'];

    $datos = array();
    $datos['error'] = $conexion->error;
    $datos['id'] = $ultimoid;
    echo json_encode($datos);
}

if ($operacion == 'GRUPOBORRAR') {
    $datos = array();
    $grupo = $_POST['grupo'];
    $idusuario = $_POST['idusuario'];

    $sqlBorrarGrupo = "Update grupo SET activo = 0 Where GrupoID = '$grupo'";
    $conexion->query($sqlBorrarGrupo);
    $datos['error'] = $conexion->error;

    $sqlBorrarUsuario = "Update usuariocontacto Set Activo = 0 Where GrupoID = '$grupo' and UsuarioID = '$idusuario'";
    $conexion->query($sqlBorrarUsuario);
    $datos['error'] = $conexion->error;

    echo json_encode($datos);
}

if ($operacion == 'RECUPERARCONTACTO') {
    $grupo = $_POST['grupo'];
    $idusuario = $_POST['idusuario'];
    $idtemp = $_POST['idtemp'];
    $datos = array();
    //$sqlContactos = "SELECT ContactoID, Nombre, Correo FROM usuariocontacto WHERE UsuarioID = '$idusuario' AND GrupoID = '$grupo' AND Activo = 1";
    $sqlContactos = "SELECT contactos.ContactoID, contactos.Nombre, CASE WHEN CHAR_LENGTH(contactos.Correo) > 22 THEN CONCAT(SUBSTR(contactos.Correo,1,22),'...') ELSE contactos.Correo END AS Correo, IFNULL(invitados.RegistroID,0) AS Registro ".
        "FROM usuariocontacto contactos ".
        "LEFT JOIN reservacioninvitados invitados ON contactos.ContactoID = invitados.ContactoID AND invitados.IDTemp = '$idtemp' ".
        "WHERE contactos.UsuarioID = '$idusuario' AND contactos.GrupoID = '$grupo' AND contactos.Activo = 1";

    $datosContactos = $conexion->query($sqlContactos);
    if ($datosContactos->num_rows > 0){
        while ($filaContactos = $datosContactos->fetch_assoc()){
            $datos[] = $filaContactos;
        }
    }else{
        $datos[0]['ContactoID'] = 0;
        $datos[0]['Nombre'] = '-';
        $datos[0]['Correo'] = '-';
    }

    echo json_encode($datos);

}

if ($operacion == 'NUMERO'){
    $usuarioid = $_POST['usuarioid'];
    $sqlCuantos = "SELECT count(ReservacionID) as Cuantos From reservacion Where UsuarioID = '$usuarioid' and EstadoID = 1";
    $datosCuantos = $conexion->query($sqlCuantos);
    $filaCuantos = $datosCuantos->fetch_assoc();
    $citasProgramadas = $filaCuantos['Cuantos'];

    echo json_encode($citasProgramadas);
}

?>