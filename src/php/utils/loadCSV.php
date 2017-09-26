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
$usersGenerated = array();

foreach ($characters as $key => $value) {
    $query2 = mysqli_prepare($connection->getConnection(), "CALL registerUser(?,?,?)");
    $pass = substr(uniqid('paca', true), 0, 14);
    $encriptedPass = sha1($pass);
    $status = 1;
    $query2->bind_param('sss', $value['Owner'], $encriptedPass, $status);
    if($query2->execute()) {
        $query2->bind_result($resul2);
        $query2->fetch();
        if ($resul2 == 0) {
            $usersGenerated[] = array(
                'name' => $value['Owner'],
                'password' => $pass
            );
        }
    }
    $query2->close();

    $query = mysqli_prepare($connection->getConnection(), "CALL registerCharacterWithOwner(?,?,?,?,?)");
    $desc = (isset($value['Description']) ? $value['Description'] : null);
    $name = 'NA';
    if($value['Image'] != '' && isset($value['Image'])) {
        $query->bind_param('sssss',
            $value['Registry Number'],
            $value['Image'],
            $name,
            $desc,
            $value['Owner']
        );
        if($query->execute()) {
            $query->bind_result($resul);
            $query->fetch();
            if ($resul == 0) {

            }
        } else {
            echo json_encode(array(
                'status' => 'ERROR',
                'error' => $query->error
            ));
        }
    }
    $query->close();
}

echo json_encode($usersGenerated);

?>
