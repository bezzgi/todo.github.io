<?php

    include('db_connector.php');

        // Initialisierung
        $error = $message =  '';
        $admin = $username = $password = '';
	
	    session_start();

        session_regenerate_id();

        //prüft ob eine Session vorhanden ist, wenn nicht wird man auf die Login Seite geleidet

        if(!isset($_SESSION['username']))
        {
            header("Location: login.php");
        }

        //schaut ob ein Admin eingeloggt ist

        if($_SESSION['admin'] == 0)
        {
            header('Location: index.php');
        }

        //liest den Benutzernamen aus der Session

        if(isset($_SESSION['username']) && !empty($_SESSION['bool'] === true))
        {
            $username_sesh = $_SESSION['username'];
            $upperusername = strtoupper($username_sesh);
        }

        //wird ausgeführt wenn Submit gedrückt wurde

        if(isset($_POST['submit']))
        {
            //setzt 1 oder 0 für Admin oder User
            if(isset($_POST['admin']) && !empty(trim($_POST['admin'])) && strlen(trim($_POST['admin'])) <= 1)
            {
                $admin = 1;
            }
            else
            {
                $admin = 0;
            }
    
            // benutzername vorhanden, mindestens 6 Zeichen und maximal 30 zeichen lang
            if(isset($_POST['username']) && !empty(trim($_POST['username'])) && strlen(trim($_POST['username'])) <= 30)
            {
                $username = trim($_POST['username']);
            }
            
            // entspricht der benutzername unseren vogaben (minimal 6 Zeichen, Gross- und Kleinbuchstaben, Zahlen)
            if(!empty($_POST['username']))
            {
                if(!preg_match("/[0-9a-zA-Z]{6,}/", $username))
                {
                    $error .= "Der Benutzername entspricht nicht dem geforderten Format.<br />";
                }
            }
            else
            {
                $error .= "Bitte geben Sie einen Benutzernamen an.<br />";
            }
                
    
            // passwort vorhanden, mindestens 8 Zeichen
            if(isset($_POST['password']) && !empty(trim($_POST['password'])))
            {
                $password_unhashed = trim($_POST['password']);
                $password = password_hash($password_unhashed, PASSWORD_DEFAULT);
            }
    
            //entspricht das passwort unseren vorgaben? (minimal 8 Zeichen, Zahlen, Buchstaben, mindestens ein Gross- und ein Kleinbuchstabe)
            if(!empty($_POST['password']))
            {
                if(!preg_match("/(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}/", $password))
                {
                    $error .= "Das Passwort entspricht nicht dem geforderten Format.<br />";
                }
            }
            else
            {
                $error .= "Bitte geben Sie ein Passwort an.<br />";
            }
    
            //wurde beim Erstellen eines Users mindestens eine Kategorie gewählt, beim Admin muss keine gewählt sein
            if(!isset($_POST['category']))
            {
                if($admin == 1)
                {
                    $error .= "";
                }
                else
                {
                    $error .= "Es muss mindestens eine Kategorie ausgewählt werden.";
                }
            }

            // wenn kein Fehler vorhanden ist, schreiben der Daten in die Datenbank

            if(empty($error))
            {

                //liest die Kategorien aus

                $query = "SELECT * FROM categories";
    
                $result = $conn->query($query);

                $row = $result->fetch_assoc();

                if($row != null)
                {
                    $id_cat = $row['id_cat'];
                }

                //Schreiben der Daten in die Datenbank nur wenn eine Kategorie für eien Benutzer ausgewählt wurde

                    if($admin == 0)
                    {
                        if(isset($_POST['category']))
                        {

                            //Schreibt Admin. Benutzernamen und Passwort in die Datenbank

                            $query = "INSERT INTO users (admin, username, password) VALUES (?, ?, ?)";
                
                            $stmt = $conn->prepare($query);
                
                            $stmt->bind_param("iss", $admin, $username, $password);
                
                            $stmt->execute();
                
                            $stmt->close();

                            //liest die User_ID aus

                            $query_user = "SELECT * FROM users where username like '%$username%'";
    
                            $result_user = $conn->query($query_user);

                            $row_user = $result_user->fetch_assoc();

                            $id_users = $row_user['id_users'];

                            //Schreibt Array mit Kategorie ind users_has_categories

                            foreach($_POST['category'] as $value)
                            {
                                $query_u_has_c = "INSERT INTO users_has_categories (users_id_users, categories_id_cat) VALUES (?, ?)";

                                $stmt_u_has_c = $conn->prepare($query_u_has_c);
                            
                                $stmt_u_has_c->bind_param("ii", $id_users ,$value);
                        
                                $stmt_u_has_c->execute();
                        
                                $stmt_u_has_c->close();
                            }

                            $message .= "Benutzer wurde erstellt.";
                        }
                        else
                        {
                            echo "Es muss mindestens eine Kategorie ausgewählt werden.";
                        }
                    }
                    
                    if($admin == 1)
                    {
                        if(!isset($_POST['category']))
                        {
                            //Schreibt Admin. Benutzernamen und Passwort in die Datenbank

                            $query = "INSERT INTO users (admin, username, password) VALUES (?, ?, ?)";
                
                            $stmt = $conn->prepare($query);
                
                            $stmt->bind_param("iss", $admin, $username, $password);
                
                            $stmt->execute();
                
                            $stmt->close();

                            $message .= "Benutzer wurde erstellt.";
                        }
                        else
                        {
                            $error .= "Admins dürfen keine Kategorie haben.";
                        }
                    }
                }
            }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Benutzer erstellen</title>

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
                        <a class='dropdown-item disabled' href='#'>Benutzer</a>
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

            <h3>Neuen Benutzer erstellen</h3>

            <br>
            <div class="row">
            <form action="createuser.php" method="POST" class="col-sm-12">

                
                

                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text mb-3"><i class="bi bi-person-fill"></i></span>
                    </div>
                    <input pattern="[0-9a-zA-Z]{6,}" type="text" placeholder="Benutzername (mind. 6 Zeichen)" name="username" class="form-control mb-3" required>
                </div>

                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text mb-3"><i class="bi bi-lock-fill"></i></span>
                    </div>
                    <input pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" type="password" placeholder="Passwort  (mind. 8 Zeichen, eine Zahl, ein Gross- & Kleinbuchstabe)" name="password" class="form-control mb-3" required>
                </div>

                <select name="admin" id="admin" class="form-control" required>
                    <option value="0" selected>Kein Admin</option>
                    <option value="1">Admin</option>
                </select>

                <br>

                <h5><strong>Kategorien</strong></h5>

                <p style="font-size: 10px;">(Admins dürfen keine Kategorie haben)</p>

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
                        echo "<input class'custom-control-input' type='checkbox' name='category[]' value='$id_cat'> | ";
                        echo $category."<br>";
                    }

                    //wenn es noch keine Einträge gibt wird dies ausgegeben

                    if($row_count['count(*)'] == 0)
                    {
                        echo "Es gibt noch keine <a href='createcategory.php'>Kategorien</a>. <br>";
                    }

                    //verbindung zur Datenbank wird geschlossen

                    $conn->close();

                ?>

                <br><a href=""></a>
                
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
                        <input class="btn btn-primary w-100"type="submit" class="submit" value="Benutzer erstellen" name="submit">
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