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
                if (!is_null($idUser)) {
                    session_start();
                    $data = array(
                        'status' => 'OK',
                        'data' => array(
                            'idUser' => $idUser,
                            'name' => $name,
                            'rol' => $rol,
                            'status' => $status,
                            'options' => ''
                        )
                    );
            		$_SESSION['idUser'] = $idUser;
                    $_SESSION['name'] = $name;
                    $_SESSION['rol'] = $rol;
                    $_SESSION['status'] = $status;
                    echo json_encode($data);
                } else {
                    echo json_encode(array(
                        'status' => 'ERROR'
                    ));
                }
            }
            break;
        }
        case 'logout': {
            session_start();
            unset($_SESSION['idUser']);
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
        case 'checkSession': {
            session_start();
            if (isset($_SESSION['idUser'])) {
                $data = array(
                    'status' => 'logged',
                    'data' => array(
                        'idUser' => $_SESSION['idUser'],
                        'name' => $_SESSION['name'],
                        'rol' => $_SESSION['rol'],
                        'status' => $_SESSION['status']
                    )
                );
                echo json_encode($data);
            } else {
                echo json_encode(array(
                    'status' => 'not logged'
                ));
            }
            break;
        }
        case 'getUsers': {
            $resul = mysqli_query($connection->getConnection(), "CALL getUsers()");
            $users = array();
            while($row = mysqli_fetch_assoc($resul)) {
                $users[] = $row;
            }
            echo json_encode(array(
                'status' => 'OK',
                'users' => $users
            ));
            break;
        }
        case 'registerUser': {
            $query = mysqli_prepare($connection->getConnection(), "CALL registerUser(?,?,?)");
            $pass = sha1($_POST['pass']);
            $query->bind_param('ssi',
                $_POST['name'],
                $pass,
                $_POST['status']
            );
            if($query->execute()) {
                $query->bind_result($resul);
                $query->fetch();
                $data = array(
                    'status' => ($resul == 0 ? 'OK' : 'ERROR'),
                    'error' => 'Can\'t complete operation'
                );
                echo json_encode($data);
            } else {
                echo json_encode(array(
                    'status' => 'ERROR',
                    'error' => $query->error
                ));
            }
            break;
        }
        case 'adminUpdateUser': {
            $query = mysqli_prepare($connection->getConnection(), "CALL adminUpdateUser(?,?,?)");
            $query->bind_param('isi',
                $_POST['idUser'],
                $_POST['name'],
                $_POST['status']
            );
            if($query->execute()) {
                $query->bind_result($resul);
                $query->fetch();
                $data = array(
                    'status' => ($resul == 0 ? 'OK' : 'ERROR'),
                    'error' => 'Can\'t complete operation'
                );
                echo json_encode($data);
            } else {
                echo json_encode(array(
                    'status' => 'ERROR',
                    'error' => $query->error
                ));
            }
            break;
        }
        case 'giveItem': {
            $query = mysqli_prepare($connection->getConnection(), "CALL giveObjectToUser(?,?,?)");
            $query->bind_param('iii',
                $_POST['item'],
                $_POST['user'],
                $_POST['quantity']
            );
            if($query->execute()) {
                $query->bind_result($status, $message);
                $query->fetch();
                $data = array(
                    'status' => $status,
                    'error' => $message
                );
                echo json_encode($data);
            } else {
                echo json_encode(array(
                    'status' => 'ERROR',
                    'error' => $query->error
                ));
            }
            break;
        }
        case 'getInventory':{
            $query = mysqli_prepare($connection->getConnection(), "CALL getUserInventory(?)");
            $query->bind_param('i', $_POST['id']);
            if($query->execute()) {
                $query->bind_result($id, $name, $desc, $image);
                $items = array();
                while ($query->fetch()) {
                    $items[] = array(
                        'idItem' => $id,
                        'name' => $name,
                        'description' => $desc,
                        'image' => $image,
                    );
                }
                $data = array(
                    'status' => 'OK',
                    'items' => $items
                );
                echo json_encode($data);
            } else {
                echo json_encode(array(
                    'status' => 'ERROR',
                    'error' => $query->error
                ));
            }
            break;
        }
        case 'search': {
            $query = mysqli_prepare($connection->getConnection(), "CALL searchUsers(?)");
            $query->bind_param('s', $_POST['name'] );
            if($query->execute()) {
                $query->bind_result($idUser, $name);
                $users = array();
                while($query->fetch()) {
                    $users[] = array(
                        'value' => $idUser,
                        'label' => $name
                    );
                }
                $data = array(
                    'status' => 'OK',
                    'users' => $users
                );
                echo json_encode($data);
            } else {
                echo json_encode(array(
                    'status' => 'ERROR',
                    'error' => $query->error
                ));
            }
            break;
        }
    };
}
?>
