<?php
   $db = mysqli_connect('localhost','root','PASSWORD','link_shortner') or die('Error connecting to MySQL server.');
   $siteURL = 'https://kellenschmidt.com/php/';

   // Retrieve code from GET parameter
   $code = $_GET['code'];

   // Create and execute query to get long URL and count from database
   $linkQuery = 'SELECT long_url, count FROM links WHERE code="' . $code . '"';
   $getLinks = mysqli_query($db, $linkQuery) or die('Error querying database.');

   // Read from database
   $row = mysqli_fetch_array($getLinks);

   // Display 404 error message if short link is invalid (code not in database)
   if($row == NULL) {
    echo '404: Page not found – the page ' . $siteURL . $code . ' does not exist.<br/>If you typed in or copied/pasted this URL, make sure you included all the characters, with no extra punctuation.';
   } else {
    $longUrl = $row['long_url'];
    $count = $row['count'];

    // Increment count and create and execute query to update count in database
    $count += 1;
    $countQuery = 'UPDATE links SET count=' . $count . ' WHERE code="' . $code . '"';
    mysqli_query($db, $countQuery) or die('Error querying database.');
    
    // Redirect to long URL
    header('Location: ' . $longUrl);
   }
?>
