<!--
Project:    Spirit Quest, a University of Alaska Anchorage web game
File:       addquestline.php
Author:     Gareth Bosch
Date:       Fall 2016
Supervisor: Dr. Kenrick Mock
Function:   Admin form for viewing, inserting, and 
                deleting records from the Questline table.
            Designed for UAA Spirit Quest game writers.
-->

<!DOCTYPE html>

<?php
 include("includes.inc");
 ?>

<!-- Display the page -->
<html>
    <head>
        <meta charset="UTF-8">
        <style>
            h1, h2 {font-family: Helvetica, Arial, sans-serif;}
            h1 {color: white; border-left: 10px solid #005a31;}
            h2 {border-left: 25px solid white;}
            div {background-color: #005a31; padding: 3px; 
                 border-style: hidden; border-radius: 8px;}
        </style>
        <title>Add Questlines</title>
    </head>
    <body>
        <div>
            <h1>Add & Delete Questlines</h1></div>
        <h2>Add a New Questline</h2>

 <?php  
        checkForDelete();
        checkForInput();
 ?>
<!-- The form -->
        <pre><font color="red">                 * required</font></pre>
        <pre><form method="post" action=<?php getServer() ?>>
     <b>Author(s)</b>          <input type="text" name="author" size="60"><br>
     <b>Title</b>              <input type="text" name="title" size="60"><br>
     <b>Attribute</b>          <select name="attribute" required>       
                    <!-- List populated with values from Attributes table -->
                   <?php $j=0;  
                         foreach(getAttributes() as $item)
                         { echo "<option value=\"$item\">".$item."</option>"; 
                           ++$j;} ?>
                               </select> <font color="red">*</font><br>
     <b>Points Needed</b>      <input type="number" name="pointsneeded" value="20" required> <font color="red">*</font><br>
     <b>Badge Label</b>        <input type="text" name="badgelabel"><br>
     <b>Notification</b>       <input type="text" name="notification"><br>
     <b>Goals of the Questline</b>
        <textarea name="goals" cols="99" rows="20" wrap="hard">
</textarea><br>
        <input type="submit" value="Submit Questline"></form>
<!-- Display the data that was just entered, or "[Not entered]" -->
    <p>
<?php
    echo '         <b>Record Entered</b><br>'.         
         '           Author(s):      '.$author.'<br>'.
         '           Title:          '.$title.'<br>'.
         '           Goals:          '.$goals.'<br>'.
         '           Attribute:      '.$attribute.'<br>'.
         '           Points Needed:  '.$pointsneeded.'<br>'.
         '           Badge Label:    '.$badgelabel.'<br>'.
         '           Notification:   '.$notification;
    ?>  </p></pre>

 <?php insertIntoTable(); ?>
        <hr>
        <h2>View and Delete Questlines</h2>
 <?php showAllRecords();?>
        </body>
</html>

<!---   - Functions -   -->

<?php
// Collects form data into the variables.
 function checkForInput(){
     // The server has data in POST superglobal array for the form fields
     if (isset($_POST['author'])     && isset($_POST['title'])        &&
         isset($_POST['attribute'])  && isset($_POST['pointsneeded']) &&
         isset($_POST['badgelabel']) && isset($_POST['goals'])){
         global $author; $author              = cleanHTML($_POST['author']);
         global $title; $title                = cleanHTML($_POST['title']);
         global $goals; $goals                = cleanHTML($_POST['goals']);
         global $attribute; $attribute        = cleanHTML($_POST['attribute']);
         global $pointsneeded; $pointsneeded  = cleanHTML($_POST['pointsneeded']);
         global $badgelabel; $badgelabel      = cleanHTML($_POST['badgelabel']);
         global $notification; $notification  = cleanHTML($_POST['notification']);}
     else { // The server does not have data in POST yet
         global $author; $author              = "[Not entered]";
         global $title; $title                = "[Not entered]";
         global $goals; $goals                = "[Not entered]";
         global $attribute; $attribute        = "[Not entered]";
         global $pointsneeded; $pointsneeded  = "[Not entered]";
         global $badgelabel; $badgelabel      = "[Not entered]";
         global $notification; $notification  = "[Not entered]";}
 }
 
 // Deletes the record in Questlines table if 'Delete' button was clicked.
 function checkForDelete(){
     if(isset($_POST['delete']) && isset($_POST['questlineid'])){
         $id = $_POST['questlineid'];
         $title = $_POST['title'];
         $author = $_POST['author'];
         $query = "DELETE FROM questlines WHERE  questlineid = '$id'";
         if(!mysql_query($query)){
             echo 'DELET failed: '.$query.'<br>'.mysql_errno().'<br>';}
         else {echo 'The questline titled <i><b>'.$title.'</b></i> written by <b>'.$author. 
         '</b> was just deleted';}
     }
 }
 
 function getServer(){
     return $_SERVER['PHP_SELF'];
 }
 
 // Protects against malicious form input.
 function cleanHTML($var){
     stripslashes($var);
     strip_tags($var);
     htmlentities($var);
     return $var;
 }
 function cleanMySQL($var){
     mysql_real_escape_string($var);
     cleanHTML($var);
     return $var;
 }
 
 // Returns the integer to be used as the ID number for the next 
 //     record in the table (increments the Primary Key).
 function getNextPrimaryKey(){
     $result = mysql_query("SELECT questlineid FROM questlines");
     if(!$result) {die(mysql_error());}
     $rows = mysql_num_rows($result);
     for($j=0; $j<$rows; ++$j)
        {$row = mysql_fetch_row($result);}
     return ++$row[0]; // row[0] is last primary key, new record uses ++row[0]
 }
 
 // Returns an array of strings containing every 
 //     attribute from the Attribute table.
 // Used to populate attribute selection list in the form.
 // Maintains referencial integrity between Attributes
 //     table and Questlines table.
 function getAttributes(){
     $result = mysql_query("SELECT attribute FROM attributes");
     if(!$result) {die(mysql_error());}
     $rows = mysql_num_rows($result);
     for($j=0; $j<$rows; ++$j)
     {
         $row = mysql_fetch_row($result);
         $attributesList[] = $row[0];
     }
     return $attributesList;
 }
 
 // Takes the values submited in the form and inserts 
 //     them into Questline table.
 function insertIntoTable(){
     if (isset($_POST['author'])     && isset($_POST['title'])        &&
         isset($_POST['attribute'])  && isset($_POST['pointsneeded']) &&
         isset($_POST['badgelabel']) && isset($_POST['goals']))
    {
         $questlineid   = getNextPrimaryKey();
         global $author; $author              = cleanMySQL($author);
         global $title; $title                = cleanMySQL($title);
         global $goals; $goals                = cleanMySQL($goals);
         global $attribute; $attribute        = cleanMySQL($attribute);
         global $pointsneeded; $pointsneeded  = cleanMySQL($pointsneeded);
         global $badgelabel; $badgelabel      = cleanMySQL($badgelabel);
         global $notification; $notification  = cleanMySQL($notification);
         $query = "INSERT INTO questlines VALUES" .
            "($questlineid, '$author', '$title', '$goals', '$attribute',"
                 . " $pointsneeded, '$badgelabel', '$notification')";
         if(!mysql_query($query)){
             echo "INSERT failed: $query <br> mysql_error() <br>";
         }
    }
 }
 
 // Echos each record from questlines table.
 // Delete button posts to server with this data 
 //     so we know which record to delete.
 function showAllRecords(){
     $filename = getServer();
     $result = mysql_query("SELECT * FROM questlines");
     if(!$result) {die(mysql_error());}
     $rows = mysql_num_rows($result);
     for($j=0; $j<$rows; ++$j){
         $row = mysql_fetch_row($result);
         echo <<<_END
         <fieldset><legend><b><pre> $row[2] </pre></b></legend><pre>
      Author(s):         $row[1]
      Goals:             $row[3]
      Attribute:         $row[4]
      Points Needed:     $row[5]
      Badge:             $row[6]
      ID #:              $row[0] </pre>
    <form action="$filename" method="post">
    <input type="hidden" name="delete" value="yes">
    <input type="hidden" name="questlineid" value="$row[0]">
    <input type="hidden" name="title" value="$row[2]">
    <input type="hidden" name="author" value="$row[1]">
    <input type="submit" value="Delete Questline">
    </form></fieldset><br>
_END;
     }
 }
?>
