<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'src/Exception.php';
require 'src/PHPMailer.php';
require 'src/SMTP.php';

 function sendEmail($dataMail){

		$mail = new PHPMailer(true);

		try {
			//Server settings
			//$mail->SMTPDebug = SMTP::DEBUG_SERVER;
			$mail->CharSet = "utf-8";
			$mail->isSMTP();     
			$mail->Host       = 'smtp.gmail.com'; 
			$mail->SMTPAuth   = true;   
			$mail->Username   = 'tlprofile6606@gmail.com';  
			$mail->Password   = 'uvbfpbplhgrbgxcv';
			$mail->SMTPSecure = 'tls';            
			$mail->Port       = 587;

			//Recipients
			$mail->setFrom('thonglor.profile@gmail.com', $dataMail['from_name']);

			foreach($dataMail['mail_to'] as $email)
			{
				$mail->addAddress($email);
			}
		
			foreach($dataMail['mail_cc'] as $email_cc)
			{
				$mail->addCC($email_cc);
			}
			
			//Content
			$mail->isHTML(true);                      
			$mail->Subject = $dataMail['subject'];
			$mail->Body    = $dataMail['body'];
			$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

			$mail->send();
			return json_encode(array('code'=> '1','message'=> 'success'));
		} catch (Exception $e) {

			return json_encode(array('code'=> '1','message'=> "Message could not be sent. Mailer Error: {$mail->ErrorInfo}"));
		}
	}
	
	
?>