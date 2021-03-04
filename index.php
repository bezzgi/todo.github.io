<?php

include('db_connector.php');

session_start();

session_regenerate_id();

//prüft ob eine Session vorhanden ist, wenn nicht wird man auf die Login Seite geleidet

if(!isset($_SESSION['username']))
{
    header("Location: login.php");
}

//liest den Benutzernamen aus der Session

if(isset($_SESSION['username']) && !empty($_SESSION['bool'] === true))
{
    $username = $_SESSION['username'];
    $upperusername = strtoupper($username);
}

//USER_ID
$query_user = "SELECT * FROM users where username like '%$username%'";
    
$result_user = $conn->query($query_user);
            
$row_user = $result_user->fetch_assoc();
            
$users_id = $row_user['id_users'];


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index</title>

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <!-- Popper JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>

    <!-- Latest compiled JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script> 

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">

</head>
<body class="bg-light">
<?php 

//normale Benutzerseite
if($_SESSION['admin'] == 0)
{
    echo"<div class='container'>
    <br>
    <nav class='navbar navbar-expand-lg navbar-light bg-white rounded'>
        <a class='navbar-brand'><strong>$upperusername</strong></a>
        <button class='navbar-toggler' type='button' data-toggle='collapse' data-target='#navbarsExample09' aria-controls='navbarsExample09' aria-expanded='false' aria-label='Toggle navigation'>
          <span class='navbar-toggler-icon'></span>
        </button>
        <div class='collapse navbar-collapse' id='navbarsExample09'>
            <ul class='navbar-nav mr-auto'>
                <li class='nav-item active'>
                    <a class='nav-link disabled text-secondary' href='#'>ToDo's</a>
                </li>
                <li class='nav-item active'>
                    <a class='nav-link' href='createtodo.php'>ToDo erstellen</a>
                </li>
            </ul>
            <form class='form-inline'>
                <div class='input-group'>
                    <input type='text' name='search_text' class='form-control' placeholder='Search'>
                    <div class='input-group-append'>
                        <button type='submit' name='search_but' value='1' class='btn btn-secondary '><em class='bi bi-search'></em></button>
                    </div>
                </div>
                <div>
                    <a class='nav-link' href='logout.php'>Log Out</a>
                </div>
            </form>
        </div>
    </nav>

    <br><br>

            <h3>ToDo's</h3>

            <br>
        <div class='container'>
            <div class='row justify-content-center'>
                <table class='table'>
                    <thead>
                        <tr style='font-size: 12px;'>
                            <th class='col-1' id=''>Priorität</th>
                            <th class='col-2' id=''>Kategorie</th>
                            <th class='col-3' id=''>Titel</th>
                            <th class='col-1' id=''>Erstellt</th>
                            <th class='col-2' id=''>Fällig</th>
                            <th class='col-1' id=''>Bearbeiten</th>
                            <th class='col-1' id=''>Archivieren</th>
                            <th class='col-1' id=''>Löschen</th>
                        </tr>
                    </thead>";

                        //setzt bestimmte Variabeln, für die Paginierung         

                        $page = 1;

                        if(isset($_GET['page']))
                        {
                            $page = $_GET['page'];
                        }

                        $page = max(1, $page);

                        $limit = 7;

                        $offset = ($page-1)*$limit;

                        //erkennt ob was in die Suchleiste eingegeben wurde und was  
                    
                        if(!empty($_GET['search_but']) && $_GET['search_but'] == 1)
                        {
                            $search_text = $_GET['search_text']; 

                            //führt den bestimmten Select-Befehl aus, je nachdem ob etwas eingegeben wurde

                            if(!empty($_GET['search_text']))
                            {
                                //Query um die Anzahl TODOS auszulesen

                                $query_count = "select count(*) from todo left join users_has_categories on categories_id_cat = fk_categories_id where users_id_users = $users_id and title like '%$search_text%' or users_id_users = $users_id and description like '%$search_text%' order by priority desc, end_date asc";
                                $result_count = $conn->query($query_count);
                                $row_count = $result_count->fetch_assoc();
                                $maxEntries = $row_count['count(*)'];

                                $page = 1;

                                if(isset($_GET['page']))
                                {
                                    $page = $_GET['page'];
                                }

                                $page = max(1, $page);

                                $maxPages = (int)ceil($maxEntries/$limit);

                                $offset = ($page-1)*$limit;

                                //Query um die TODOS auszulesen

                                $query = "select * from todo left join users_has_categories on categories_id_cat = fk_categories_id where users_id_users = $users_id and title like '%$search_text%' or users_id_users = $users_id and description like '%$search_text%' order by priority desc, end_date asc limit $offset, $limit"; //Im phpMyAdmin gab es ein Fehler im PHP 8.0 der nicht immer richtig nach Priorität und Datum Sortieren lässt.
                            }
                            else
                            {
                                //Query um die Anzahl TODOS auszulesen

                                $query_count = "select count(*) from todo left join users_has_categories on categories_id_cat = fk_categories_id where users_id_users = $users_id and archived = 0 order by priority desc, end_date asc";
                                $result_count = $conn->query($query_count);
                                $row_count = $result_count->fetch_assoc();
                                $maxEntries = $row_count['count(*)'];

                                $page = 1;

                                if(isset($_GET['page']))
                                {
                                    $page = $_GET['page'];
                                }

                                $page = max(1, $page);

                                $maxPages = (int)ceil($maxEntries/$limit);

                                $offset = ($page-1)*$limit;

                                //Query um die TODOS auszulesen

                                $query = "select * from todo left join users_has_categories on categories_id_cat = fk_categories_id where users_id_users = $users_id and archived = 0 order by priority desc, end_date asc limit $offset, $limit";
                            }
                        }
                        else
                        {
                            //Query um die Anzahl TODOS auszulesen

                            $query_count = "select count(*) from todo left join users_has_categories on categories_id_cat = fk_categories_id where users_id_users = $users_id and archived = 0 order by priority desc, end_date asc";
                            $result_count = $conn->query($query_count);
                            $row_count = $result_count->fetch_assoc();
                            $maxEntries = $row_count['count(*)'];

                            $page = 1;

                            if(isset($_GET['page']))
                            {
                                $page = $_GET['page'];
                            }

                            $page = max(1, $page);

                            $maxPages = (int)ceil($maxEntries/$limit);

                            $offset = ($page-1)*$limit;

                            //Query um die TODOS auszulesen

                            $query = "select * from todo left join users_has_categories on categories_id_cat = fk_categories_id where users_id_users = $users_id and archived = 0 order by priority desc, end_date asc limit $offset, $limit";
                        }
    
                        $result = $conn->query($query);

                        //gibt die TODOS aus je nach Query anders
                    
                        while($row = $result->fetch_assoc()): 
                    
                        

                    echo "<tr style='font-size: 11px;'>
                        <td>";echo $row['priority']; echo"</td>
                        <td>";

                        $fk_categories_id = $row['fk_categories_id'];
                        
                        $query_cat = "SELECT category FROM categories where id_cat = $fk_categories_id";
    
                        $result_cat = $conn->query($query_cat);
            
                        $row_cat = $result_cat->fetch_assoc();
            
                        $category = $row_cat['category'];
                        
                        echo $category; echo"</td>
                        <td>";

                        //prüft ob ein TODO archiviert ist und setzt ein Tag vor den Titel

                        if($row['archived'] == 1)
                        {
                            echo "<em class='bi bi-archive-fill'></em> ";
                            echo $row['title'];
                        }
                        else
                        {
                            echo $row['title'];
                        }
                        
                         echo"</td> 
                        <td>";echo $row['create_date']; echo"</td>        
                        <td>";

                        //berechnet Differenz aus den Datums und gibt sie in Farben aus

                        $create_date = date_create($row['create_date']);
                        $end_date = date_create($row['end_date']);

                        $diff_date = date_diff($create_date, $end_date);

                        if($create_date < $end_date)
                        {
                            echo "<a style='color: green;'>".$diff_date->format('in %a Tagen')."</a>"; 
                        }
                        elseif($create_date == $end_date)
                        {
                            echo "<a style='color: orange;'>".$diff_date->format('heute')."</a>"; 
                        }
                        else
                        {
                            echo "<a style='color: red;'>".$diff_date->format('seit %a Tagen')."</a>";
                        }

                        //Variabeln für die GET Methode erstellen

                        $todo_id = $row['id_ToDo'];
                        $priority = $row['priority'];
                        $archived = $row['archived'];
                        $title = $row['title'];
                        $description = $row['description'];
                        $end_date_string = $row['end_date'];
                        
                        //links für Bearbeitung, Archivierung und das Löschen
                        
                        echo"</td>";

                        if($row['fk_users_id'] != $users_id)
                        {
                            echo "<td><em class='bi bi-dash'></em></td>";
                            echo "<td><em class='bi bi-dash'></em></td>";
                            echo "<td><em class='bi bi-dash'></em></td>";
                        }
                        else
                        {
                            echo "<td><a href='edittodo.php?id=$todo_id&priority=$priority&archived=$archived&title=$title&description=$description&category=$fk_categories_id&end_date=$end_date_string"; echo"'><i class='bi bi-pencil'></i></a></td>
                            <td><a href='archivetodo.php?id=";echo $row['id_ToDo']; echo"'><i class='bi bi-archive'></i></a></td>
                            <td><a href='deletetodo.php?id=";echo $row['id_ToDo']; echo"'><i class='bi bi-trash'></i></a></td>";
                        }
                    echo "</tr>";
                     
                        endwhile; 
                        $conn->close();
                    echo"
                </table>";

                //Liste für die Knöpfe der Paginierung

                if($row_count['count(*)'] > 0)
                {
                    echo "<ul class='pagination'>";

                    echo "<li class='page-item ";

                    //wird deaktiviert wenn man auf der ersten Seite ist
                    
                    if($_GET['page'] == 1)
                    {
                        echo "disabled";
                    }
                    
                    echo "'><a class='page-link' href='?page=".($page-1);

                    //wenn etwas gesucht wird, wird der Suchtext übernommen
                    
                    if(isset($_GET['search_text']) && isset($_GET['search_but']))
                    {
                        echo "&search_text=$search_text&search_but=1";
                    }

                    echo "'><</a></li>";   
                
                    //wird ausgegeben, je nachdem wieviele Seiten es gibt

                    for($pageNumber = 1; $pageNumber<=$maxPages;$pageNumber++)
                    {
                        echo "<li class='page-item "; 

                        //Die aktive Seite wird blau markiert
                        
                        if($pageNumber == $_GET['page'])
                        {
                            echo "active";
                        }
                        
                        echo "'><a class='page-link' href='?page=".$pageNumber;

                        //wenn etwas gesucht wird, wird der Suchtext übernommen
                        
                        if(isset($_GET['search_text']) && isset($_GET['search_but']))
                        {
                            echo "&search_text=$search_text&search_but=1";
                        }

                        echo "'>".$pageNumber."</a></li>";   
                    }
                
                    echo "<li class='page-item ";

                    //wird deaktiviert wenn man auf der letzten Seite ist
                    
                    if($_GET['page'] == $maxPages)
                    {
                        echo "disabled";
                    }
                    
                    echo "'><a class='page-link' href='?page=".($page+1);

                    //wenn etwas gesucht wird, wird der Suchtext übernommen
                    
                    if(isset($_GET['search_text']) && isset($_GET['search_but']))
                    {
                        echo "&search_text=$search_text&search_but=1";
                    }
                    
                    echo "'>></a></li></ul>";  
                }
                else
                {
                    if(!empty($_GET['search_text']))
                    {
                        print "Für '$search_text' gibt es keine Einträge.";
                    }
                    else
                    {
                        print "Es gibt keine Einträge.";
                    }
                }

            echo "</div>
        </div>";
}

//Adminseite
else
{
    print "<div class='container'>
    <br>
        <nav class='navbar navbar-expand-lg navbar-light bg-white rounded'>
            <a class='navbar-brand'><strong>$upperusername</strong></a>
            <a href='index.php' class='nav-link disabled'>Archiv</a>
            <button class='navbar-toggler' type='button' data-toggle='collapse' data-target='#navbarsExample09' aria-controls='navbarsExample09' aria-expanded='false' aria-label='Toggle navigation'>
              <span class='navbar-toggler-icon'></span>
            </button>
            <div class='collapse navbar-collapse' id='navbarsExample09'>
                <ul class='navbar-nav mr-auto'>
                    <li class='nav-item dropdown active'>
                        <a class='nav-link dropdown-toggle' href='http://example.com' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>Erstellen</a>
                        <div class='dropdown-menu' aria-labelledby='dropdown06'>
                        <a class='dropdown-item' href='createuser.php'>Benutzer</a>
                        <a class='dropdown-item' href='createcategory.php'>Kategorien</a>
                        </div>
                    </li>
                    <li class='nav-item dropdown active'>
                        <a class='nav-link dropdown-toggle' href='http://example.com' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>Verwalten</a>
                        <div class='dropdown-menu' aria-labelledby='dropdown06'>
                        <a class='dropdown-item' href='manageuser.php'>Benutzer</a>
                        <a class='dropdown-item' href='managecategory.php'>Kategorien</a>
                        </div>
                    </li>
                </ul>
                <form class='form-inline'>
                    
                    <div>
                        <a class='nav-link' href='logout.php'>Log Out</a>
                    </div>
                </form>
            </div>
        </nav>

            <br><br>

            <h3>Archivierte Einträge</h3>

            <br>
        <div class='container'>
            <div class='row justify-content-center'>
                <table class='table'>
                    <thead>
                        <tr style='font-size: 12px;'>
                            <th class='col-1' id=''>Benutzer</th>
                            <th class='col-1' id=''>Kategorie</th>
                            <th class='col-3' id=''>Titel</th>
                            <th class='col-1' id=''>Erstellt</th>
                            <th class='col-2' id=''>Fällig</th>
                            <th class='col-1' id=''>Wiederherstellen</th>
                            <th class='col-1' id=''>Löschen</th>
                        </tr>
                    </thead>";

                        $page = 1;

                        if(isset($_GET['page']))
                        {
                            $page = $_GET['page'];
                        }

                        $page = max(1, $page);

                        $limit = 7;

                        $offset = ($page-1)*$limit;

                        $query_count = "select count(*) from todo where archived = 1 order by end_date asc";
                        $result_count = $conn->query($query_count);
                        $row_count = $result_count->fetch_assoc();
                        $maxEntries = $row_count['count(*)'];

                        $maxPages = (int)ceil($maxEntries/$limit);

                        //gibt alle archivierten TODOS aus
                    
                        $query = "select * from todo where archived = 1 order by end_date asc limit $offset, $limit";
    
                        $result = $conn->query($query);

                        while($row = $result->fetch_assoc()): 

                            //sucht den Benutzer, welcher das TODO archiviert hat

                            $fk_users_id = $row['fk_users_id'];

                            $query_user = "select username from users where id_users = $fk_users_id";

                            $result_user = $conn->query($query_user);
            
                            $row_user = $result_user->fetch_assoc();
            
                            $user = $row_user['username'];

                    echo "<tr style='font-size: 11px;'>
                        <td>";echo $user; echo"</td>
                        <td>";

                        //liest die Kategorie aus

                        $fk_categories_id = $row['fk_categories_id'];
                        
                        $query_cat = "SELECT category FROM categories where id_cat = $fk_categories_id";
    
                        $result_cat = $conn->query($query_cat);
            
                        $row_cat = $result_cat->fetch_assoc();
            
                        $category = $row_cat['category'];
                        
                        echo $category; echo"</td>
                        <td>";

                        //prüft ob ein TODO archiviert ist und setzt ein Tag vor den Titel

                        if($row['archived'] == 1)
                        {
                            echo "<em class='bi bi-archive-fill'></em> ";
                            echo $row['title'];
                        }
                        else
                        {
                            echo $row['title'];
                        }
                        
                        echo"</td> 
                        <td>";echo $row['create_date']; echo"</td>
                        <td>";

                        //berechnet Differenz aus den Datums und gibt sie in Farben aus

                        $create_date = date_create($row['create_date']);
                        $end_date = date_create($row['end_date']);

                        $diff_date = date_diff($create_date, $end_date);

                        if($create_date < $end_date)
                        {
                            echo "<a style='color: green;'>".$diff_date->format('in %a Tagen')."</a>"; 
                        }
                        elseif($create_date == $end_date)
                        {
                            echo "<a style='color: orange;'>".$diff_date->format('heute')."</a>"; 
                        }
                        else
                        {
                            echo "<a style='color: red;'>".$diff_date->format('seit %a Tagen')."</a>";
                        }

                        //links für Wiederherstellung und das Löschen
                        
                        echo"</td> 

                        <td><a href='restoretodo.php?id=";echo $row['id_ToDo']; echo"'><i class='bi bi-archive'></i></a></td>
                        <td><a href='deletetodo.php?id=";echo $row['id_ToDo']; echo"'><i class='bi bi-trash'></i></a></td>
                    </tr>";
                     
                        endwhile; 
                        $conn->close();
                    echo"
                </table>";


                if($row_count['count(*)'] > 0)
                {
                    echo "<ul class='pagination'>";

                    echo "<li class='page-item ";

                    //wird deaktiviert wenn man auf der ersten Seite ist
                    
                    if($_GET['page'] == 1)
                    {
                        echo "disabled";
                    }
                    
                    echo "'><a class='page-link' href='?page=".($page-1);

                    //wenn etwas gesucht wird, wird der Suchtext übernommen
                    
                    if(isset($_GET['search_text']) && isset($_GET['search_but']))
                    {
                        echo "&search_text=$search_text&search_but=1";
                    }

                    echo "'><</a></li>";   
                
                    //wird ausgegeben, je nachdem wieviele Seiten es gibt

                    for($pageNumber = 1; $pageNumber<=$maxPages;$pageNumber++)
                    {
                        echo "<li class='page-item "; 

                        //Die aktive Seite wird blau markiert
                        
                        if($pageNumber == $_GET['page'])
                        {
                            echo "active";
                        }
                        
                        echo "'><a class='page-link' href='?page=".$pageNumber;

                        //wenn etwas gesucht wird, wird der Suchtext übernommen
                        
                        if(isset($_GET['search_text']) && isset($_GET['search_but']))
                        {
                            echo "&search_text=$search_text&search_but=1";
                        }

                        echo "'>".$pageNumber."</a></li>";   
                    }
                
                    echo "<li class='page-item ";

                    //wird deaktiviert wenn man auf der letzten Seite ist
                    
                    if($_GET['page'] == $maxPages)
                    {
                        echo "disabled";
                    }
                    
                    echo "'><a class='page-link' href='?page=".($page+1);

                    //wenn etwas gesucht wird, wird der Suchtext übernommen
                    
                    if(isset($_GET['search_text']) && isset($_GET['search_but']))
                    {
                        echo "&search_text=$search_text&search_but=1";
                    }
                    
                    echo "'>></a></li></ul>";  
                }
                else
                {
                    print "Es gibt keine archivierten Einträge.";
                }

            echo "</div>
        </div>";
    }
?>
</body>
</html>