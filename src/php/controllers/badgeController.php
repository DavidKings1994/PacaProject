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
    }
}
?>
