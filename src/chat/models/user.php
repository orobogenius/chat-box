<?php
namespace Chat\Models;

require_once('model.php');

use Chat\Models\Model;

class User extends Model
{

  private $table = "users";
  public $userID;

  public function __construct($userID)
  {
    parent::__construct();
    $this->userID = $userID;
  }

  public function addUser($data)
  {

  }

  public function getUser()
  {
    $user = $this->dbh->query("SELECT *, CONCAT_WS(' ', firstName, lastName) as name FROM " . $this->table . " WHERE userID = '". $this->userID ."'")->fetch(\PDO::FETCH_ASSOC);
    return $user;
  }

  public function searchUser($search)
  {
    $uid = $_COOKIE['userID'];
    $user = $this->dbh->query("SELECT *, CONCAT_WS(' ', firstName, lastName) as name FROM " . $this->table . " WHERE userID = '$search' AND userID != '$uid'")->fetchAll(\PDO::FETCH_ASSOC);
    if (count($user) > 0) {
      $contact = $this->dbh->query("SELECT * FROM contacts WHERE contactID = '$search' AND userID = '$uid'")->fetchAll(\PDO::FETCH_ASSOC);
      if (!count($contact) > 0){
        return $user;
      }
    }
  }

  public function uploadImage($userID, $imageName)
  {
    if ($this->dbh->exec("UPDATE " . $this->table . " SET profile_pic = '$imageName' WHERE userID = '$userID'"))
    {
      return true;
    } else {
      return false;
    }
  }

  public function removeImage($userID)
  {
    if ($this->dbh->exec("UPDATE " . $this->table . " SET profile_pic = NULL WHERE userID = '$userID'"))
    {
      return true;
    } else {
      return false;
    }
  }

  public function getImageName($userID)
  {
    $imageName = "";
    $result = $this->dbh->query("SELECT profile_pic FROM ". $this->table . " WHERE userID = '$userID'")->fetch(\PDO::FETCH_ASSOC);
    return $imageName = $result['profile_pic'];
  }

}
