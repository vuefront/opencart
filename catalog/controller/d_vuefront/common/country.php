<?php

class ControllerDVuefrontCommonCountry extends Controller {

    private $codename = 'd_vuefront';
    
    public function get($args)
    {
        $this->load->model($this->codename.'/country');
        $information_info = $this->model_d_vuefront_country->getCountry($args['id']);

        return array(
            'id'          => $information_info['country_id'],
            'name'        => $information_info['name']
        );
    }

    public function getList($args) {
        $this->load->model($this->codename.'/country');

        $countries = array();

        $filter_data = array(
            'sort' => $args['sort'],
            'order' => $args['order']
        );

        if($args['size'] != -1) {
            $filter_data['start'] = ($args['page'] - 1) * $args['size'];
            $filter_data['limit'] = $args['size'];
        }

        if (!empty($args['search'])) {
            $filter_data['filter_name'] = $args['search'];
        }
        
        $country_total = $this->model_d_vuefront_country->getTotalCountries($filter_data);

        $results = $this->model_d_vuefront_country->getCountries($filter_data);

        if($args['size'] == -1) {
            $args['size'] = $country_total;
        }

        foreach ($results as $result) {
            $countries[] = $this->get(array('id' => $result['country_id']));
        }

        return array(
            'content' => $countries,
            'first' => $args['page'] === 1,
            'last' => $args['page'] === ceil($country_total / $args['size']),
            'number' => (int)$args['page'],
            'numberOfElements' => count($countries),
            'size' => (int)$args['size'],
            'totalPages' => (int)ceil($country_total / $args['size']),
            'totalElements' => (int)$country_total,
        );
    }
} 