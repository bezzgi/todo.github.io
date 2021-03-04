<?php
    include('db_connector.php');

    session_start();

    session_regenerate_id();

    //löscht das TODO

    $id = $_GET['id'];
    $query = "DELETE FROM todo WHERE id_ToDo = $id";
    $delete = mysqli_query($conn, $query);

    header("Location: index.php");
?>