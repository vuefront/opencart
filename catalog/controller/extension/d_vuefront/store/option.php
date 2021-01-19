<?php
use SemVer\version;

class ControllerExtensionDVuefrontStoreOption extends Controller
{
    private $codename = "d_vuefront";

    public function getList($args)
    {
        $this->load->model('extension/' . $this->codename . '/option');

        if (in_array($args['sort'], array('sort_order', ))) {
            $args['sort'] = 'o.' . $args['sort'];
        } elseif (in_array($args['sort'], array('name'))) {
            $args['sort'] = 'od.' . $args['sort'];
        }

        $options = array();

        $filter_data = array(
            'sort' => $args['sort'],
            'order' => $args['order']
        );

        if($args['size'] != '-1') {
            $filter_data['start'] = ($args['page'] - 1) * $args['size'];
            $filter_data['limit'] = $args['size'];
        }

        if (!empty($args['search'])) {
            $filter_data['filter_name'] = $args['search'];
        }

        $option_total = $this->model_extension_d_vuefront_option->getTotalOptions($filter_data);

        $results = $this->model_extension_d_vuefront_option->getOptions($filter_data);

        if ($args['size'] == -1 && $option_total != 0) {
            $args['size'] = $option_total;
        } else if($args['size'] == -1 && $option_total == 0) {
            $args['size'] = 1;
        }

        foreach ($results as $result) {
            $options[] = $this->get(array('id' => $result['option_id']));
        }

        return array(
            'content' => $options,
            'first' => $args['page'] === 1,
            'last' => $args['page'] === ceil($option_total / $args['size']),
            'number' => (int)$args['page'],
            'numberOfElements' => count($options),
            'size' => (int)$args['size'],
            'totalPages' => (int)ceil($option_total / $args['size']),
            'totalElements' => (int)$option_total,
        );
    }

    public function get($args)
    {
        $this->load->model('extension/'.$this->codename.'/option');
        $option_info = $this->model_extension_d_vuefront_option->getOption($args['id']);

        if (!$option_info) {
            return array();
        }


        return array(
            'id' => $option_info['option_id'],
            'name' => html_entity_decode($option_info['name'], ENT_QUOTES, 'UTF-8'),
            'type' => $option_info['name'],
            'sort_order' => $option_info['sort_order'],
            'values' => $this->vfload->resolver('store/option/getValues')
        );
    }

    public function getValues($data) {
        $this->load->model('extension/'.$this->codename.'/option');
        $results = array();
        $option_values = $this->model_extension_d_vuefront_option->getOptionValues($data['parent']['id']);

        foreach ($option_values as $key => $value) {
            $results[] = array(
                'id' => $value['option_value_id'],
                'name' => $value['name']
            );
        }

        return $results;
    }
}