<?php

class ControllerExtensionDVuefrontStoreManufacturer extends Controller
{
    private $codename = "d_vuefront";

    public function get($args)
    {
        $this->load->model('extension/'.$this->codename.'/manufacturer');
        $this->load->model('tool/image');
        $manufacturer_info = $this->model_extension_d_vuefront_manufacturer->getManufacturer($args['id']);

        if (empty($manufacturer_info)) {
            return array();
        }

        if(VERSION >= "3.0.0.0"){
            $width = $this->config->get('theme_' . $this->config->get('config_theme') . '_image_category_width');
            $height = $this->config->get('theme_' . $this->config->get('config_theme') . '_image_category_height');
        } else if (VERSION >= '2.2.0.0') {
            $width = $this->config->get($this->config->get('config_theme') . '_image_category_width');
            $height = $this->config->get($this->config->get('config_theme') . '_image_category_height');
        } else {
            $width = $this->config->get('config_image_category_width');
            $height = $this->config->get('config_image_category_height');
        }
        if ($manufacturer_info['image']) {
            $image = $this->model_tool_image->resize($manufacturer_info['image'], $width, $height);
            $imageLazy = $this->model_tool_image->resize($manufacturer_info['image'], 10, ceil(10 * $height / $width));
        } else {
            $image = '';
            $imageLazy = '';
        }

        $manufacturer_keyword = $this->model_extension_d_vuefront_manufacturer->getManufacturerKeyword($args['id']);

        if(!empty($manufacturer_keyword['keyword'])) {
            $keyword = $manufacturer_keyword['keyword'];
        } else {
            $keyword = '';
        }

        return array(
            'id'          => $manufacturer_info['manufacturer_id'],
            'name'        => html_entity_decode($manufacturer_info['name'], ENT_QUOTES, 'UTF-8'),
            'image'       => $image,
            'imageLazy'   => $imageLazy,
            'url' => $this->vfload->resolver('store/manufacturer/url'),
            'keyword' => $keyword,
            'sort_order'  => $manufacturer_info['sort_order']
        );
    }

    public function getList($args)
    {
        $this->load->model('extension/'.$this->codename.'/manufacturer');

        $filter_data = array(
            'sort' => $args['sort'],
            'order'   => $args['order']
        );

        if ($args['size'] !== -1) {
            $filter_data['start'] = ($args['page'] - 1) * $args['size'];
            $filter_data['limit'] = $args['size'];
        }

        if (!empty($args['search'])) {
            $filter_data['filter_name'] = $args['search'];
        }

        $results = $this->model_extension_d_vuefront_manufacturer->getManufacturers($filter_data);
        $manufacturer_total = $this->model_extension_d_vuefront_manufacturer->getTotalManufacturers($filter_data);

        $manufacturers = array();

        foreach ($results as $result) {
            $manufacturers[] = $this->get(array('id' => $result['manufacturer_id']));
        }

        return array(
            'content' => $manufacturers,
            'first' => $args['page'] === 1,
            'last' => $args['page'] === ceil($manufacturer_total / $args['size']),
            'number' => (int)$args['page'],
            'numberOfElements' => count($manufacturers),
            'size' => (int)$args['size'],
            'totalPages' => (int)ceil($manufacturer_total / $args['size']),
            'totalElements' => (int)$manufacturer_total,
        );
    }


    public function url($data)
    {
        $category_info = $data['parent'];
        $result = $data['args']['url'];

        $result = str_replace("_id", $category_info['id'], $result);
        $result = str_replace("_name", $category_info['name'], $result);


        if ($category_info['keyword']) {
            $result = '/'.$category_info['keyword'];
        }


        return $result;
    }
}
