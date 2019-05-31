<?php

class ControllerExtensionDVuefrontCommonZone extends Controller
{
    private $codename = 'd_vuefront';
    
    public function get($args)
    {
        $this->load->model('extension/'.$this->codename.'/zone');
        $zone_info = $this->model_extension_d_vuefront_zone->getZone($args['id']);

        return array(
            'id'          => $zone_info['zone_id'],
            'name'        => $zone_info['name'],
            'countryId'   => $zone_info['country_id']
        );
    }

    public function getList($args)
    {

        $this->load->model('extension/'.$this->codename.'/zone');

        $countries = array();

        $filter_data = array(
            'sort' => $args['sort'],
            'order' => $args['order']
        );

        if ($args['size'] != -1) {
            $filter_data['start'] = ($args['page'] - 1) * $args['size'];
            $filter_data['limit'] = $args['size'];
        }

        if (!empty($args['search'])) {
            $filter_data['filter_name'] = $args['search'];
        }
        if (!empty($args['country_id'])) {
            $filter_data['filter_country_id'] = $args['country_id'];
        }

        $zone_total = $this->model_extension_d_vuefront_zone->getTotalZones($filter_data);

        $results = $this->model_extension_d_vuefront_zone->getZones($filter_data);

        if ($args['size'] == -1 && $zone_total != 0) {
            $args['size'] = $zone_total;
        } else if($args['size'] == -1 && $zone_total == 0) {
            $args['size'] = 1;
        }

        foreach ($results as $result) {
            $countries[] = $this->get(array('id' => $result['zone_id']));
        }
        return array(
            'content' => $countries,
            'first' => $args['page'] === 1,
            'last' => $args['page'] === ceil($zone_total / $args['size']),
            'number' => (int)$args['page'],
            'numberOfElements' => count($countries),
            'size' => (int)$args['size'],
            'totalPages' => (int)ceil($zone_total / $args['size']),
            'totalElements' => (int)$zone_total,
        );
    }
}
