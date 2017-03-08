<?php
 $db = mysqli_connect('localhost','root','PASSWORD_HERE','link_shortner') or die('Error connecting to MySQL server.');
 
 // Create and execute query to remove URL from database using code from GET parameter
 $removeQuery = 'DELETE FROM links WHERE code="' . $_GET['code'] . '"';
 mysqli_query($db, $removeQuery) or die('Error querying database.');

 // Close database
 mysqli_close($db);

 // Link back to main links.php page
 $homePage = '//kellenschmidt.com/links.php';
 header('Location: ' . $homePage);
?>