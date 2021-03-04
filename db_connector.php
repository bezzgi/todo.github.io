<?php
	$host     = 'localhost';    	// Host
	$username = 'root';          	// Benutzername
	$password = '';        			// Passwort
	$database = 'pamodul151';   	// Datenbank
	
	// mit Datenbank verbinden
	$conn = new mysqli($host, $username, null, $database);
	
	// fehlermeldung, falls die Verbindung fehlschlägt.
    if ($conn->connect_error) 
    {
	   die('Verbindungsfehler (' . $conn->connect_error . ') '. $conn->connect_error);
	}
    
    echo "";
?>
