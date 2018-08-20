<?php
ini_set('max_execution_time', 300);
error_reporting(0);
class Connection {
    private $server = 'localhost';
    private $user = 'root';
    private $password = 'jjkli8jlkjlu';
    private $database = 'sirnus';
    private $connection = null;

    function __construct() {
        $this->connection = mysqli_connect($this->server, $this->user, $this->password, $this->database)
            or die('Connection error: ' . mysql_error());
    }

    function __destruct() {
        mysqli_close($this->connection);
    }

    function getConnection() {
        return $this->connection;
    }

    function openConnection() {
        $this->connection = mysqli_connect($this->server, $this->user, $this->password, $this->database);
    }

    function closeConnection() {
        mysqli_close($this->connection);
    }
}
?>
