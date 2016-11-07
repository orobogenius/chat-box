<?php
namespace Chat\Controllers;

use Chat\Models\User;

class SearchController
{

	private $model;

	public function __construct()
	{
		$this->model = new User($_COOKIE['userID']);
	}

	public function search($search)
	{
		return $this->model->searchUser($search);
	}

}
