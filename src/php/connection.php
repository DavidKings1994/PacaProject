<?php
class Conexion {
    private $server = 'localhost';
    private $user = 'root';
    private $password = 'root';
    private $database = 'guitar_champion';
    private $connection = null;

    function __construct() {
        $this->connection = mysqli_connect($this->server, $this->user, $this->password, $this->database)
            or die('Connection error: ' . mysql_error());
    }

    function __destruct() {
        mysqli_close($this->connection);
    }

    function GetConection() {
        return $this->connection;
    }

    function openConection() {
        $this->connection = mysqli_connect($this->server, $this->user, $this->password, $this->database)
            or die('Connection error: ' . mysql_error());
    }

    function closeConnection() {
        mysqli_close($this->connection);
    }
}
?>
