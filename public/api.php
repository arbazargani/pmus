<?php

// Includes
include_once("classes.php");
include_once("uploader.php");

// Getting the URL from script AJAX
$req_url = $_REQUEST["url"];


// TEMP
$push = new push_to_pushe($req_url);
$result = $push->push();

echo "<pre>";
// print_r($json_data);
echo "<br>";
echo $result;
echo "</pre>";

exit(200);
// END TEMP

$site = new rec_driver($req_url);
$site = $site->site;

// Tokens
$YOUR_TOKEN = '6789c30fa6ec4649f41b3ccb6f3a2d8bb3e66bd4';
$YOUR_APP_ID = '0gr61p7l3524ozng';

$RK_TOKEN = "6789c30fa6ec4649f41b3ccb6f3a2d8bb3e66bd4";
$RK_APP_ID = "0gr61p7l3524ozng";

$EGHT100_TOKEN = 'a6144ba1cfe1913bf714fc9febdf59b1ff2a0439';
$EGHT100_APP_ID = 'qd2j32m5j6v5m52g';

$EGHTIR_TOKEN = 'a6144ba1cfe1913bf714fc9febdf59b1ff2a0439';
$EGHTIR_APP_ID = 'ldwz0zml5j5q347d';

$KHT_TOKEN = "a6144ba1cfe1913bf714fc9febdf59b1ff2a0439";
$KHT_APP_ID = "3e4l9y689r8w9zod";

// Grabbing data Class
switch ($site) {
    case 'rokna':
        $data = new grab_data_rk($req_url);
        $TOKEN = $RK_TOKEN;
        $APP_ID = $RK_APP_ID;
        break;
    case 'eghtesadeirani':
        $data = new grab_data_eght($req_url);
        $TOKEN = $EGHTIR_TOKEN;
        $APP_ID = $EGHTIR_APP_ID;
        break;
    case 'khatesalamat':
        $data = new grab_data_kht($req_url);
        $TOKEN = $KHT_TOKEN;
        $APP_ID = $KHT_APP_ID;
        break;
    default:
        break;
}

// $uploaded_files = pushe_upload($data->img , $TOKEN);

if($data->short_url){
    $push_url = $data->short_url;
}else{
    $push_url = $data->url;
}

// $json_data = '{
//     "data":{
//             "title":"'.$data->title.'",
//             "content":"'.$data->desc.'",
//             "icon":"'.$uploaded_files["icon"]["url"].'",
//             "action":{
//                 "id":"open_link",
//                 "action_type":"U",
//                 "params":{
//                     "url":"'.$push_url.'"
//                 },
//                 "url":"'.$push_url.'"
//             },
//             "buttons":[
//             {
//                 "btn_content":"اخبار ۲۴ ساعت",
//                 "btn_action":{
//                     "id":"open_link",
//                     "action_type":"U",
//                     "params":{
//                         "url":"https://www.rokna.net/fa/tiny/news-361587"
//                     },
//                     "url":"https://www.rokna.net/fa/tiny/news-361587"
//                 },
//                 "btn_order":0
//             },
//             {
//                 "btn_content":"مشاهده خبر",
//                 "btn_action":{
//                     "id":"open_link",
//                     "action_type":"U",
//                     "params":{
//                         "url":"'.$push_url.'"
//                     },
//                     "url":"'.$push_url.'"
//                 },
//                 "btn_order":1
//             }],
//             "image":"'.$uploaded_files["img"]["url"].'",
//             "close_on_click":false
//     },
//     "app_ids":['.$APP_ID.'],
//     "time_to_live":604800,
//     "data_type":1,
//     "is_draft":true,
//     "platform":2
// }';

if($site == "eghtesadeirani"){
    $json_data_2 = create_json($data , $push_url , $EGHTIR_APP_ID);
    $result = push($json_data_2 , $TOKEN);
    $json_data_2 = create_json($data , $push_url , $EGHT100_APP_ID);
    $result = push($json_data_2 , $TOKEN);
}else{
    $json_data_2 = create_json($data , $push_url , $APP_ID);
    $result = push($json_data_2 , $TOKEN);
}



echo "<pre>";
// print_r($json_data);
echo "<br>";
echo $result;
echo "</pre>";


// JSON Create Function
function create_json($data , $push_url , $APP_ID){
    $json_data = '{
        "data":{
                "title":"'.$data->title.'",
                "content":"'.$data->desc.'",
                "icon":"'.$data->img.'",
                "action":{
                    "id":"open_link",
                    "action_type":"U",
                    "params":{
                        "url":"https://rokna.net"
                    },
                    "url":"'.$push_url.'"
                },
                "image":"'.$data->img.'",
                "close_on_click":false
        },
        
        "app_ids": ["'.$APP_ID.'"],
        "time_to_live":604800,
        "data_type":1,
        "is_draft":true,
        "platform":2
    }';

    return $json_data;
}

// Curl Function to Send data to Pushe API
function push($json_data , $TOKEN){
    $ch = curl_init('https://api.pushe.co/v2/messaging/notifications/');
    curl_setopt_array($ch, array(
        CURLOPT_URL => 'https://api.pushe.co/v2/messaging/notifications',

        // SSL OFF
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,

        CURLOPT_POST  => 1,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => array(
            "Content-Type: application/json",
            "Authorization: Token ".$TOKEN,
        ),
    ));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}