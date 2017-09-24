<?php
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
    }
}
?>
