<?php
require_once("config.php");
require_once("GPlaceAPI.php");
$place = new GPlaceAPI();

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
                // Make an AJAX call.
                form_elements = $("#frm_message_board").serialize();
                $.ajax({
                    type: "POST",
                    data: "q=showmsgs&"+form_elements,
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
                    async: true,
                    success: function(msg){
                        $("#msg_content").attr("value","");
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
                    setInterval("fn_show_msgboard();",3000);
                }
            );
        </script>
    </head>
    <body>
        <?php
        $userId = 0;
        $placeId = 0;
        $placeReference = "";
        $placeName = "";
        $lat = 0;
        $lng = 0;
        //if(isset($_SESSION['user_id'])){
        //    $userId = $_SESSION['user_id'];
        //}
        //else{
        if (isset($_GET['uid'])) {
            $userId = $_GET['uid'];
        }
        //}
        if (isset($_GET['place_id'])) {
            $placeId = $_GET['place_id'];
        }
        if (isset($_GET['reference'])) {
            $placeReference = $_GET['reference'];
        }
        if (isset($_GET['place_name'])) {
            $placeName = $_GET['place_name'];
        }
        if (isset($_GET['lat'])) {
            $lat = $_GET['lat'];
        }
        if (isset($_GET['access_token'])) {
            $access_token = $_GET['access_token'];
        }
        if (isset($_GET['lng'])) {
            $lng = $_GET['lng'];
        }

        $freshPlaceId = 0;
        $isOldPlace = false;

        $placeDetails = getPlaceDetails( $placeId );
        if( $placeDetails == 0 ){
            $query = sprintf("INSERT INTO places(place_id,source,place_name,lat,lng,place_ref) values ('%s','G','%s',%s,%s,'%s') ", $placeId, mysql_real_escape_string($placeName), $lat, $lng, mysql_real_escape_string($placeReference));
            $result = mysql_query($query);
            $freshPlaceId = mysql_insert_id();
        }
        else {
            $freshPlaceId = $placeDetails['fresh_place_id'];
            $isOldPlace = true;
        }

        $query = sprintf("INSERT INTO checkin(fresh_place_id,person_id) VALUES(%s,'%s')", $freshPlaceId, $userId);
        $result = mysql_query($query);

        // Check result
        if ($isOldPlace) {
            //Place already existed
            $query = sprintf("SELECT place_name,vicinity,phone,address,rating FROM place_detail WHERE place_id='%s' ", $placeId);
            // Perform Query
            $result = mysql_query($query);
            // Check result
            // This shows the actual query sent to MySQL, and the error. Useful for debugging.
            if (!$result) {
                $message = 'Could not get place detail!' . $query;
                die($message);
            }
            // Use result
            // One of the mysql result functions must be used
            if ($row = mysql_fetch_assoc($result)) {
                echo "You are now Checked Into: ";
                $locationName = $row['place_name'];
                echo $locationName . "!";
                // commenting these results out because now we are going to focus on messages, but they were originally there to enable confirmation of successful check-in and access to Gplaces
                // echo "<br />Vicinity: " . $row['vicinity'];
                // echo "<br />Phone: " . $row['phone'];
                // echo "<br />Address: " . $row['address'];
                // echo "<br />Rating: " . $row['rating'];
            }
        }
        else {
            // commenting out text like phone number etc, to focus on messaging, but they were there for debugging
            echo "You are now Checked Into: ";
            $businessDetail = $place->getBusinessDetail($placeReference);
            $result = $businessDetail['result'];
            echo "<br />Location Name: " . $result['name'];
            $locationName = mysql_real_escape_string($result['name']);
            $vicinity = "";
            if (isset($result['vicinity'])) {
                //echo "<br />Vicinity: " . $result['vicinity'];
                $vicinity = mysql_real_escape_string($result['vicinity']);
            }
            $phone = "";
            if (isset($result['formatted_phone_number'])) {
                // echo "<br />Phone: " . $result['formatted_phone_number'];
                $phone = mysql_real_escape_string($result['formatted_phone_number']);
            }
            echo "<br />Address: " . $result['formatted_address'];
            $address = mysql_real_escape_string($result['formatted_address']);
            $rating = 0;
            if (isset($result['rating'])) {
                // echo "<br />Rating: " . $result['rating'];
                $rating = mysql_real_escape_string($result['rating']);
            }
            $query = sprintf("INSERT INTO place_detail(fresh_place_id,place_id,place_name,lat,lng,place_ref,vicinity,phone,address,rating) values (%s,'%s','%s',%s,%s,'%s','%s','%s','%s',%s) ", $freshPlaceId, $placeId, $locationName, $lat, $lng, mysql_real_escape_string($placeReference), $vicinity, $phone, $address, $rating);
            $result = mysql_query($query);
            //echo $query;
        }

        function getPlaceDetails( $prm_placeId ){
            // Perform Query and get fresh_place_id
            $query = "SELECT * FROM places WHERE place_id = '$prm_placeId'";
            $result = mysql_query($query);
            $row = mysql_fetch_assoc($result);
            if (mysql_num_rows($result) == 0) {
                return 0;
            }
            return $row;
        }

        ?>

        <!-- Now we let people leave and read messages -->
        Leave a message!
        <form id="frm_message_board" name="frm_message_board" method="POST" action="javascript: fn_post_message();">
            <input type="hidden" name="user_id" id="user_id" value="<?php echo $userId; ?>" />
            <input type="hidden" name="place_id" id="place_id" value="<?php echo $placeId; ?>" />
			<input type="hidden" name="access_token" id="access_token" value="<?php echo $access_token; ?>" >
            <br />
            <input type="text" name="msg_content" id="msg_content" />
            <input type="button" value="Yodel It!" onclick="fn_post_message();" />
        </form>

        <!-- Read messages -->
        <br>
        'Da <?php echo $locationName; ?> Message Board:
        <br>
        <div id="div_messages"></div>
    </body>
</html>