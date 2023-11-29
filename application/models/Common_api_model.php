<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Common_api_model extends CI_Model
{

    function add_data($table, $data)
    {
        $this->db->set($data);
        if ($this->db->insert($table)) {
            return $this->db->insert_id();
        }
        return FALSE;
    }

    function update_data($table, $data, $condition)
    {
        $this->db->set($data);
        $this->db->where($condition);
        if ($this->db->update($table)) {
            return TRUE;
        }
        return FALSE;
    }

    function get_id($table, $condition)
    {
        $this->db->select('id');
        $this->db->where($condition);
        $id = $this->db->get($table)->row()->id;
        if ($id) {
            return $id;
        }
        return 0;
    }

    function get_user_data($user_id)
    {
        return $this->db->select('users.*, ctowns.ct_name')->from('users')->join('ctowns', 'ctowns.ct_id = users.ct_id', 'left')->where('users.user_id', $user_id)->limit(1)->get()->row();
    }

    function execute_raw_sql($sql)
    {
        return $this->db->query($sql)->result();
    }
}
