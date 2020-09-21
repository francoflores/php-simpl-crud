<?php
echo "Exporting data \n";

spl_autoload_register(function($name){

  if (file_exists("database/$name.php")) {
    include "database/$name.php";
  } 
});

$connection = new Connection();

$table = "CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

$keys = "ALTER TABLE `users`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `email` (`email`);";

$autoincrements = "ALTER TABLE `users`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;";

$data = [
  'first_name' => 'Admin',
  'last_name' => 'Example',
  'email' => 'admin@example.com',
  'password' => "123456",
  'active' => 1
];

$connection->executeQuery($table);
$connection->executeQuery($keys);
$connection->executeQuery($autoincrements);
$connection->insertUser($data);
?>