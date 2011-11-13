<?php
    require_once("config.php");

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

        $fresh_PlaceId = getFreshPlaceId( $placeId );
        // SQL QUERY TO RETREIVE MESSAGES, AND RETREIVE THE FIRST NAME OF THE AUTHOR OF EACH MESSAGE
        $query = sprintf("SELECT messages.date_and_time,messages.msg_content,person.first_name FROM messages LEFT JOIN person ON (person.person_id = messages.person_id) WHERE messages.place_id='%s' ", mysql_real_escape_string($fresh_PlaceId));

        // Perform Query
        $result = mysql_query($query);

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

        $fresh_PlaceId = getFreshPlaceId( $placeId );

        //add the message from the form on the check-in page into the messages database
        $query = sprintf("INSERT INTO messages(person_id,place_id,msg_content) VALUES ('%s','%s','%s')", mysql_real_escape_string($userId), mysql_real_escape_string($fresh_PlaceId), mysql_real_escape_string($msg_content));
        $result = mysql_query($query);
    }

    function getFreshPlaceId( $prm_placeId ){
        // Perform Query and get fresh_place_id
        $query = "SELECT * FROM places WHERE place_id = '$prm_placeId'";
        $result = mysql_query($query);
        $row = mysql_fetch_assoc($result);
        $fresh_PlaceId = $row['fresh_place_id'];
        return $fresh_PlaceId;
    }

?>
