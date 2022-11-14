<?php 

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Methods: *');
header('Access-Control-Allow-Credentials: true');

header('Content-type: json/application');

require 'function.php';
require 'connect.php';

$method = $_SERVER['REQUEST_METHOD'];
$short = $_GET['short'];
$params = explode('/',$short);
$type = $params[0];
if(isset($params[1])){
    $book_id = $params[1];
}

if($method === 'GET'){
    if($type === 'books'){
        if(isset($book_id)){
            getBook($connect, $book_id);
        }
        else{
            getBooks($connect);
        }
    }
    elseif($type === genre){
        getGenrename($connect);
    }
}