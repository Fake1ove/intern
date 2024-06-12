<?php 
    
    function Getdata($site=null,$branch=null){

          $curl = curl_init();

          curl_setopt_array($curl, array(
            // CURLOPT_URL => 'https://script.google.com/macros/s/AKfycbyzdu4F8htnwiuxV_gGo46Fonjle7Ha34PLD7jeaiG9ZOBa4jBtPF6LCdVi9ckMd5hT/exec?action=getUsers',
            CURLOPT_URL => 'https://script.google.com/macros/s/AKfycbxqawsXAcCZfqVeqknK39OU-WazCS0juCApUD4mOsfjDfReY76vV6DglU36e3KE4wOOLQ/exec?action=getUsers',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
          ));
          
          $response = curl_exec($curl);
          
          curl_close($curl);
          return $response;
      

        
    }

?>