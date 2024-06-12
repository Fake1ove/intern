<?php
include '_connect_line.php';
include '_getdatasheet.php';
include '_getdatasheet2.php';
include 'sendemail/sendEmail.php';

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
    curl_exec($ch);
    curl_close($ch);
}

$request = file_get_contents('php://input');
$data = json_decode($request, true);

if (isset($data['events'])) {
    foreach ($data['events'] as $event) {
        if ($event['type'] == 'message' && $event['message']['type'] == 'text') {
            $replyToken = $event['replyToken'];
            $Text_message = $event['message']['text'];

            if (substr($Text_message, 0, 9) == 'NC online') {
                $lines = explode("\n", $Text_message);
                foreach ($lines as $line) {
                    if (trim($line) === '') {
                        continue;
                    }

                    list($key, $value) = explode(" : ", $line);
                    $key = trim($key);
                    $value = trim($value);

                    if ($key == 'สาขาผู้รับ NC') {
                        $site = $value;
                    } elseif ($key == 'แผนกผู้รับ NC') {
                        $department = $value;
                    } elseif ($key == 'สรุปปัญหา/เหตุการณ์โดยย่อ') {
                        $problem = $value;
                    } elseif ($key == 'id') {
                        $id = $value;
                    }
                }
               
                         if ($site === null || $department === null || $problem === null || $id === null) {
                                replyMessage($replyToken, "การส่งเมลล์ล้มเหลว");

                         } else {$getData = Getdata();
                                $getDataArray = json_decode($getData, true);
                
                                $Datafilter = array();
                             foreach ($getDataArray as $data) {
                                if ($data['site'] == $site && $data['department'] == $department) {
                                    array_push($Datafilter, $data['e_mail']);
                                }
                            }
                             foreach ($getDataArray as $type) {
                                if ($type['site'] == $site && $type['position'] == 'ผู้จัดการ') {
                                    array_push($Datafilter, $type['e_mail']);
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
                                
                                $url = "https://www.youtube.com/watch?v=r817RLqmLac";
                
                                $dataMail = [
                                    'from_name' => 'NC Online',
                                    'mail_to' => $Datafilter,
                                    'subject' => 'NC Online',
                                    'body' =>   'สรุปปัญหา/เหตุการณ์โดยย่อ : ' . $problem . '<br>' .
                                                'ID : '  . $id . '<br>' .
                                                'สาขาผู้รับ NC : '  . $site . '<br>' .
                                                'แผนกผู้รับ NC : ' .$department . '<br>' . 
                                                'ลิ้งค์สำหรับการแก้ไข : ' . $url,
                                    'mail_cc' => $email_cc 
                                ];
                
                                $sendResult = sendEmail($dataMail);
                
                                if ($sendResult) {
                                    replyMessage($replyToken, "ส่งเมลล์เรียบร้อย");
            
                        } 
                    }
                } 
            }
        }
    }

http_response_code(200);
echo 'OK';
?>
