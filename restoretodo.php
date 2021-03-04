<?php
    include('db_connector.php');

    session_start();

    session_regenerate_id();

    //schaut ob ein Admin eingeloggt ist

    if($_SESSION['admin'] == 0)
    {
        header('Location: index.php');
    }

    //setzt das archived auf 0 damit dieses als nicht archiviert erkannt wird

    else
    {
        $id = $_GET['id'];
        
        $archived = 0;
        $query = "UPDATE todo set archived = $archived WHERE id_ToDo = $id";

        $delete = mysqli_query($conn, $query);

        header("Location: index.php");
    }
?>