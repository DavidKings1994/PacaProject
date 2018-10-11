<?php
include_once  './../connection.php';
$connection = new Connection();

if(isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'getPets': {
            $resul = mysqli_query($connection->getConnection(), "CALL getPets()");
            $pets = array();
            while($row = mysqli_fetch_assoc($resul)) {
                $pets[] = $row;
            }
            echo json_encode(array(
                'status' => 'OK',
                'petsList' => $pets
            ));
            break;
        }
    }
}
?>
