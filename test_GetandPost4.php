<?php
include '_connect.php';
include '_getdatasheet.php';
include 'sendemail/sendEmail.php';

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
    if (curl_errno($ch)) {
        error_log('cURL error: ' . curl_error($ch));
    }
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

            // ดึงข้อมูลจาก data sheet
            $getData = Getdata();
            $getDataArray = json_decode($getData, true);

            // กรองข้อมูลที่สาขาเป็น 'R9' และแผนกเป็น 'LAB'
            $filteredData = array_filter($getDataArray, function ($item) {
                return $item['site'] === 'R9' && $item['department'] === 'LAB';
            });

            // ส่งอีเมลถึงทุกคนในรายการที่กรองมา
            foreach ($filteredData as $item) {
                $email = $item['e_mail'];
                $name = $item['name'] ?? 'User';
                $site = $item['site'] ?? 'Unknown';
                $department = $item['department'] ?? 'Unknown';

                $dataMail = [
                    'from_name' => 'e-Smart test0',
                    'mail_to' => $email,
                    'subject' => 'test0',
                    'body' => 'Hello ' . $name  . $site . ' and department ' . $department . '.' 
                ];

                $sendResult = sendEmail($dataMail);

                if ($sendResult) {
                    replyMessage($replyToken, "ส่งเมลล์เรียบร้อย");
                } else {
                    replyMessage($replyToken, "Error: Failed to send email.");
                }
            }
        }
    }
} else {
    error_log('Error: Invalid JSON format or no events found.');
}

header('Content-Type: application/json; charset=utf-8');
http_response_code(200);
echo json_encode(['status' => 'success']);
?>
