<?php
   $db = mysqli_connect('localhost','root','g9z9wCPG8xG8^yi*','link_shortner') or die('Error connecting to MySQL server.');

   // Retrieve code from GET parameter
   $code = $_GET['b'];

   // Create and execute query to get long URL and count from database
   $linkQuery = 'SELECT long_url, count FROM links WHERE code="' . $code . '"';
   $getLinks = mysqli_query($db, $linkQuery) or die('Error querying database.');
   
   // Read from database
   $row = mysqli_fetch_array($getLinks);
   $longUrl = $row['long_url'];
   $count = $row['count'];

   // Increment count and create and execute query to update count in database
   $count += 1;
   $countQuery = 'UPDATE links SET count=' . $count . ' WHERE code="' . $code . '"';
   mysqli_query($db, $countQuery) or die('Error querying database.');

   mysqli_close($db);
   
   // Redirect to long URL
   header('Location: ' . $longUrl);
?>