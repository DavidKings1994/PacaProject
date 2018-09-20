<?php
require './../autoload.php';
require('./../pusher/pusher-php-server/src/Pusher.php');
require('./../pusher/pusher-php-server/src/PusherCrypto.php');
require('./../pusher/pusher-php-server/src/PusherException.php');
require('./../pusher/pusher-php-server/src/PusherInstance.php');
require('./../pusher/pusher-php-server/src/Webhook.php');
include_once  './../connection.php';
$connection = new Connection();

if(isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'getCharacters': {
            $resul = mysqli_query($connection->getConnection(), "CALL getCharacters()");
            $characters = array();
            while($row = mysqli_fetch_assoc($resul)) {
                $characters[] = $row;
            }
            echo json_encode(array(
                'status' => 'OK',
                'characters' => $characters
            ));
            break;
        }
        case 'registerCharacter': {
            $query = mysqli_prepare($connection->getConnection(), "CALL registerCharacter(?,?,?,?,?,?,?,?)");
            $query->bind_param('ssssssss',
                $_POST['id'],
                $_POST['image'],
                $_POST['name'],
                $_POST['desc'],
                $_POST['type'],
                $_POST['species'],
                $_POST['traits'],
                $_POST['owner']
            );
            if($query->execute()) {
                $query->bind_result($resul);
                $query->fetch();
                $data = array(
                    'status' => ((is_numeric($resul) && $resul == 0) ? 'OK' : 'ERROR'),
                    'error' => $resul
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
        case 'updateCharacter': {
            $query = mysqli_prepare($connection->getConnection(), "CALL updateCharacter(?,?,?,?,?,?,?,?,?)");
            $query->bind_param('sssssssss',
                $_POST['id'],
                $_POST['image'],
                $_POST['name'],
                $_POST['desc'],
                $_POST['type'],
                $_POST['species'],
                $_POST['traits'],
                $_POST['owner'],
                $_POST['class']
            );
            if($query->execute()) {
                $query->bind_result($resul, $message);
                $query->fetch();
                $data = array(
                    'status' => ((is_numeric($resul) && $resul == 0) ? 'OK' : 'ERROR'),
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
        case 'sendTransferRequest': {
            $query = mysqli_prepare($connection->getConnection(), "CALL registerTransferRequest(?,?)");
            $query->bind_param('si',
                $_POST['character'],
                $_POST['user']
            );
            if($query->execute()) {
                $query->bind_result($resul);
                $query->fetch();
                if ($resul == 0) {
                    echo json_encode(array(
                        'status' => 'OK'
                    ));
                } else {
                    echo json_encode(array(
                        'status' => 'ERROR',
                        'message' => 'character already has a transfer request'
                    ));
                }

                $options = array(
                    'cluster' => 'us2'
                );
                $pusher = new Pusher\Pusher(
                    '1f4e2261136ad4420076',
                    'fbd2b761ae45b3230055',
                    '596250',
                    $options
                );

                $data['message'] = 'OK';
                $pusher->trigger('sirnusNotifications', 'newNotification', $data);
            } else {
                echo json_encode(array(
                    'status' => 'ERROR',
                    'error' => $query->error
                ));
            }
            break;
        }
        case 'updateTransferRequest': {
            $query = mysqli_prepare($connection->getConnection(), "CALL updateTransferRequest(?,?)");
            $query->bind_param('si',
                $_POST['request'],
                $_POST['status']
            );
            if($query->execute()) {
                echo json_encode(array(
                    'status' => 'OK'
                ));

                $options = array(
                    'cluster' => 'us2'
                );
                $pusher = new Pusher\Pusher(
                    '1f4e2261136ad4420076',
                    'fbd2b761ae45b3230055',
                    '596250',
                    $options
                );

                $data['message'] = 'OK';
                $pusher->trigger('sirnusNotifications', 'newNotification', $data);
            } else {
                echo json_encode(array(
                    'status' => 'ERROR',
                    'error' => $query->error
                ));
            }
            break;
        }
        case 'giveItem': {
            $query = mysqli_prepare($connection->getConnection(), "CALL giveObjectToCharacter(?,?,?)");
            $query->bind_param('isi',
                $_POST['item'],
                $_POST['character'],
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
            $query = mysqli_prepare($connection->getConnection(), "CALL useObjectFromCharacter(?,?,?)");
            $query->bind_param('isi',
                $_POST['item'],
                $_POST['character'],
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
        case 'giveBadge': {
            $query = mysqli_prepare($connection->getConnection(), "CALL giveBadgeToCharacter(?,?)");
            $query->bind_param('is',
                $_POST['badge'],
                $_POST['character']
            );
            if($query->execute()) {
                $query->bind_result($result);
                $query->fetch();
                $data = array(
                    'status' => ($result == 0 ? 'OK' : 'ERROR'),
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
        case 'getInventory':{
            $query = mysqli_prepare($connection->getConnection(), "CALL getCharacterInventory(?)");
            $query->bind_param('s', $_POST['id']);
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
            $query = mysqli_prepare($connection->getConnection(), "CALL getCharacterInventoryCount(?)");
            $query->bind_param('s', $_POST['id']);
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
        case 'getBadges':{
            $query = mysqli_prepare($connection->getConnection(), "CALL getCharacterBadges(?)");
            $query->bind_param('s', $_POST['id']);
            if($query->execute()) {
                $query->bind_result($id, $name, $desc, $image);
                $badges = array();
                while ($query->fetch()) {
                    $badges[] = array(
                        'idBadge' => $id,
                        'name' => $name,
                        'description' => $desc,
                        'image' => $image,
                    );
                }
                $data = array(
                    'status' => 'OK',
                    'badges' => $badges
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
    }
}
?>
