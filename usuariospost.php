<?php
include('conexion.php');

$conexion = new mysqli(db_Servidor, db_Usuario, db_Pass, db_BaseDatos);
$conexion->set_charset('utf8');
if (isset($_POST['campoOpcion'])){
    $opcion = $_POST['campoOpcion'];
}else{
    $opcion = $_GET['campoOpcion'];
}


if ($opcion == 'AGREGAR'){
    $nombre = $_POST['txtNombre'];
    $usuario = $_POST['txtUsuario'];
    $contrasenia = $_POST['txtPass'];
    $empresa = $_POST['campoEmpresa'];

    $sqlInsertar = "INSERT INTO usuario(Nombre, Usuario, Contrasenia, RolID, Vigente, EmpresaID) ".
        "VALUES('$nombre', '$usuario', '$contrasenia','2','1', '$empresa')";
    $conexion->query($sqlInsertar);

    header("location: usuarios.php");
}

if ($opcion == 'EDITAR'){
    $id = $_POST['campoID'];
    $nombre = $_POST['txtNombre'];
    $usuario = $_POST['txtUsuario'];
    $contrasenia = $_POST['txtPass'];
    $empresa = $_POST['campoEmpresa'];

    $sqlActualizar = "UPDATE usuario SET Nombre = '$nombre', Usuario = '$usuario', Contrasenia = '$contrasenia' WHERE UsuarioID = '$id' ";
    $conexion->query($sqlActualizar);

    header("location: usuarios.php");
}

if ($opcion == "BORRAR"){
    $id = $_GET['id'];
    $sqlActualizar = "UPDATE usuario SET Vigente = 0 WHERE UsuarioID = '$id' ";
    $conexion->query($sqlActualizar);

    header("location: usuarios.php");
}

if ($opcion == 'RECUPERAR'){
    $id = $_POST['idusuario'];
    $sqlUsuario = "SELECT UsuarioID, Nombre, Usuario FROM usuario WHERE UsuarioID = '$id'";
    $datosUsuario = $conexion->query($sqlUsuario);
    $filaUsuario = $datosUsuario->fetch_assoc();
    $datos = array();
    $datos[] = $filaUsuario;

    echo json_encode($datos);
}
