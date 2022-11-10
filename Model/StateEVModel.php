<?php
    require_once "/home/indeli9/public_html/test/david/inc/bootstrap.php";
    
    require_once PROJECT_ROOT_PATH . "/Model/Database.php";

    class StateEVModel extends Database
    {
        public function create_state_EV($election_year_id, $state_id, $electoral_votes, $initial_color_id)
        {
            $param_types = "iiii";
            return $this->insert("INSERT INTO state_EVs (election_year_id, state_id, electoral_votes, initial_color_id) VALUES (?, ?, ?, ?)", $param_types, [$election_year_id, $state_id, $electoral_votes, $initial_color_id]);
        }
        
        
        public function get_by_state_id($state_id)
        {
            $param_types = "i";
            return $this->select("SELECT * FROM state_EVs where state_id = ?", $param_types, [$state_id]);
        }
        
        public function validate_by_state_id($state_id)
        {
            $has_errors = false;
            $error_code = "";
            $error_msg = "";
            $data = null;
            
            $results = $this->get_by_state_id($state_id);
            if (!$results || count($results) <= 0)
            {
                $has_errors = true;
                $error_code = "no_state_ev_found";
                $error_msg = "Could not find state associated with id $state_id";
            }
            else
            {
                $data = $results[0];
            }
            
            return new ApiResponse($data, $has_errors, $error_code, $error_msg);
        }
        
        public function get_states_by_election_year($election_year_id)
        {
            $param_types = "i";
            return $this->select("SELECT * FROM state_EVs where election_year_id = ?", $param_types, [$election_year_id]);
        }
    }