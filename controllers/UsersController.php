<?php

class UsersController 
{
  public function getUsers()
  {
    $connection = new Connection();
    return $connection->getUsers();
  }

  public function addUser($data) {
    $connection = new Connection();
    return $connection->insertUser($data);
  }

  public function updateUser($data, $user_id) {
    $connection = new Connection();
    return $connection->updateUser($data, $user_id);
  }

  public function deleteUser($user_id) {
    $connection = new Connection();
    return $connection->deleteUser($user_id);
  }
}
