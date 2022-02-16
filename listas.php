<?php
require('seguridad.php');
require('conexion.php');
include('clases/fpdf/fpdf.php');

$conexion = new mysqli(db_Servidor, db_Usuario, db_Pass, db_BaseDatos);
$conexion->set_charset("utf8");

global $logo, $correo, $web, $titulo, $tituloReservacion, $noPersonas, $Fecha, $Horario, $Empresa, $Direccion, $Telefono;

class PDF extends FPDF
{
    // Cabecera de p�gina
   function Header()
    {
        $this->AddFont('Open Sans','','OpenSans-Light.php');
        $this->SetFont('Open Sans','',10);
        $this->SetXY(-70,20);
        $this->Image($GLOBALS['logo'],null,null,60,24);//,0,0,60,24);
        $this->ln();
        $this->SetXY(10,25);
        $this->SetTextColor(17,17,17);
        $this->Cell(20, 0,utf8_decode('Reunión:'),0,0);
        $this->SetTextColor(141,141,141);
        $this->Cell(20,0 ,$GLOBALS['tituloReservacion']  ,0,0);
        $this->ln(5);
        $this->SetTextColor(17,17,17);
        $this->Cell(20, 0,'Fecha: '  ,0,0);
        $this->SetTextColor(141,141,141);
        $this->Cell(20,0 , $GLOBALS['Fecha']  ,0,0);
        $this->ln(5);
        $this->SetTextColor(17,17,17);
        $this->Cell(20, 0,'Horario: ' ,0,0);
        $this->SetTextColor(141,141,141);
        $this->Cell(20, 0, $GLOBALS['Horario'] ,0,0);
        $this->ln(5);
        $this->SetTextColor(17,17,17);
        $this->Cell(20, 0,'Empresa:' ,0,0);
        $this->SetTextColor(141,141,141);
        $this->Cell(20, 0, $GLOBALS['Empresa'] ,0,0);
        $this->ln(5);
        $this->SetTextColor(17,17,17);
        $this->Cell(20, 0,'Direccion: ' ,0,0);
        $this->SetTextColor(141,141,141);
        $this->Cell(100, 0, $GLOBALS['Direccion'] ,0,0);
        $this->ln(15);
        $this->SetTextColor(17,17,17);
        $this->SetXY(-60,60);
        $this->SetTextColor(17,17,17);
        $this->Cell(5, 0,'email ' ,60,0,'R');
        $this->SetTextColor(141,141,141);
        $this->Cell(0, 0,$GLOBALS['correo'] ,60,0,'R');
        $this->SetXY(-60,65);
        $this->SetTextColor(17,17,17);
        $this->Cell(3, 0,'web ',60,0,'R');
        $this->SetTextColor(141,141,141);
        $this->Cell(0, 0,$GLOBALS['web'] ,60,0,'R');


    }

    // Pie de página
     function Footer()
     {
         $titulo = $GLOBALS['titulo'];
         // Posicion: a 1 cm del final
         $this->SetXY(10,-20);
         $this->Image('clases/fpdf/logolista.png', null, null, 45, 12);

         //$this->SetFont('Arial', '', 10);
         $this->SetFont('Open Sans','',10);
         $this->SetTextColor(17,17,17);
         $this->SetXY(110,-20);
         $this->Cell(50, 10,'Pag. ' . $this->PageNo() . " - " .  utf8_decode($titulo));
         $this->SetY(-15);
         $this->Cell(200, null, 'soporte@meerge.com', 0, 0, 'R');
         $this->SetXY(-60, -15);
         $this->Cell(0, null, 'www.meerge.com', 0, 0, 'R');
     }
}

$id = $_GET["id"];
$regPorHoja = 5;
$usuarioID = $_SESSION['ClaveUsuario'];

$sqlCuantos = "SELECT COUNT(RegistroID) AS Numero FROM reservacioninvitados WHERE ReservacionID = '$id' ";
$datosCuantos = $conexion->query($sqlCuantos);
$filaCuantos = $datosCuantos->fetch_assoc();
$noInvitados = $filaCuantos['Numero'];

$noPaginas = $noInvitados / $regPorHoja;
if ($noPaginas < 1){
    $noPaginas = 1;
}else {
    $noPaginas = round($noPaginas, 0, PHP_ROUND_HALF_UP);
}
$sqlDatos = "SELECT ReservacionID, Titulo, Personas, DATE_FORMAT(Fecha,'%d/%m/%Y') as Fecha, Time_Format(HoraInicio,'%H:%i') as HoraInicio, Time_Format(HoraTermino,'%H:%i') as HoraTermino, usuario.Nombre AS Usuario, reservacion.EstadoID, reservacion.RazonSocialID, sysestado.Descripcion AS Estado,".
       " Comentario, razonsocial.EmpresaID, razonsocial.Nombre AS Empresa,  razonsocial.Direccion AS EmpresaDireccion, razonsocial.Correo, razonsocial.SitioWeb, razonsocial.Telefono AS EmpresaTelefono FROM reservacion ".
       " INNER JOIN razonsocial ON reservacion.RazonSocialID = razonsocial.RazonSocialID ".
       " INNER JOIN usuario ON reservacion.UsuarioID = usuario.UsuarioID ".
       " INNER JOIN sysestado ON reservacion.EstadoID = sysestado.EstadoID ".
       " WHERE reservacion.ReservacionID = '$id'";

$datos = $conexion->query($sqlDatos);
$filaDatos = $datos->fetch_assoc();

$tituloReservacion = utf8_decode($filaDatos['Titulo']);
$noPersonas = utf8_decode($filaDatos['Personas']);
$Fecha = $filaDatos['Fecha'];
$Horario = $filaDatos['HoraInicio']. "-". $filaDatos['HoraTermino'];
$Empresa = utf8_decode($filaDatos['Empresa']);
$Direccion = utf8_decode($filaDatos['EmpresaDireccion']);
$Telefono = $filaDatos['EmpresaTelefono'];
$RazonSocialID = $filaDatos['RazonSocialID'];
$correo = utf8_decode($filaDatos['Correo']);
$web = utf8_decode($filaDatos['SitioWeb']);

$titulo = $noInvitados.' Invitados';
$empresaID = $filaDatos['EmpresaID'];

$sqlLogo = "Select Logo from razonsocial Where RazonSocialID = '$RazonSocialID'";
$datosLogo = $conexion->query($sqlLogo);
$filaLogo = $datosLogo->fetch_assoc();
$logo = $filaLogo['Logo'];

//if ($empresaID == 1){
//    //$logo = 'clases/fpdf/ynbadi.png';
//    $correo = 'contacto@ynbadi.com';
//    $web = 'www.ynbadi.com';
//}else{
//    //$logo = 'clases/fpdf/idihub.png';
//    $correo = 'innovacion@idihub.com';
//    $web = 'www.idihub.com';
//}

$pdf = new PDF('L','mm','Letter');
$pdf->AddFont('Open Sans','','OpenSans-Light.php');
$pdf->AddFont('Open Sans','B','OpenSans-Bold.php');
$margen = 10;
$margeninicial = 95;
$pdf->SetFont('Open Sans','',10);
for ($i=1;$i<=$noPaginas;$i++){
    $offset = ($i-1) * $regPorHoja;
    $pdf->AddPage();
    $pdf->SetXY(10,65);
    $pdf->SetFont('Open Sans','',10);

    $pdf->SetTextColor(17,17,17);
    $pdf->Cell(0, 0,'LISTA DE ASISTENCIA SALA DE JUNTAS' ,0,0);
    $pdf->SetDrawColor(200,200,200);
    $pdf->Rect(11,80,257,105);
    $pdf->SetXY(15,85);
    $pdf->SetTextColor(141,141,141);
    $pdf->Cell(75, 0,'Nombre' ,0,0,'L');
    $pdf->Cell(75, 0,'Email' ,0,0,'L');
    $pdf->Cell(65, 0,utf8_decode('Institución/Empresa') ,0,0,'L');
    $pdf->Cell(40, 0,'Firma' ,0,0,'C');
    $pdf->SetDrawColor(17,17,17);
    $pdf->Line(11,90,268,90);
    $pdf->SetXY(15,$margeninicial);
    $pdf->SetFont('Open Sans','B',11);
    $pdf->SetTextColor(17,17,17);
    $sqlInvitados = "SELECT contactos.Nombre, contactos.Correo, contactos.Empresa FROM usuariocontacto contactos".
        " INNER JOIN reservacioninvitados invitados ON invitados.ContactoID = contactos.ContactoID".
        " AND invitados.ReservacionID = '$id' Limit $offset, $regPorHoja";
    $datosInvitados = $conexion->query($sqlInvitados);
    while ($filaInvitados = $datosInvitados->fetch_assoc()){
            $pdf->MultiCell(75,5,$filaInvitados['Nombre']);
            $pdf->SetXY(90,$margeninicial);
            $pdf->MultiCell(75,5,$filaInvitados['Correo']);
            $pdf->SetXY(165,$margeninicial);
            $pdf->MultiCell(65,5,$filaInvitados['Empresa']);
            $margeninicial=$margeninicial + $margen;
            $pdf->SetXY(15, $margeninicial);
    }
}

$pdf->Output('Asistencia.pdf','I');


