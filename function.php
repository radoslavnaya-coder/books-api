<?php

function getBooks($connect){
    $books = mysqli_query($connect,"SELECT * FROM `book`,`author`");
    $booklist = [];

    while($book = mysqli_fetch_assoc($books)){
        $booklist[] = $book;
    }

    echo json_encode($booklist);
}

function getBook($connect, $book_id){
    $book = mysqli_query($connect, "SELECT * FROM `book` WHERE `book_id` = '$book_id'");
    if(mysqli_num_rows($book) === 0){
        http_response_code(404);
    }
    else{
        $book = mysqli_fetch_assoc($book);
        echo json_encode($book);
    }
}

function getGenrename($connect){
    $genres = mysqli_query($connect, "SELECT * FROM `genre`");
    $genrelist = [];

    while($genre = mysqli_fetch_assoc($genres)){
        $genrelist[] = $genre;
    }

    echo json_encode($genrelist);
}

function getAuthors($connect){
    $authors = mysqli_query($connect, "SELECT * FROM `author`");
    $authorList = [];

    while($author = mysqli_fetch_assoc($authors)){
        $authorList = $author;
    }

    echo json_encode($authorList);
}

function addBook($connect, $data){
    $image = $data['book_img'];
    $name = $data['book_name'];
    $author = $data['author_id'];
    $script = $data['book_script'];
    $year = $data['book_year'];
    $genre = $data['book_genre_id'];

    mysqli_query($connect, "INSERT INTO `book` (`book_id`, `book_img`, `book_name`, `author_id`, `book_script`, `book_year`, `book_genre_id`) 
    VALUES (NULL, '$image', '$name', '$author', '$script', '$year', '$genre') ");

    http_response_code(201);
    $mes = [
        "status" => true,
        "book_id" => mysqli_insert_id($connect)
    ];
    echo json_encode($mes);
}

function deleteBook($connect, $book_id){
    mysqli_query($connect, "DELETE FROM `book` WHERE `book`.`book_id` = '$book_id'");
    $mes = [
        "status" => true,
        "message" => "Книга удалена"
    ];
    echo json_encode($mes);
}