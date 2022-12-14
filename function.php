<?php

function getBooks($connect){
    $books = mysqli_query($connect,"SELECT DISTINCT
    `book`.`book_id` AS 'book_id',
    `book`.`book_name` AS 'book_name',
    `book`.`book_img` AS 'book_img',
    `book`.`book_year` AS 'book_year',
    (SELECT GROUP_CONCAT(`author`.`author_name` separator ', ') FROM `author`, `booksauthor` 
     where `author`.`author_id` = `booksauthor`.`author_id`
     and `booksauthor`.`book_id` = `book`.`book_id`)
     as 'author_namer'
    
    FROM `book`
    ");
    $booklist = [];

    while($book = mysqli_fetch_assoc($books)){
        $booklist[] = $book;
    }

    echo json_encode($booklist);
}

function getBook($connect, $book_id){
    $book = mysqli_query($connect, "SELECT DISTINCT
    `book`.`book_id` AS 'book_id',
    `book`.`book_name` AS 'book_name',
    `book`.`book_img` AS 'book_img',
    `book`.`book_year` AS 'book_year',
    (SELECT GROUP_CONCAT(`author`.`author_name` separator ', ') FROM `author`, `booksauthor` 
     where `author`.`author_id` = `booksauthor`.`author_id`
     and `booksauthor`.`book_id` = `book`.`book_id`)
     as 'author_namer'
    
    FROM `book` WHERE `book`.`book_id` = $book_id");
    if(mysqli_num_rows($book) === 0){
        http_response_code(404);
    }
    else{
        $book = mysqli_fetch_assoc($book);
        echo json_encode($book);
    }
}

function getGenrename($connect){
    $genres = mysqli_query($connect, "
    SELECT * FROM `genre`
    ");
    // $genres = mysqli_query($connect, "SELECT DISTINCT
    // `book`.`book_id` AS 'book_id',
    // `book`.`book_name` AS 'book_name',
    // `book`.`book_img` AS 'book_img',
    // `book`.`book_year` AS 'book_year',
    // (SELECT GROUP_CONCAT(`author`.`author_name` separator ', ') FROM `author`, `booksauthor` 
    //  where `author`.`author_id` = `booksauthor`.`author_id`
    //  and `booksauthor`.`book_id` = `book`.`book_id`)
    //  as 'author_namer',
    //  (SELECT GROUP_CONCAT(`booksgenres`.`book_genre_id` separator ', ') FROM `genre`, `booksgenres` 
    //  where `genre`.`book_genre_id` = `booksgenres`.`book_genre_id`
    //  and `booksgenres`.`book_id` = `book`.`book_id`)
    //  as 'book_genre_id',
    //  (SELECT GROUP_CONCAT(`genre`.`genre_name` separator ', ') FROM `genre`, `booksgenres` 
    //  where `genre`.`book_genre_id` = `booksgenres`.`book_genre_id`
    //  and `booksgenres`.`book_id` = `book`.`book_id`)
    //  as 'genre_name'

    // FROM `book`");
    
    $genrelist = [];

    while($genre = mysqli_fetch_assoc($genres)){
        $genrelist[] = $genre;
    }

    echo json_encode($genrelist);
}
function getGenresname($connect, $book_genre_id){
    $genres = mysqli_query($connect, "SELECT DISTINCT
    `book`.`book_id` AS 'book_id',
    `book`.`book_name` AS 'book_name',
    `genre`.`genre_name` AS 'genre_name',
    `genre`.`book_genre_id` AS 'book_genre_id',
    `book`.`book_img` AS 'book_img',
    `book`.`book_year` AS 'book_year',

    (SELECT GROUP_CONCAT(`author`.`author_name` separator ', ') FROM `author`, `booksauthor` 
     WHERE `author`.`author_id` = `booksauthor`.`author_id`
     AND `booksauthor`.`book_id` = `book`.`book_id`)
     AS 'author_namer'
    
    FROM `book`,`genre`,`booksgenres`
    
    WHERE `genre`.`book_genre_id`= `booksgenres`.`book_genre_id` 
    AND `booksgenres`.`book_id` = `book`.`book_id` 
    AND `genre`.`book_genre_id` = '$book_genre_id'");
    
    if(mysqli_num_rows($genres) === 0){
        http_response_code(404);
        $res = [
            "status" => false,
            "message" => "Books not found"
        ];
        echo json_encode($res);
    }
    else{
        $genreList = [];
        while($genre = mysqli_fetch_assoc($genres)){
            $genreList[] = $genre;
        }
        echo json_encode($genreList);
    }
}
function getAuthors($connect){
    $authors = mysqli_query($connect, "SELECT * FROM `author`");
    $authorList = [];

    while($author = mysqli_fetch_assoc($authors)){
        $authorList[] = $author;
    }

    echo json_encode($authorList);
}

function addBook($connect, $data, $file){

    $ex = pathinfo($file['bookimage']['name'], PATHINFO_EXTENSION);
    $filename = uniqid().".".$ex;
    move_uploaded_file($file['bookimage']['tmp_name'], "../books/uploads/".$filename);
    $filename = 'uploads/'.$filename;

    // $image = $data['book_img'];
    
    $name = $data['book_name'];
    $author = $data['author_id'];
    $script = $data['book_script'];
    $year = $data['book_year'];
    $genre = $data['book_genre_id'];

    mysqli_query($connect, "INSERT INTO `book` (`book_id`, `book_name`, `book_img`, `book_script`, `book_year`) 
    VALUES (NULL, '$name', '$filename', '$script', '$year')");
    mysqli_query($connect, "INSERT INTO `author` (`author_id`) VALUES ('$author')");
    $id = mysqli_insert_id($connect);
    mysqli_query($connect, "INSERT INTO `booksauthor` (`id`, `book_id`, `author_id`) VALUES (NULL, '$id', '$author') ");
    mysqli_query($connect, "INSERT INTO `booksgenres` (`id`, `book_id`, `book_genre_id`) VALUES (NULL, '$id', '$genre') ");
    mysqli_query($connect, "INSERT INTO `genre` (`book_genre_id`) VALUES ('$genre')");
    http_response_code(201);
    $mes = [
        "status" => true,
        "book_id" => mysqli_insert_id($connect)
    ];
    echo json_encode($mes);
}

function deleteBook($connect, $book_id){
    mysqli_query($connect, "DELETE FROM `book` WHERE `book`.`book_id` = '$book_id'");
    mysqli_query($connect, "DELETE FROM `booksauthor` WHERE `booksauthor`.`book_id` = '$book_id'");
    mysqli_query($connect, "DELETE FROM `booksgenres` WHERE `booksgenres`.`book_id` = '$book_id'");
    $mes = [
        "status" => true,
        "message" => "Книга удалена"
    ];
    echo json_encode($mes);
}

function updateBook($connect, $book_id, $data, $file){
   
    $ex = pathinfo($file['bookimage']['name'], PATHINFO_EXTENSION);
    $filename = uniqid().".".$ex;
    move_uploaded_file($file['bookimage']['tmp_name'], "../books/uploads/".$filename);
    $filename = 'uploads/'.$filename;
   
    $name = $data['book_name'];
    $author = $data['author_id'];
    $script = $data['book_script'];
    $year = $data['book_year'];
    $genre = $data['book_genre_id'];

    mysqli_query($connect, "UPDATE `book` SET `book_img`='$filename',
    `book_name`='$name',`book_script`='$script',`book_year`='$year'
    WHERE `book`.`book_id` = '$book_id'");
    mysqli_query($connect, "UPDATE `author` SET `author_id` = '$author' WHERE `author`.`author_id` = '$author' ");
    mysqli_query($connect, "UPDATE `booksauthor` SET `book_id` = '$book_id', `author_id` = '$author' WHERE `booksauthor`.`book_id` = '$book_id' ");
    mysqli_query($connect, "UPDATE `booksgenres` SET `book_id` = '$book_id', `book_genre_id` = '$genre' WHERE `booksgenres`.`book_id` = '$book_id' ");
    mysqli_query($connect, "UPDATE `genre` SET `book_genre_id` = '$genre' WHERE `genre`.`book_genre_id` = '$genre'");

    http_response_code(200);
    $res = [
        "status" => true,
        "message" => "Book update"
    ];
    echo json_encode($res);
}