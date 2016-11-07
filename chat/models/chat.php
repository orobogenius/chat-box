<?php
namespace Chat\Models;

require_once('model.php');

use Chat\Models\Model;

class Chat extends Model
{
  /**
  * @var Table name of the model
  **/
  private $table = "chats";

  public function __construct()
  {
    parent::__construct();
  }

  /**
  * Adds a new chat message to storage
  * @param $users - A concatenation of both user's ID using colon. For example
  *                 12345678:8765421
  * @param $chatBy - The userID of the user sending in the chat message
  * @param $message - The chat message
  * @return true | false
  **/
  public function addChat($users, $chatBy, $message)
  {
  	$date = \Date('Y-m-d H:i:s');
  	$stmt = $this->dbh->prepare("INSERT INTO " . $this->table . " VALUES ('', ?, ?, ?, ?)");
  	$stmt->bindParam(1, $users, \PDO::PARAM_STR);
  	$stmt->bindParam(2, $chatBy, \PDO::PARAM_INT);
  	$stmt->bindParam(3, $message, \PDO::PARAM_STR);
  	$stmt->bindParam(4, $date);
	  return $stmt->execute();
  }

  /**
  * Gets all chat messages for the specified users identifier
  * @param $users - A concatenation of both user's ID using colon. For example
  *                 12345678:8765421
  * @return mixed
  **/
  public function getChats($users)
  {
    $type_a = $users[0];
    $type_b = $users[1];
    $stmt = $this->dbh->prepare("SELECT * FROM " . $this->table . " WHERE users = ? OR users = ?");
    $stmt->bindParam(1, $type_a, \PDO::PARAM_STR);
    $stmt->bindParam(2, $type_b, \PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  /**
  * Gets all chat messages on the platform
  * @return mixed
  **/
  public function getTotalChats()
  {
    $stmt = $this->dbh->prepare("SELECT * FROM " . $this->table);
    $stmt->execute();
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

}
