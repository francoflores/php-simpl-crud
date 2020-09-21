<?php

  spl_autoload_register(function($name){

    if (file_exists("../controllers/$name.php")) {
      include "../controllers/$name.php";
    } 

    if (file_exists("../database/$name.php")) {
      include "../database/$name.php";
    } 
  });

  // function getUsers() {
  //   $userController = new UsersController();
  //   return $userController->getUsers();
  // }

  function deleteUser($user_id) {
    $userController = new UsersController();
    return $userController->deleteUser();
  }

  try {
    if(!isset($_SERVER["REQUEST_METHOD"])) {
      $data=array("status"=>"0","message"=>"Please enter proper request method !! ");
			echo json_encode($data);
    }
    else {
      $method=$_SERVER["REQUEST_METHOD"];
      $userController = new UsersController();
      switch($method) {
        case "GET": 
          //echo json_encode(["result"=>"hola"]);
          $data = $userController->getUsers();
          echo json_encode(["result"=> $data, "success"=>$data != null]);
          break;
        case "POST": 
          //echo json_encode(["result"=>"hola"]);
          $data = [
            'first_name' => $_REQUEST['first_name'],
            'last_name' => $_REQUEST['last_name'],
            'email' => $_REQUEST['email'],
            'password' => $_REQUEST['password'],
            'active' => 1,
          ];
          $response = $userController->addUser($data);
          echo json_encode($response);
          break;
        case "PUT": 
          $body = file_get_contents("php://input");
          $dataRequest = json_decode($body, true);

          $data = [
            'first_name' => $dataRequest['first_name'],
            'last_name' => $dataRequest['last_name'],
            'email' => $dataRequest['email'],
          ];
          $response = $userController->updateUser($data, $_REQUEST['user_id']);
          echo json_encode($response);
          break;
        case "DELETE":
          //$data = deleteUser($_REQUEST['user_id']);
          $data = $userController->deleteUser($_REQUEST['user_id']);
          echo json_encode(["result"=>$data]);
          break;
        default:
          break;
      }
    }
    
  }
  catch(Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
  }
?>