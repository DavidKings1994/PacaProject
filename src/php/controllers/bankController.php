<?php
include_once  './../connection.php';
$connection = new Connection();

if(isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'getTickets': {
            $resul = mysqli_query($connection->getConnection(), "CALL getTickets()");
            $tickets = array();
            while($row = mysqli_fetch_assoc($resul)) {
                $tickets[] = $row;
            }
            echo json_encode(array(
                'status' => 'OK',
                'tickets' => $tickets
            ));
            break;
        }
        case 'currencyOperation': {
            $query = mysqli_prepare($connection->getConnection(), "CALL currencyOperation(?,?,?)");
            $query->bind_param('iis',
                $_POST['user'],
                $_POST['amount'],
                $_POST['operation']
            );
            if($query->execute()) {
                $query->bind_result($status, $message);
                $query->fetch();
                $data = array(
                    'status' => ($status == 0 ? 'OK' : 'ERROR'),
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
    }
}
?>
