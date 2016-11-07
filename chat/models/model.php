<?php
namespace Chat\Models;

require_once('../connection.php');

use Chat\Connection;

class Model
{

  protected $connection;
  protected $dbh;

  /**
  * Initializes a connection to the database
  * @return $dbh - The database handle
  **/
  public function __construct()
  {
    $this->connection = new Connection("localhost", "chatdb", "root", "this.mysql");
    $this->dbh = $this->connection->connect();
  }

}
