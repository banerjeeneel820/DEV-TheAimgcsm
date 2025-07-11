<?php
 defined('ROOTPATH') OR exit('No direct script access allowed');

    class GlobalInterfaceModel {
        
        private $db;
        
        public function __construct(){
          $this->db = DB::$WRITELINK;
        }

        public function global_Fetch_All_DB($sql){
            $rows = array();

            $result = $this->db->query($sql);
            
            while($row = $result->fetch_object()) {
                $rows[] = $row;
            }
            return $rows;
        }

        public function global_Rows_Count_DB($sql){
            
            $result = $this->db->query($sql);
            
            $row_count = $result->num_rows;
                
            return $row_count;
        }
        
        public function global_CRUD_DB($sql){

            // Turn autocommit off
            /*$this->db->autocommit(FALSE);

            $returnArr = array();
            $result = $this->db->query($sql);
            
            // Commit transaction
            if($this->db->commit() == false) {
               // Rollback transaction
               $this->db->rollback(); 
               $returnArr = array("check" => "failure", "message" => "Something went wrong!");    
            }else{
               $last_insert_id = $this->db->insert_id;  
               $returnArr = array("check" => "success", "message" => "Query has been successfully excuted!","last_insert_id"=>$last_insert_id);
            }
            //$this->db->close(); 
            return $returnArr;*/

            $returnArr = array();
            $result = $this->db->query($sql);
            
            if($result){
              $last_insert_id = $this->db->insert_id;  
              $returnArr = array("check" => "success", "message" => "Query has been successfully excuted!","last_insert_id"=>$last_insert_id);    
            }else{
              $returnArr = array("check" => "failure", "message" => "Something went wrong!");  
            }
            return $returnArr;
        }

        public function global_Fetch_Single_DB($sql){
            
            //$rows = array();
            $result = $this->db->query($sql);
            
            $single_row = $result->fetch_object();
            
            return $single_row;
        }
 }
?>