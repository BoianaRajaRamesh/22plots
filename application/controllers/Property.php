<?php

defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class Property extends REST_Controller
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

    public function searchProperty_post()
    {
        $this->checkAuth();
        if (empty($this->post('property_type'))) {
            $this->throw_error('Property Type Required');
        }
        if (empty($this->post('ct_id'))) {
            $this->throw_error('ct id Required');
        }
        $property_type = $this->post('property_type');
        $ct_id = $this->post('ct_id');
        $prop_purpose = $this->post('prop_purpose');
        $cdate = CURRENT_DATE;
        if ($property_type == 'APARTMENT') {
            $sql = "select p.property_id, p.banner_img, p.property_name, p.property_type, p.area_name, p.ct_id, ct.ct_name, case when p.contact_for_price then 'contact for price' when lower(p.price_type)='fixed' then p.fixed_price when lower(p.price_type)='range' then concat(p.min_price, 'to', p.max_price) else 'contact for price' end as price, p.brokarage, c1.apartment_config as config, pp.preference from properties p inner join ctowns ct on p.ct_id=ct.ct_id and p.ct_id=$ct_id and upper(p.prop_purpose)='SELL' and p.prop_status='OPEN' and upper(p.property_type)='APARTMENT' inner join (select property_id, concat(group_concat(bhk_type), ' BHK Apartments') as apartment_config from flats_config group by property_id) as c1 on p.property_id=c1.property_id left join prop_priority pp on p.property_id=pp.property_id and '$cdate' >=pp.active_st_dt and '$cdate' <=pp.active_end_dt";
            $sql1 = "select image_url, property_id, property_type, preference from prop_adds where (all_ct is true or ct_id=$ct_id) and (all_prop is true or property_type like '%$property_type%') and '$cdate' >=active_st_dt and '$cdate' <=active_end_dt";
        } else if ($property_type == 'HOUSE') {
            $sql = "select p.property_id, p.banner_img, p.property_name, p.property_type, p.area_name, p.ct_id, ct.ct_name, case when p.contact_for_price then 'contact for price' when lower(p.price_type)='fixed' then p.fixed_price when lower(p.price_type)='range' then concat(p.min_price, 'to', p.max_price) else 'contact for price' end as price, p.brokarage, c1.house_config as config, pp.preference from properties p inner join ctowns ct on p.ct_id=ct.ct_id and p.ct_id=$ct_id and upper(p.prop_purpose)='SELL' and p.prop_status='OPEN' and upper(p.property_type) like '%HOUSE%' left join (select property_id, concat(bhk_type, ' Bed | ', bathrooms, ' Bath | ', case when super_builtup_area is not null then super_builtup_area when builtup_area is not null then builtup_area when carpet_area is not null then carpet_area else 'NA' end) as house_config from flats_config group by property_id) as c1 on p.property_id=c1.property_id left join prop_priority pp on p.property_id=pp.property_id and '$cdate' >=pp.active_st_dt and '$cdate' <=pp.active_end_dt";

            $sql1 = "select image_url, property_id, property_type, preference from prop_adds where (all_ct is true or ct_id=$ct_id) and (all_prop is true or property_type like '%$property_type%') and '$cdate' >=active_st_dt and '$cdate' <=active_end_dt";
        } else if ($property_type == 'PLOT' || $property_type == 'VENTURE') {
            $sql = "select p.property_id, p.banner_img, p.property_name, p.property_type, p.area_name, p.ct_id, ct.ct_name, case when p.contact_for_price then 'contact for price' when lower(p.price_type)='fixed' then p.fixed_price when lower(p.price_type)='range' then concat(p.min_price, 'to', p.max_price) else 'contact for price' end as price, p.brokarage, pp.preference from properties p inner join ctowns ct on p.ct_id=ct.ct_id and p.ct_id=$ct_id and p.prop_purpose='SELL' and p.prop_status='OPEN' and upper(p.property_type) in('VENTURE', 'PLOT') left join prop_priority pp on p.property_id=pp.property_id and '$cdate' >=pp.active_st_dt and '$cdate' <=pp.active_end_dt";

            $sql1 = "select image_url, property_id, property_type, preference from prop_adds where (all_ct is true or ct_id=$ct_id) and (all_prop is true or property_type like '%$property_type%') and '$cdate' >=active_st_dt and '$cdate' <=active_end_dt";
        } else if ($property_type == 'LAND') {
            $sql = "select p.property_id, p.banner_img, p.property_name, p.property_type, p.area_name, p.ct_id, ct.ct_name, case when p.contact_for_price then 'contact for price' when lower(p.price_type)='fixed' then p.fixed_price when lower(p.price_type)='range' then concat(p.min_price, 'to', p.max_price) else 'contact for price' end as price, p.brokarage, pp.preference from properties p inner join ctowns ct on p.ct_id=ct.ct_id and p.ct_id=$ct_id and p.prop_purpose='SELL' and p.prop_status='OPEN' and upper(p.property_type)='LAND' left join prop_priority pp on p.property_id=pp.property_id and '$cdate' >=pp.active_st_dt and '$cdate' <=pp.active_end_dt";

            $sql1 = "select image_url, property_id, property_type, preference from prop_adds where (all_ct is true or ct_id=$ct_id) and (all_prop is true or property_type like '%$property_type%') and '$cdate' >=active_st_dt and '$cdate' <=active_end_dt";
        } else {
            $this->throw_error('Property Type Must Be APARTMENT/HOUSE/PLOT/VENTURE/LAND');
        }

        $properties = $this->common_api_model->execute_raw_sql($sql);
        $adds = $this->common_api_model->execute_raw_sql($sql1);

        $data = array(
            'status' => 200,
            "message" => "properties list.",
            "properties" => $properties,
            "adds" => $adds
        );
        $this->response($data, 200);
    }

    public function saveApartment_post()
    {
        if (empty($this->post('posted_by'))) {
            $this->throw_error('posted_by Required');
        }
        $this->checkAuth($this->post('posted_by'));

        $apartmentData = [
            'posted_by' =>  $this->post('posted_by'),
            'prop_purpose' =>  $this->post('prop_purpose'),
            'ct_id' => $this->post('ct_id'),
            'area_name' => $this->post('area_name'),
            'user_type' => $this->post('user_type'),
            'property_name' => $this->post('property_name'),
            'description' => $this->post('description'),
            'address' => $this->post('address'),
            'landmark' => $this->post('landmark'),
            'facing' => $this->post('facing'),
            'construction_year' => $this->post('construction_year'),
            'current_status' => $this->post('current_status'),
            'total_area' => $this->post('total_area'),
            'road_width' => $this->post('road_width'),
            'govt_approval' => $this->post('govt_approval'),
            'approvals' => $this->post('approvals'),
            'contact_for_price' => $this->post('contact_for_price'),
            'price_type' => $this->post('price_type'),
            'fixed_price' => $this->post('fixed_price'),
            'min_price' => $this->post('min_price'),
            'max_price' => $this->post('max_price'),
            'negotiable' => $this->post('negotiable'),
            'brokarage' => $this->post('brokerage'),
            'loan_availability' => $this->post('loan_availability'),
            'cost_sheet' => $this->post('cost_sheet'),
            'uds' => $this->post('uds'),
            'distance_from_main_road' => $this->post('distance_from_main_road'),
            'latitude' => $this->post('latitude'),
            'longitude' => $this->post('longitude'),
            'location_advantages' => $this->post('location_advantages'),
            'brochure' => $this->post('brochure'),
            'amenities' => $this->post('amenities'),
            'video' => $this->post('video'),
            'comp_id' => $this->post('company_id'),
            'no_of_floors' => $this->post('no_of_floors'),
            'msg' => $this->post('msg'),
            'need_help' => $this->post('need_help'),
            'prop_status' => $this->post('prop_status'),
        ];

        foreach ($apartmentData as $field => $value) {
            if ($field == 'prop_purpose' || $field == 'ct_id' || $field == 'area_name' || $field == 'user_type' || $field == 'property_name' || $field == 'address' || $field == 'facing' || $field == 'total_area' || $field == 'approvals') {
                if (empty($value)) {
                    $this->throw_error("$field is required", 400);
                }
            }
        }

        foreach ($apartmentData as $field => $value) {
            if ($field == 'govt_approval' || $field == 'contact_for_price' || $field == 'negotiable' || $field == 'brokarage' || $field == 'loan_availability' || $field == 'need_help') {
                if (!empty($value)) {
                    if (strtolower($value) != 'true' && strtolower($value) != 'false') {
                        $this->throw_error("$field must be boolen", 400);
                    }
                    if (strtolower($value) == 'true') {
                        $apartmentData[$field] = 1;
                    } else {
                        $apartmentData[$field] = 0;
                    }
                } else {
                    $apartmentData[$field] = 0;
                }
            }
        }

        if ($apartmentData['contact_for_price'] == 0) {
            if ($this->post('price_type') == 'range') {
                if (empty($this->post('min_price')) || empty($this->post('max_price'))) {
                    $this->throw_error('min_price and max_price are Required');
                }
            } else if ($this->post('price_type') == 'fixed') {
                if (empty($this->post('fixed_price'))) {
                    $this->throw_error('fixed_price Required');
                }
            }
        }

        $flats_config = $this->post('flats_config');
        foreach ($flats_config as $key => $value) {
            $requiredKeys = ['bhk_type', 'bathrooms', 'balcony', 'facing'];
            foreach ($requiredKeys as $key) {
                if (empty($value[$key])) {
                    $this->throw_error(ucfirst($key) . " is required");
                }
            }
        }
        if (isset($_FILES['banner_img']['name'])) {
            $banner_img = $this->file_upload($_FILES['banner_img'], PROPERTY_BANNER);
        } else {
            $banner_img = "";
        }
        $apartmentData['added_on'] = CURRENT_DATE_TIME;
        $apartmentData['updated_on'] = CURRENT_DATE_TIME;
        $apartmentData['banner_img'] = $banner_img;
        $apartmentData['verification_status'] = 0;
        $apartmentData['property_type'] = 'APARTMENT';
        $image_paths = [];

        for ($i = 0; $i < count($_FILES['photos']['name']); $i++) {
            $image['name'] = $_FILES['photos']['name'][$i];
            $image['type'] = $_FILES['photos']['type'][$i];
            $image['tmp_name'] = $_FILES['photos']['tmp_name'][$i];
            $image['size'] = $_FILES['photos']['size'][$i];
            $image['error'] = $_FILES['photos']['error'][$i];

            $image_paths[] = $this->file_upload($image, PROPERTY_IMAGES);
        }

        $last_id = $this->common_api_model->add_data('properties', $apartmentData);
        if ($last_id) {
            foreach ($image_paths as $image) {
                $img_data = array(
                    "property_id" => $last_id,
                    "image_url" => $image
                );
                $this->common_api_model->add_data('property_images', $img_data);
            }
            foreach ($flats_config as $key => $row) {
                if (!empty($row['floor_plan'])) {
                    $file['name'] = $_FILES['floor_plan']['name'][$row['floor_plan']];
                    $file['type'] = $_FILES['floor_plan']['type'][$row['floor_plan']];
                    $file['tmp_name'] = $_FILES['floor_plan']['tmp_name'][$row['floor_plan']];
                    $file['error'] = $_FILES['floor_plan']['error'][$row['floor_plan']];
                    $file['size'] = $_FILES['floor_plan']['size'][$row['floor_plan']];
                    $floor_plan = $this->file_upload($file, PROPERTY_FLOORPLAN);
                } else {
                    $floor_plan = "";
                }
                $flat_config_data = array(
                    "property_id" => $last_id,
                    "bhk_type" => $row['bhk_type'],
                    "floor_num" => $row['floor_num'],
                    "facing" => $row['facing'],
                    "bathrooms" => $row['bathrooms'],
                    "balcony" => $row['balcony'],
                    "super_builtup_area" => $row['super_builtup_area'],
                    "builtup_area" => $row['builtup_area'],
                    "carpet_area" => $row['carpet_area'],
                    "floor_plan" => $floor_plan,
                    "price" => $row['price']
                );
                $this->common_api_model->add_data('flats_config', $flat_config_data);
            }
            $data = array(
                'status' => 201,
                "message" => "Apartment added successfully."
            );
            $this->response($data, 201);
        } else {
            $data = array(
                'status' => 404,
                "message" => "Apartment adding failed."
            );
            $this->response($data, 404);
        }
    }

    public function saveIndependentHouse_post()
    {
        if (empty($this->post('posted_by'))) {
            $this->throw_error('posted_by Required');
        }
        $this->checkAuth($this->post('posted_by'));

        $apartmentData = [
            'posted_by' => $this->post('posted_by'),
            'prop_purpose' => $this->post('prop_purpose'),
            'ct_id' => $this->post('ct_id'),
            'area_name' => $this->post('area_name'),
            'user_type' => $this->post('user_type'),
            'property_name' => $this->post('property_name'),
            'description' => $this->post('description'),
            'address' => $this->post('address'),
            'landmark' => $this->post('landmark'),
            'facing' => $this->post('facing'),
            'construction_year' => $this->post('construction_year'),
            'current_status' => $this->post('current_status'),
            'total_area' => $this->post('total_area'),
            'road_width' => $this->post('road_width'),
            'govt_approval' => $this->post('govt_approval'),
            'approvals' => $this->post('approvals'),
            'contact_for_price' => $this->post('contact_for_price'),
            'price_type' => $this->post('price_type'),
            'fixed_price' => $this->post('fixed_price'),
            'min_price' => $this->post('min_price'),
            'max_price' => $this->post('max_price'),
            'negotiable' => $this->post('negotiable'),
            'brokarage' => $this->post('brokerage'),
            'loan_availability' => $this->post('loan_availability'),
            'cost_sheet' => $this->post('cost_sheet'),
            'uds' => $this->post('uds'),
            'distance_from_main_road' => $this->post('distance_from_main_road'),
            'latitude' => $this->post('latitude'),
            'longitude' => $this->post('longitude'),
            'location_advantages' => $this->post('location_advantages'),
            'brochure' => $this->post('brochure'),
            'amenities' => $this->post('amenities'),
            'video' => $this->post('video'),
            'comp_id' => $this->post('company_id'),
            'no_of_floors' => $this->post('no_of_floors'),
            'msg' => $this->post('msg'),
            'need_help' => $this->post('need_help'),
            'prop_status' => $this->post('prop_status'),
            'updated_on' => CURRENT_DATE_TIME,
            'added_on' => CURRENT_DATE_TIME
        ];

        foreach ($apartmentData as $field => $value) {
            if ($field == 'prop_purpose' || $field == 'ct_id' || $field == 'area_name' || $field == 'user_type' || $field == 'property_name' || $field == 'address' || $field == 'facing') {
                if (empty($value)) {
                    $this->throw_error("$field is required", 400);
                }
            }
        }

        foreach ($apartmentData as $field => $value) {
            if ($field == 'govt_approval' || $field == 'contact_for_price' || $field == 'negotiable' || $field == 'brokarage' || $field == 'loan_availability' || $field == 'need_help') {
                if (!empty($value)) {
                    if (strtolower($value) != 'true' && strtolower($value) != 'false') {
                        $this->throw_error("$field must be boolen", 400);
                    }
                    if (strtolower($value) == 'true') {
                        $apartmentData[$field] = 1;
                    } else {
                        $apartmentData[$field] = 0;
                    }
                } else {
                    $apartmentData[$field] = 0;
                }
            }
        }

        if ($apartmentData['contact_for_price'] == 0) {
            if ($this->post('price_type') == 'range') {
                if (empty($this->post('min_price')) || empty($this->post('max_price'))) {
                    $this->throw_error('min_price and max_price are Required');
                }
            } else if ($this->post('price_type') == 'fixed') {
                if (empty($this->post('fixed_price'))) {
                    $this->throw_error('fixed_price Required');
                }
            }
        }

        $flats_config = $this->post('house_config');
        foreach ($flats_config as $key => $value) {
            $requiredKeys = ['bhk_type', 'bathrooms', 'balcony', 'facing'];
            foreach ($requiredKeys as $key) {
                if (empty($value[$key])) {
                    $this->throw_error(ucfirst($key) . " is required");
                }
            }
        }
        if (isset($_FILES['banner_img']['name'])) {
            $banner_img = $this->file_upload($_FILES['banner_img'], PROPERTY_BANNER);
        } else {
            $banner_img = "";
        }
        $apartmentData['banner_img'] = $banner_img;
        $apartmentData['verification_status'] = 0;
        $apartmentData['property_type'] = 'HOUSE';

        $image_paths = [];

        for ($i = 0; $i < count($_FILES['photos']['name']); $i++) {
            $image['name'] = $_FILES['photos']['name'][$i];
            $image['type'] = $_FILES['photos']['type'][$i];
            $image['tmp_name'] = $_FILES['photos']['tmp_name'][$i];
            $image['size'] = $_FILES['photos']['size'][$i];
            $image['error'] = $_FILES['photos']['error'][$i];

            $image_paths[] = $this->file_upload($image, PROPERTY_IMAGES);
        }

        $last_id = $this->common_api_model->add_data('properties', $apartmentData);
        if ($last_id) {
            foreach ($image_paths as $image) {
                $img_data = array(
                    "property_id" => $last_id,
                    "image_url" => $image
                );
                $this->common_api_model->add_data('property_images', $img_data);
            }
            foreach ($flats_config as $key => $row) {
                if (!empty($row['floor_plan'])) {
                    $file['name'] = $_FILES['floor_plan']['name'][$row['floor_plan']];
                    $file['type'] = $_FILES['floor_plan']['type'][$row['floor_plan']];
                    $file['tmp_name'] = $_FILES['floor_plan']['tmp_name'][$row['floor_plan']];
                    $file['error'] = $_FILES['floor_plan']['error'][$row['floor_plan']];
                    $file['size'] = $_FILES['floor_plan']['size'][$row['floor_plan']];
                    $floor_plan = $this->file_upload($file, PROPERTY_FLOORPLAN);
                } else {
                    $floor_plan = "";
                }
                $flat_config_data = array(
                    "property_id" => $last_id,
                    "bhk_type" => $row['bhk_type'],
                    "floor_num" => $row['floor_num'],
                    "facing" => $row['facing'],
                    "bathrooms" => $row['bathrooms'],
                    "balcony" => $row['balcony'],
                    "super_builtup_area" => $row['super_builtup_area'],
                    "builtup_area" => $row['builtup_area'],
                    "carpet_area" => $row['carpet_area'],
                    "floor_plan" => $floor_plan,
                    "price" => $row['price']
                );
                $this->common_api_model->add_data('flats_config', $flat_config_data);
            }
            $data = array(
                'status' => 201,
                "message" => "House added successfully."
            );
            $this->response($data, 201);
        } else {
            $data = array(
                'status' => 404,
                "message" => "House adding failed."
            );
            $this->response($data, 404);
        }
    }

    public function saveVenture_post()
    {
        if (empty($this->post('posted_by'))) {
            $this->throw_error('posted_by Required');
        }
        $this->checkAuth($this->post('posted_by'));

        $apartmentData = [
            'posted_by' => $this->post('posted_by'),
            'prop_purpose' => $this->post('prop_purpose'),
            'ct_id' => $this->post('ct_id'),
            'area_name' => $this->post('area_name'),
            'user_type' => $this->post('user_type'),
            'property_name' => $this->post('property_name'),
            'description' => $this->post('description'),
            'address' => $this->post('address'),
            'landmark' => $this->post('landmark'),
            'facing' => $this->post('facing'),
            'construction_year' => $this->post('construction_year'),
            'current_status' => $this->post('current_status'),
            'total_area' => $this->post('total_area'),
            'road_width' => $this->post('road_width'),
            'govt_approval' => $this->post('govt_approval'),
            'approvals' => $this->post('approvals'),
            'contact_for_price' => $this->post('contact_for_price'),
            'price_type' => $this->post('price_type'),
            'fixed_price' => $this->post('fixed_price'),
            'min_price' => $this->post('min_price'),
            'max_price' => $this->post('max_price'),
            'negotiable' => $this->post('negotiable'),
            'brokarage' => $this->post('brokerage'),
            'loan_availability' => $this->post('loan_availability'),
            'cost_sheet' => $this->post('cost_sheet'),
            'distance_from_main_road' => $this->post('distance_from_main_road'),
            'latitude' => $this->post('latitude'),
            'longitude' => $this->post('longitude'),
            'location_advantages' => $this->post('location_advantages'),
            'brochure' => $this->post('brochure'),
            'amenities' => $this->post('amenities'),
            'video' => $this->post('video'),
            'comp_id' => $this->post('company_id'),
            'msg' => $this->post('msg'),
            'need_help' => $this->post('need_help'),
            'prop_status' => $this->post('prop_status'),
            'updated_on' => CURRENT_DATE_TIME,
            'added_on' => CURRENT_DATE_TIME
        ];

        foreach ($apartmentData as $field => $value) {
            if ($field == 'prop_purpose' || $field == 'ct_id' || $field == 'area_name' || $field == 'user_type' || $field == 'property_name' || $field == 'address') {
                if (empty($value)) {
                    $this->throw_error("$field is required", 400);
                }
            }
        }

        foreach ($apartmentData as $field => $value) {
            if ($field == 'govt_approval' || $field == 'contact_for_price' || $field == 'negotiable' || $field == 'brokarage' || $field == 'loan_availability' || $field == 'need_help') {
                if (!empty($value)) {
                    if (strtolower($value) != 'true' && strtolower($value) != 'false') {
                        $this->throw_error("$field must be boolen", 400);
                    }
                    if (strtolower($value) == 'true') {
                        $apartmentData[$field] = 1;
                    } else {
                        $apartmentData[$field] = 0;
                    }
                } else {
                    $apartmentData[$field] = 0;
                }
            }
        }

        if ($apartmentData['contact_for_price'] == 0) {
            if ($this->post('price_type') == 'range') {
                if (empty($this->post('min_price')) || empty($this->post('max_price'))) {
                    $this->throw_error('min_price and max_price are Required');
                }
            } else if ($this->post('price_type') == 'fixed') {
                if (empty($this->post('fixed_price'))) {
                    $this->throw_error('fixed_price Required');
                }
            }
        }
        $flats_config = $this->post('plots_config');

        if (isset($_FILES['banner_img']['name'])) {
            $banner_img = $this->file_upload($_FILES['banner_img'], PROPERTY_BANNER);
        } else {
            $banner_img = "";
        }
        $apartmentData['banner_img'] = $banner_img;
        $apartmentData['verification_status'] = 0;
        $apartmentData['property_type'] = 'VENTURE';

        $image_paths = [];

        for ($i = 0; $i < count($_FILES['photos']['name']); $i++) {
            $image['name'] = $_FILES['photos']['name'][$i];
            $image['type'] = $_FILES['photos']['type'][$i];
            $image['tmp_name'] = $_FILES['photos']['tmp_name'][$i];
            $image['size'] = $_FILES['photos']['size'][$i];
            $image['error'] = $_FILES['photos']['error'][$i];

            $image_paths[] = $this->file_upload($image, PROPERTY_IMAGES);
        }

        $last_id = $this->common_api_model->add_data('properties', $apartmentData);
        if ($last_id) {
            foreach ($image_paths as $image) {
                $img_data = array(
                    "property_id" => $last_id,
                    "image_url" => $image
                );
                $this->common_api_model->add_data('property_images', $img_data);
            }
            foreach ($flats_config as $key => $row) {
                $flat_config_data = array(
                    "property_id" => $last_id,
                    "width" => $row['width'],
                    "length" => $row['length'],
                    "facing" => $row['facing'],
                    "square_yards" => $row['square_yards'],
                    "price" => $row['price']
                );
                $this->common_api_model->add_data('plots_config', $flat_config_data);
            }
            $data = array(
                'status' => 201,
                "message" => "Venture added successfully."
            );
            $this->response($data, 201);
        } else {
            $data = array(
                'status' => 404,
                "message" => "Venture adding failed."
            );
            $this->response($data, 404);
        }
    }


    public function saveIndependentPlot_post()
    {
        if (empty($this->post('posted_by'))) {
            $this->throw_error('posted_by Required');
        }
        $this->checkAuth($this->post('posted_by'));

        $apartmentData = [
            'posted_by' => $this->post('posted_by'),
            'prop_purpose' => $this->post('prop_purpose'),
            'ct_id' => $this->post('ct_id'),
            'area_name' => $this->post('area_name'),
            'user_type' => $this->post('user_type'),
            'property_name' => $this->post('property_name'),
            'description' => $this->post('description'),
            'address' => $this->post('address'),
            'landmark' => $this->post('landmark'),
            'facing' => $this->post('facing'),
            'road_width' => $this->post('road_width'),
            'govt_approval' => $this->post('govt_approval'),
            'approvals' => $this->post('approvals'),
            'contact_for_price' => $this->post('contact_for_price'),
            'price_type' => $this->post('price_type'),
            'fixed_price' => $this->post('fixed_price'),
            'min_price' => $this->post('min_price'),
            'max_price' => $this->post('max_price'),
            'negotiable' => $this->post('negotiable'),
            'brokarage' => $this->post('brokerage'),
            'loan_availability' => $this->post('loan_availability'),
            'distance_from_main_road' => $this->post('distance_from_main_road'),
            'latitude' => $this->post('latitude'),
            'longitude' => $this->post('longitude'),
            'location_advantages' => $this->post('location_advantages'),
            'amenities' => $this->post('amenities'),
            'video' => $this->post('video'),
            'comp_id' => $this->post('company_id'),
            'msg' => $this->post('msg'),
            'need_help' => $this->post('need_help'),
            'prop_status' => $this->post('prop_status'),
            'updated_on' => CURRENT_DATE_TIME,
            'added_on' => CURRENT_DATE_TIME
        ];

        foreach ($apartmentData as $field => $value) {
            if ($field == 'prop_purpose' || $field == 'ct_id' || $field == 'area_name' || $field == 'user_type' || $field == 'property_name' || $field == 'address' || $field == 'description') {
                if (empty($value)) {
                    $this->throw_error("$field is required", 400);
                }
            }
        }

        foreach ($apartmentData as $field => $value) {
            if ($field == 'govt_approval' || $field == 'contact_for_price' || $field == 'negotiable' || $field == 'brokarage' || $field == 'loan_availability' || $field == 'need_help') {
                if (!empty($value)) {
                    if (strtolower($value) != 'true' && strtolower($value) != 'false') {
                        $this->throw_error("$field must be boolen", 400);
                    }
                    if (strtolower($value) == 'true') {
                        $apartmentData[$field] = 1;
                    } else {
                        $apartmentData[$field] = 0;
                    }
                } else {
                    $apartmentData[$field] = 0;
                }
            }
        }

        if ($apartmentData['contact_for_price'] == 0) {
            if ($this->post('price_type') == 'range') {
                if (empty($this->post('min_price')) || empty($this->post('max_price'))) {
                    $this->throw_error('min_price and max_price are Required');
                }
            } else if ($this->post('price_type') == 'fixed') {
                if (empty($this->post('fixed_price'))) {
                    $this->throw_error('fixed_price Required');
                }
            }
        }

        $flats_config = $this->post('plots_config');
        if (empty($flats_config)) {
            $this->throw_error("plots config is required", 400);
        }
        if (isset($_FILES['banner_img']['name'])) {
            $banner_img = $this->file_upload($_FILES['banner_img'], PROPERTY_BANNER);
        } else {
            $banner_img = "";
        }
        $apartmentData['banner_img'] = $banner_img;
        $apartmentData['verification_status'] = 0;
        $apartmentData['property_type'] = 'PLOT';

        $image_paths = [];

        for ($i = 0; $i < count($_FILES['photos']['name']); $i++) {
            $image['name'] = $_FILES['photos']['name'][$i];
            $image['type'] = $_FILES['photos']['type'][$i];
            $image['tmp_name'] = $_FILES['photos']['tmp_name'][$i];
            $image['size'] = $_FILES['photos']['size'][$i];
            $image['error'] = $_FILES['photos']['error'][$i];

            $image_paths[] = $this->file_upload($image, PROPERTY_IMAGES);
        }

        $last_id = $this->common_api_model->add_data('properties', $apartmentData);
        if ($last_id) {
            foreach ($image_paths as $image) {
                $img_data = array(
                    "property_id" => $last_id,
                    "image_url" => $image
                );
                $this->common_api_model->add_data('property_images', $img_data);
            }
            foreach ($flats_config as $key => $row) {
                $flat_config_data = array(
                    "property_id" => $last_id,
                    "width" => $row['width'],
                    "length" => $row['length'],
                    "facing" => $row['facing'],
                    "square_yards" => $row['square_yards'],
                    "price" => $row['price']
                );
                $this->common_api_model->add_data('plots_config', $flat_config_data);
            }
            $data = array(
                'status' => 201,
                "message" => "Independent Plot added successfully."
            );
            $this->response($data, 201);
        } else {
            $data = array(
                'status' => 404,
                "message" => "Independent Plot adding failed."
            );
            $this->response($data, 404);
        }
    }

    public function saveLand_post()
    {
        if (empty($this->post('posted_by'))) {
            $this->throw_error('posted_by Required');
        }
        $this->checkAuth($this->post('posted_by'));
        $apartmentData = [
            'posted_by' => $this->post('posted_by'),
            'prop_purpose' => $this->post('prop_purpose'),
            'ct_id' => $this->post('ct_id'),
            'area_name' => $this->post('area_name'),
            'user_type' => $this->post('user_type'),
            'property_name' => $this->post('property_name'),
            'description' => $this->post('description'),
            'address' => $this->post('address'),
            'landmark' => $this->post('landmark'),
            'current_status' => $this->post('current_status'),
            'total_area' => $this->post('total_area'),
            'road_width' => $this->post('road_width'),
            'contact_for_price' => $this->post('contact_for_price'),
            'price_type' => $this->post('price_type'),
            'fixed_price' => $this->post('fixed_price'),
            'min_price' => $this->post('min_price'),
            'max_price' => $this->post('max_price'),
            'negotiable' => $this->post('negotiable'),
            'brokarage' => $this->post('brokerage'),
            'loan_availability' => $this->post('loan_availability'),
            'distance_from_main_road' => $this->post('distance_from_main_road'),
            'latitude' => $this->post('latitude'),
            'longitude' => $this->post('longitude'),
            'location_advantages' => $this->post('location_advantages'),
            'amenities' => $this->post('amenities'),
            'video' => $this->post('video'),
            'comp_id' => $this->post('company_id'),
            'msg' => $this->post('msg'),
            'need_help' => $this->post('need_help'),
            'prop_status' => $this->post('prop_status'),
            'updated_on' => CURRENT_DATE_TIME,
            'added_on' => CURRENT_DATE_TIME
        ];

        foreach ($apartmentData as $field => $value) {
            if ($field == 'prop_purpose' || $field == 'ct_id' || $field == 'area_name' || $field == 'user_type' || $field == 'property_name' || $field == 'address' || $field == 'description') {
                if (empty($value)) {
                    $this->throw_error("$field is required", 400);
                }
            }
        }

        foreach ($apartmentData as $field => $value) {
            if ($field == 'contact_for_price' || $field == 'negotiable' || $field == 'brokarage' || $field == 'loan_availability' || $field == 'need_help') {
                if (!empty($value)) {
                    if (strtolower($value) != 'true' && strtolower($value) != 'false') {
                        $this->throw_error("$field must be boolen", 400);
                    }
                    if (strtolower($value) == 'true') {
                        $apartmentData[$field] = 1;
                    } else {
                        $apartmentData[$field] = 0;
                    }
                } else {
                    $apartmentData[$field] = 0;
                }
            }
        }

        if ($apartmentData['contact_for_price'] == 0) {
            if ($this->post('price_type') == 'range') {
                if (empty($this->post('min_price')) || empty($this->post('max_price'))) {
                    $this->throw_error('min_price and max_price are Required');
                }
            } else if ($this->post('price_type') == 'fixed') {
                if (empty($this->post('fixed_price'))) {
                    $this->throw_error('fixed_price Required');
                }
            }
        }
        if (isset($_FILES['banner_img']['name'])) {
            $banner_img = $this->file_upload($_FILES['banner_img'], PROPERTY_BANNER);
        } else {
            $banner_img = "";
        }
        $apartmentData['banner_img'] = $banner_img;
        $apartmentData['verification_status'] = 0;
        $apartmentData['property_type'] = 'LAND';

        $image_paths = [];

        for ($i = 0; $i < count($_FILES['photos']['name']); $i++) {
            $image['name'] = $_FILES['photos']['name'][$i];
            $image['type'] = $_FILES['photos']['type'][$i];
            $image['tmp_name'] = $_FILES['photos']['tmp_name'][$i];
            $image['size'] = $_FILES['photos']['size'][$i];
            $image['error'] = $_FILES['photos']['error'][$i];

            $image_paths[] = $this->file_upload($image, PROPERTY_IMAGES);
        }

        $last_id = $this->common_api_model->add_data('properties', $apartmentData);
        if ($last_id) {
            foreach ($image_paths as $image) {
                $img_data = array(
                    "property_id" => $last_id,
                    "image_url" => $image
                );
                $this->common_api_model->add_data('property_images', $img_data);
            }
            $data = array(
                'status' => 201,
                "message" => "Land added successfully."
            );
            $this->response($data, 201);
        } else {
            $data = array(
                'status' => 404,
                "message" => "Land adding failed."
            );
            $this->response($data, 404);
        }
    }

    public function updatePropertyStatus_post()
    {
        $this->checkAuth();
        if (empty($this->post('property_id'))) {
            $this->throw_error('property id required');
        }
        if (empty($this->post('prop_status'))) {
            $this->throw_error('property status required');
        }
        $property_id = $this->post('property_id');
        $prop_status = $this->post('prop_status');

        $data = array(
            'prop_status' => $prop_status,
            'updated_on' => CURRENT_DATE_TIME
        );
        $condition = "property_id=$property_id";
        $updateStatus = $this->common_api_model->update_data('properties', $data, $condition);
        if ($updateStatus) {
            $data = array(
                'status' => 201,
                "message" => "Property status updated successfully."
            );
            $this->response($data, 201);
        } else {
            $data = array(
                'status' => 404,
                "message" => "Status update failed."
            );
            $this->response($data, 404);
        }
    }

    public function propertyDetailsByCt_post()
    {
        $this->checkAuth();
        if (empty($this->post('ct_id'))) {
            $this->throw_error('ct id required');
        }
        if (empty($this->post('property_type'))) {
            $this->throw_error('property type required');
        }
        $ct_id = $this->post('ct_id');
        $property_type = $this->post('property_type');

        $sql = "select property_type, p.property_id, p.latitude, p.longitude, case when lower(p.price_type)='fixed' then p.fixed_price when lower(p.price_type)='range' then concat(p.min_price, '-', p.max_price) end as price from properties p where p.ct_id=$ct_id and p.prop_purpose='SELL' and p.prop_status='OPEN' and !(p.contact_for_price) and property_type like '%$property_type%'";
        $properties = $this->common_api_model->execute_raw_sql($sql);
        $data = array(
            'status' => 200,
            "message" => "Properties list.",
            'properties' => $properties
        );
        $this->response($data, 200);
    }

    public function propertyDetailsById_post()
    {
        $this->checkAuth();
        if (empty($this->post('property_id'))) {
            $this->throw_error('property id required');
        }
        if (empty($this->post('property_type'))) {
            $this->throw_error('property type required');
        }
        $property_type = $this->post('property_type');
        $property_id = $this->post('property_id');
        $properties = [];
        $data = array(
            'status' => 200,
            "message" => "Properties list.",
            'properties' => $properties
        );
        $this->response($data, 200);
    }
}
