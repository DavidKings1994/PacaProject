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
        case 'registerCharacter': {
            $query = mysqli_prepare($connection->getConnection(), "CALL registerCharacter(?,?,?,?,?)");
            $query->bind_param('sssss',
                $_POST['id'],
                $_POST['image'],
                $_POST['name'],
                $_POST['desc'],
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
    }
}
?>
