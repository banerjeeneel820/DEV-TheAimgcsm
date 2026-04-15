<?php
defined('ROOTPATH') OR exit('No direct script access allowed');

class GlobalInterfaceModel {

    private $db;

    public function __construct(){
        $this->db = DB::$WRITELINK;
    }

    private function getTypes($params){
        $types = '';
        foreach ($params as $p) {
            if (is_int($p)) $types .= 'i';
            elseif (is_double($p)) $types .= 'd';
            else $types .= 's';
        }
        return $types;
    }

    public function global_Fetch_All_DB($sql, $params = []){
        $rows = [];

        if (!empty($params)) {
            $stmt = $this->db->prepare($sql);
            $types = $this->getTypes($params);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $this->db->query($sql);
        }

        while($row = $result->fetch_object()) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function global_Fetch_Single_DB($sql, $params = []){
        if (!empty($params)) {
            $stmt = $this->db->prepare($sql);
            $types = $this->getTypes($params);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $this->db->query($sql);
        }

        return $result->fetch_object();
    }

    public function global_Rows_Count_DB($sql, $params = []){
        if (!empty($params)) {
            $stmt = $this->db->prepare($sql);
            $types = $this->getTypes($params);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $this->db->query($sql);
        }

        return $result->num_rows;
    }

    public function global_Aggregate_Value_DB($sql, $params = [])
    {
        if (!empty($params)) {
            $stmt = $this->db->prepare($sql);
    
            $types = $this->getTypes($params);
            $stmt->bind_param($types, ...$params);
    
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $this->db->query($sql);
        }
    
        if ($result) {
            $row = $result->fetch_assoc();
            return (int) ($row ? reset($row) : 0);
        }
    
        return 0;
    }

    public function global_CRUD_DB($sql, $params = []){
        if (!empty($params)) {
            $stmt = $this->db->prepare($sql);
            $types = $this->getTypes($params);
            $stmt->bind_param($types, ...$params);
            $result = $stmt->execute();
        } else {
            $result = $this->db->query($sql);
        }

        if($result){
            return [
                "check" => "success",
                "last_insert_id" => $this->db->insert_id
            ];
        } else {
            return ["check" => "failure"];
        }
    }
}
