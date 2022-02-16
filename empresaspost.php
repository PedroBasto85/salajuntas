<?php
include ('seguridad.php');
include('conexion.php');

$conexion = new mysqli(db_Servidor, db_Usuario, db_Pass, db_BaseDatos);
$conexion->set_charset('utf8');
if (isset($_POST['campoOpcion'])){
    $opcion = $_POST['campoOpcion'];
}else{
    $opcion = $_GET['campoOpcion'];
}

$usuarioid = $_SESSION['ClaveUsuario'];

if ($opcion == 'AGREGAR'){
    $nombre = $_POST['txtNombre'];
    $direccion = $_POST['txtDireccion'];
    $telefono = $_POST['txtTelefono'];
    $correo = $_POST['txtCorreo'];
    $web = $_POST['txtWeb'];
    $empresa = $_POST['campoEmpresa'];
    $logo = '';

    $sqlInsertar = "call pRazonSocialInsertar('$empresa','$nombre', '$direccion', '$telefono','$correo','$web', '$logo','$usuarioid')";
    $conexion->query($sqlInsertar);

    $sqlUltimo = "Select ifnull(Max(RazonSocialID),0) as Ultimo From razonsocial Where EmpresaID = '$empresa' and UsuarioUM = '$usuarioid'";
    $datosUltimo = $conexion->query($sqlUltimo);
    $filaUltimo = $datosUltimo->fetch_assoc();

    $RazonSocialID = $filaUltimo['Ultimo'];

    //$dirarchivos = "C:/xampp/htdocs/salajuntas/logos/"; //"logos/";
    $dirarchivos = "logos/";
    $dirempresa = $dirarchivos . $RazonSocialID . "/";
    //echo $dirempresa;
    if (!file_exists($dirempresa)) {
        if (!mkdir($dirempresa, 0777, false)) {
            echo "error al crear carpeta";
        }
    }
    if ($_FILES['fileLogo']['size'] > 0) {
        $ficherosubido = $dirempresa . basename($_FILES['fileLogo']['name']);
        move_uploaded_file($_FILES['fileLogo']['tmp_name'], $ficherosubido);

        //insertar en bd
        $sqllogo= "update razonsocial set Logo = '$ficherosubido' where RazonSocialID = '$RazonSocialID'";
        $conexion->query("$sqllogo");
        //echo $conexion->error;
    }
    header("location: empresas.php");

}

if ($opcion == 'EDITAR'){
    $nombre = $_POST['txtNombre'];
    $direccion = $_POST['txtDireccion'];
    $telefono = $_POST['txtTelefono'];
    $correo = $_POST['txtCorreo'];
    $web = $_POST['txtWeb'];
    $id = $_POST['campoID'];
    $logo = '';

    $sqlActualizar = "call pRazonsocialActualizar('$id','$nombre', '$direccion','$telefono','$correo','$web','$usuarioid')";
    $conexion->query($sqlActualizar);

    //$dirarchivos = "C:/xampp/htdocs/salajuntas/logos/"; //"logos/";
    $dirarchivos = "logos/";
    $dirempresa = $dirarchivos . $id . "/";
    //echo $dirempresa;
    if (!file_exists($dirempresa)) {
        if (!mkdir($dirempresa, 0777, false)) {
            echo "error al crear carpeta";
        }
    }
    if ($_FILES['fileLogo']['size'] > 0) {
        $ficherosubido = $dirempresa . basename($_FILES['fileLogo']['name']);
        move_uploaded_file($_FILES['fileLogo']['tmp_name'], $ficherosubido);

        //insertar en bd
        $sqllogo= "update razonsocial set Logo = '$ficherosubido' where RazonSocialID = '$id'";
        $conexion->query("$sqllogo");
        //echo $conexion->error;
    }
    header("location: empresas.php");
}


if ($opcion == "BORRAR"){
    $id = $_GET['id'];
    $sqlActualizar = "UPDATE razonsocial SET Activo = 0 WHERE RazonSocialID = '$id' ";
    $conexion->query($sqlActualizar);

    header("location: empresas.php");
}

if ($opcion == 'RECUPERAR'){
    $id = $_POST['idrazonsocial'];
    $sqlEmpresa = "SELECT RazonSocialID, Nombre, Direccion, Telefono, Correo, SitioWeb, Logo, Activo FROM razonsocial WHERE Activo = 1 AND RazonSocialID = '$id'";
    $datosEmpresa = $conexion->query($sqlEmpresa);
    $filaEmpresa = $datosEmpresa->fetch_assoc();
    $datos = array();
    $datos[] = $filaEmpresa;

    echo json_encode($datos);
}
