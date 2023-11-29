<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Users_model extends CI_Model
{
    function create_user($phone_number, $otp, $terms)
    {
        $user = $this->db->where('phone_number', $phone_number)->get('users');
        $currentTime = new DateTime();
        $currentTime->add(new DateInterval('PT10M'));
        $otp_valid = $currentTime->format('Y-m-d H:i:s');
        if ($user->num_rows() == 1) {
            $user = $user->row();
            $this->db->set('otp', $otp);
            $this->db->set('otp_valid_time', $otp_valid);
            $this->db->set('updated_on', CURRENT_DATE_TIME);
            $this->db->where('phone_number', $phone_number);
            $this->db->update('users');
            return array("status" => true, "user_id" => $user->user_id);
        } else {
            $this->db->set('otp', $otp);
            if ($terms == true) {
                $this->db->set('terms', 1);
            } else {
                $this->db->set('terms', 0);
            }
            $this->db->set('profile_status', 0);
            $this->db->set('otp_valid_time', $otp_valid);
            $this->db->set('updated_on', CURRENT_DATE_TIME);
            $this->db->set('registered_on', CURRENT_DATE_TIME);
            $this->db->set('phone_number', $phone_number);
            $this->db->insert('users');

            $last_id = $this->db->insert_id();
            return array("status" => true, "user_id" => $last_id);
        }
        return array("status" => false);
    }

    function update_user_otp($phone_number, $otp)
    {
        $currentTime = new DateTime();
        $currentTime->add(new DateInterval('PT10M'));
        $otp_valid = $currentTime->format('Y-m-d H:i:s');

        $this->db->select('user_id');
        $this->db->where('phone_number', $phone_number);
        $user = $this->db->get('users');
        if ($user->num_rows() == 1) {
            $this->db->set('otp', $otp);
            $this->db->set('otp_valid_time', $otp_valid);
            $this->db->set('updated_on', CURRENT_DATE_TIME);
            $this->db->where('phone_number', $phone_number);
            $this->db->update('users');
            return array("status" => true);
        }
        return array("status" => false);
    }

    function update_user_otp_to_null($phone_number)
    {
        $this->db->select('user_id');
        $this->db->where('phone_number', $phone_number);
        $user = $this->db->get('users');
        if ($user->num_rows() == 1) {
            $this->db->set('profile_status', 1);
            $this->db->set('otp', NULL);
            $this->db->set('otp_valid_time', NULL);
            $this->db->set('is_login', 1);
            $this->db->set('updated_on', CURRENT_DATE_TIME);
            $this->db->where('phone_number', $phone_number);
            $this->db->update('users');
            return true;
        }
        return false;
    }

    function getUserDetailsByPhone($number)
    {
        return $this->db->where('phone_number', $number)->get('users')->row();
    }
}
