<?php
    require dirname(dirname(__FILE__)) . "/inc/bootstrap.php";
    
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

        public function get_user_by_google_id($google_id) {
            $param_types = "s";
            return $this->select("SELECT * FROM users WHERE google_id = ?", $param_types, [$google_id]);
        }

        public function create_google_user($email, $google_id) {
            $param_types = "sss";
            // Assuming 'insert' is a method defined elsewhere in your class that inserts a record
            return $this->insert("INSERT INTO users (user_name, google_id, email) VALUES (?, ?, ?)", $param_types, [$email, $google_id, $email]);
        }

        public function validate_or_create_google_user($google_id, $email)
        {
            $user = $this->get_user_by_google_id($google_id);

            if (!empty($user)) {
                // User exists, return a successful ApiResponse with the user data
                return ApiResponse::success_data($user);
            } else {
                // Create the user in the database
                $new_user_id = $this->create_google_user($email, $google_id);

                if ($new_user_id) {
                    // Fetch the newly created user's data
                    $new_user = $this->get_user_by_google_id($google_id);
                    if (!empty($new_user)) {
                        // Return a successful ApiResponse with the new user data
                        return ApiResponse::success_data($new_user);
                    } else {
                        // The user was supposedly created, but we can't find it immediately after creation.
                        // This indicates a more systemic issue, such as a database inconsistency or a replication lag.
                        return ApiResponse::error("google_id_login", "User was created but could not be retrieved.");
                    }
                } else {
                    // User creation failed, return an error ApiResponse
                    return ApiResponse::error("google_id_create", "Failed when creating a new user with Google ID.");
                }
            }
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
            return ApiResponse::validate_single_entry_not_empty($results, $error_code, $error_msg);
        }
    }