<?php
    require_once "/home/indeli9/public_html/test/david/inc/bootstrap.php";
    
    require_once PROJECT_ROOT_PATH . "/Model/Database.php";
    require_once PROJECT_ROOT_PATH . "/Model/ApiResponse.php";

    class EventDeckModel extends Database
    {
        
        public function get_event_deck($election_year, $color)
        {
            $param_types = "ii";
            return $this->select("SELECT * FROM event_decks where election_year_id = ? AND color_id = ?", $param_types, [$election_year, $color]);
        }
        
        public function validate_event_deck($election_year, $color)
        {
            $error_code = "event_deck_not_found";
            $error_msg = "Could not find deck for year $election_year and color $color";
            $results = $this->get_event_deck($election_year, $color);
            return ApiResponse::validate_single_entry_not_empty($results, $error_code, $error_msg);
        }
       
    }