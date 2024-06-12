<?php 
    
    function Getdata2($site=null,$branch=null){

          $curl = curl_init();

          curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://script.google.com/macros/s/AKfycbxSUupv2CDh9Tw6spWgktM4xOOZLKUDb_cKgNSCMBFMAwcRa6ZqPsvDXVDiMgoolTBrNw/exec?action=getUsers',
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