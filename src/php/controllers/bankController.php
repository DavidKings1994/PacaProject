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
    }
}
?>
