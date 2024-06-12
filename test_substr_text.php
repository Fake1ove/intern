<?php
include '_connect.php';

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

            if(substr($Text_message,0,9) == 'NC online' ){
                // $site = explode("สาขาผู้รับ NC : ",$Text_message);
                // $site = $site[1];
                // $site = substr($site,0,2);

                // $Text_message = str_replace("\n","",$Text_message);
                // $department = explode(" : ",$Text_message);
                // $department = $department[14];
                
                // $id = explode("id : ",$Text_message);
                // $id = $id[1];
                // $id = substr($id,0,8);
                
                // $text_reply .=  "สาขาผู้รับ NC : " .  $site . "\n";
                // $text_reply .=  "แผนกผู้รับ NC : " .  $department . "\n";
                // $text_reply .=  "id : " .  $id; "\n";
                
                $lines = explode("\n", $Text_message);
                $site = '';
                $department = '';
                $id = '';
                foreach ($lines as $line) {
                    // ข้ามบรรทัดที่ว่าง
                    if (trim($line) === '') {
                        continue;
                    }
                    // แยก key และ value
                    list($key, $value) = explode(" : ", $line);
                    // ตัดช่องว่างที่ไม่จำเป็นออกจาก key และ value
                    $key = trim($key);
                    $value = trim($value);
                    
                    // ตรวจสอบ key และกำหนดค่าให้ตัวแปรที่ต้องการ
                    if ($key == 'สาขาผู้รับ NC') {
                        $site = $value;
                    } elseif ($key == 'แผนกผู้รับ NC') {
                        $department = $value;
                    } elseif ($key == 'id') {
                        $id = $value;
                    }
                }
                
                $text_reply .=  "สาขาผู้รับ NC : " .  $site . "\n";
                $text_reply .=  "แผนกผู้รับ NC : " .  $department . "\n";
                $text_reply .=  "id : " .  $id; "\n";

                replyMessage($replyToken,$text_reply );
               




            }else{
                replyMessage($replyToken,substr($Text_message,0,9));
            }
          
            (explode("สาขาผู้รับ NC",$Text_message));
            
        }
    }
}

// ส่ง HTTP status code 200 เพื่อบอก LINE ว่าได้รับ webhook แล้ว
http_response_code(200);
echo 'OK';
?>
