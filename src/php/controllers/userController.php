<?php
require('./../phpmailer/phpmailer/src/PHPMailer.php');
require('./../phpmailer/phpmailer/src/Exception.php');
require('./../phpmailer/phpmailer/src/SMTP.php');
require './../autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
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
                $query->bind_result($idUser, $name, $rol, $status, $currency);
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
                            'currency' => $currency,
                            'options' => ''
                        )
                    );
            		$_SESSION['idUser'] = $idUser;
                    $_SESSION['name'] = $name;
                    $_SESSION['rol'] = $rol;
                    $_SESSION['status'] = $status;
                    $_SESSION['currency'] = $currency;
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
            unset($_SESSION['currency']);
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
                        'status' => $_SESSION['status'],
                        'currency' => $_SESSION['currency']
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
        case 'changePassword': {
            $query = mysqli_prepare($connection->getConnection(), "CALL changePassword(?,?)");
            $newPass = sha1($_POST['newPass']);
            $query->bind_param('is',
                $_POST['id'],
                $newPass
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
        case 'changeEmail': {
            $query = mysqli_prepare($connection->getConnection(), "CALL changeEmail(?,?)");
            $query->bind_param('is',
                $_POST['id'],
                $_POST['newEmail']
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
        case 'useItem': {
            $query = mysqli_prepare($connection->getConnection(), "CALL useObjectFromUser(?,?,?)");
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
        case 'transferItem': {
            $query = mysqli_prepare($connection->getConnection(), "CALL transferObject(?,?,?,?,?)");
            $character = ($_POST['character'] != "" && $_POST['character'] != null ? $_POST['character'] : null);
            $user = ($_POST['user'] != "" && $_POST['user'] != null ? $_POST['user'] : null);
            $query->bind_param('iisii',
                $_POST['owner'],
                $user,
                $character,
                $_POST['item'],
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
        case 'getInventoryCount':{
            $query = mysqli_prepare($connection->getConnection(), "CALL getUserInventoryCount(?)");
            $query->bind_param('i', $_POST['id']);
            if($query->execute()) {
                $query->bind_result($total, $id, $name, $desc, $image);
                $items = array();
                while ($query->fetch()) {
                    $items[] = array(
                        'total' => $total,
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
        case 'getCharacters': {
            $query = mysqli_prepare($connection->getConnection(), "CALL getUserCharacters(?)");
            $query->bind_param('i', $_POST['id']);
            if($query->execute()) {
                $characters = array();
                $query->bind_result($idCharacter, $image, $name, $desc, $status);
                while ($query->fetch()) {
                    $characters[] = array(
                        'idCharacter' => $idCharacter,
                        'image' => $image,
                        'name' => $name,
                        'description' => $desc,
                        'status' => $status,
                    );
                }
                $data = array(
                    'status' => 'OK',
                    'characters' => $characters
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
        case 'searchUsersCharacters': {
            $query = mysqli_prepare($connection->getConnection(), "CALL searchUsersCharacters(?,?)");
            $query->bind_param('is', $_POST['user'], $_POST['name']);
            if($query->execute()) {
                $query->bind_result($idCharacter, $name);
                $characters = array();
                while($query->fetch()) {
                    $characters[] = array(
                        'value' => $idCharacter,
                        'label' => $name
                    );
                }
                $data = array(
                    'status' => 'OK',
                    'users' => $characters
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
        case 'getProfile': {
            $query = mysqli_prepare($connection->getConnection(), "CALL getProfile(?)");
            $query->bind_param('s', $_POST['name']);
            if($query->execute()) {
                $query->bind_result($idUser, $name, $image, $rol, $status, $currency, $email);
                if($query->fetch()) {
                    $data = array(
                        'status' => 'OK',
                        'profile' => array(
                            'idUser' => $idUser,
                            'name' => $name,
                            'rol' => $rol,
                            'image' => $image,
                            'status' => $status,
                            'currency' => $currency,
                            'email' => $email
                        )
                    );
                    echo json_encode($data);
                } else {
                    echo json_encode(array(
                        'status' => 'ERROR',
                        'error' => '404'
                    ));
                }
            } else {
                echo json_encode(array(
                    'status' => 'ERROR',
                    'error' => $query->error
                ));
            }
            break;
        }
        case 'checkPasswordRestoreKey': {
            $query = mysqli_prepare($connection->getConnection(), "CALL checkPasswordRestoreKey(?)");
            $query->bind_param('s', $_POST['key']);
            if($query->execute()) {
                $query->bind_result($user);
                if($query->fetch()) {
                    echo json_encode(array(
                        'status' => 'OK',
                        'user' => $user
                    ));
                } else {
                    echo json_encode(array(
                        'status' => 'ERROR',
                        'error' => 'key not found'
                    ));
                }
            } else {
                echo json_encode(array(
                    'status' => 'ERROR',
                    'error' => 'Ups! there was error'
                ));
            }
            break;
        }
        case 'restorePassword': {
            $query = mysqli_prepare($connection->getConnection(), "CALL restorePassword(?)");
            $query->bind_param('s', $_POST['user']);
            if($query->execute()) {
                $query->bind_result($resul, $link, $_email);
                if($query->fetch()) {
                    if($_email != null and $_email != '') {
                        $message = '
                        <html lang="en">
                            <head>
                                <meta charset="utf-8">
                                <title>Sirnus password restore</title>
                            </head>
                            <body>
                                <p>Hello, ' . $_POST['user'] . '!</p>
                                <p><a href="http://sirnus.com/#/passwordRestore/' . $link . '">
                                    Click here to change the password of your Sirnus account
                                </a></p>
                                <p>We suggest that you change it after login in to
                                one that you can better remember. If you did not request a password change
                                then ignore this mail.</p>
                            </body>
                        </html>
                        ';
                        try {
                            $mail=new PHPMailer(true);
                            $mail->CharSet = 'UTF-8';

                            $mail->IsSMTP();
                            $mail->Host       = 'smtpout.secureserver.net';
                            $mail->Port       = 80;
                            $mail->SMTPAuth   = true;
                            $mail->Username   = 'no-reply@sirnus.com';
                            $mail->Password   = 'S1rnu5123!';

                            $mail->SetFrom('no-reply@sirnus.com', 'no-reply');
                            $mail->AddReplyTo('no-reply@sirnus.com','no-reply');
                            $mail->Subject = 'Sirnus password restore';
                            $mail->isHTML(true);
                            $mail->msgHTML($message, __DIR__);
                            $mail->AltBody = 'Hello, ' . $_POST['user'] . '!
                            Click here to change the password of your sirnus account:
                            http://sirnus.com/#/passwordRestore/' . $link . '.
                            We suggest that you change it after login in to
                            one that you can better remember. If you did not request
                            a password change ignore this mail.';
                            $mail->AddAddress($_email);
                            if (!$mail->send()) {
                                echo json_encode(array(
                                    'status' => 'ERROR',
                                    'error' => $mail->ErrorInfo
                                ));
                            } else {
                                echo json_encode(array(
                                    'status' => 'OK'
                                ));
                            }
                        } catch (Exception $e) {
                            echo json_encode(array(
                                'status' => 'ERROR',
                                'error' => $e->message
                            ));
                        }
                    } else {
                        echo json_encode(array(
                            'status' => 'ERROR',
                            'error' => 'User don\'t have an e-mail registered'
                        ));
                    }
                } else {
                    echo json_encode(array(
                        'status' => 'ERROR',
                        'error' => '404'
                    ));
                }
            } else {
                echo json_encode(array(
                    'status' => 'ERROR',
                    'error' => $query->error
                ));
            }
            break;
        }
        case 'deleteProfilePic': {
            $data = $_POST['url'];
            $data =  basename($data);
            $dir = $_SERVER['DOCUMENT_ROOT'].'/assets/profile_pics';
            $dirHandle = opendir($dir);
            while ($file = readdir($dirHandle)) {
                if($file==$data) {
                    unlink($dir."/".$file);
                }
            }
            closedir($dirHandle);

            $query = mysqli_prepare($connection->getConnection(), "CALL deleteProfilePicture(?)");
            $query->bind_param('i', $_POST['user']);
            if($query->execute()) {
                echo json_encode(array('status' => 'OK'));
            } else {
                echo json_encode(array(
                    'status' => 'ERROR',
                    'error' => $query->error
                ));
            }
            break;
        }
        case 'uploadProfilePic': {
            try {
                ini_set('file_uploads', 'on');
                ini_set('upload_max_filesize', '2M');
                ini_set('post_max_size', '2M');
                $localDir = '/assets/profile_pics/';
                $allow = array("jpg", "jpeg", "gif", "png");
                $todir = $_SERVER['DOCUMENT_ROOT'].$localDir;
                $fileError = $_FILES['file']['error'];
                if($fileError == UPLOAD_ERR_OK){
                    if ( !!$_FILES['file']['tmp_name'] ) {
                        $info = explode('.', strtolower( $_FILES['file']['name']) );
                        if ( in_array( end($info), $allow) ) {
                            $milliseconds = round(microtime(true) * 1000);
                            $info = pathinfo($_FILES['file']['name']);
                            $ext = $info['extension'];
                            $newname = $milliseconds.".".$ext;
                            if ( move_uploaded_file( $_FILES['file']['tmp_name'], $todir . $newname ) ) {
                                $finalpath = $localDir . $newname;
                                $query = mysqli_prepare($connection->getConnection(), "CALL setProfilePic(?,?)");
                                $query->bind_param('is', $_POST['id'], $finalpath);
                                if($query->execute()) {
                                    $data = array(
                                        'status' => 'OK',
                                        'message' => $localDir . $newname
                                    );
                                    echo json_encode($data);
                                } else {
                                    echo json_encode(array(
                                        'status' => 'ERROR',
                                        'error' => $query->error
                                    ));
                                }
                            } else {
                                $message = 'Unable to upload profile image, server rejected it';
                                echo json_encode(array(
                                    'status' => 'ERROR',
                                    'error' => $message
                                ));
                            }
                        } else {
                            $message = 'Unable to upload profile image, extension not allowed';
                            echo json_encode(array(
                                'status' => 'ERROR',
                                'error' => $message
                            ));
                        }
                    } else {
                        $message = 'Unable to upload profile image, file not found';
                        echo json_encode(array(
                            'status' => 'ERROR',
                            'error' => $message
                        ));
                    }
                }else{
                    switch($fileError){
                        case UPLOAD_ERR_INI_SIZE:
                        $message = 'Error al intentar subir un archivo que excede el tamaño permitido.';
                        break;
                        case UPLOAD_ERR_FORM_SIZE:
                        $message = 'Error al intentar subir un archivo que excede el tamaño permitido.';
                        break;
                        case UPLOAD_ERR_PARTIAL:
                        $message = 'Error: no terminó la acción de subir el archivo.';
                        break;
                        case UPLOAD_ERR_NO_FILE:
                        $message = 'Error: ningún archivo fue subido.';
                        break;
                        case UPLOAD_ERR_NO_TMP_DIR:
                        $message = 'Error: servidor no configurado para carga de archivos.';
                        break;
                        case UPLOAD_ERR_CANT_WRITE:
                        $message= 'Error: posible falla al grabar el archivo.';
                        break;
                        case  UPLOAD_ERR_EXTENSION:
                        $message = 'Error: carga de archivo no completada.';
                        break;
                        default: $message = 'Error: carga de archivo no completada.';
                        break;
                    }
                    echo json_encode(array(
                        'status' => 'ERROR',
                        'message' => $message
                    ));
                }
            } catch (Exception $e) {
                echo json_encode(array(
                    'status' => 'ERROR',
                    'message' => $e->getMessage()
                ));
            }
            break;
        }
    };
}
?>
