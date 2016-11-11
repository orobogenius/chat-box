<?php
namespace Chat\Controllers;

use Chat\Models\Request;

class RequestController
{

  private $request;

  public function __construct()
  {
    $this->request = new Request();
  }

  public function dispatchRequest($user, $contact)
  {
    $response = $this->request->sendRequest($user, $contact);
    return $response;
  }

  public function acceptRequest($user, $contact)
  {
    return $this->request->acceptRequest($user, $contact);
  }

  public function declineRequest($user, $contact)
  {
    return $this->request->declineRequest($user, $contact);
  }

}
