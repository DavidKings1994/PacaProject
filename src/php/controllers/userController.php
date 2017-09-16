<?php
include_once  './../connection.php';
$connection = new Connection();

if(isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'login': {
            $query = mysqli_prepare($connection->getConnection(), "CALL login(?,?)");
            $pass = sha1($_POST['pass']);
            $query->bind_param('ss',
                $_POST['name'],
                $pass
            );
            if($query->execute()) {
                $query->bind_result($idUser, $name, $rol, $status);
                $query->fetch();
                session_start();
                $data = array(
                    'idPlayer' => $idUser,
                    'name' => $name,
                    'rol' => $rol,
                    'status' => $status
                );
        		$_SESSION['idPlayer'] = $idUser;
                $_SESSION['name'] = $name;
                $_SESSION['rol'] = $rol;
                $_SESSION['status'] = $status;
                echo json_encode($data);
            }
            break;
        }
        case 'logout': {
            session_start();
            unset($_SESSION['idPlayer']);
            unset($_SESSION['name']);
            unset($_SESSION['rol']);
            unset($_SESSION['status']);
            $_SESSION = array();
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                );
            }
            session_destroy();
            break;
        }
    };
}
?>
