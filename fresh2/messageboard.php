<?php
require_once("config.php");
header('Content-type: text/html; charset=utf-8');
?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html>
    <head>
        <?php require_once("css.php"); ?>
        <!-- Included online JQuery library and to avoid local copy, to use its AJAX functionality -->
        <script type="text/javascript" src="http://code.jquery.com/jquery-1.4.2.min.js"></script>
        <script type="text/javascript">
            // Function defined here to show message board updates thru AJAX
            function fn_show_msgboard(){
                $.ajax({
                    type: "POST",
                    data: "q=showmsgs",
                    url: "messageboard.php",
                    async: true,
                    success: function(msg){
                        $("#div_messages").attr("innerHTML", msg);
                    }
                });
            }
            
            // Function defined here to show message board updates thru AJAX
            function fn_post_message(){
                // Check, if there is no text entered in the msg_content, don't do anything.
                if( $("#msg_content").val().trim() == "" ){
                    return false;
                }
                
                // Else make an AJAX call and post the message.
                form_elements = $("#frm_message_board").serialize();
                $.ajax({
                    type: "POST",
                    data: "q=post_message&"+form_elements,
                    url: "messageboard.php",
                    async: false,
                    success: function(msg){
                        fn_show_msgboard();
                    }
                });
            }
            
            // This is on ready call to function fn_show_msgboard(), to start showing messageboard updates automatically.
            // The frequency of refreshing the messageboard is kept as 5 seconds.
            $(document).ready(
                function(){
                    // Call to this function for first time update.
                    fn_show_msgboard();
                    
                    // Messages refresh function.
                    setTimeout("fn_show_msgboard()",5000);
                }
            );
        </script>
    </head>
    <body>
        <?php

        //get the user and place ids from the form on the check-in page
        $userId = 0;
        $placeId = 0;
        $msg_content = "";
        $userId = isset($_POST["user_id"])?$_POST['user_id']:"";
        $placeId = isset($_POST["place_id"])?$_POST['place_id']:"";
        $msg_content = isset($_POST["msg_content"])?$_POST['msg_content']:"";

        # Added to use it with AJAX.
        
        # This If condition is called for showing messages list.
        if( isset($_POST["q"]) && $_POST["q"] == "showmsgs" ){
            
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
                
                # Applying word wrapping to Message Board message. Kept the width as 40 characters, but can be modified according to iPhone's screen width.
                $message_content = wordwrap($row["msg_content"], 40, "<br />\n");

                # Added Date formatting while echoing the date.
                echo "<td><b>" . $row['first_name'] . "</b>" . " (" . date('l H:i:s A', intval($row['date_and_time'])) . ") : " . $message_content . "</td>";
                echo '</tr>';
                if ($count++ > 20)
                    break;
            }
            echo '</table>';
            die;
        }

        # This If condition is called for posting message.
        if( isset($_POST["q"]) && $_POST["q"] == "post_message" ){
            //add the message from the form on the check-in page into the messages database
            $query = sprintf("INSERT INTO messages(person_id,place_id,msg_content) VALUES ('%s','%s','%s')", mysql_real_escape_string($userId), mysql_real_escape_string($placeId), mysql_real_escape_string($msg_content));
            $result = mysql_query($query);
            die;
        }
        ?>

        <!-- Now we let people leave and read messages -->
        Leave a message!
        <form id="frm_message_board" action="messageboard.php" method="POST">
            <input type="hidden" name="user_id" id="user_id" value="<?php echo $userId; ?>" />
            <input type="hidden" name="place_id" id="place_id" value="<?php echo $placeId; ?>" />
            <br />
            <input type="text" name="msg_content" id="msg_content" />
            <input type="button" value="Yodel It!" onclick="fn_post_message();" /><br />
        </form>

        <!-- Read messages -->
        <br>
        'Da Message Board:
        <br>
        <div id="div_messages"></div>
    </body>
</html>