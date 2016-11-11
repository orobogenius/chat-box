<?php

namespace Chat\Models;

require_once('model.php');
require_once('contact.php');

use Chat\Models\Model;
use Chat\Models\Contact;

class Request extends Model
{
  /**
  * @var Table name of the model
  **/
  private $table = "requests";

  public function __construct()
  {
    parent::__construct();
  }

  /**
  * Adds a friend request to the current user's request list
  * @param $user - The current user
  * @param $contact - The contact to be added
  * @return mixed
  **/
  public function sendRequest($user, $contact)
  {
    $request = $this->dbh->query("SELECT * FROM " . $this->table . " WHERE userID = '$contact' AND contactID = '" . $user['userID'] ."'")->fetchAll(\PDO::FETCH_ASSOC);
    if (count($request) > 0) {
      return "The Request has already been sent";
    } else {
      if ($this->dbh->exec("INSERT INTO " . $this->table . " VALUES (null,  '$contact', '". $user['userID'] ."')"))
      {
        return "Your Request to Add this contact has been sent successfully!";
      } else {
        return "Unable to Send Request to the contact. Please try again!";
      }
    }
  }

  /**
  * Adds the specified contact to the current user's contact list
  * @param $user - The current user
  * @param $contact - The contact to be added
  * @return mixed
  **/
  public function acceptRequest($user, $contact)
  {
    $contact_m = new Contact();
    if ($contact_m->addContact($user, $contact)){
        $this->dbh->exec("DELETE FROM " . $this->table . " WHERE userID = '".$user['userID']."'");
        return true;
    } else {
      return false;
    }
  }

  /**
  * Gets all request resource for the specified user
  * @param $user - The current user
  * @return mixed
  **/
  public function getRequests($user)
  {
    $requests = $this->dbh->query("SELECT * FROM " . $this->table . " WHERE userID = '". $user->userID ."'")->fetchAll(\PDO::FETCH_ASSOC);
    return $requests;
  }

  /**
  * Remove a request from the current user's request list
  * @param $user - The current user
  * @param $contact - The contact to be removed
  * @return mixed
  **/
  public function declineRequest($user, $contact)
  {
    if ($this->dbh->exec("DELETE FROM " . $this->table. " WHERE userID = '".$user['userID']."' AND contactID = '$contact'"))
    {
      return true;
    } else {
      return false;
    }
  }

}
