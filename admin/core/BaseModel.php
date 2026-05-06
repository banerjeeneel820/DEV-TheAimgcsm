<?php
defined('ROOTPATH') OR exit('No direct script access allowed');

class BaseModel {

    protected $db;

    public function __construct($db){
        $this->db = $db->getConnection();
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

    protected function global_Fetch_All_DB($sql, $params = []){
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

    protected function global_Fetch_Single_DB($sql, $params = []){
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

    protected function global_Rows_Count_DB($sql, $params = []){
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

    protected function global_Aggregate_Value_DB($sql, $params = [])
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

    protected function global_CRUD_DB($sql, $params = []){
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

    protected function escape($value)
    {
       return mysqli_real_escape_string(DB::$WRITELINK, trim($value));
    }
}