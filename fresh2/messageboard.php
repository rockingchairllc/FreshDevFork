<?php
require_once("config.php");
header('Content-type: text/html; charset=utf-8');
?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html>
    <head>
        <?php require_once("css.php"); ?>
    </head>
    <body>
        <?php
        //get the user and place ids from the form on the check-in page
        $userId = 0;
        $placeId = 0;
        $msg_content = "";
        $userId = $_POST['user_id'];
        $placeId = $_POST['place_id'];
        $msg_content = $_POST['msg_content'];

        //add the message from the form on the check-in page into the messages database
        $query = sprintf("Insert into messages(person_id,place_id,msg_content) values ('%s','%s','%s')", mysql_real_escape_string($userId), mysql_real_escape_string($placeId), mysql_real_escape_string($msg_content));
        $result = mysql_query($query);
        ?>

        <!-- Now we let people leave and read messages -->
        Leave a message!
        <form action="messageboard.php" method="post">
            <input type="hidden" name="user_id" id="user_id" value="<?php echo $userId; ?>">
            <input type="hidden" name="place_id" id="place_id" value="<?php echo $placeId; ?>">
            <br />
            <input type="text" name="msg_content" id="msg_content" >
            <input type="submit" value="Yodel It!" >
        </form>

        <!-- Read messages -->
        <br>
        'Da Message Board:
        <br>
        <?php
        // SQL QUERY TO RETREIVE MESSAGES, AND RETREIVE THE FIRST NAME OF THE AUTHOR OF EACH MESSAGE
        $query = sprintf("SELECT messages.date_and_time,messages.msg_content,person.first_name FROM messages LEFT JOIN person ON (person.person_id = messages.person_id) WHERE messages.place_id='%s' ", mysql_real_escape_string($placeId));
 
        // Perform Query
        $result = mysql_query($query);

        // Check result
        //echo $query;
        // This shows the actual query sent to MySQL, and the error. Useful for debugging.
        if (!$result) {
            $message = 'Zero Messages';
            die($message);
        }
        // Fetch the messages from mysql and echo in a table
        echo '<table>';
        $count = 1;
        while ($row = mysql_fetch_assoc($result)) {
            echo '<tr>';
            # Added Date formatting while echoing the date.
            echo "<td><b>" . $row['first_name'] . "</b>" . " (" . date('l H:i:s A', intval($row['date_and_time'])) . ") : " . $row['msg_content']."</td>";
            echo '</tr>';
            if ($count++ > 20)
                break;
        }
        echo '</table>';
//		else{
//			die("Sorry, we had an error accessing rows for this place in the MySQL database.");
//		}
        ?>

    </body>
</html>