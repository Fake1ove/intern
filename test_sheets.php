<?php
include '_connect_line.php';
include '_getdatasheet.php';

// รับข้อมูลจาก LINE 
function replyMessage($replyToken, $messageText)
{
    global $channel_access_token;
    $response = [
        'replyToken' => $replyToken,
        'messages' => [
            ['type' => 'text', 'text' => $messageText]
        ]
    ];

    $ch = curl_init('https://api.line.me/v2/bot/message/reply');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($response));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $channel_access_token
    ]);

    $result = curl_exec($ch);
    curl_close($ch);

    return $result;
}
$request = file_get_contents('php://input');
$data = json_decode($request, true);

if (isset($data['events'])) {
    foreach ($data['events'] as $event) {
        if ($event['type'] == 'message' && $event['message']['type'] == 'text') {
            $replyToken = $event['replyToken'];
            $Text_message = $event['message']['text'];
        }
    }
}


$Getdata = Getdata();
$ssss = json_decode($Getdata,true);

// $text_reply  = "สาขาผู้รับ NC : " .  $site . "\n";
$text_reply  = $ssss[0]['name'];
// $text_reply  = $ssss[1]['name'.'site'.'department'.'e_mail'];
// $text_reply  = $ssss[2]['name'.'site'.'department'.'e_mail'];



// $text_reply = '';
// foreach ($ssss as $type) {
//     $text_reply .= "ชื่อ-นามสกุล: " .$type['name'] . "\n";  
//     $text_reply .= "สาขา: " . $type['site'] . "\n";  
//     $text_reply .= "แผนก: " . $type['department'] . "\n";  
//     $text_reply .= "E-mail: " . $type['e_mail'] . "\n";  

// }
replyMessage($replyToken, $Getdata);




// ส่ง HTTP status code 200 เพื่อบอก LINE ว่าได้รับ webhook แล้ว
http_response_code(200);
echo 'OK';
