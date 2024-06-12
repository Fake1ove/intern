<?php
include '_connect.php';
include '_getdatasheet.php';
include '_getdatasheet2.php';
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
            $textMessage = $event['message']['text'];

            $getData = Getdata();
            $getDataArray = json_decode($getData, true);

            $emails = [];
            foreach ($getDataArray as $item) {
                if (isset($item['e_mail'])) {
                    $emails[] = $item['e_mail'];
                }
            }

            $getData2 = Getdata2();
            $getDataArray2 = json_decode($getData2, true);

            $email_cc = [];
            foreach ($getDataArray2 as $item) {
                if (isset($item['e_mail'])) {
                    $email_cc[] = $item['e_mail'];
                }
            }

            $dataMail = [
                'from_name' => 'e-Smart test_cc0',
                'mail_to' => $emails,
                'subject' => 'test_cc0',
                'body' => 'test0',
                'mail_cc' => $email_cc
                ];

            $sendResult = sendEmail($dataMail);

            if ($sendResult) {
                replyMessage($replyToken, "ส่งเมลล์เรียบร้อย");
            } else {
                replyMessage($replyToken, "Error: Failed to send email.");
            }
        }
    }
} else {
    error_log('Error: Invalid JSON format or no events found.');
}

header('Content-Type: application/json; charset=utf-8');
http_response_code(200);

?>
