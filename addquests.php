<!--
File:       addquests.php
Author:     Gareth Bosch
Date:       Fall 2016
Supervisor: Dr. Kenrick Mock
Function:   Admin form for viewing, inserting, and 
                deleting records from the Questline table
-->

<!DOCTYPE html>

<?php
include("includes.inc");
?>

<html>
<head>
    <meta charset="UTF-8">
    <style>
        h1, h2 {font-family: Helvetica, Arial, sans-serif;}
        h1 {color: white; border-left: 10px solid #005a31;}
        h2 {border-left: 25px solid white;}
        body {font-family: Courier New; font-size: 12px;}
        div {background-color: #005a31; padding: 3px; 
             border-style: hidden; border-radius: 8px;}
        table, th, td {padding: 2px;}
        table#t1, th#r1 {width: 35%; background-color: #f1f1c1; border: 4px solid #005a31; border-collapse: collapse;}
    </style>
    <title>Add Quests</title>
</head>
<body>
    <div>
 <h1>Add Quests</h1></div>
    <h2><table id="t1"><tr id="r1"><th>Write a Quest for a Questline</th></table></h2>
    
    <?php checkForInput(); ?>
    
  <form method="post" action=<?php getServer() ?>><pre>     
    Which Questline is this Quest for?          <select name="questline">
                                        <?php $j=0; 
                                         foreach(getQuestlineTitles() as $item)
                                         { echo "<option value=\"$item\">".$item."</option>"; 
                                           ++$j;} ?></select>

    How many tasks will be in this Quest?       <input type="number" min="1" max="99" value="1"  name="taskcount">

    How many times can this Quest be repeated?  <input type="number" min="0" max="99" value="0"  name="repeats">
      </pre>
   <fieldset><legend><b><pre>Task 1</pre></b></legend>
    <table style="width: 75%">
        <tr>
          <td> Title:          <input type="text" size="30"  name="task1"></td>
          <td> Question Type:    Short Answer<input type="radio" name="questiontype" value="2">
                  Multiple Choice<input type="radio" name="questiontype" value="1">
                  Simple Completion<input type="radio" name="questiontype" value="3">
          </td>
        </tr>
    </table><pre>
 Are students required to complete it?      Yes  <input type="radio" name="required" value="1" checked>  
                                            No   <input type="radio" name="required" value="0" checked>

 Question or Student Goal:
                 <textarea name="evidence" cols="80" rows="2" wrap="hard" required></textarea>   

 Answer:
                 <textarea name="answer" cols="80" rows="2" wrap="hard" required></textarea>

 How many points are awarded for completion? <input type="number" min="1" max="99" value="1"  name="points" required>
                 </pre>
   </fieldset>
        <br>
        <pre><input type="submit" value="Submit Quest"></form>      <!--<button type="button">Clear Form</button></pre>-->
        <br>
        <pre>
<?php
echo     '     <b>Record Entered</b><br>'.         
         '       Questline:      '.$questline.'<br>'.
         '       Task Count:     '.$taskcount.'<br>'.
         '       Repeats:        '.$repeats.'<br>'.
         '       Task:           '.$task1.'<br>'.
         '       Question type:  '.$questiontype.'<br>'.
         '       Required:       '.$required.'<br>'.
         '       Evidence:       '.$evidence.'<br>'.
         '       Answer:         '.$answer. '<br>'.
         '       Points:         '.$points;
?>

        </pre>
<?php insertIntoTable(); ?>
</body>
</html>

<?php            
             
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
 
 function getServer(){
     return $_SERVER['PHP_SELF'];
 }
 
 function getOneQuestlineID($q) {
     $query = "SELECT questlineid FROM questlines WHERE title = '$q'";
     $result = mysql_query($query);
     if(!$result) {die(mysql_error());
        echo "SELECT failed: $query <br> mysql_error() <br>";
     }
     $row = mysql_fetch_row($result);
     return $row[0];
 }

 function getQuestlineIDs(){
     // Returns an array containing only unique questlineids
     $result = mysql_query("SELECT questlineid FROM quests");
     if(!$result) {die(mysql_error());}
     $rows = mysql_num_rows($result);
     for($j=0; $j<$rows; ++$j)
     {
         $row = mysql_fetch_row($result);
         $questlineidList[] = $row[0];
     }
     // Normalize the list of quest ids so there are no repeats
     $sequenceList = range(0,50);
     $normalizedList = array_intersect($sequenceList, $questlineidList);
     return $normalizedList;
 }

 function getQuestlineTitles(){
     $result = mysql_query("SELECT title FROM questlines");
     if(!$result) {die(mysql_error());}
     $rows = mysql_num_rows($result);
     for($j=0; $j<$rows; ++$j)
     {
         $row = mysql_fetch_row($result);
         $questlineNames[] = $row[0];
     }
     return $questlineNames;
 }
 
 function checkForInput(){
     // Check the form for input, collect it if it's there
     if (isset($_POST['questline'])    && isset($_POST['taskcount'])    &&
         isset($_POST['repeats'])      && isset($_POST['task1'])        &&
         isset($_POST['questiontype']) && isset($_POST['required'])     &&
         isset($_POST['evidence'])     && isset($_POST['answer'])       &&
         isset($_POST['points'])) {
         global $questline; $questline        = cleanHTML($questline);
         global $taskcount; $taskcount        = cleanHTML($taskcount);
         global $repeats; $repeats            = cleanHTML($repeats);
         global $task1; $task1                = cleanHTML($task1);
         global $questiontype; $questiontype  = cleanHTML($questiontype);
         global $required; $required          = cleanHTML($required);
         global $evidence; $evidence          = cleanHTML($evidence);
         global $answer; $answer              = cleanHTML($answer);
         global $points; $points              = cleanHTML($points); }
     else {
         global $questline; $questline        = "[Not entered]";
         global $taskcount; $taskcount        = "[Not entered]";
         global $repeats; $repeats            = "[Not entered]";
         global $task1; $task1                = "[Not entered]";
         global $questiontype; $questiontype  = "[Not entered]";
         global $required; $required          = "[Not entered]";
         global $evidence; $evidence          = "[Not entered]";
         global $answer; $answer              = "[Not entered]";
         global $points; $points              = "[Not entered]";
     }
 }
 
 function insertIntoTable(){
     // Takes the values submited to the form
     //    and inserts them into Quests table
     if (isset($_POST['questline'])    && isset($_POST['taskcount'])    &&
         isset($_POST['repeats'])      && isset($_POST['task1'])        &&
         isset($_POST['questiontype']) && isset($_POST['required'])     &&
         isset($_POST['evidence'])     && isset($_POST['answer'])       &&
         isset($_POST['points'])) {
         global $questlineID;
         global $questline; $questlineID      = getOneQuestlineID($questline);
         global $taskcount; $taskcount        = cleanMySQL($taskcount);
         global $repeats; $repeats            = cleanMySQL($repeats);
         global $task1; $task1                = cleanMySQL($task1);
         global $questiontype; $questiontype  = cleanMySQL($questiontype);
         global $required; $required          = cleanMySQL($required);
         global $evidence; $evidence          = cleanMySQL($evidence);
         global $answer; $answer              = cleanMySQL($answer);
         global $points; $points              = cleanMySQL($points);
         $query = "INSERT INTO quests VALUES" .
            "('$questlineID', '$taskcount', '$repeats', '$task1', '$questiontype',"
                 . " $required, '$evidence', '$points')";
         if(!mysql_query($query)){
             echo "INSERT failed: $query <br> mysql_error() <br>";}
    }
 }
 
?>