<?php
include '_connect_line.php';
$requestMethod = $_SERVER["REQUEST_METHOD"];

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

$ask_hello = ["สวัสดี", " สวัสดีครับ", "สวัสดีค่ะ", "hi"];
$ask_name = ["ชื่ออะไร", " name", ];

$request = file_get_contents('php://input');
$data = json_decode($request, true);
$text = $data['events'][0]['message']['text'];
$replyToken = $data['events'][0]['replyToken'];
        
        if ($text) {
                if (in_array($text, $ask_hello)) {
                        // แปลงข้อมูล array เป็นข้อความที่จะแสดง
                        $text_reply = "สวัสดีครับ";
                        replyMessage($replyToken, $text_reply);
                    }
                 elseif (in_array($text, $ask_name)) {
                        // แปลงข้อมูล array เป็นข้อความที่จะแสดง
                        $text_reply = "ชื่อตัวอันตรายครับ";
                        replyMessage($replyToken, $text_reply);

                    }
                }
       
        
        
        http_response_code(200);
        echo 'OK';
        ?>