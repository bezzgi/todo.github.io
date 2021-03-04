<?php

    include('db_connector.php');
	
	session_start();

    session_regenerate_id();

    //schaut ob ein Admin eingeloggt ist

    if($_SESSION['admin'] == 0)
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

    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategorien verwalten</title>

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
                        <a class='nav-link dropdown-toggle' href='http://example.com' id='dropdown06' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>Erstellen</a>
                        <div class='dropdown-menu' aria-labelledby='dropdown06'>
                        <a class='dropdown-item' href='createuser.php'>Benutzer</a>
                        <a class='dropdown-item' href='createcategory.php'>Kategorien</a>
                        </div>
                    </li>
                    <li class='nav-item dropdown active'>
                        <a class='nav-link dropdown-toggle' href='http://example.com' id='dropdown06' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>Verwalten</a>
                        <div class='dropdown-menu' aria-labelledby='dropdown06'>
                        <a class='dropdown-item' href='manageuser.php'>Benutzer</a>
                        <a class='dropdown-item disabled' href='#'>Kategorien</a>
                        </div>
                    </li>
                    
                </ul>
                <form class='form-inline'>
                    
                    <div>
                        <a class='nav-link' href='logout.php'>Log Out</a>
                    </div>
                </form>
            </nav>

            <br><br>

            <h3>Kategorien verwalten</h3>

            <br>
        <div class="container">
            <div class="row justify-content-center">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="col-1" id="">ID</th>
                            <th class="col-8" id="">Kategorie</th>
                            <th class="col-1" id="">Löschen</th>
                        </tr>
                    </thead>

                    <?php 

                        //Query um alle Daten aus categories auszulesen

                        $query = "SELECT * FROM categories";
    
                        $result = $conn->query($query);
                        
                        //gibt die Kategorien_ID und Kategorie aus
                    
                        while($row = $result->fetch_assoc()): 
                    ?>

                    <tr>
                        <td><?php echo $row['id_cat']; ?></td>
                        <td><?php echo $row['category']; ?></td>
                        <td><a href="deletecategory.php?id=<?php echo $row['id_cat']; ?>"><i class="bi bi-trash"></i></a></td>
                    </tr>
                    <?php 

                        //Beendet die While-Loop und schliesst die Verbindung zur Datenbank

                        endwhile; 
                        $conn->close();
                    ?>
                </table>
            </div>
        </div>
</body>
</html>