<?php
    require dirname(dirname(__FILE__)) . "/inc/bootstrap.php";
    
    require_once PROJECT_ROOT_PATH . "/Model/Database.php";
    require_once PROJECT_ROOT_PATH . "/Model/ApiResponse.php";

    class EventCardModel extends Database
    {
        
        public function get_event_card($card_id)
        {
            $param_types = "i";
            return $this->select("SELECT * FROM event_cards where id = ? ", $param_types, [$card_id]);
        }
        
        public function validate_event_card($card_id)
        {
            $error_code = "event_card_not_found";
            $error_msg = "Could not find card for id $card_id";
            $results = $this->get_event_card($card_id);
            return ApiResponse::validate_single_entry_not_empty($results, $error_code, $error_msg);
        }
        
        public function validate_card_has_key($card_obj, $event_key)
        {
            $error_code = "event_card_wrong_key";
            $error_msg = "Card with id " . $card_obj["id"] . " does not have key $event_key";
            
            if ($card_obj["event_key"] == $event_key)
            {
                return ApiResponse::success();
            }
            else
            {
                return ApiResponse::error($error_code, $error_msg);
            }
        }
       
    }