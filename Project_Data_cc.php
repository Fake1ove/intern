<?php 
    
    function Getdata_cc($site=null,$branch=null){

          $curl = curl_init();

          curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://script.google.com/macros/s/AKfycbyPaOxAGbJeY8DfGQlJgLtFQk1U4P7Zzm5gwJi9RX7PxvHRSUd43OWIw7WnXQ5XyWFR/exec?action=getUsers',
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