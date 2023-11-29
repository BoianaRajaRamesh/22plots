<?php

class My_db extends CI_Model
{

    function get_records_count($query_command)
    {
        $q = $this->db->query($query_command);
        $cnt = $q->row()->records_count;
        $q->next_result();
        $q->free_result();
        if ($cnt) {
            return $cnt;
        }
        return 0;
    }

    function get_records_list($query_command)
    {
        $q = $this->db->query($query_command);
        if ($q->num_rows() == 0) {
            $q->next_result();
            $q->free_result();
            return [];
        }
        $data = $q->result();
        $q->next_result();
        $q->free_result();
        return $data;
    }

    function get_by_column_value($query_command, $column_name)
    {
        $q = $this->db->query($query_command);
        $cnt = $q->row()->{$column_name};
        $q->next_result();
        $q->free_result();
        return $cnt;
        //        if ($cnt) {
        //            return $cnt;
        //        } else {
        //            return TRUE;
        //        }
    }

    function void_command($query_command)
    {
        $q = $this->db->query($query_command);
        $q->free_result();
        return TRUE;
    }

    function get_multiple_column_values($query_command)
    {
        $q = $this->db->query($query_command);
        $cnt = $q->row();
        $q->next_result();
        $q->free_result();
        return $cnt;
    }

    public function updateData($tablename, $data, $where)
    {
        $this->db->set($data)->where($where)->update($tablename);

        return true;
    }
}
