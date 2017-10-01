<?php
include_once  './../connection.php';
$connection = new Connection();

if(isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'getItems': {
            $resul = mysqli_query($connection->getConnection(), "CALL getItems()");
            $items = array();
            while($row = mysqli_fetch_assoc($resul)) {
                $items[] = $row;
            }
            echo json_encode(array(
                'status' => 'OK',
                'items' => $items
            ));
            break;
        }
        case 'registerItem': {
            $query = mysqli_prepare($connection->getConnection(), "CALL registerItem(?,?,?)");
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
        case 'updateItem': {
            $query = mysqli_prepare($connection->getConnection(), "CALL updateItem(?,?,?,?)");
            $query->bind_param('isss',
                $_POST['item'],
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
        case 'deleteItem': {
            $query = mysqli_prepare($connection->getConnection(), "CALL deleteItem(?)");
            $query->bind_param('i', $_POST['item']);
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
