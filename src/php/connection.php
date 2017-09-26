<?php
class Connection {
    private $server = 'localhost';
    private $user = 'root'; //'id2845823_pacaadmin';
    private $password = 'root';//'jjkli8jlkjlu';
    private $database = 'paca_project_db'; //'id2845823_paca_project_db';
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
