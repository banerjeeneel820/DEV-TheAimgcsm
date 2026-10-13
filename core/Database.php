<?php 
defined('ROOTPATH') or exit('No direct script access allowed');

class Database
{
    private $connection;

    public function __construct()
    {
        $this->connect();
    }

    private function connect()
    {
        $this->connection = mysqli_connect(HOST, MYSQL_USER, MYSQL_PASS);

        if (!$this->connection) {
            throw new Exception("Database connection failed");
        }

        mysqli_select_db($this->connection, DB_AIMGCSM);
        mysqli_query($this->connection, 'SET NAMES "utf8"');
    }

    public function getConnection()
    {
        return $this->connection;
    }
}