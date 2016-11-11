<?php
namespace Chat;


require 'src/chat/connection.php';

class ConnectionTest Extends \PHPUnit_Framework_TestCase
{
  public function testSetConnectionProperties()
  {
    $dbh = new Connection("localhost", "chatdb", "root", "this.mysql");
    $this->assertAttributeEquals("localhost", "server", $dbh);
    $this->assertAttributeEquals("chatdb", "database", $dbh);
    $this->assertAttributeEquals("root", "username", $dbh);
    $this->assertAttributeEquals("this.mysql", "password", $dbh);
  }

  public function testConnect()
  {
    $dbh = new Connection("localhost", "chatdb", "root", "this.mysql");
    $hn = $dbh->connect();
    //$this->assertInstanceOf(\PDO::class, $hn);
  }

  public function testCloseConnection()
  {
    $dbh = new Connection("localhost", "chatdb", "root", "this.mysql");
    $hn = $dbh->connect();
    $hn = $dbh->close();
    $this->assertNull($hn);
  }

}
