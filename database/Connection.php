<?php

class Connection {
  private $db = null;

  private $host = "localhost";
  private $port = 3306;
  private $user = "root";
  private $password = "";
  private $database = "simple_crud";

  function __construct() 
  { }

  function connect() 
  {
    $this->db = new mysqli(
      $this->host, 
      $this->user, 
      $this->password, 
      $this->database,
      $this->port
    );
  }

  function disconnect() 
  {
    $this->db->close();
  }

  public function getUsers(): Array 
  {
    $this->connect();
    $sql = "SELECT * FROM users WHERE active = 1";
    $result = $this->db->query($sql); //mysqli_query($conn, $sql);
    $records = [];

    while($row = $result->fetch_object()) {
      array_push(
        $records, 
        array(
          "id" => $row->id, 
          "first_name"=> $row->first_name, 
          "last_name"=> $row->last_name,
          "email"=> $row->email)
      );
    }

    $this->disconnect();
    return $records;
  }

  public function getUser(int $id): Array
  {
    $this->connect();
    $sql = "SELECT * FROM users WHERE id = $id limit 0,1";

    $result = $this->db->query($sql); //mysqli_query($conn, $sql);
    $records = [];

    while($row = $result->fetch_object()) {
      array_push(
        $records, 
        array(
          "id" => $row->id, 
          "first_name"=> $row->first_name, 
          "last_name"=> $row->last_name,
          "email"=> $row->email)
      );
    }

    $this->disconnect();
    return $records[0];
  }

  public function isEmailRegistered($email, $id = null)
  {
    $this->connect();
    $sql = "SELECT * FROM users WHERE email = '$email' ".($id != null? " AND id <> $id":'')." limit 0, 1";
    $result = $this->db->query($sql); //mysqli_query($conn, $sql);
    $records = [];

    while($result && $row = $result->fetch_object()) {
      array_push(
        $records, 
        array(
          "id" => $row->id, 
          "first_name"=> $row->first_name, 
          "last_name"=> $row->last_name,
          "email"=> $row->email)
      );
    }

    $this->disconnect();
    return count($records) > 0;
  }

  public function insertUser($data)
  {
    if($this->isEmailRegistered($data['email'])) {
      return ['success' => false, 'msg' => 'Email is registered'];
    }
    $this->connect();
    $fields = "";
    $values = "";
    foreach ($data as $key => $value) {
      $fields .= ($fields == '' ? '':', ')."$key";
      $values .= ($values == '' ? '':', ')."'$value'";
    }

    $sql = "INSERT INTO users ($fields) VALUES ($values)";

    $result = $this->db->query($sql);
    $this->disconnect();
    return ['success' => true, 'result'=>$result, 'msg' => 'User add correctly'];
    
  }

  public function updateUser($data, $id) 
  {
    if($this->isEmailRegistered($data['email'], $id)) {
      return ['success' => false, 'msg' => 'Email is registered'];
    }
    $this->connect();
    $fields = "";
    foreach ($data as $key => $value) {
      $fields .= ($fields == '' ? '':', ')."$key = '$value'";
    }

    $sql = "UPDATE users SET $fields WHERE id = $id";

    $result = $this->db->query($sql);
    $this->disconnect();
    return ['success' => true, 'result'=>$result, 'msg' => 'User updated correctly'];
  }

  public function deleteUser($user_id)
  {
    $this->connect();
    $sql = "UPDATE users SET active = 0 WHERE id = $user_id";
    $result = $this->db->query($sql);
    $this->disconnect();
    if(!$result) {
      return $this->getLastQueryError();
    } else {
      return $result;
    }
  }

  public function getLastConnectionError(): Array
  {
    return $this->db->connect_error;
  }

  public function getLastConnectionErrorCode(): int
  {
    return $this->db->connect_errno;
  }

  public function getLastQueryError() 
  {
    return $this->db->error_list;
  }

  public function getLastQueryErrorCode(): int
  {
    return $this->db->errno;
  }

  public function executeQuery($query)
  {
    $this->connect();
    $result = $this->db->query($query); //mysqli_query($conn, $sql);

    $this->disconnect();
  }

}

?>