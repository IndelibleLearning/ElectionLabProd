<?php
    require dirname(dirname(__FILE__)) . "/inc/bootstrap.php";
    
    require_once PROJECT_ROOT_PATH . "/Model/Database.php";
    require_once PROJECT_ROOT_PATH . "/Model/DateTimeUtility.php";
    require_once PROJECT_ROOT_PATH . "/Model/ApiResponse.php";

    class RoomModel extends Database
    {
        public function getRooms()
        {
            return $this->select("SELECT * FROM rooms");
        }
        
        public function get_room_by_user_id($id)
        {
            $param_types = "i";
            return $this->select("SELECT * FROM rooms where host_id = ?", $param_types, [$id]);
        }
        
        public function get_room_with_name_by_user_id($id, $room_name)
        {
            $param_types = "is";
            return $this->select("SELECT * FROM rooms where host_id = ? and room_name=?", $param_types, [$id, $room_name]);
        }
        
        public function validate_unique_room_name($id, $room_name)
        {
            $error_code = "user_already_used_room";
            $error_msg = $error_msg = "Already used room name $room_name";
            $results = $this->get_room_with_name_by_user_id($id, $room_name);
            return ApiResponse::validate_is_empty($results, $error_code, $error_msg);
        }
        
        public function get_room_with_code($room_code)
        {
            $param_types = "s";
            return $this->select("SELECT * FROM rooms where room_code = ?", $param_types, [$room_code]);
        }
        
        public function validate_room_code_unused($room_code)
        {
            $error_code = "already_used_room_code";
            $error_msg = $error_msg = "Problem with room code. Please try again.";
            $results = $this->get_room_with_code($room_code);
            return ApiResponse::validate_is_empty($results, $error_code, $error_msg);
        }
        
        public function create_room($host_id, $room_code, $settings)
        {
            $param_types = "issiidds";
            $expire_date = DateTimeUtility::datetime_from_now("+1 hour");
            return $this->insert("INSERT INTO rooms (host_id, room_code, room_name, auto_assign, auto_assign_games_started, join_timer, pair_timer, expires_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)", $param_types, [$host_id, $room_code, $settings["room_name"], 
                    (int)$settings["auto_assign"],
                    (int)$settings["auto_assign_games_started"],
                    $settings["join_timer"],
                    $settings["pair_timer"], $expire_date]);
        }
        
        public function get_room_by_code($room_code)
        {
            $param_types = "s";
            return $this->select("SELECT * FROM rooms where room_code = ?", $param_types, [$room_code]);
        }
        
        public function validate_room_by_code($room_code)
        {
            $error_code = "no_room_with_code";
            $error_msg = $error_msg = "Could not find room associated with code $room_code";
            $results = $this->get_room_by_code($room_code);
            return ApiResponse::validate_single_entry_not_empty($results, $error_code, $error_msg);
        }
    }