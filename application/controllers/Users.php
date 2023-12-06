<?php

defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class Users extends REST_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('users_model');
    }

    public function index_get()
    {
        $this->throw_error('Method not allowed', 404);
    }

    public function index_post()
    {

        if (empty($this->post('mobile_number'))) {
            $this->throw_error('Mobile Number Required');
        }
        if (empty($this->post('terms'))) {
            $this->throw_error('Terms Required');
        }
        $phone_number = $this->post('mobile_number');
        $terms = $this->post('terms');
        $otp = OTP_VALUE;
        $login = $this->users_model->create_user($phone_number, $otp, $terms);
        if ($login['status']) {
            $user = $this->common_api_model->get_user_data($login['user_id']);
            $data = array(
                'status' => 200,
                "message" => "OTP sent successfully.",
                "user_id" => $login['user_id'],
                "icon" => $user->icon,
                "ct_id" => $user->ct_id,
                "ct_name" => $user->ct_name,
                "profile_status" => $user->profile_status
            );
            $this->response($data, 200);
        } else {
            $data = array(
                'status' => 404,
                "message" => "Login failed, Please try again."
            );
            $this->response($data, 404);
        }
    }

    public function resendOtp_post()
    {

        if (empty($this->post('phone_number'))) {
            $this->throw_error('Phone Number Required');
        }
        $phone_number = $this->post('phone_number');
        $otp = OTP_VALUE;
        $login = $this->users_model->update_user_otp($phone_number, $otp);
        if ($login['status']) {
            $data = array(
                'status' => 200,
                "message" => "OTP sent successfully."
            );
            $this->response($data, 200);
        } else {
            $data = array(
                'status' => 404,
                "message" => "User not found"
            );
            $this->response($data, 404);
        }
    }

    public function validateOtp_post()
    {
        if (empty($this->post('user_id'))) {
            $this->throw_error('User id Required');
        }
        if (empty($this->post('otp'))) {
            $this->throw_error('OTP Required');
        }
        $user_id = $this->post('user_id');
        $otp = $this->post('otp');
        // $user = $this->users_model->getUserDetailsByPhone($phone_number);
        $user = $this->common_api_model->get_user_data($this->post('user_id'));
        $cdate = CURRENT_DATE_TIME;
        if ($cdate > $user->otp_valid_time) {
            $this->throw_error('OTP Expired', 401);
        }
        $phone_number = $user->phone_number;
        if ($otp == $user->otp) {
            if ($this->users_model->update_user_otp_to_null($phone_number)) {
                $token = $this->generateToken($user->user_id, $phone_number);
                $data = array(
                    'status' => 200,
                    "message" => "OTP verfied successfully.",
                    "token" => $token
                );
                $this->response($data, 200);
            } else {
                $data = array(
                    'status' => 404,
                    "message" => "OTP verfied failed.",
                );
                $this->response($data, 404);
            }
        } else {
            $this->throw_error('OTP Not Matched', 401);
            $this->response($data, 401);
        }
    }

    public function userDetails_post()
    {
        $this->checkAuth();
        if (empty($this->post('user_id'))) {
            $this->throw_error('User ID Required');
        }
        $user = $this->common_api_model->get_user_data($this->post('user_id'));
        if ($user) {
            $data = array(
                'status' => 200,
                "message" => "User details.",
                "user_id" => $user->user_id,
                "first_name" => $user->first_name,
                "last_name" => $user->last_name,
                "gender" => $user->gender,
                "phone_number" => $user->phone_number,
                "alt_phone_number" => $user->alt_phone_number,
                "email" => $user->email,
                "profession" => $user->profession,
                "realestate" => $user->realestate,
                "icon" => $user->icon,
                "ct_id" => $user->ct_id,
                "ct_name" => $user->ct_name
            );
            $this->response($data, 200);
        } else {
            $this->throw_error('User Details Not Found', 401);
        }
    }

    public function updateUserDetails_post()
    {
        if (empty($this->post('user_id'))) {
            $this->throw_error('User ID Required');
        }
        $user_id = $this->checkAuth($this->post('user_id'))['user_id'];
        $profile_data = [];

        if ($this->post('first_name')) {
            $profile_data['first_name'] = $this->post('first_name');
        }
        if ($this->post('last_name')) {
            $profile_data['last_name'] = $this->post('last_name');
        }
        if ($this->post('gender')) {
            if ($this->post('gender') != 'M' && $this->post('gender') != 'F') {
                $this->throw_error('Gender must be M or F', 401);
            }
            $profile_data['gender'] = $this->post('gender');
        }
        if ($this->post('ct_id')) {
            $profile_data['ct_id'] = $this->post('ct_id');
        }
        if ($this->post('email')) {
            $profile_data['email'] = $this->post('email');
        }
        if ($this->post('profession')) {
            $profile_data['profession'] = $this->post('profession');
        }
        if ($this->post('realestate')) {
            if ($this->post('realestate') == true) {
                $profile_data['realestate'] = 1;
            } else {
                $profile_data['realestate'] = 0;
            }
        }
        if ($this->post('alt_phone_number')) {
            $profile_data['alt_phone_number'] = $this->post('alt_phone_number');
        }
        if ($this->post('wa_notifications')) {
            if ($this->post('wa_notifications') == true) {
                $profile_data['wa_notifications'] = 1;
            } else {
                $profile_data['wa_notifications'] = 0;
            }
        }
        if (isset($_FILES['icon']['name'])) {
            $profile_data['icon'] = $this->file_upload($_FILES['icon'], USERS_FOLDER);
        }
        $profile_data['profile_status'] = 2;
        $profile_data['updated_on'] = CURRENT_DATE_TIME;
        $condition = "user_id = $user_id";
        if ($this->common_api_model->update_data('users', $profile_data, $condition)) {
            $data = array(
                'status' => 200,
                "message" => "User details updated successfully."
            );
            $this->response($data, REST_Controller::HTTP_OK);
        } else {
            $data = array(
                'status' => 404,
                "message" => "User details update failed."
            );
            $this->response($data, REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    public function saveUserLog_post()
    {
        if (empty($this->post('user_id'))) {
            $this->throw_error('User ID Required');
        }
        $user_id = $this->checkAuth($this->post('user_id'))['user_id'];
        $data = [
            'user_id' => $user_id,
            'visited_on' => CURRENT_DATE_TIME
        ];
        $inserid = $this->common_api_model->add_data("daily_users", $data);
        if ($inserid) {
            $data = array(
                'status' => 200,
                "message" => "User log added successfully.",
                "log_id" => $inserid
            );
            $this->response($data, REST_Controller::HTTP_OK);
        } else {
            $data = array(
                'status' => 404,
                "message" => "User log adding failed."
            );
            $this->response($data, REST_Controller::HTTP_BAD_REQUEST);
        }
    }
}
