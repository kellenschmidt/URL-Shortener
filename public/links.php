<?php
 // Connect to database
 $db = mysqli_connect('localhost','root',getenv('MYSQL_PASS'),'link_shortner') or die('Error connecting to MySQL server.');
 $siteURL = 'https://kellenschmidt.com/php/';

 // Get timestamp
 function getDatetime() {
  date_default_timezone_set('America/Chicago');
  return date('Y-m-d H:i:s');
 }

 // Log page visit
 /*
 function logInteraction() {
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    preg_match('#\((.*?)\)#', $user_agent, $match);
    $operating_system = $match[1];
    $start = strrpos($user_agent, ')') + 2;
    $end = strrpos($user_agent, ' ');
    $browser = substr($user_agent, $start, $end-$start);
    $date = getDatetime();

    $type = 0;
    $ip_address = "test";
    $operating_system = "test2";
    $browser = "test3";
    $date = "1-1-1 1:1:1";

    // $logInteractionQuery = 'INSERT INTO interactions (interaction_type, ip_address, browser, operating_system, "date") VALUES (0, "' . $ip_address . '","' . $browser . '","' . $operating_system . '","' . $date . '")';
    $logInteractionQuery = 'INSERT INTO interactions SET interaction_type = 0, ip_address = "' . $ip_address . '", browser = "' . $browser . '", operating_system = "' . $operating_system . '", interaction_date = "' . $date . '")';
    // $logInteractionQuery = 'INSERT INTO interactions (interaction_type, ip_address, browser, operating_system, interaction_date) VALUES (0,"' . $type . '","' . $ip_address .'","' . $browser . '","' . $operating_system .'","' . $date .'")';
    mysqli_query($db, $logInteractionQuery) or die('Error querying database to log interaction');
}

logInteraction();*/
?>

<html>
 <head>
  <title>Kellen's URL Shortener</title>
  <!--<link href="http://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">-->
  <link href='//fonts.googleapis.com/css?family=Roboto|Roboto:300|Roboto:500' rel='stylesheet'>
  <link href="styles.css" rel="stylesheet" type="text/css"/>
 </head>

 <body>

 <!-- Header of page: "Kellen URL Shortner" -->

 <header>
 <div class="kellen-logo">
  <span id="k">K</span><span id="e1">e</span><span id="l1">l</span><span id="l2">l</span><span id="e2">e</span><span id="n">n</span><span id="title">URL Shortener</span>
 </header>

 <!-- Link input and output -->

  <?php
   // Display screen to enter new link when no there are no GET arguments
   if((int) $_SERVER['CONTENT_LENGTH'] == 0) {
    $inputDisplayVal = 'inherit';
   } else {
    $inputDisplayVal = 'none';
   }
   echo '<div style="display:' . $inputDisplayVal . '">';
  ?>
   <div>
     <!-- "action" property of the form redirects to the supplied file when submit button is clicked -->
     <form action="links.php" method="post" class="link-data">
      <p id="link-data-heading">Simplify your links</p>
      <input type="text"
             name="longUrl"
             id="url-input"
             placeholder="Your original URL here">

      <button type="submit" id="link-button">Shorten URL</button>
     </form>
    </div>
   </div>
 </div>
 <?php
  // Display screen with newly created short URL when there are GET arguments
  if((int) $_SERVER['CONTENT_LENGTH'] != 0) {
   $outputDisplayVal = 'inherit';
   $longUrl = $_POST['longUrl'];

   // Prepend long url with http:// if it doesn't have it already'
   if(substr($longUrl, 0, 4) != 'http') {
    $longUrl = 'http://' . $longUrl;
   }
   
   // Create and execute query to get code of long url if it is already in the database
   $existingUrlQuery = 'SELECT code FROM links WHERE long_url="' . $longUrl  . '"';
   $getExistingLink = mysqli_query($db, $existingUrlQuery) or die('Error querying database for existing url');
   $row = mysqli_fetch_array($getExistingLink);
   // If code is already in database update timestamp and make visible
   if($row != NULL) {
    $code = $row['code'];
    $shortUrl = $siteURL . $code;
    $date = getDatetime();
    // Create and execute query to update creation date and visibility for given code
    $updateExistingUrlQuery = 'UPDATE links SET date_created="' . $date . '", visible=1 WHERE code="' . $code . '"';
    mysqli_query($db, $updateExistingUrlQuery) or die('Error querying database to update existing url');
   }
   // Else code is not already in database
   else {
    
    // Test whether URL code is already in use or not
    function isUnusedCode($testCode) {
     $db = mysqli_connect('localhost','root','ph@X$91PAl27u&or','link_shortner') or die('Error connecting to MySQL server.');
     // Create and execute query to get all codes in database
     $codesQuery = 'SELECT code FROM links';
     $getCodes = mysqli_query($db, $codesQuery) or die('Error querying database for codes.');
     while ($row = mysqli_fetch_array($getCodes)) {
      if ($row['code'] == $testCode) {
       return False;
      }
     }
     return True;
    }

    // Generate new URL code
    do {
     $code = substr(md5(microtime()),rand(0,26),3);
    } while (isUnusedCode($code) == False);

    // Creates short URL with the form http://example.com/<code>
    // Uses mod_rewite in .htaccesss to redirect this to http://example.com/shortener.php?b=<code>
    $shortUrl = $siteURL . $code;
    $date = getDatetime();
    // Create and execute query to add new URL to database
    $insertQuery = 'INSERT INTO links (code, long_url, date_created, count) VALUES ("' . $code . '","' . $longUrl .'","' . $date . '",0)';
    mysqli_query($db, $insertQuery) or die('Error querying database to insert');
   }
  } else {
   $outputDisplayVal = 'none';
  }
  echo '<div style="display:' . $outputDisplayVal . '">';
 ?>
  <div class="link-data">
   <p id="link-data-heading">Your short URL</p>
   <p id="new-link">
   <?php
    // Display new short url
    echo $shortUrl;
   ?>
   </p>
   <br/>
   <a id="home-btn" href="links.php">Go back</a>
  </div>
 </div>

 <!-- Table of URLs and associated data -->
 <table id="links-table">
   <thead>
    <tr>
     <th>Original URL</th>
     <th>Created</th>
     <th>Short URL</th>
     <th>Clicks</th>
     <th></th>
    </tr>
   </thead>

   <tbody>
   <?php
    // Create and execute query to get information about URL from database
    $linkQuery = 'SELECT * FROM links ORDER BY date_created DESC';
    $getLinks = mysqli_query($db, $linkQuery) or die('Error querying database for links.');
    $totalClicks = 0;

    // Display data for each URL
    while ($row = mysqli_fetch_array($getLinks)) {
     // Only display row if visibility is 1
     if($row['visible'] == 1) {

       echo '<tr>';
       echo '<td class="truncate"><a href="' . $row['long_url'] . '">' . $row['long_url'] . '</a></td>';

  /* $date = new DateTime($row['date_created']);
     $now = new DateTime(date('Y-m-d H:i:s'));
     $diff = $date->diff($now);
     $minutes = $diff->days * 24 * 60;
     $minutes += $diff->h * 60;
     $minutes += $diff->i;
     echo '<td>';
     if ($minutes >= 60*24) {
      echo $date->format('M j, Y');
     } else if ($minutes >= 60) {
      echo $date->h . ' hours ago';
     } else if ($minutes > 0) {
      echo $date->i . ' minutes ago';
     } else {
      echo $date->s . ' seconds ago';
     }*/

       $date = date_create($row['date_created']);
       echo '<td>' . date_format($date, 'M j, Y') . '</td>';
       $shortUrl = $siteURL . $row['code'];
       echo '<td><a href="' . $shortUrl . '">' . substr($shortUrl, 8) . '</a></td>';
       echo '<td>' . $row['count'] . '</td>';
       // Add click count to total clicks
       $totalClicks = $totalClicks + $row['count'];

       // Create button to remove from database
       // Links to a different php file and passes the URL code as a parameter
       echo '<td><a class="remove-btn" href="remove.php?code=' . $row['code'] . '">X</a></td>';
       echo '</tr>';
     }
    }
   ?>
   </tbody>

   <tfoot>
    <tr>
     <td></td>
     <td></td>
     <td></td>
     <td><strong>
      <?php
       // Print total number of clicks at bottom of table
       echo $totalClicks;
      ?>
     </strong></td>
     <td></td>
    </tr>
   </tfoot>
  </table>
 </div>
 </body>
 </html>


