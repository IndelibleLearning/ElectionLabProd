<?php
    require dirname(dirname(__FILE__)) . "/inc/bootstrap.php";
    
    class Database
    {
        protected $connection = null;
    
     
        public function __construct()
        {
            
            try {
                $this->connection = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE_NAME);
             
                if ( mysqli_connect_errno()) {
                    throw new Exception("Could not connect to database: " . mysqli_connect_error());   
                }
            } catch (Exception $e) {
                throw new Exception($e->getMessage());   
            }           
        }
     
        public function select($query = "" , $param_types = "", $params = [])
        {
            try {
                $stmt = $this->executeStatement( $query , $param_types, $params );
                $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);               
                $stmt->close();
     
                return $result;
            } catch(Exception $e) {
                throw New Exception( $e->getMessage() );
            }
            return false;
        }
        
        public function insert($query = "" , $param_types = "", $params = [])
        {
            try {
                $stmt = $this->executeStatement( $query , $param_types, $params );
                if ($stmt)
                {
                    if ($stmt->errno == 0)
                    {
                        $result = $stmt->insert_id;
                    }
                    else
                    {
                        $result = $stmt->error;
                    }
                }
                else 
                {
                    $result = "ERROR: Could not execute query: $query. " . mysqli_error($this->connection);
                }
                $stmt->close();
     
                return $result;
            } catch(Exception $e) {
                throw New Exception( $e->getMessage() );
            }
            return false;
        }
        
        public function update($query = "" , $param_types = "", $params = [])
        {
            try {
                $stmt = $this->executeStatement( $query , $param_types, $params );
                if ($stmt)
                {
                    if ($stmt->errno == 0)
                    {
                        $result = "Updated " . $stmt->affected_rows . " rows.";
                    }
                    else
                    {
                        $result = $stmt->error;
                    }
                }
                else 
                {
                    $result = "ERROR: Could not execute query: $query. " . mysqli_error($this->connection);
                }
                $stmt->close();
     
                return $result;
            } catch(Exception $e) {
                throw New Exception( $e->getMessage() );
            }
            return false;
        }
     
        private function executeStatement($query = "" , $param_types = "", $params = [])
        {
            try {
                $stmt = $this->connection->prepare( $query );
     
                if($stmt === false) {
                    throw New Exception("Unable to do prepared statement: " . $query);
                }
     
                if( $param_types && $params ) {
                    $stmt->bind_param($param_types, ...$params);
                }
     
                $stmt->execute();
     
                return $stmt;
            } catch(Exception $e) {
                throw New Exception( $e->getMessage() );
            }   
        }
    }