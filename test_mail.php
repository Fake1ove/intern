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
  
    $data = Getdata();

    $datamail = array(
		'from_name' => 'e-Smart SSO',
		'mail_to' => array('teebenten10@gmail.com'),
		'subject' => 'สวัสดี ',
		'body' => 'สวัสดีฮะ ',
	                    );

	$return = sendEmail($datamail);

    replyMessage($replyToken, "ส่งเมลล์เรียบร้อย");

          }
        }
    }

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($return);
?>