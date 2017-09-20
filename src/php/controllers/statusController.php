<?php
include_once  './../connection.php';
$connection = new Connection();

if(isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'getStatus': {
            $resul = mysqli_query($connection->getConnection(), "CALL getStatus()");
            $status = array();
            while($row = mysqli_fetch_assoc($resul)) {
                $status[] = $row;
            }
            echo json_encode(array(
                'status' => 'OK',
                'statusList' => $status
            ));
            break;
        }
    }
}
?>
