<?php
    include('db_connector.php');

    session_start();

    session_regenerate_id();

    //schaut ob ein User eingeloggt ist

    if($_SESSION['admin'] == 1)
    {
        header('Location: index.php');
    }

    //setzt das archived auf 1 damit dieses als archiviert erkannt wird

    else
    {
        $id = $_GET['id'];
        
        $archived = 1;
        $query = "UPDATE todo set archived = $archived WHERE id_ToDo = $id";

        $delete = mysqli_query($conn, $query);

        header("Location: index.php");
    }
?>