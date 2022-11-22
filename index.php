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
    elseif($type === author){
        getAuthors($connect);
    }
}
elseif($method === 'POST'){
    if($type === 'books'){
        addBook($connect, $_POST, $_FILES);
    }
}
elseif($method === 'DELETE'){
    if($type === 'books'){
        if(isset($book_id)){
            deleteBook($connect, $book_id);
        }
    }
}
elseif($method === 'PATCH'){
    if($type === 'books'){
        if(isset($book_id)){
            $data = file_get_contents('php://input');
            $data = json_decode($data, true);
            updateBook($connect, $book_id, $data, $_FILES);
        }
    }
}