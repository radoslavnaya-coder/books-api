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
    $genres = mysqli_query($connect, "SELECT `genre_name` FROM `genre`");
    $genrelist = [];

    while($genre = mysqli_fetch_assoc($genres)){
        $genrelist[] = $genre;
    }

    echo json_encode($genrelist);
}