<?php
    require_once "/home/indeli9/public_html/test/david/inc/bootstrap.php";
    
    require_once PROJECT_ROOT_PATH . "/Model/Database.php";
     
    class GameModeModel extends Database
    {
        public function get_by_id($game_mode_id)
        {
            $param_types = "i";
            return $this->select("SELECT * FROM game_modes where id = ?", $param_types, [$game_mode_id]);
        }
        
        public function validate_by_id($game_mode_id)
        {
            $error_code = "no_game_mode_found";
            $error_msg = "Could not find game mode associated with id $game_id";
            $results = $this->get_by_id($game_mode_id);
            return ApiResponse::validate_single_entry_not_empty($results, $error_code, $error_msg);
        }
    }