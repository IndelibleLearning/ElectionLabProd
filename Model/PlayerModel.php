<?php
    require_once "/home/indeli9/public_html/test/david/inc/bootstrap.php";
    
    require_once PROJECT_ROOT_PATH . "/Model/Database.php";
    require_once PROJECT_ROOT_PATH . "/Model/ApiResponse.php";

    class PlayerModel extends Database
    {
       
        public function get_players_by_room($room_id)
        {
            $param_types = "s";
            return $this->select("SELECT * FROM players where room_id = ?", $param_types, [$room_id]);
        }
        
        public function get_player_in_room($room_id, $player_name)
        {
            $param_types = "is";
            return $this->select("SELECT * FROM players where room_id = ? AND player_name = ?", $param_types, [$room_id, $player_name]);
        }
        
        public function validate_player_in_room($room_id, $player_name)
        {
            $error_code = "player_not_found_in_room";
            $error_msg = "Could not find player with name $player_name in room $room_id";
            $results = $this->get_player_in_room($room_id, $player_name);
            return ApiResponse::validate_single_entry_not_empty($results, $error_code, $error_msg);
        }
        
        public function get_player_in_game($game_id, $player_name)
        {
            $param_types = "is";
            return $this->select("SELECT * FROM players where game_id = ? AND player_name = ?", $param_types, [$game_id, $player_name]);
        }
        
        public function validate_player_in_game($game_id, $player_name)
        {
            $error_code = "player_not_found_in_game";
            $error_msg = "Could not find player with name $player_name in game $game_id";
            $results = $this->get_player_in_game($game_id, $player_name);
            return ApiResponse::validate_single_entry_not_empty($results, $error_code, $error_msg);
        }
        
        public function validate_player_not_in_room($room_id, $player_name)
        {
            $error_code = "player_found_in_room";
            $error_msg = "Found player with name $player_name already in room";
            $results = $this->get_player_in_room($room_id, $player_name);
            return ApiResponse::validate_is_empty($results, $error_code, $error_msg);
        }
        
        public function get_players_in_game($game_id)
        {
            $param_types = "i";
            return $this->select("SELECT * FROM players where game_id = ?", $param_types, [$game_id]);
        }
        
        public function validate_players_in_game($game_id)
        {
            $has_errors = false;
            $error_code = "";
            $error_msg = "";
            $data = null;
            
            $player_results = $this->get_players_in_game($game_id);
            if (!$player_results || count($player_results) <= 0)
            {
                $has_errors = true;
                $error_code = "no_players_found";
                $error_msg = "Could not find players associated with game $game_id";
            }
            else
            {
                $data = $player_results;
            }
            
            return new ApiResponse($data, $has_errors, $error_code, $error_msg);
        }
        
        public function get_opponent($game_id, $player_id)
        {
            $param_types = "ii";
            return $this->select("SELECT * FROM players where game_id = ? AND id <> ?", $param_types, [$game_id, $player_id]);
        }
        
        public function get_player_by_id($player_id)
        {
            $param_types = "i";
            return $this->select("SELECT * FROM players where id = ?", $param_types, [$player_id]);
        }
        
        public function validate_player_by_id($player_id)
        {
            $error_code = "player_not_found";
            $error_msg = "Could not find player associated with id $player_id";
            $results = $this->get_player_by_id($player_id);
            return ApiResponse::validate_single_entry_not_empty($results, $error_code, $error_msg);
        }
        
        public function create_player($room_id, $player_name)
        {
            $param_types = "is";
            return $this->insert("INSERT INTO players (room_id, player_name) VALUES (?, ?)", $param_types, [$room_id, $player_name]);
        }
        
        public function join_game($player_id, $game_id)
        {
            $param_types = "ss";
            return $this->update("UPDATE players SET game_id=? where id=?", $param_types, [$game_id, $player_id]);
        }
        
        public function update_winning($player_id, $is_winning)
        {
            $param_types = "ss";
            return $this->update("UPDATE players SET is_winning=1 where id=?", $param_types, [$is_winning, $player_id]);
        }
        
        public function set_color($player_id, $color_id)
        {
            $param_types = "ii";
            return $this->update("UPDATE players SET color_id=? where id=?", $param_types, [$color_id, $player_id]);
        }
        
        public function leave_game($player_id)
        {
            $param_types = "i";
            return $this->update("UPDATE players SET color_id=NULL, game_id=NULL where id=?", $param_types, [$player_id]);
        }
    }