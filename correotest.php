<?php
require_once('seguridad.php');
include('conexion.php');
include('clases/phpmailer/PHPMailerAutoload.php');
include('smtp.php');

date_default_timezone_set('America/Bogota');

$MensajeCompleto = "Hola...";

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


try {
    $MensajeCompleto ="<!doctype html><html lang='ES'><head><meta charset='UTF-8'></head><body>".
        "<div style='font-family: Arial;display:block;position:relative;'>".
        "<h1 style='display:block;position:relative; padding-top: 20px;'>". 'Pedro Jose'."</h1>".
        "<h1 style='position:relative; top: 20px;' topmargin='50'>Tu próxima reunión</h1>".
        "<div style='position:relative;top: 50px;width: 300px; height: 80px; line-height:80px; border: 1px solid #aaaaaa; border-radius: 10px;-moz-border-radius: 10px;-webkit-border-radius: 10px; text-align: center'>".
        "<span style='font-size: 50px; line-height:80px; font-weight: bold; color: #787878;'>".'12/12/2015'."</span>".
        "</div>".
        "</div>".
        "</body></html>";
    $mail->SetFrom(correo_username, 'Recordatorio');
    $mail->Subject = "Agenda";
    $mail->MsgHTML($MensajeCompleto);
    $address = 'pedrobasto85@gmail.com';
        $mail->AddAddress($address, $address);
        if (!$mail->Send()) {
            echo $mail->ErrorInfo;
        }
    } catch (Exception $e) {

}