<?php

    include('db_connector.php');

    // Initialisierung
    $error = $message =  '';
    $admin = $username = $password = '';
	
	session_start();

    session_regenerate_id();

    //schaut ob ein Admin eingeloggt ist

    if($_SESSION['admin'] == 0)
    {
        header('Location: index.php');
    }

    //wird ausgeführt wenn Submit gedrückt wurde

    if(isset($_POST['submit']))
    {
        //setzt Variabel wenn der Input nicht leer ist und unter oder gleich 30 Zeichen hat, wenn nicht gibt es einen Error

        if(isset($_POST['category']) && !empty($_POST['category']) && strlen($_POST['category']) <= 30)
        {
            $category = $_POST['category'];
        }
        else
        {
            $error .= "Geben Sie eine Kategorie ein, welche unter 30 Zeichen lang ist.";
        }
    
    
        // wenn kein Fehler vorhanden ist, schreiben der Daten in die Datenbank

        if(empty($error))
        {
            $query = "INSERT INTO categories (category) VALUES (?)";
             
            $stmt = $conn->prepare($query);
                
            $stmt->bind_param("s", $category);
                
            $stmt->execute();
                
            $stmt->close();

            $message .= "Kategorie wurde erstellt.";
        }
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategorie erstellen</title>

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <!-- Popper JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>

    <!-- Latest compiled JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script> 

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>


</head>
<body class="bg-light">
<div class='container'>
    <br>
        <nav class='navbar navbar-expand-lg navbar-light bg-white rounded'>
            <a class='navbar-brand'><?php print "<strong>$upperusername</strong>"; ?></a>
            <a href="index.php" class='nav-link' style="color: black;">Archiv</a>
            <button class='navbar-toggler' type='button' data-toggle='collapse' data-target='#navbarsExample09' aria-controls='navbarsExample09' aria-expanded='false' aria-label='Toggle navigation'>
              <span class='navbar-toggler-icon'></span>
            </button>
            <div class='collapse navbar-collapse' id='navbarsExample09'>
                <ul class='navbar-nav mr-auto'>
                    <li class='nav-item dropdown active'>
                        <a class='nav-link dropdown-toggle' href='http://example.com' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>Erstellen</a>
                        <div class='dropdown-menu' aria-labelledby='dropdown06'>
                        <a class='dropdown-item' href='createuser.php'>Benutzer</a>
                        <a class='dropdown-item disabled' href='#'>Kategorien</a>
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

            <h3>Neue Kategorie erstellen</h3>

            <br>
            <div class="row">
            <form action="createcategory.php" method="POST" class="col-sm-12">

                
                

                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text mb-3"><i class="bi bi-pencil"></i></span>
                    </div>
                    <input type="text" placeholder="Kategorie" name="category" class="form-control mb-3" maxlength="30">
                </div>

                <br>

                <h5><strong>Kategorien</strong></h5>

                <?php

                    //liest die Daten aus categories aus und zählt sie
                    
                    $query = "SELECT * FROM categories";
                    $query_count = "SELECT count(*) FROM categories";
    
                    $result = $conn->query($query);
                    $result_count = $conn->query($query_count);

                    $row_count = $result_count->fetch_assoc();

                    //gibt bereits erstellte Kategorien aus

                    while($row = $result->fetch_assoc())
                    {
                        $category = $row['category'];
                        $id_cat = $row['id_cat'];
                        echo "- ".$category."<br>";
                    }

                    //wenn es noch keine Einträge gibt wird dies ausgegeben

                    if($row_count['count(*)'] == 0)
                    {
                        echo "Es gibt noch keine Kategorien. <br>";
                    }
                    
                    //verbindung zur Datenbank wird geschlossen

                    $conn->close();
                ?>

                

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
                        <input class="btn btn-primary w-100"type="submit" class="submit" value="Kategorie erstellen" name="submit">
                    </div>
                    <div class="col">
                        <input class="btn btn-primary col w-100" type="reset" class="submit" value="Löschen" >
                    </div>
                </div>
                </form>

                    

            </div>
        </div>

        

</body>
</html>