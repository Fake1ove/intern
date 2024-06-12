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

function fetchPaymentTypes($conn) {
		$stmt = $conn->prepare("SELECT * from intra_tl.tlpayment_type ");
		$stmt->execute();
        // $sss = runGet($stmt);
    //     while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    //         echo 'รหัส : "' . $row['tlpayment_type_id'] . "\"\n";
    //         echo 'ชื่อ : "' . $row['tlpayment_type'] . "\"\n";
    // }
            $paymentTypes = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $paymentTypes[] = $row;
            }

            return $paymentTypes;
        }
            
$paymentTypes = fetchPaymentTypes($conn);
$ask_payment_type = ["ขอ payment_type", "tlpayment type", "tlpayment", "hi"];
$ask_payment_type_id = ["รหัส". $type['tlpayment_type_id'],"id". $type['tlpayment_type_id']];

$request = file_get_contents('php://input');
$data = json_decode($request, true);
$text = $data['events'][0]['message']['text'];
$replyToken = $data['events'][0]['replyToken'];
        
        if ($text) {
                if (in_array($text, $ask_payment_type)) {
                        // แปลงข้อมูล array เป็นข้อความที่จะแสดง
                        $text_reply = '';
                        foreach ($paymentTypes as $type) {
                            $text_reply .= 'รหัส: ' . $type['tlpayment_type_id'] . "\n";
                            $text_reply .= 'ชื่อ: ' . $type['tlpayment_type'] . "\n\n";
                        }
                        replyMessage($replyToken, $text_reply);
                
                }else { (in_array($text, $ask_payment_type)) ;
                    // Check if the text matches a payment type ID
                    $stmt = $conn->prepare("SELECT * from intra_tl.tlpayment_type WHERE tlpayment_type_id = ?");
                    $stmt->execute([$text]);
                    $paymentType = $stmt->fetch(PDO::FETCH_ASSOC);
                
                    if ($paymentType) {
                    //   หาเจอ
                        $text_reply = 'รหัส: ' . $paymentType['tlpayment_type_id'] . "\n";
                        $text_reply = 'ชื่อ: ' . $paymentType['tlpayment_type'] . "\n";
                        replyMessage($replyToken, $text_reply);
                    } else {
                    //   หาไม่เจอ
                        $text_reply = 'ไม่พบประเภทรายการชำระเงินที่ตรงกับรหัสนี้';
                        replyMessage($replyToken, $text_reply);
                          }
                       }
                    }
          
         else {
                $text_reply = "No Data";
                replyMessage($replyToken, $text_reply);
            }
        
        
        http_response_code(200);
        echo 'OK';
        ?>