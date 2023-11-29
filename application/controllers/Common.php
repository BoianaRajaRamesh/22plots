<?php

defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class Common extends REST_Controller
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

    public function homePage_post()
    {
        $this->checkAuth();
        if (empty($this->post('ct_id'))) {
            $this->throw_error('ct id Required');
        }
        $ct_id = $this->post('ct_id');
        $cdate = CURRENT_DATE;
        $sql = "Select * from home_v1 where '$cdate'>=active_st_dt and '$cdate'<=active_end_dt and ct_id=$ct_id";
        $data1 = $this->common_api_model->execute_raw_sql($sql);

        $sql = "select distinct h.ct_id, h.prop_id, h.name as prop_type, a.sequence as preference1, h.sequence as preference2, p.banner_img, p.property_name, p.area_name, ct.ct_name, case when p.contact_for_price then 'contact for price' when lower(p.price_type)='fixed' then p.fixed_price when lower(p.price_type)='range' then concat(p.min_price, 'to', p.max_price) else 'contact for price' end as price, p.brokarage, case when p.property_type='APARTMENT' then c1.apartment_config when p.property_type like '%HOUSE%' then c1.house_config else 'NA' end as config from home_v1 as h inner join ctowns as ct on h.ct_id=ct.ct_id and h.ct_id=$ct_id and current_date>=h.active_st_dt and current_date<=h.active_end_dt and name in ('APARTMENT', 'HOUSE', 'VENTURE', 'PLOT', 'LAND') inner join ct_prop_home as a on a.ct_id=h.ct_id and h.name=a.prop_type and current_date>=a.active_st_dt and current_date<=a.active_end_dt inner join properties as p on h.prop_id=p.property_id and p.prop_status='OPEN' left join (select property_id, concat(group_concat(bhk_type), ' BHK Apartments') as apartment_config, concat(bhk_type, ' Bed | ', bathrooms, ' Bath | ', case when super_builtup_area is not null then super_builtup_area when builtup_area is not null then builtup_area when carpet_area is not null then carpet_area else 'NA' end) as house_config from flats_config group by property_id) as c1 on p.property_id=c1.property_id order by ct_id, prop_type, preference1, preference2";
        $data2 = $this->common_api_model->execute_raw_sql($sql);

        $data = array(
            'status' => 200,
            "message" => "Home Page Details.",
            "home_list" => $data1,
            "house_list" => $data2,
        );
        $this->response($data, 200);
    }

    public function myContactedProperties_post()
    {
        $this->checkAuth();
        if (empty($this->post('ct_id'))) {
            $this->throw_error('ct id Required');
        }
        $ct_id = $this->post('ct_id');

        $sql = "Select p.property_id, p.banner_img, p.property_name, p.property_type, p.area_name, p.ct_id, ct.ct_name, case when p.contact_for_price then 'contact for price' when lower(p.price_type)='fixed' then p.fixed_price when lower(p.price_type)='range' then concat(p.min_price, 'to', p.max_price) else 'contact for price' end as price, p.brokarage, p.prop_status, case when p.property_type='APARTMENT' then c1.apartment_config when p.property_type like '%HOUSE%' then c1.house_config else 'NA' end as config, ch.called_on from properties p inner join call_history ch on ch.user_id=$ct_id and p.property_id=ch.property_id inner join ctowns as ct on p.ct_id=ct.ct_id left join (select property_id, concat(group_concat(bhk_type), ' BHK Apartments') as apartment_config, concat(bhk_type, ' Bed | ', bathrooms, ' Bath | ', case when super_builtup_area is not null then super_builtup_area when builtup_area is not null then builtup_area when carpet_area is not null then carpet_area else 'NA' end) as house_config from flats_config group by property_id) as c1 on p.property_id=c1.property_id";
        $data1 = $this->common_api_model->execute_raw_sql($sql);

        $data = array(
            'status' => 200,
            "message" => "Properties List.",
            "properties_list" => $data1
        );
        $this->response($data, 200);
    }

    public function viewedProperties_get()
    {
        $user_id = $this->checkAuth()['user_id'];

        $sql = "Select p.property_id, p.banner_img, p.property_name, p.property_type, p.area_name, p.ct_id, ct.ct_name, case when p.contact_for_price then 'contact for price' when lower(p.price_type)='fixed' then p.fixed_price when lower(p.price_type)='range' then concat(p.min_price, 'to', p.max_price) else 'contact for price' end as price, p.brokarage, p.prop_status, case when p.property_type='APARTMENT' then c1.apartment_config when p.property_type like '%HOUSE%' then c1.house_config else 'NA' end as config, pv.viewed_on from properties p inner join property_views pv on pv.user_id=$user_id and p.property_id=pv.property_id inner join ctowns as ct on p.ct_id=ct.ct_id left join (select property_id, concat(group_concat(bhk_type), ' BHK Apartments') as apartment_config, concat(bhk_type, ' Bed | ', bathrooms, ' Bath | ', case when super_builtup_area is not null then super_builtup_area when builtup_area is not null then builtup_area when carpet_area is not null then carpet_area else 'NA' end) as house_config from flats_config group by property_id) as c1 on p.property_id=c1.property_id";
        $data1 = $this->common_api_model->execute_raw_sql($sql);

        $data = array(
            'status' => 200,
            "message" => "Viewed Properties List.",
            "properties_list" => $data1
        );
        $this->response($data, 200);
    }

    public function wishlist_get()
    {
        $user_id = $this->checkAuth()['user_id'];

        $sql = "Select p.property_id, p.banner_img, p.property_name, p.property_type, p.area_name, p.ct_id, ct.ct_name, case when p.contact_for_price then 'contact for price' when lower(p.price_type)='fixed' then p.fixed_price when lower(p.price_type)='range' then concat(p.min_price, 'to', p.max_price) else 'contact for price' end as price, p.brokarage, p.prop_status, case when p.property_type='APARTMENT' then c1.apartment_config when p.property_type like '%HOUSE%' then c1.house_config else 'NA' end as config, pw.wishlisted_on from properties p inner join prop_wishlist pw on pw.user_id=$user_id and p.property_id=pw.property_id inner join ctowns as ct on p.ct_id=ct.ct_id left join (select property_id, concat(group_concat(bhk_type), ' BHK Apartments') as apartment_config, concat(bhk_type, ' Bed | ', bathrooms, ' Bath | ', case when super_builtup_area is not null then super_builtup_area when builtup_area is not null then builtup_area when carpet_area is not null then carpet_area else 'NA' end) as house_config from flats_config group by property_id) as c1 on p.property_id=c1.property_id";
        $data1 = $this->common_api_model->execute_raw_sql($sql);

        $data = array(
            'status' => 200,
            "message" => "WishList.",
            "properties_list" => $data1
        );
        $this->response($data, 200);
    }
}
