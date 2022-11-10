<?php
    require_once "/home/indeli9/public_html/test/david/inc/bootstrap.php";
    
    class ApiResponse
    {
        private $data = null;
        private $has_errors = false;
        private $error_code = "";
        private $error_msg = "";
        
     
        public function __construct($data, $has_errors, $error_code, $error_msg)
        {
            $this->data = $data;
            $this->has_errors = $has_errors;
            $this->error_code = $error_code;
            $this->error_msg = $error_msg;
        }
        
        public function get_data()
        {
            return $this->data;
        }
        
        private function set_data($new_data)
        {
            $this->data = $new_data;
        }
        
        public function get_has_errors()
        {
            return $this->has_errors;
        }
        
        public function get_error_code()
        {
            return $this->error_code;
        }
        
        public function get_error_msg()
        {
            return $this->error_msg;
        }
        
        public function get_json_error()
        {
            $error = [];
            $error["err_code"] = $this->error_code;
            $error["has_errors"] = $this->has_errors;
            $error["err_msg"] = $this->error_msg;
            return json_encode($error);
        }
        
        public function get_json()
        {
            $this_resp = [];
            $this_resp["data"] = $this->data;
            $this_resp["has_errors"] = $this->has_errors;
            $this_resp["err_code"] = $this->error_code;
            $this_resp["err_msg"] = $this->error_msg;
            return json_encode($this_resp);
        }
        
        public static function validate_array_not_empty($results, $error_code, $error_msg)
        {
            $has_errors = false;
            $err_code = "";
            $err_msg = "";
            $data = null;
            
            if (!$results || count($results) <= 0)
            {
                $has_errors = true;
                $err_code = $error_code;
                $err_msg = $error_msg;
            }
            else
            {
                $data = $results;
            }
            
            return new ApiResponse($data, $has_errors, $err_code, $err_msg);
        }
        
        public static function validate_single_entry_not_empty($results, $error_code, $error_msg)
        {
            $api_response = ApiResponse::validate_array_not_empty($results, $error_code, $error_msg);
            $first_entry = $api_response->get_data()[0];
            
            $api_response->set_data($first_entry);
            
            return $api_response;
        }
        
        public static function validate_not_null_or_blank($results, $error_code, $error_msg)
        {
            $has_errors = false;
            $err_code = "";
            $err_msg = "";
            $data = null;
            
            
            if (!$results || $results == "") 
            {
                $has_errors = true;
                $err_code = $error_code;
                $err_msg = $error_msg;
            }
            
            return new ApiResponse($data, $has_errors, $err_code, $err_msg);
        }
        
        public static function validate_is_empty($results, $error_code, $error_msg)
        {
            $has_errors = false;
            $err_code = "";
            $err_msg = "";
            $data = null;
            
            if ($results || count($results) > 0)
            {
                $has_errors = true;
                $err_code = $error_code;
                $err_msg = $error_msg;
            }
            else
            {
                $data = $results;
            }
            
            return new ApiResponse($data, $has_errors, $err_code, $err_msg);
        }
        
        public static function success()
        {
            return ApiResponse::success_data("Success");
        }
        
        public static function success_data($data)
        {
            return new ApiResponse($data, false, "", "");
        }
        
        public static function error($err_code, $err_msg)
        {
            return new ApiResponse(null, true, $err_code, $err_msg);
        }
    }