<?php
namespace Chat\Models;

require_once('model.php');

use Chat\Models\Model;

class Contact extends Model
{
  /**
  * @var Table name of the model
  **/
  private $table = "contacts";

  public function __construct()
  {
    parent::__construct();
  }

  /**
  * Adds a new contact resource to storage
  * @param $user - The current user
  * @param $contact - The contact to be added to the user's contact list
  * @return true | false
  **/
  public function addContact($user, $contact)
  {
    $stmt = $this->dbh->prepare("INSERT INTO " . $this->table . " VALUES('', ?, ?)");
    $stmt->bindParam(1, $user['userID'], \PDO::FETCH_ASSOC);
    $stmt->bindParam(2, $contact, \PDO::FETCH_ASSOC);
    if ($stmt->execute())
    {
      $stmt = $this->dbh->prepare("INSERT INTO " . $this->table . " VALUES('', ?, ?)");
      $stmt->bindParam(1, $contact, \PDO::FETCH_ASSOC);
      $stmt->bindParam(2, $user['userID'], \PDO::FETCH_ASSOC);
      return $stmt->execute();
    } else return false;
  }

  /**
  * Gets all contact resource for the specified user
  * @param $user - The current user
  * @return mixed
  **/
  public function getContacts($user)
  {
    $stmt = $this->dbh->prepare("SELECT * FROM " . $this->table . " WHERE userID = ?");
    $stmt->bindParam(1, $user->userID, \PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  /**
  * Deletes a contact resource from the specified user's contact list
  * @param $user - The current user
  * @param $contact - The contact to be deleted
  * @return mixed
  **/
  public function deleteContact($user, $contact)
  {
    $stmt = $this->dbh->prepare("DELETE FROM " . $this->table . " WHERE userID = ? AND contact = ?");
    $stmt->bindParam(1, $user->userID, \PDO::PARAM_INT);
    $stmt->bindParam(2, $contact, \PDO::PARAM_INT);
    return $stmt->execute();
  }

}
