<?php
// Connect to database
$db = mysqli_connect('localhost', 'root', 'g9z9wCPG8xG8^yi*', 'link_shortner') or die('Error connecting to MySQL server.');
$siteURL = 'http://52.34.61.64/';
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
if ((int) $_SERVER['CONTENT_LENGTH'] == 0) {
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
if ((int) $_SERVER['CONTENT_LENGTH'] != 0) {
    $outputDisplayVal = 'inherit';
    $longUrl          = $_POST['longUrl'];
    
	// Test whether URL code is already in use or not
    function isUnusedCode($testCode)
    {
        $db = mysqli_connect('localhost', 'root', 'g9z9wCPG8xG8^yi*', 'link_shortner') or die('Error connecting to MySQL server.');
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
        $code = substr(md5(microtime()), rand(0, 26), 3);
    } while (isUnusedCode($code) == False);
    
    // Creates short URL with the form http://example.com/<code>
    // Uses mod_rewite in .htaccesss to redirect this to http://example.com/shortener.php?b=<code>
    $shortUrl = $siteURL . $code;
    date_default_timezone_set('America/Chicago');
    $date        = date('Y-m-d H:i:s');
	// Create query to add new URL to database
    $insertQuery = 'INSERT INTO links (code, long_url, date_created, count) VALUES ("' . $code . '","' . $longUrl . '","' . $date . '",0)';
} else {
    $outputDisplayVal = 'none';
}
echo '<div style="display:' . $outputDisplayVal . '">';
?>

  <div class="link-data">
   <p id="link-data-heading">Your short URL</p>
   <p id="new-link">

   <?php
// Execute query to add to database and print short URL
if (mysqli_query($db, $insertQuery)) {
    echo $shortUrl;
} else {
    echo "Error: " . $insertQuery . "<br>" . mysqli_error($db);
}
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
    echo '<tr>';
    echo '<td class="truncate"><a href="' . $row['long_url'] . '">' . $row['long_url'] . '</a></td>';
    
    $date = date_create($row['date_created']);
    echo '<td>' . date_format($date, 'M j, Y') . '</td>';
    $shortUrl = $siteURL . $row['code'];
    echo '<td><a href="' . $shortUrl . '">' . $shortUrl . '</a></td>';
    echo '<td>' . $row['count'] . '</td>';
	// Add click count to total clicks
    $totalClicks = $totalClicks + $row['count'];
    
	// Create button to remove from database
	// Links to a different php file and passes the URL code as a parameter
    echo '<td><a class="remove-btn" href="remove.php?code=' . $row['code'] . '">X</a></td>';
    echo '</tr>';
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

// Close database
mysqli_close($db);
?>

     </strong></td>
     <td></td>
    </tr>
   </tfoot>
  </table>
 </div>
 </body>
 </html>
