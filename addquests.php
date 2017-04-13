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
        table#t1, th#r1 {text-align: right;}
/*            width: 35%; background-color: #f1f1c1; text-align: right;
            border: 4px solid #005a31; border-collapse: collapse;
            padding: 2px;}*/
/*        table#t2, th, td { 
            width: fit-content; vertical-align: top; text-align: left;}*/
    </style>
    <title>Add Quests</title>
</head>
<body>
    <div>
        <h1>Add Quests</h1>
    </div>
        <br>
        <h2><table><tr><th>Write a Quest for a Questline</th></tr></table></h2>
        <br>
    <?php checkForInput(); ?>
    
<form method="post" action="<?php getServer() ?>">   
    <pre>
    Which Questline is this Quest for?          <select name="questline">
                                        <?php $j=0; 
                                         foreach(getQuestlineTitles() as $item)
                                         { echo "<option value=\"$item\">".$item."</option>"; 
                                           ++$j;} ?></select>

    How many tasks will be in this Quest?       <input type="number" min="1" max="99" value="1"  name="taskcount">

    Progressive                                 <input type="number" min="0" max="99" value="0"  name="progressive">

<!--    If there are multiple tasks, do they have to be completed in order?   
                            Yes <input type="radio" name="progressive" value="1">
                            No  <input type="radio" name="progressive" value="2" checked>-->
    How many times can this Quest be repeated?  <input type="number" min="0" max="99" value="0"  name="repeats">
      </pre>
      <fieldset><legend><pre><b>Task 1</b></pre></legend>
    <pre>
    <table id="t2">
        <tr><td> Title:          <input type="text" size="30"  name="task1" value="ANOTHER test title" required></td>
          <td> Question Type:      Short Answer<input type="radio" name="questiontype" value="2" checked>
                  Multiple Choice<input type="radio" name="questiontype" value="1">
                  Simple Completion<input type="radio" name="questiontype" value="3">
          </td></tr>
    </table>
 Are students required to complete it?      Yes  <input type="radio" name="required" value="1">  
                                            No   <input type="radio" name="required" value="0" checked>

 Question or Student Goal:
                 <textarea name="evidence" cols="80" rows="2" wrap="hard" required>a test question again</textarea>

 Answer options, if multiple choice (separated by commas)
                 <input type="text" size="80"  name="options" value=" ">

 Correct Answer:        <!-- empty string is default value for some fields because they aren't 
                            required in the database, and if the user leaves 
                            them blank, the variable must still have a value when inserted into the table -->
                 <textarea name="answer" cols="80" rows="2" wrap="hard" >a test answer too</textarea>

 How many points are awarded for completion? <input type="number" min="1" max="99" value="1"  name="points" required>
                 </pre>
   </fieldset>
        <br>
        <input type="submit" value="Submit Quest">  <!--<button type="button">Clear Form</button>-->
</form>  
        <br>
        <pre>
<?php

echo     '     <b>Record Entered</b><br>'.         
         '       Questline:      '.$questline.'<br>'.
         '       Task Count(ID): '.$taskcount.'<br>'.
         '       Task:           '.$task1.'<br>'.
         '       Evidence:       '.$evidence.'<br>'.
         '       Progressive:    '.$progressive.'<br>'.
         '       Points:         '.$points.'<br>'.
         '       Required:       '.$required.'<br>'.
         '       Repeats:        '.$repeats.'<br>'.
         '       Question type:  '.$questiontype.'<br>'.
         '       Answer Options: '.$options.'<br>'.
         '       Correct Answer: '.$answer. '<br>';

?>
        </pre>
    <?php insertIntoTable(); ?>
</body>
</html>

<?php            
             
 function cleanHTML($arrRef){
     $fieldVal = $_POST[$arrRef];
     $stripped = stripslashes($fieldVal);
     $cleaned = strip_tags($stripped);
     return htmlentities($cleaned);

 }

 function cleanMySQL($valueForDB){
     return mysql_real_escape_string($valueForDB);
 }
 
 function getServer(){
     return $_SERVER['PHP_SELF'];
 }
  
 // The empty string is used as default value for some fields because they 
 //     aren't required in the database and if the user leaves them blank, 
 //     the variable must still have a value when inserted into the table.
 function emptyString(){
     return "";
 }
 
 // Returns an integer that is the primary key for the given
 //     Questline title
 function getOneQuestlineID($q) {
     $query = "SELECT questlineid FROM questlines WHERE title = '$q'";
     $result = mysql_query($query);
     if(!$result) {die(mysql_error());
        echo "SELECT failed: $query <br> mysql_error() <br>";
     }
     $row = mysql_fetch_row($result);
     return $row[0];
 }

 // Returns an int array containing unique questlineids that are foriegn 
 //     keys in the Quests table
 function getQuestlineIDs(){
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

 // Returns a string array of all Questline titles
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
 
 // Has data from the form fields been submitted to the server via POST?
 // Returns true if yes
 function fieldsAreSubmitted(){
     return 
       ( isset($_POST['questline'])); 
//             $_POST['taskcount'],
//            $_POST['task1'], $_POST['evidence'], $_POST['progressive'], 
//            $_POST['points'], $_POST['required'],$_POST['repeats'], 
//            $_POST['questiontype'],$_POST['options'], $_POST['answer']) );
 }
 
 // Check the form for input, collect it into variables if it's been posted
 // If not posted, set variables to [Not entered] for display
 function checkForInput(){
     if ( fieldsAreSubmitted() ) {
         global $questline; $questline        = cleanHTML("questline");
         global $taskcount; $taskcount        = cleanHTML("taskcount");
         global $task1; $task1                = cleanHTML("task1");
         global $evidence; $evidence          = cleanHTML("evidence");
         global $progressive; $progressive    = cleanHTML("progressive");
         global $points; $points              = cleanHTML("points");
         global $required; $required          = cleanHTML("required");
         global $repeats; $repeats            = cleanHTML("repeats");
         global $questiontype; $questiontype  = cleanHTML("questiontype");
         global $options; $options            = cleanHTML("options");
         global $answer; $answer              = cleanHTML("answer"); 
         echo "DATA POSTED!!!";
     }
     else {
         global $questline; $questline        = "[Not entered]";
         global $taskcount; $taskcount        = "[Not entered]";
         global $task1; $task1                = "[Not entered]";
         global $evidence; $evidence          = "[Not entered]";
         global $progressive; $progressive    = "[Not entered]";
         global $points; $points              = "[Not entered]";
         global $required; $required          = "[Not entered]";
         global $repeats; $repeats            = "[Not entered]";
         global $questiontype; $questiontype  = "[Not entered]";
         global $options; $options            = "[Not entered]";
         global $answer; $answer              = "[Not entered]";
         echo "DATA IS NOT POSTED!!!";
     }
 }
 
 // Check the form for input
 // Takes the values submited to the form and inserts them into Quests table
 function insertIntoTable(){
     if ( fieldsAreSubmitted() ) {
         global $questline;
         global $questlineID; $questlineID    = getOneQuestlineID($questline);
         global $taskcount; $taskcount        = cleanMySQL($taskcount);
         global $task1; $task1                = cleanMySQL($task1);
         global $evidence; $evidence          = cleanMySQL($evidence);
         global $progressive; $progressive    = cleanMySQL($progressive);
         global $points; $points              = cleanMySQL($points);
         global $required; $required          = cleanMySQL($required);
         global $repeats; $repeats            = cleanMySQL($repeats);
         global $questiontype; $questiontype  = cleanMySQL($questiontype);
         global $options; $options            = cleanMySQL($options);
         global $answer; $answer              = cleanMySQL($answer);
         
      // -questlineid(int)-, taskid(int), task, -evidence-, progressive(int), 
      //  -points(int)-, required(int), -repeats(int)-, -questtype(int)-, -options-, -correctanswer-
         $query = "INSERT INTO quests VALUES" .
            "($questlineID, $taskcount, '$task1', '$evidence', $progressive, "
          . "$points, $required, $repeats, $questiontype, '$options', '$answer')";
         if(!mysql_query($query)){
             $errorMsg = mysql_error();
             echo "INSERT failed: $query  --  ERROR MSG: $errorMsg";
             
         }
    }
 }
 
?>