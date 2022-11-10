<?php
    require_once "/home/indeli9/public_html/test/david/inc/bootstrap.php";
    
    require_once PROJECT_ROOT_PATH . "/Model/Database.php";
    require_once PROJECT_ROOT_PATH . "/Model/ApiResponse.php";
     
    class StateModel extends Database
    {
        public function get_state_by_abbrev($state_abbrev)
        {
            $param_types = "s";
            return $this->select("SELECT * FROM states where state_abbrev = ?", $param_types, [$state_abbrev]);
        }
        
        public function validate_state_by_abbrev($state_abbrev)
        {
            $error_code = "state_not_found";
            $error_msg = "Could not find state $state_abbrev";
            $results = $this->get_state_by_abbrev($state_abbrev);
            return ApiResponse::validate_single_entry_not_empty($results, $error_code, $error_msg);
        }
        
        public function get_state_by_id($id)
        {
            $param_types = "s";
            return $this->select("SELECT * FROM states where id = ?", $param_types, [$id]);
        }
        
        public function validate_by_id($id)
        {
            $error_code = "state_id_not_found";
            $error_msg = "Could not find state with id $id";
            $results = $this->get_state_by_id($id);
            return ApiResponse::validate_single_entry_not_empty($results, $error_code, $error_msg);
        }
    }