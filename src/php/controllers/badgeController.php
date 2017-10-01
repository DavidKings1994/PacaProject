<?php
include_once  './../connection.php';
$connection = new Connection();

if(isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'getBadges': {
            $resul = mysqli_query($connection->getConnection(), "CALL getBadges()");
            $badges = array();
            while($row = mysqli_fetch_assoc($resul)) {
                $badges[] = $row;
            }
            echo json_encode(array(
                'status' => 'OK',
                'badges' => $badges
            ));
            break;
        }
        case 'registerBadge': {
            $query = mysqli_prepare($connection->getConnection(), "CALL registerBadge(?,?,?)");
            $query->bind_param('sss',
                $_POST['name'],
                $_POST['desc'],
                $_POST['image']
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
        case 'updateBadge': {
            $query = mysqli_prepare($connection->getConnection(), "CALL updateBadge(?,?,?,?)");
            $query->bind_param('isss',
                $_POST['badge'],
                $_POST['name'],
                $_POST['desc'],
                $_POST['image']
            );
            if($query->execute()) {
                $query->bind_result($resul, $message);
                $query->fetch();
                $data = array(
                    'status' => ($resul == 0 ? 'OK' : 'ERROR'),
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
        case 'deleteBadge': {
            $query = mysqli_prepare($connection->getConnection(), "CALL deleteBadge(?)");
            $query->bind_param('i', $_POST['badge']);
            if($query->execute()) {
                $data = array(
                    'status' => 'OK'
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
