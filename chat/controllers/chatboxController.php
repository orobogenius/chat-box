<?php
namespace Chat\Controllers;

require_once('../models/contact.php');
require_once('../models/request.php');
require_once('../models/chat.php');
require_once('../models/user.php');
require_once('../controllers/searchController.php');
require_once('../controllers/requestController.php');
require_once('../controllers/chatController.php');
require_once('../controllers/imageController.php');

use Chat\Models\Contact;
use Chat\Models\Request;
use Chat\Models\Chat;
use Chat\Models\User;
use Chat\Controllers\SearchController;

class ChatBoxController
{
  private $contact;
  private $request;
  private $chat;
  private $user;

  public function __construct($contact, $request, $chat, $user)
  {
    $this->contact = $contact;
    $this->request = $request;
    $this->chat = $chat;
    $this->user = $user;
  }

  public function getContacts()
  {
    return $this->contact->getContacts($this->user);
  }

  public function getRequests()
  {
    return $this->request->getRequests($this->user);
  }

  public function __toString()
  {
    return "ChatBoxController";
  }

}

if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['action'])) {
  $user = new User($_COOKIE['userID']);
  $chatBox = new ChatBoxController(new Contact(), new Request(), new Chat(), $user);
  switch ($_POST['action']) {
    case 'getChatData':{
      $contacts = $chatBox->getContacts();
      $contact_data = $request_data = array();
      $requests = $chatBox->getRequests();
      foreach ($contacts as $con) {
        $contact = new User($con['contactID']);
        $con['c_userdata'] = $contact->getUser();
        $contact_data[] = $con;
      }
      foreach ($requests as $req) {
        $request = new User($req['contactID']);
        $req['c_userdata'] = $request->getUser();
        $request_data[] = $req;
      }
      $data = [
        'contacts' => $contact_data,
        'requests' => $request_data,
        'userdata' => $user->getUser()
      ];
      echo json_encode($data);
    } break;
    case 'getSearchData':{
      $searchCI = new SearchController();
      echo json_encode([
        'userdata' => $searchCI->search($_POST['userID'])
      ]);
    } break;
    case 'sendRequest':{
      $request = new RequestController();

      echo $request->dispatchRequest($user->getUser(), $_POST['userID']);
    } break;
    case 'acceptRequest': {
      $request = new RequestController();
      if ($request->acceptRequest($user->getUser(), $_POST['userID'])) {
        echo "success";
      } else {
        echo "failed";
      }
    } break;
    case 'declineRequest':{
      $request = new RequestController();
      if ($request->declineRequest($user->getUser(), $_POST['userID'])) {
        echo "success";
      } else {
        echo "failed";
      }
    } break;
    case 'getChat':{
      $contact = new User($_POST['userID']);
      $contact_data = $contact->getUser();
      $chat = new ChatController();
      $chats = $chat->getChats($user->getUser(), $contact->getUser());
      echo json_encode([
          'chats' => $chats,
          'contact_data' => $contact_data,
          'numMsg' => count($chats)
        ]);
    } break;
    case 'updateChat':{
      $contact = new User($_POST['userID']);
      $contact_data = $contact->getUser();
      $chat = new ChatController();
      $chats = $chat->getChats($user->getUser(), $contact->getUser());
      $count = count($chats);
      $new = array();
      if ($_POST['numMsg'] == $count) {
        $numMsg = $count;
      } else {
        $numMsg = $_POST['numMsg'] + count($chats) - $_POST['numMsg'];
        foreach($chats as $index => $chat) {
          if ($index >= $_POST['numMsg']) {
            $new[] = $chat;
          }
        }
      }
      echo json_encode([
        'chats' => $new,
        'numMsg' => count($chats),
        'contact_data' => $contact_data
      ]);
    } break;
    case 'saveChat':{
      $chat = new ChatController();
      $users = $user->userID . ':' . $_POST['userID'];
      if ($chat->addChat($users, $user->userID, $_POST['message'])) {
        echo 'success';
      } else {
        echo 'failed';
      }
    } break;
    case 'removeImage': {
      $image = new ImageController($user);
      echo $image->removeImage();
    } break;
    default:
      return "Oops! An error has occured!";
      break;
  }
} else if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_FILES)) {
  session_start();
  $user = new User($_COOKIE['userID']);
  $image = new ImageController($user);
  echo $image->uploadImage();
}
