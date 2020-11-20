<?php

function connectToDB()
{
    if (isLocal())
    {
        // for local WAMP/MAMP installations
        $connection = new mysqli("localhost", "root", PASSWORD, DATABASE);
    }
    else
    {
        $connection = mysqli_connect("localhost" , DATABASE , PASSWORD, DATABASE);
    }
    if ($connection->connect_errno)
    {
        die("Database connection failed: " . $connection->connect_error);
    }
    return $connection;
}

function closeConnection($connection)
{
    mysqli_close($connection);
}

function isLocal()
{
    if ($_SERVER["HTTP_HOST"] == "localhost")
    {
        return true;
    }
    else
    {
        return false;
    }
}