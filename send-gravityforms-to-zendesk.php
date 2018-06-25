<?php
/*
* This code was originally part of the functions file and was used to send entries from gravity forms into zendesk.
* Use this to help getting started with getting gravity form entries to send to a blockchain. 
*/


/*
 * ZENDESK API
 */
define("ZDAPIKEY", "r5az19PLJmsfoznLRgl8G5cREqxqmknduupwo19M");
define("ZDUSER", "drron@draftbernie.org");
define("ZDURL", "https://draftbernie.zendesk.com/api/v2");
/*
 * Here goes the Gravity Forms Functions
 */
add_action("gform_after_submission_1", "send_contact_to_zendesk1", 10, 2); // Invoke ZENDESK for form #1
add_action("gform_after_submission_3", "send_contact_to_zendesk3", 10, 2); // Invoke ZENDESK for form #1
//add_action("gform_after_submission_1", "ult_cu_disable_post_creation", 20, 2);  // Disable entry creation for FROM id #1

/*
 * Deletes entry created in WordPress Dashboard 
 */
//function ult_cu_disable_post_creation( $entry, $form ) {
   // GFAPI::delete_entry( $entry['id'] );
//}

/*
 * Prepares and sends data to Zendesk
 */
function send_contact_to_zendesk1($entry,$form){
    $create = json_encode(
        array(
            'ticket' => array(
                'subject' => 'Contact Form Entry', // Title of ticket ids of fields you need in subject field ours was type of request - message title
                'comment' => array(
                    "value"=> 'Joining: ' . $entry[4]. ' - Zipcode: ' . $entry["5.5"] . ' - Country: ' . $entry["5.6"] . ' - Skills: ' . $entry[6] . ' - Comment: ' . $entry[3]     // content of the ticket
                ),
                'requester' => array(
                    'name' => $entry[1], // Name of ticket creator
                    'email' => $entry[2] // email of ticket creator
                )
            )
        )
    );
    $return = ZD_contact_us_curlWrap("/tickets.json", $create);
}
function send_contact_to_zendesk3($entry,$form){

    $interests = "";

    if($entry["17.1"]){
        $interests .= $entry["17.1"] . ', ';
    }
    if($entry["17.2"]){
        $interests .= $entry["17.2"] . ', ';
    }
    if($entry["17.3"]){
        $interests .= $entry["17.3"] . ', ';
    }
    if($entry["17.4"]){
        $interests .= $entry["17.4"] . ', ';
    }
    if($entry["17.5"]){
        $interests .= $entry["17.5"] . ', ';
    }
    if($entry["17.6"]){
        $interests .= $entry["17.6"] . ', ';
    }

    $social = "";

    if($entry[12]){
        $social .= ' - Facebook: ' . $entry[12] . ', ';
    }
    if($entry[13]){
        $social .= ' - Twitter: ' . $entry[13] . ', ';
    }
    if($entry[14]){
        $social .= ' - Instagram: ' . $entry[14] . ', ';
    }
    if($entry[15]){
        $social .= ' - Reddit: ' . $entry[15] . ', ';
    }


    $create = json_encode(
        array(
            'ticket' => array(
                'subject' => 'Volunteer Form Entry', // Title of ticket ids of fields you need in subject field ours was type of request - message title
                'comment' => array(
                    "value"=> 'City: ' . $entry[4] . ' - Zipcode: ' . $entry[7] . ' - Phone: ' . $entry[5] . ' - Presidential Primary Involvement: ' . $entry[9] . ' - Interests: ' . $interests . $entry[11] . $social . ' - Comments: ' . $entry[16]     // content of the ticket
                ),
                'requester' => array(
                    'name' => $entry[1] . ' ' . $entry[2], // Name of ticket creator
                    'email' => $entry[6] // email of ticket creator
                )
            )
        )
    );
    $return = ZD_contact_us_curlWrap("/tickets.json", $create);
}

/*
 * Zendesk post function 
 * TODO : use wp_remote_post
 */
function ZD_contact_us_curlWrap($url, $json){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true );
    curl_setopt($ch, CURLOPT_MAXREDIRS, 10 );
    curl_setopt($ch, CURLOPT_URL, ZDURL.$url);
    curl_setopt($ch, CURLOPT_USERPWD, ZDUSER."/token:".ZDAPIKEY);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
    curl_setopt($ch, CURLOPT_USERAGENT, "MozillaXYZ/1.0");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $output = curl_exec($ch);
    curl_close($ch);
    $decoded = json_decode($output);
    return $decoded;
}
?>
