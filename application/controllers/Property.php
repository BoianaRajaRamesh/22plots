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

        $data = array(
            'status' => 200,
            "message" => "properties list.",
            "properties" => $properties
        );
        $this->response($data, 200);
    }
}
