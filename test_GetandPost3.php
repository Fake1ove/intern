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

            $getData = Getdata();
            $getDataArray = json_decode($getData, true);

          

            // กรองข้อมูลที่สาขาเป็น 'R9' และแผนกเป็น 'LAB'
            // $filteredData = array_filter($getDataArray, function ($value) {
            //     $sss = array();
            //     if ($item['site'] === 'R9' && $item['department'] === 'LAB') {
            //         array_push($sss, $item['e_mail']);
            //     }
            //     return $sss;
            // });

            $sss = array();
            foreach ($getDataArray as $key => $value) {
                if ($value['site'] === 'R9' && $value['department'] === 'LAB') {
                    array_push($sss, $value['e_mail']);
                }
            }
        };


            // $sss = json_encode($sss);
            // replyMessage($replyToken, $sss);

            // เลือกอีเมลแรกที่พบในข้อมูลที่กรองแล้ว
          
            // $dataMail = [
            //     'from_name' => 'NC Online',
            //     'mail_to' => $sss,
            //     'subject' => 'NC Online',
            //     'body' => 'สรุปปัญหา/เหตุการณ์โดยย่อ  : เคสบนวอร์ดส่งตัวไปทำ CT scan ที่โรงพยาบาลสัตว์อื่น หลังจากกลับมาเจ้าของพาสุนัขขึ้นวอร์ดมาด้วยตนเองเพื่อมาฝากดูแลต่อ โดยไม่ได้ผ่านฟร้อนรับเรื่องด้านหน้า' . "\n" .
            //                'ID : UID56537'  . "\n" .
            //                'สาขาผู้รับ NC : PK'  . "\n" .
            //                'แผนกผู้รับ NC : FRONT OFFICE' ,
            //     'mail_cc' => $email_cc
            //     ];



            $dataMail = [
                    'from_name' => 'NC Online',
                    'mail_to' => $sss,
                    'subject' => 'NC Online',
                    'body' => 'สรุปปัญหา/เหตุการณ์โดยย่อ  : เคสบนวอร์ดส่งตัวไปทำ CT scan ที่โรงพยาบาลสัตว์อื่น หลังจากกลับมาเจ้าของพาสุนัขขึ้นวอร์ดมาด้วยตนเองเพื่อมาฝากดูแลต่อ โดยไม่ได้ผ่านฟร้อนรับเรื่องด้านหน้า' . '<br>' .
                              'ID : UID56537'  . '<br>' .
                              'สาขาผู้รับ NC : PK'  . '<br>' .
                              'แผนกผู้รับ NC : FRONT OFFICE',
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
    



header('Content-Type: application/json; charset=utf-8');
http_response_code(200);
echo json_encode(['status' => 'success']);
