<?php


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

function isAuth(string $username, string $password, string $role): bool
{
    $array = fileToArray('auth_users.txt');
    $coLen = count($array[1]);

    $username_col = 0;
    $password_col = 0;
    $role_col = 0;

    for ($i = 1; $i <= $coLen; $i++) {
        if ($array[1][$i] === 'user_name') {
            $username_col = $i;
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
        if ($array[$j][$username_col] === $username && $array[$j][$password_col] === $password) {
            if ($array[$j][$role_col] === $role) {
                return true;
            }
            return false;
        }
    }
    return false;
}

function userAuth()
{
    $user_name = (string)readline("\n UserName : ");
    $password = (string)readline("\n Password : ");
    if (isAuth($user_name, $password, 'user')) {
        echo 'Welcome';
    }else {
        echo '!';
    }
}

function operatorAuth()
{
    $user_name = (string)readline("\n UserName : ");
    $password = (string)readline("\n Password : ");
    if (isAuth($user_name, $password, 'user')) {
        echo 'Welcome';
    }else {
        echo '!';
    }
}

function roleLogin()
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
                echo "Login operator";
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


roleLogin();
