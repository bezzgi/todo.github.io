<?php

    //holt mit GET Methode die Werte aus der URL

    $todo_id = $_GET['id'];
    $priority = $_GET['priority'];
    $archived = $_GET['archived'];
    $title = $_GET['title'];
    $description = $_GET['description'];
    $category_id = $_GET['category'];
    $end_date_string = $_GET['end_date'];

    include('db_connector.php');

    $error = '';
    $message = '';
	
	session_start();

    session_regenerate_id();

    //prüft ob der User eingeloggt ist

    if($_SESSION['admin'] == 1)
    {
        header('Location: index.php');
    }    

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

    //wird ausgeführt wenn Submit gedrückt wurde

    if(isset($_POST['submit']))
    {    
        //PRIORITÄT
        if(isset($_POST['priority']) && !empty($_POST['priority']))
        {
            $priority = $_POST['priority'];
        }

        //TITEL
        if(isset($_POST['title']) && strlen($_POST['title']) <= 45)
        {
            $title = $_POST['title'];
        }
        if(empty($_POST['title']))
        {
            $error .= "Bitte geben Sie eine Titel ein. <br>";
        }
        if(strlen($_POST['title']) > 45)
        {
            $error .= "Der Titel kann höchstens 45 Zeichen enthalten. <br>";
        }

        //BESCHREIBUNG
        if(isset($_POST['description']) && strlen($_POST['description']) <= 200)
        {
            $description = $_POST['description'];
        }
        if(empty($_POST['description']))
        {
            $error .= "Bitte geben Sie eine Beschreibung ein. <br>";
        }
        if(strlen($_POST['description']) > 200)
        {
            $error .= "Die Beschreibung kann höchstens 200 Zeichen enthalten. <br>";
        }

        //KATEGORIE
        if(isset($_POST['category']) && !empty($_POST['category']))
        {
            $category = $_POST['category'];
        }

        //DATUM FÄLLIG
        if(isset($_POST['date']) && !empty($_POST['date']))
        {
            $end_date = $_POST['date'];
        }
        else
        {
            $error .= "Geben Sie bitte ein Ablaufdatum an. <br>";
        }

        //DATUM ERSTELLT
        $create_date = date("Y-m-d");

        //USER_ID
        $query_user = "SELECT * FROM users where username like '%$username%'";
    
        $result_user = $conn->query($query_user);
                    
        $row_user = $result_user->fetch_assoc();
                    
        $users_id = $row_user['id_users'];

        //CAT_ID
        $cat_id = $_POST['category'];

        // wenn kein Fehler vorhanden ist, schreiben der Daten in die Datenbank
        if(empty($error))
        {
            $query_insert = "INSERT INTO todo (priority, archived, title, description, create_date, end_date, fk_users_id, fk_categories_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                
            $stmt = $conn->prepare($query_insert);
           
            $stmt->bind_param("iissssii",$priority, $archived, $title, $description, $create_date, $end_date, $users_id, $cat_id);
            
            $stmt->execute();
            
            $stmt->close();

            //Das bereits vorhandene TODO wird gelöscht

            header('Location: deletetodo.php?id='.$todo_id);
        }
    }
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ToDo bearbeiten</title>

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
<div class="container">
<br>
    <nav class="navbar navbar-expand-lg navbar-light bg-white rounded">
        <a class="navbar-brand"><?php print "<strong>$upperusername</strong>"; ?></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExample09" aria-controls="navbarsExample09" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarsExample09">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="index.php">ToDo's</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="createtodo.php">ToDo erstellen</a>
                </li>
                
            </ul>
            <form class="form-inline">
                <div class="input-group">
                </div>
                <div>
                    <a class="nav-link" href="logout.php">Log Out</a>
                </div>
            </form>
        </div>
    </nav>

    <div>
            <form method="POST">
                <br><br>

                <h3>ToDo bearbeiten</h3>

                <br>


                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text mb-2"><i class="bi bi-list-ol"></i></span>
                    </div>
                    <select name="priority" id="priority" class="form-control mb-2">
                        <?php
                            //schaut welcher Priority Wert aus der URL ausgelesen wurde und selektiert dem nach

                            if($priority == 1)
                            {
                                echo "<option selected value='1'>1</option>
                                <option value='2'>2</option>
                                <option value='3'>3</option>
                                <option value='4'>4</option>
                                <option value='5'>5</option>";
                            }
                            elseif($priority == 2)
                            {
                                echo "<option value='1'>1</option>
                                <option selected value='2'>2</option>
                                <option value='3'>3</option>
                                <option value='4'>4</option>
                                <option value='5'>5</option>";
                            }
                            elseif($priority == 3)
                            {
                                echo "<option value='1'>1</option>
                                <option value='2'>2</option>
                                <option selected value='3'>3</option>
                                <option value='4'>4</option>
                                <option value='5'>5</option>";
                            }
                            elseif($priority == 4)
                            {
                                echo "<option value='1'>1</option>
                                <option value='2'>2</option>
                                <option value='3'>3</option>
                                <option selected value='4'>4</option>
                                <option value='5'>5</option>";
                            }
                            else
                            {
                                echo "<option value='1'>1</option>
                                <option value='2'>2</option>
                                <option value='3'>3</option>
                                <option value='4'>4</option>
                                <option selected value='5'>5</option>";
                            }
                        ?>
                        
                    </select>
                </div>

                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text mb-2"><i class="bi bi-pencil-square"></i></span>
                    </div>
                    <input type="text" placeholder="Titel" name="title" value="<?php echo "$title"; ?>" class="form-control mb-2" maxlength="45">
                </div>

                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text mb-2"><i class="bi bi-textarea-t"></i></span>
                    </div>
                    <textarea placeholder="Beschreibung" name="description" class="form-control mb-2"><?php echo "$description"; ?></textarea>
                </div>

                <?php 

                    //liest die Kategorien aus welche einem bestimmten Benutzer zugeteilt wurden

                    $query_user = "SELECT * FROM users where username like '%$username%'";

                    $result_user = $conn->query($query_user);
            
                    $row_user = $result_user->fetch_assoc();
            
                    $users_id = $row_user['id_users'];

                    $query_cat = "select * from categories left join users_has_categories on categories_id_cat = id_cat where users_id_users = $users_id";

                    $result_cat = $conn->query($query_cat); 
                ?>
                

                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text mb-2"><i class="bi bi-pin-fill"></i></span>
                    </div>
                    <select name="category" id="category" class="form-control mb-2">
                    <?php

                        //gibt die Kategorien aus welche einem bestimmten Benutzer zugeteilt wurden

                        while($row_cat = $result_cat->fetch_assoc())
                        {
                            $category = $row_cat['category'];
                            $id = $row_cat['id_cat'];

                            echo "<option value='$id'";

                            if($category_id == $id)
                            {
                                echo " selected";
                            }
                            
                            echo ">$category</option>";

                            
                        } 
                    ?>   
                    </select>
                </div>

                <div class="input-group">
                <div class="input-group-prepend">
                        <span class="input-group-text mb-4"><i class="bi bi-calendar"></i></span>
                    </div>
                    <input type="date" name="date" class="form-control mb-4" value="<?php echo "$end_date_string"; ?>">
                </div>
                <br>
                

                <?php

                    //Errors und Nachrichten werden ausgegeben

                    if(!empty($message))
                    {
                        echo "<div class='alert alert-info' role='alert'>
                            $message
                        </div>";
                    }
                    if(!empty($error))
                    {
                        echo "<div class='alert alert-danger' role='alert'>
                            $error
                        </div>";
                    }
                ?>
                <div class="row">
                    <div class="col">
                        <input class="btn btn-primary w-100" type="submit" class="submit" value="ToDo bearbeiten" name="submit">
                    </div>
                    <div class="col">
                        <input class="btn btn-primary w-100" type="reset" class="submit" value="Löschen">                    
                    </div>
                </div>

                <br><br><br><br>
                
            </form>
        </div>
</body>
</html>

<?php
    $conn->close();
?>