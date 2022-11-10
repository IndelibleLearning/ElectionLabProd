<?php
    require_once "/home/indeli9/public_html/test/david/inc/bootstrap.php";
    
    require_once PROJECT_ROOT_PATH . "/Model/Database.php";
    require_once PROJECT_ROOT_PATH . "/Model/ApiResponse.php";

    class EventDeckCardModel extends Database
    {
        
        public function get_event_cards_for_deck($event_deck_id)
        {
            $param_types = "i";
            return $this->select("SELECT * FROM event_deck_cards where $event_deck_id = ?", $param_types, [$event_deck_id]);
        }
        
        public function validate_event_deck_cards($event_deck_id)
        {
            $error_code = "no_cards_for_event_deck";
            $error_msg = "Could not find any cards for deck with id $event_deck_id";
            $results = $this->get_event_cards_for_deck($event_deck_id);
            return ApiResponse::validate_array_not_empty($results, $error_code, $error_msg);
        }
       
    }