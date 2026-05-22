<?php
require('db/conn.php');
require('models/User.php');

$userModel = new User($conn);
$users = $userModel->GetAllUsers();
var_dump($users); // para ver el resultado completo
// o
print_r($users); // más legible
?>
