<?php
    require dirname(dirname(dirname(__FILE__))) . "/inc/bootstrap.php";
 
    class RestHelper {
        
        public static function rest_call($url) 
        {
            $ch = curl_init($url);
            $options = array(
                CURLOPT_RETURNTRANSFER => true,   // return web page
                CURLOPT_HEADER         => false,  // don't return headers
                CURLOPT_FOLLOWLOCATION => true,   // follow redirects
                CURLOPT_MAXREDIRS      => 10,     // stop after 10 redirects
                CURLOPT_ENCODING       => "",     // handle compressed
                CURLOPT_USERAGENT      => "test", // name of client
                CURLOPT_AUTOREFERER    => true,   // set referrer on redirect
                CURLOPT_CONNECTTIMEOUT => 120,    // time-out on connect
                CURLOPT_TIMEOUT        => 120,    // time-out on response
            );
            curl_setopt_array($ch, $options);
            $result = json_decode(curl_exec($ch), true);
            curl_close ($ch);
            
            return $result;
        }
        
    } 
    
    
    
    