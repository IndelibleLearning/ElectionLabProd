<?php
    require_once "/home/indeli9/public_html/test/david/inc/bootstrap.php";
    
    require_once PROJECT_ROOT_PATH . "/Model/Database.php";

    class TurnModel extends Database
    {
        
        public function create_turn(
            $game_id, 
            $turn_number, 
            $state_id)
        {
            $param_types = "iii";
            return $this->insert("INSERT INTO turns (
                game_id, 
                turn_number, 
                state_id) 
                VALUES (?, ?, ?)", 
                $param_types, 
                [$game_id, $turn_number, $state_id]);
        }
        
        public function get_turn($game_id, $turn_number)
        {
            $param_types = "ii";
            return $this->select("SELECT * FROM turns where game_id = ? and turn_number = ?", $param_types, [$game_id, $turn_number]);
        }
        
        public function validate_get_turn($game_id, $turn_number)
        {
            $error_code = "no_turn_for_game";
            $error_msg = "No turn $turn_number for game $game_id";
            $results = $this->get_turn($game_id, $turn_number);
            return ApiResponse::validate_single_entry_not_empty($results, $error_code, $error_msg);
        }
        
        public function get_turn_by_id($turn_id)
        {
            $param_types = "i";
            return $this->select("SELECT * FROM turns where id = ?", $param_types, [$turn_id]);
        }
        
        public function get_turn_by_state($game_id, $state_id)
        {
            $param_types = "ii";
            return $this->select("SELECT * FROM turns where game_id = ? and state_id = ?", $param_types, [$game_id, $state_id]);
        }
        
        public function validate_already_did_state($game_id, $state_id)
        {
            $error_code = "already_did_state_turn";
            $error_msg = "Already had turn for state";
            $results = $this->get_turn_by_state($game_id, $state_id);
            return ApiResponse::validate_is_empty($results, $error_code, $error_msg);
        }
        
        public function get_turns_by_game($game_id)
        {
            $param_types = "i";
            return $this->select("SELECT * FROM turns where game_id = ?", $param_types, [$game_id]);
        }
        
        public function set_winner($turn_id, $winner_id, $total_num_rolls)
        {
            $param_types = "iii";
            return $this->update("UPDATE turns SET winner=?, total_num_rolls=? where id=?", $param_types, [$winner_id, $total_num_rolls, $turn_id]);
        }
        
        public function validate_turn($game_id, $turn_num)
        {
            $error_code = "no_turn_found";
            $error_msg = "Could not find turn associated with game $game_id and turn number $turn_num";
            $results = $this->get_turn($game_id, $turn_num);
            return ApiResponse::validate_single_entry_not_empty($results, $error_code, $error_msg);
        }
        
        // SPECIAL CASE - only is called after we already retrieved turn
        public function validate_turn_not_rolled_yet($turn)
        {
            $has_errors = false;
            $error_code = "";
            $error_msg = "";
            $data = null;
            
            if ($turn["winner"])
            {
                $has_errors = true;
                $error_code = "turn_already_rolled";
                $error_msg = "You have already rolled for this turn";
            }
            
            return new ApiResponse($data, $has_errors, $error_code, $error_msg);
        }
        
        public function validate_turns_by_game($game_id)
        {
            $error_code = "no_turns_found";
            $error_msg = "Could not find turns associated with game $game_id";
            $results = $this->get_turns_by_game($game_id);
            return ApiResponse::validate_array_not_empty($results, $error_code, $error_msg);
        }
    }