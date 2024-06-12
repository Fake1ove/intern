<?php
include '_connect.php';
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
    $stmt = $conn->prepare("SELECT * FROM intra_tl.tmp_test");
    $stmt->execute();
    $Member = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $Member[] = $row;
    }
    return $Member;
}

$Member = fetchMember($conn);

$ask_Member = ["Member", "สมาชิก", "hi"];
$ask_plus_Member = ["เพิ่ม", "pp"];

$request = file_get_contents('php://input');
$data = json_decode($request, true);
$text = $data['events'][0]['message']['text'];
$replyToken = $data['events'][0]['replyToken'];

if ($text) {
    if (in_array($text, $ask_Member)) {
        // แปลงข้อมูล array เป็นข้อความที่จะแสดง
        $text_reply = '';
        foreach ($Member as $type) {
            $text_reply .= 'รหัส:' . $type['pass_id'] . "\n";  
            $text_reply .= $type['pre_name'] . $type['first_name'] . " " . $type['last_name'] . "\n"; 
            $text_reply .= 'อายุ:' . $type['age'] . " " . 'เพศ:' . $type['gender'] . "\n";     
        }
        replyMessage($replyToken, $text_reply);

    } elseif (in_array($text, $ask_plus_Member)) {
        // สมมติว่าคุณจะเพิ่มข้อมูลใหม่เข้าไป
        $pass_id = "2"; // เปลี่ยนเป็นข้อมูลที่ต้องการเพิ่ม
        $pre_name = "นาย"; 
        $first_name = "ประเสริฐ";
        $last_name = "เศรษฐี";
        $age = 30; // เปลี่ยนเป็นข้อมูลที่ต้องการเพิ่ม
        $gender = "ชาย"; // เปลี่ยนเป็นข้อมูลที่ต้องการเพิ่ม

        // $stmt = $conn->prepare("INSERT INTO intra_tl.tmp_test (pass_id, pre_name, first_name, last_name, age, gender) VALUES ('?', ?, ?, ?, ?, ?)");
        // $stmt->bind_param("ssssii", $pass_id, $pre_name, $first_name, $last_name, $age, $gender);

        $stmt = $conn->prepare("INSERT INTO intra_tl.tmp_test VALUES
        (:1,:2,:3,:4,:5,:6);");
        $return = $stmt->execute(
            [
                ':1' => $pass_id,
                ':2' => $pre_name,
                ':3' => $first_name,
                ':4' => $last_name,
                ':5' => $age,
                ':6' => $gender,
            
            ]
        );

        if ($return) {
            $text_reply = "เพิ่มข้อมูลสมาชิกใหม่เรียบร้อยแล้ว";
        } else {
            $text_reply = "เกิดข้อผิดพลาด: " . $stmt->error;
        }

        replyMessage($replyToken, $text_reply);
        $stmt->close();
    }
}

http_response_code(200);
echo 'OK';
?>
