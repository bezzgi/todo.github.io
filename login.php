<?php

    include('db_connector.php');

    $error = '';
    $message = '';

    // Formular wurde gesendet und Besucher ist noch nicht angemeldet
    if ($_SERVER["REQUEST_METHOD"] == "POST" && empty($error))
    {
	    //setzt den Benutzernamen

        if(!empty(trim($_POST['username'])))
        {
            $username = trim($_POST['username']);
		
		    //prüft den Benutzernamen auf Pattern

            if(!preg_match("/[0-9a-zA-Z]{6,}/", $username) || strlen($username) > 30)
            {
			    $error .= "Der Benutzername entspricht nicht dem geforderten Format.<br />";
            }
        } 
	    else 
	    {
		    $error .= "Geben Sie bitte den Benutzername an.<br />";
        }

	    //setzt das Passwort

        if(!empty(trim($_POST['password'])))
        {
		    $password = trim($_POST['password']);
		
		    //prüft das Passwort auf Pattern

            if(!preg_match("/(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}/", $password))
            {
			    $error .= "Das Passwort entspricht nicht dem geforderten Format.<br />";
		    }
        } 
        else 
        {
	    	$error .= "Geben Sie bitte das Passwort an.<br />";
	    }
	
	    //wird ausgeführt wenn kein Fehler vorhanden ist

        if(empty($error))
        {
	    	//Query um Admin, Benutzername und Passwort auszulesen
            $query = "SELECT admin, username, password from users where username = ?";
            // query vorbereiten
		    $stmt = $conn->prepare($query);
		
		    if($stmt===false)
		    {
                $error .= 'prepare() failed '. $conn->error . '<br>';
            }
            //parameter an Query binden
		    if(!$stmt->bind_param("s", $username))
		    {
                $error .= 'bind_param() failed '. $conn->error . '<br>';
            }
            //Query ausführen
		    if(!$stmt->execute())
		    {
                $error .= 'execute() failed '. $conn->error . '<br>';
            }
            //Daten auslesen
            $result = $stmt->get_result();
            //prüft ob Benutzer vorhanden sind
		    if($result->num_rows)
		    {
                //Benutzerdaten lesen
                $row = $result->fetch_assoc();
                // passwort prüfen
			    if(password_verify($password, $row['password']))
			    {
				    //Session Generieren
				    session_start();
				    session_regenerate_id();

                    //schreibt Benutzernamen, bool ob Session vorhanden ist und Admin in die Session

				    $_SESSION['username'] = $username;
                    $_SESSION['bool'] = true;
                    if($row['admin'] === 1)
                    {
                        $_SESSION['admin'] = 1;
                    }
                    else
                    {
                        $_SESSION['admin'] = 0;
                    }
                    
                    //Weiterleitung auf index.php
                    
                    header('Location: index.php');
			    } 
			    else 
			    {
                    $error .= "Benutzername oder Passwort sind falsch.";  
                }
		    } 
		    else 
		    {
                $error .= "Benutzername oder Passwort sind falsch.";
		    }
	    }
    }
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="" type="text/css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@100;300;500;700;900&display=swap" rel="stylesheet">

     <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <!-- Popper JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>

    <!-- Latest compiled JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
     
    <title>Anmelden</title>
</head>
<body class="bg-light">
<br><br><br><br><br><br><br>    
    <div  class="container-fluid w-50">
    <br>
        <h3>Anmelden</h3>

        <div>
            <form action="login.php" method="POST">
                <br>

                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text mb-3"><i class="bi bi-person-fill"></i></span>
                    </div>
                    <input pattern="[0-9a-zA-Z]{6,}" type="text" placeholder="(mind. 6 Zeichen)" name="username" class="form-control mb-3">
                </div>

                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text mb-3"><i class="bi bi-lock-fill"></i></span>
                    </div>
                    <input pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" type="password" placeholder="(mind. 8 Zeichen, eine Zahl, ein Gross- & Kleinbuchstabe)" name="password" class="form-control mb-3">
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
                        <input class="btn btn-primary w-100" type="submit" class="submit" value="Anmelden">
                    </div>
                    <div class="col">
                        <input class="btn btn-primary w-100" type="reset" class="submit" value="Löschen">                    
                    </div>
                </div>
                
            </form>
        </div>
    </div>
</body>
</html>