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
    }
}
?>
