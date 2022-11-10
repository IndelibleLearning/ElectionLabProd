<?php
    require_once "/home/indeli9/public_html/test/david/inc/bootstrap.php";
     
    class DateTimeUtility 
    {
        public static function datetime_from_now($time_to_add) 
        {
            $start_time = date("Y-m-d H:i:s");

            $converted_time = date('Y-m-d H:i:s', strtotime($time_to_add, strtotime($start_time)));
            
            return $converted_time;
        }
    }