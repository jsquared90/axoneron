<?php

function validateUser($connection)
{
    $user = 0;
    if (isset($_COOKIE['id']) && isset($_COOKIE['password']))
    {
        $id = $_COOKIE['id'];
        $password = $_COOKIE['password'];
        $user = getUserByFullValidation($id, $password, $connection);
    }
    
    if ($user == 0)
    {
        if (isset($_POST['validateEmail']))
        {
            $email = $_POST['emailValidate'];
            $user = getUserByEmail($email, $connection);
            if ($user == 0)
            {
                echo "<script type='text/javascript'>window.alert('Not a registered email address');</script>";
            }
        }
        else if (isset($_POST['completeValidation']))
        {
            $id = $_POST['id'];
            $password = $_POST['passwordValidate'];
            $user = getUserByFullValidation($id, $password, $connection);
            if ($user == 0)
            {
                echo "<script type='text/javascript'>window.alert('Login failure. Invalid password.');</script>";
            }
            else
            {
                setcookie('id', $id, time() + (86400 * 30), "/");
                setcookie('password', $password, time() + (86400 * 30), "/");
                //var_dump($user);
            }
            
        }
        else if (isset($_POST['registerUser']))
        {
            $id = $_POST['id'];
            $user = getUserById($id, $connection);
            if ($user == 0)
            {
                echo "<script type='text/javascript'>window.alert('Not a registered email address');</script>";
            }
        }
    }
    
    return $user;
}

