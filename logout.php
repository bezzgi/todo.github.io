<?php

//Session starten

session_start();

//Session auf 0 setzen

$_SESSION = [];

//Session löschen

session_destroy();

//Weiterleitung auf Login-Seite

header("Location: login.php");

?>