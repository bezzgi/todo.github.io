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

        //Löscht alle TODOS und Kategorieeinträge wenn eine Kategorie gelöscht wurde
        
        $query_u_has_c = "DELETE FROM users_has_categories WHERE categories_id_cat = $id";
        $query_category = "DELETE FROM categories WHERE id_cat = $id";
        $query_todo = "DELETE FROM todo where fk_categories_id = $id";

        $delete_u_has_c = mysqli_query($conn, $query_u_has_c);  
        $delete_todo = mysqli_query($conn, $query_todo); 
        $delete_category = mysqli_query($conn, $query_category);  
        
        header('Location: managecategory.php');
    }
?>