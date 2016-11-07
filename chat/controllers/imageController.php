<?php
namespace Chat\Controllers;

use Chat\Models\User;

class ImageController
{

  private $user;

  public function __construct($user)
  {
    $this->user = $user;
  }

  public function uploadImage()
  {
    $filename = $_FILES['profilePic']['name'];
    $tmpname = $_FILES['profilePic']['tmp_name'];
    $filesize = $_FILES['profilePic']['size'];

    if ($filesize > (716800) || $filesize == 0) {
      echo "Too large";
      exit();
    }

    if (strtolower(explode('.', $filename)[1]) != "jpg") {
      echo "Invalid Fmt";
      exit();
    }
    $imageName = $this->user->userID . '.' . explode('.', $filename)[1];
    move_uploaded_file($tmpname, "../profile_pics/" . $imageName);
    $this->user->uploadImage($this->user->userID, $imageName);
    echo "Success";
  }

  public function removeImage()
  {
    $imageName = $this->user->getImageName($this->user->userID);
    if (file_exists('../profile_pics/' . $imageName)) {
      unlink('../profile_pics/' . $imageName);
      $this->user->removeImage($this->user->userID);
      return "success";
    } else {
      return "failed";
      exit();
    }
  }

}
