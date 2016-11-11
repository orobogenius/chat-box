<?php
namespace Chat\Controllers;

use Chat\Models\Chat;

class ChatController
{

  private $chat;

  public function __construct()
  {
    $this->chat = new Chat();
  }

  public function getChats($user, $contact)
  {
    $users[] = $user['userID'] . ':' . $contact['userID'];
    $users[] = $contact['userID'] . ':' . $user['userID'];
    return $this->chat->getChats($users);
  }

  public function addChat($users, $chatBy, $message)
  {
      return $this->chat->addChat($users, $chatBy, $message);
  }

}
