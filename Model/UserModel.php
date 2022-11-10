<?php
    require_once "/home/indeli9/public_html/test/david/inc/bootstrap.php";
    
    require_once PROJECT_ROOT_PATH . "/Model/Database.php";
    require_once PROJECT_ROOT_PATH . "/Model/DateTimeUtility.php";
    require_once PROJECT_ROOT_PATH . "/Model/ApiResponse.php";
     
    class UserModel extends Database
    {
        public function getUsers()
        {
            return $this->select("SELECT * FROM users");
        }
        
        public function get_user_by_username($user_name)
        {
            $param_types = "s";
            return $this->select("SELECT * FROM users where user_name = ?", $param_types, [$user_name]);
        }
        
        public function validate_user_by_username($user_name)
        {
            $error_code = "user_not_found";
            $error_msg = "Could not find user $user_name";
            $results = $this->get_user_by_username($user_name);
            return ApiResponse::validate_single_entry_not_empty($results, $error_code, $error_msg);
        }
        
        public function create_user($user_name, $password, $email)
        {
            $param_types = "sss";
            return $this->insert("INSERT INTO users (user_name, password, email) VALUES (?, ?, ?)", $param_types, [$user_name, $password, $email]);
        }
        
        public function set_login_token($user_id, $token)
        {
            $param_types = "sss";
            $expire_date = DateTimeUtility::datetime_from_now("+1 hour");
            return $this->update("UPDATE users SET login_token=?, login_expires=? where id=?", $param_types, [$token, $expire_date, $user_id]);
        }
        
        public function check_login_token($user_id, $token)
        {
            $param_types = "is";
            return $this->select("SELECT * FROM users where id = ? AND login_token=?", $param_types, [$user_id, $token]);
        }
        
        public function validate_login_token($user_id, $token)
        {
            $error_code = "incorrect_token";
            $error_msg = "Could not find token $token";
            $results = $this->check_login_token($user_id, $token);
            return ApiResponse::validate_not_null_or_blank($results, $error_code, $error_msg);
        }
    }