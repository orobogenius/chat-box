<?php
namespace Chat;

class Connection
{

  private   $server;
  private   $database;
  private   $username;
  private   $password;
  protected $port;
  protected $dbh;

  function __construct($server, $database, $username, $password, $port = 3306)
  {
    $this->server = $server;
    $this->database = $database;
    $this->username = $username;
    $this->password = $password;
  }

  public function connect()
  {
    try {
      $this->dbh =
          new \PDO("mysql:host=".$this->server . ";dbname=".$this->database."", "".$this->username."", "".$this->password."");
    }
    catch (PDOException $ex)
    {
      die($ex->getMessage());
    }
    return $this->dbh;
  }

  public function close()
  {
    $this->dbh = null;
  }

}
