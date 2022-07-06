<?php

function redirectTo404() {
    header('Location:404.php');
    exit();
}

try {
    $db = new PDO('mysql:host=localhost;dbname=lpanel', 'root', '');
}catch(PDOException $e) {
    $e->getMessage();
}