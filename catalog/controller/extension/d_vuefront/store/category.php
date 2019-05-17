<?php

class ControllerExtensionDVuefrontStoreCategory extends Controller
{
    private $codename = "d_vuefront";

    public function get($args)
    {
        $this->load->model('catalog/category');
        $this->load->model('extension/'.$this->codename.'/category');
        $this->load->model('tool/image');
        $category_info = $this->model_catalog_category->getCategory($args['id']);
        $category_keyword = $this->model_extension_d_vuefront_category->getCategoryKeyword($args['id']);

        if (!empty($category_keyword['keyword'])) {
            $keyword = $category_keyword['keyword'];
        } else {
            $keyword = '';
        }

        $width = $this->config->get('theme_' . $this->config->get('config_theme') . '_image_category_width');
        $height = $this->config->get('theme_' . $this->config->get('config_theme') . '_image_category_height');
        if ($category_info['image']) {
            $image = $this->model_tool_image->resize($category_info['image'], $width, $height);
            $imageLazy = $this->model_tool_image->resize($category_info['image'], 10, ceil(10 * $height / $width));
        } else {
            $image = '';
            $imageLazy = '';
        }

        return array(
            'id'          => $category_info['category_id'],
            'name'        => html_entity_decode($category_info['name'], ENT_QUOTES, 'UTF-8'),
            'description' => html_entity_decode($category_info['description'], ENT_QUOTES, 'UTF-8'),
            'parent_id'   => $category_info['parent_id'],
            'image'       => $image,
            'imageLazy'   => $imageLazy,
            'url' => $this->vfload->resolver('store/category/url'),
            'categories' => $this->vfload->resolver('store/category/child'),
            'keyword'    => $keyword
        );
    }

    public function getList($args)
    {
        $this->load->model('extension/'.$this->codename.'/category');

        $filter_data = array(
            'sort' => $args['sort'],
            'order'   => $args['order']
        );

        if ($args['size'] !== -1) {
            $filter_data['start'] = ($args['page'] - 1) * $args['size'];
            $filter_data['limit'] = $args['size'];
        }

        if ($args['parent'] !== -1) {
            $filter_data['parent'] = $args['parent'];
        }

        $results = $this->model_extension_d_vuefront_category->getCategories($filter_data);
        $category_total = $this->model_extension_d_vuefront_category->getTotalCategories($filter_data);

        $categories = array();

        foreach ($results as $result) {
            $categories[] = $this->get(array('id' => $result['category_id']));
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

    public function child($data)
    {
        $this->load->model('extension/'.$this->codename.'/category');
        $category_info = $data['parent'];
        $results = $this->model_extension_d_vuefront_category->getCategories(array('parent' => $category_info['id']));

        $categories = array();

        foreach ($results as $result) {
            $categories[] = $this->get(array('id' => $result['category_id']));
        }

        return $categories;
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
