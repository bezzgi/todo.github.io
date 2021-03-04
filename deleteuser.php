<?php
    include('db_connector.php');

    session_start();

    session_regenerate_id();

    //schaut ob ein Admin eingeloggt ist

    if($_SESSION['admin'] == 0)
    {
        header('Location: index.php');
    }
    else
    {
        $id = $_GET['id'];

        //Löscht alle TODOS und Usereinträge wenn ein User gelöscht wurde

        $query_todo = "DELETE FROM todo WHERE fk_users_id = $id";
        $query_users = "DELETE FROM users WHERE id_users = $id";
        $query_u_has_c = "DELETE FROM users_has_categories WHERE users_id_users = $id";

        $delete_todo = mysqli_query($conn, $query_todo);
        $delete_u_has_c = mysqli_query($conn, $query_u_has_c);
        $delete_user = mysqli_query($conn, $query_users);

        header('Location: manageuser.php');
    }
?>