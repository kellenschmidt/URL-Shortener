<?php
 $db = mysqli_connect('localhost','root','PASSWORD','link_shortner') or die('Error connecting to MySQL server.');
 
 // Create and execute query to remove URL from list by setting visiblity to 0 utilizing code from GET parameter
 $removeQuery = 'UPDATE links SET visible=0 WHERE code="' . $_GET['code'] . '"';
 mysqli_query($db, $removeQuery) or die('Error querying database.');

 // Link back to main links.php page
 $homePage = '//kellenschmidt.com/links.php';
 header('Location: ' . $homePage);
?>
