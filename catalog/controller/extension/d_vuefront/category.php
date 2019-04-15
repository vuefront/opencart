<?php

class ControllerExtensionDVuefrontCategory extends Controller
{
    private $codename = "d_vuefront";

    public function category($args) {
        $this->load->model('catalog/category');
        $this->load->model('tool/image');
        $category_info = $this->model_catalog_category->getCategory($args['id']);

        $width = $this->config->get('theme_' . $this->config->get('config_theme') . '_image_category_width');
        $height = $this->config->get('theme_' . $this->config->get('config_theme') . '_image_category_height');
        if ($category_info['image']) {
            $image = $this->model_tool_image->resize($category_info['image'], $width, $height);
            $imageLazy = $this->model_tool_image->resize($category_info['image'], 10, ceil(10 * $height / $width));
        } else {
            $image = $this->model_tool_image->resize('placeholder.png', $width, $height);
            $imageLazy = $this->model_tool_image->resize('placeholder.png', 10, ceil(10 * $height / $width));
        }

        return array(
            'id'          => $category_info['category_id'],
            'name'        => html_entity_decode($category_info['name'], ENT_QUOTES, 'UTF-8'),
            'description' => html_entity_decode($category_info['description'], ENT_QUOTES, 'UTF-8'),
            'parent_id'   => $category_info['parent_id'],
            'image'       => $image,
            'imageLazy'   => $imageLazy
        );
    }

    public function categoryList($args) {
        $this->load->model('extension/module/'.$this->codename);

        $filter_data = array(
            'sort' => $args['sort'],
            'order'   => $args['order']
        );

        if($args['size'] !== -1) {
            $filter_data['start'] = ($args['page'] - 1) * $args['size'];
            $filter_data['limit'] = $args['size'];
        }

        if ( $args['parent'] !== -1 ) {
            $filter_data['parent'] = $args['parent'];
        }

        $results = $this->model_extension_module_d_vuefront->getCategories($filter_data);
        $category_total = $this->model_extension_module_d_vuefront->getTotalCategories($filter_data);

        $categories = array();

        foreach ($results as $result) {
            $categories[] = $this->category(array('id' => $result['category_id']));
        }

        return array(
            'content' => $categories,
            'first' => $args['page'] === 1,
            'last' => $args['page'] === ceil($category_total / $args['size']),
            'number' => (int)$args['page'],
            'numberOfElements' => count($categories),
            'size' => (int)$args['size'],
            'totalPages' => (int)ceil($category_total / $args['size']),
            'totalElements' => (int)$category_total,
        );
    }

    public function childCategories($data) {
        $this->load->model('extension/module/'.$this->codename);
        $category_info = $data['parent'];
        $results = $this->model_extension_module_d_vuefront->getCategories(array('parent' => $category_info['id']));

        $categories = array();

        foreach ($results as $result) {
            $categories[] = $this->category(array('id' => $result['category_id']));
        }

        return $categories;
    }
}