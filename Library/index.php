<?php

$global_national_id = 0;

class Book
{
    public function __construct(
        protected string $title,
        protected string $writer,
        protected string $publish_date,
        protected int    $number
    )
    {
        $file = fopen('books.txt', 'ab');
        fwrite($file, "\n" . $this->title . "      " .
            $this->writer . "      " .
            $this->publish_date . "      " .
            $this->number);
        fclose($file);
    }

    public static function fetch(): array
    {
        $books = fileToArray('books.txt');
        $coLen = count($books[1]);
        foreach ($books as $book) {
            for ($i = 1; $i <= $coLen; $i++) {
                echo $book[$i] . "                              ";
            }
            echo "\n";
        }
        return $books;
    }


}


function fileToArray(string $file_name): array|string
{
    $file = fopen($file_name, 'rb');
    if ($file) {
        $row = 0;
        $col = 0;
        $arr = [];

        while (($line = fgets($file)) !== false) {
            $row++;
            $line = trim($line);
            $words = explode(' ', $line);
            foreach ($words as $word) {
                if ($word !== '') {
                    $col++;
                    $arr[$row][$col] = $word;
                }
            }
            $col = 0;
        }
        fclose($file);
        return $arr;

    }

    return "server error !";
}

function isAuth(string $nationalId, string $password, string $role): bool
{
    $array = fileToArray('auth_users.txt');
    $coLen = count($array[1]);

    $nationalId_col = 0;
    $password_col = 0;
    $role_col = 0;

    for ($i = 1; $i <= $coLen; $i++) {
        if ($array[1][$i] === 'national_id') {
            $nationalId_col = $i;
        }
        if ($array[1][$i] === 'password') {
            $password_col = $i;
        }
        if ($array[1][$i] === 'role') {
            $role_col = $i;
        }
    }

    $rowLen = count($array);
    for ($j = 2; $j <= $rowLen; $j++) {
        if ($array[$j][$nationalId_col] === $nationalId && $array[$j][$password_col] === $password) {
            return $array[$j][$role_col] === $role;
        }
    }
    return false;
}

function userAuth(): void
{
    $national_id = (string)readline("\n National ID : ");
    $password = (string)readline("\n Password : ");
    if (isAuth($national_id, $password, 'user')) {
        global $global_national_id;
        $global_national_id = $national_id;
        userFlow();
    } else {
        system('clear');
        echo "Wrong ! \n";
        userAuth();
    }
}


function userFlow()
{
    echo "Menu
      1-Show And Borrow Book
      2-My Request
      0-exit
      \n\n";

    $input = (string)readline("\n Choose : ");
    switch ($input) {
        case '1':
            requestBook();
            system('clear');
            break;
        case '0':
            system('exit');
            break;
        default:

    }
}

function requestBook(): void
{
    $books = Book::fetch();
    $requestBook = readline("\n Choose the Book (Enter title) : ");
    $coLen = count($books[1]);
    $rowLen = count($books);

    $titleCol = '';
    $numberCol = '';

    for ($i = 1; $i <= $coLen; $i++) {
        if ($books[1][$i] === 'title') {
            $titleCol = $i;
        }
        if ($books[1][$i] === 'number') {
            $numberCol = $i;
        }
    }

    for ($j = 2; $j < $rowLen; $j++) {
        echo "\n" . $books[$j][$titleCol];
        if ($books[$j][$titleCol] === $requestBook) {
            echo $books[$j][$titleCol];
            break;
        }
    }

    global $global_national_id;


    if ($books[$j][$numberCol] > 0 && isAllow($global_national_id)) {
        $file = fopen('users_books.txt', 'ab');
        fwrite($file, "\n" . $global_national_id . "        " . $books[$j][$titleCol] . "           " . 0);
        fclose($file);
        system('clear');
        echo "\n Your request has been registered .";
    } else {
        echo "\n Not allow";
    }

}

function isAllow($id): bool
{
    $users_Book = fileToArray('users_books.txt');
    $coLen = count($users_Book[1]);
    $rowLen = count($users_Book);
    $national_id = '';

    for ($i = 1; $i <= $coLen; $i++) {
        if ($users_Book[1][$i] === 'national_id') {
            $national_id = $i;
        }
    }

    for ($j = 2; $j < $rowLen; $j++) {
        echo "\n" . $users_Book[$j][$national_id];
        if ($users_Book[$j][$national_id] === $id) {
            echo $users_Book[$j][$national_id];

            return true;
        }
    }

    return false;
}

function operatorAuth(): void
{
    $national_id = (string)readline("\n National ID : ");
    $password = (string)readline("\n Password : ");
    if (isAuth($national_id, $password, 'operator')) {
        global $global_national_id;
        $global_national_id = $national_id;
        operatorFlow();
    } else {
        system('clear');
        echo "Wrong ! \n";
        operatorAuth();
    }
}

function operatorFlow(): void
{
    echo "Menu
      1-Show book list
      1-Show Users
      3-Create User
      4-Create book
      5-Request Borrow
      \n\n";

    $input = (string)readline("\n Choose : ");
    switch ($input) {
        case '1':
            showBooks();
            system('clear');

            break;
        case '2':
            showUsers();
            system('clear');
            break;
        case '3':
            createUser();
            system('clear');
            break;
        case '4':
            createBook();
            system('clear');
            break;
        case '5':
            requestHandling();
            system('clear');
        default:

    }
}


function requestHandling()
{
    $users_book = fileToArray('users_books.txt');
    $coLen = count($users_book[1]);
    foreach ($users_book as $user_book) {
        for ($i = 1; $i <= $coLen; $i++) {
            echo $user_book[$i] . "                      ";
        }
        echo "\n";
    }

    echo "
      1-Accept National ID
      2-Delete Request
      0-Back
      \n\n";

    $input = (string)readline("\n Choose : ");
    switch ($input) {
        case '1':
            acceptRequest();
            system('clear');
            break;
        case '2':
            deleteRequest();
            system('clear');
            break;
        case '0':
            system('clear');
            operatorFlow();
            break;
        default:
    }
}

function deleteRequest()
{

    $id = readline('Enter national id for delete  : ');
    $requests = fileToArray('users_books.txt');

    $coLen = count($requests[1]);
    $rowLen = count($requests);
    $national_id = 0;
    $status = 0;

    for ($i = 1; $i <= $coLen; $i++) {
        if ($requests[1][$i] === 'national_id') {
            $national_id = $i;
        }
    }

    for ($j = 2; $j <= $rowLen; $j++) {
        if ($requests[$j][$national_id] === $id) {
            unset($requests[$j]);
            updateFile('users_books.txt', $requests);
        } else {
            system('clear');
            echo "National Id Not Found";
            acceptRequest();
        }
    }


}

function acceptRequest()
{
    $id = readline('Enter national id for accept : ');
    $requests = fileToArray('users_books.txt');

    $coLen = count($requests[1]);
    $rowLen = count($requests);
    $national_id = 0;
    $status = 0;

    for ($i = 1; $i <= $coLen; $i++) {
        if ($requests[1][$i] === 'national_id') {
            $national_id = $i;
        }
    }


    for ($h = 1; $h <= $coLen; $h++) {
        if ($requests[1][$h] === 'status') {
            $status = $h;
        }
    }


    for ($j = 2; $j <= $rowLen; $j++) {
        if ($requests[$j][$national_id] === $id) {
            $requests[$j][$status] = 1;
            updateFile('users_books.txt', $requests);
        } else {
            system('clear');
            echo "National Id Not Found";
            acceptRequest();
        }
    }


}

function updateFile($address, $array)
{
    $file_clear = fopen($address, 'wb');
    fwrite($file_clear, '');
    fclose($file_clear);

    $file = fopen($address, 'ab');

    foreach ($array as $arr) {
        foreach ($arr as $ar) {
            fwrite($file, $ar . "          ");
        }
        fwrite($file, "\n");
    }
    fclose($file);
}


function showBooks(): void
{
    Book::fetch();
    echo "\n\n";
    operatorFlow();
}

function showUsers(): void
{
    $users = fileToArray('auth_users.txt');
    $coLen = count($users[1]);
    foreach ($users as $user) {
        for ($i = 1; $i <= $coLen; $i++) {
            echo $user[$i] . "                              ";
        }
        echo "\n";
    }
}

function createUser(): void
{
    system('clear');
    $name = readline("\n Name : ");
    $family = readline("\n Family : ");
    $password = readline("\n Password : ");
    $national_id = readline("\n National ID : ");
    try {
        $file = fopen('auth_users.txt', 'ab');
        fwrite($file, "\n" . $name . "     " .
            $family . "       " .
            $password . "     " .
            "  user       " .
            $national_id);
        fclose($file);
    } catch (Exception $e) {
        echo $e->getMessage();
        createUser();
    }

}


function createBook(): void
{
    system('clear');
    $title = readline("\n Title : ");
    $writer = readline("\n Writer : ");
    $publish_date = readline("\n Publish Date : ");
    $number = readline("\n Number : ");
    try {
        new Book($title, $writer, $publish_date, $number);
        system('clear');
        echo "Book created successfully ";
        operatorFlow();
    } catch (Exception $e) {
        echo $e->getMessage();
        createBook();
    }
}

function roleLogin(): void
{
    echo "Menu
      1-Login user
      2-Login Operator \n\n";

    $role = (int)readline('Choice : ');

    if ($role === 1 || $role === 2) {
        switch ($role) {
            case 1:
                system('clear');
                userAuth();
                break;
            case 2 :
                system('clear');
                operatorAuth();
                break;
            default :
                //set default
        }
    } else {
        system('clear');
        echo "               ***** Invalid Role **** \n";
        roleLogin();
    }

}


//roleLogin();
