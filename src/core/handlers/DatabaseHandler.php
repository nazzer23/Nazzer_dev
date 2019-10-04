<?php

class DatabaseHandler
{
    public $db;

    public function __construct()
    {
        $this->db = new mysqli(Configuration::dbHost, Configuration::dbUser, Configuration::dbPass, Configuration::dbName);
        if ($this->db->connect_error) {
            die("There was an error whilst connecting to the database. Please contact the site administrator.");
        }
    }

    public function fetchArray($query)
    {
        return $this->executeQuery($query)->fetch_array();
    }

    public function executeQuery($query)
    {
        return $this->db->query($query);
    }

    public function fetchObject($query)
    {
        return $this->executeQuery($query)->fetch_object();
    }

    public function getNumberOfRows($query)
    {
        return $this->executeQuery($query)->num_rows;
    }

    public function escapeArray($array)
    {
        foreach ($array as $key => $value) {
            $array[$key] = $this->escapeString($value);
        }
        return $array;
    }

    public function escapeString($value)
    {
        return $this->db->real_escape_string($value);
    }
}

?>