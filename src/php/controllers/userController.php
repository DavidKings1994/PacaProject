<?php
include_once  './../connection.php';
$conexion = new Connection();

if(isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'login': {
            $query = mysqli_prepare($conexion->GetConexion(), "CALL login(?,?)");
            $pass = sha1($_POST['pass']);
            $query->bind_param('ss',
                $_POST['name'],
                $pass
            );
            if($query->execute()) {
                $row = mysqli_fetch_assoc($query->execute());
                session_start();
        		$_SESSION['idPlayer'] = $row["idUser"];
                $_SESSION['name'] = $row["name"];
                $_SESSION['mail'] = $row["email"];
                $_SESSION['rol'] = $row["rol"];
                header("Location: ./../../index.php");
            }
            break;
        }
    };
}
?>
