<?php
include_once  './../connection.php';
$connection = new Connection();

function parseCSVtoArray($path) {
    $csv = array_map('str_getcsv', file($path));
    $data = array();
    foreach ($csv as $key => $value) {
        $data[] = array(
            $csv[0][0] => utf8_encode($value[0]),
            $csv[0][1] => utf8_encode($value[1]),
            $csv[0][2] => utf8_encode($value[2]),
            $csv[0][3] => utf8_encode($value[3])
        );
    }
    return array_slice($data, 1);
}

$characters = parseCSVtoArray('masterlist.csv');
// echo json_encode($characters);

foreach ($characters as $key => $value) {
    $query = mysqli_prepare($connection->getConnection(), "CALL registerCharacter(?,?,?,?)");
    $desc = (isset($value['Description']) ? $value['Description'] : null);
    $name = 'NA';
    if($value['Image'] != '' && isset($value['Image'])) {
        $query->bind_param('ssss',
            $value['Registry Number'],
            $value['Image'],
            $name,
            $desc
        );
        if($query->execute()) {
            $query->bind_result($resul);
            $query->fetch();
            $data = array(
                'status' => ($resul == 0 ? 'OK' : 'ERROR')
            );
            echo json_encode($data);
        } else {
            echo json_encode(array(
                'status' => 'ERROR',
                'error' => $query->error
            ));
        }
    }
    $connection->closeConnection();
    $connection->openConnection();
}
?>
