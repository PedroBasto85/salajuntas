<?php
//iniciamos la sesi�n
session_name("loginUsuario");
session_start();

//COMPRUEBA QUE EL USUARIO ESTA AUTENTIFICADO
if ($_SESSION["Autentificado"] != "SI") {
    header("Location: login.php");
    exit();
}
?>