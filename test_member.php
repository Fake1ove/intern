<?php
include '_connect.php';
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

function fetchMember($conn) {
		$stmt = $conn->prepare("SELECT * from intra_tl.tmp_test ");
		$stmt->execute();
        $Member = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $Member[] = $row;
            }

            return $Member;
        }

$Member = fetchMember($conn);



$ask_Member = ["Member", "ขอสมาชิก", "hi"];

        
$request = file_get_contents('php://input');
$data = json_decode($request, true);
$text = $data['events'][0]['message']['text'];
$replyToken = $data['events'][0]['replyToken'];
                
if ($text) {
    if (in_array($text, $ask_Member)) {
            // แปลงข้อมูล array เป็นข้อความที่จะแสดง
            $text_reply = '';
                 foreach ($Member as $type) {
                        $text_reply .=  'รหัส:' . $type['pass_id'] . "\n";  
                        $text_reply .=  $type['pre_name'] . $type['first_name'] . "  ". $type['last_name'] . "\n"; 
                        $text_reply .=  'อายุ:' . $type['age'] . "  ". 'เพศ:' . $type['gender'] . "\n";     
            }
                replyMessage($replyToken, $text_reply);

  
             
         } 
    }
http_response_code(200);
echo 'OK';
    
?>