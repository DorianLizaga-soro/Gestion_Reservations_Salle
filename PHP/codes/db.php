

/*********************************************************
Just keep this as the first copie of the Db or BDD to see with whichever we will end up going, Zoro and Doryann are currently on BDD and maintaining the workflow within thos parameters /// set up final conn parms for async
**********************************************************/


<?php

$conn = new mysqli("localhost", "root", "", "your_database");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>