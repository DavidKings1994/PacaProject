<?php
class Connection {
    private $server = 'sql3.freemysqlhosting.net';//'localhost';
    private $user = 'sql3195203';//'root';
    private $password = 'JkGP7Umqav';//'root';
    private $database = 'sql3195203';//'paca_project_db';
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
