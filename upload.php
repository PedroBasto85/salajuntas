<?php
include("seguridad.php");
include("conexion.php");

$conexion = new mysqli(db_Servidor, db_Usuario, db_Pass, db_BaseDatos);
$conexion->set_charset('utf8');
$usuarioid = $_SESSION['ClaveUsuario'];
$dirarchivos = "files/";
$dirusuario = $dirarchivos . $usuarioid . "/";
echo $dirusuario;
if (!file_exists($dirusuario)) {
    if (!mkdir($dirusuario, 0777, false)) {
        echo "error al crear carpeta";
    }
}
if ($_FILES['fileAvatar']['size'] > 0) {
    $ficherosubido = $dirusuario . basename($_FILES['fileAvatar']['name']);
    move_uploaded_file($_FILES['fileAvatar']['tmp_name'], $ficherosubido);

    //insertar en bd
    $sqlavatar= "update usuario set Avatar = '$ficherosubido' where UsuarioID = '$usuarioid'";
    $conexion->query("$sqlavatar");
    //echo $conexion->error;
}
header("Location: agenda.php");

?>
