<?php
include '_connect_line.php';
include 'Project_Data_to.php';

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

// สมมติว่า Getdata_to() ส่งคืน JSON
$getData_to = Getdata_to();
$getDataArray_to = json_decode($getData_to, true); // แก้ไขจาก $getData เป็น $getData_to

$text_reply  = $getDataArray_to[0]['ชื่อเล่น'];

// $text_reply = '';
// foreach ($getDataArray_to as $type) {
//     // $text_reply .= "ชื่อ-นามสกุล: " . $type['ชื่อ_นามสกุล'] . "\n";  
//     $text_reply .= "ชื่อเล่น: " . $type['ชื่อเล่น'] . "\n";  
//     // $text_reply .= "ตำแหน่ง: " . $type['ตำแหน่ง'] . "\n";  
//     // $text_reply .= "ฝ่าย/สังกัด: " . $type['ฝ่าย/สังกัด'] . "\n"; // เปลี่ยนจาก 'ฝ่าย/สังกัด' เป็น 'สาขา'
//     // $text_reply .= "แผนก: " . $type['แผนก'] . "\n"; 
//     // $text_reply .= "Email: " . $type['Email'] . "\n"; // เปลี่ยนจาก 'Email' เป็น 'e_mail'
//     $text_reply .= "\n"; 
// }

replyMessage($replyToken, $text_reply);

// ส่ง HTTP status code 200 เพื่อบอก LINE ว่าได้รับ webhook แล้ว
http_response_code(200);
echo 'OK';
?>
