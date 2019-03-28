<?php

class ControllerExtensionDVuefrontBlogCategory extends Controller
{
    private $codename = "d_vuefront";

    public function category($args) {
        $this->load->model('extension/d_blog_module/category');
        $this->load->model('tool/image');
        $category_info = $this->model_extension_d_blog_module_category->getCategory($args['id']);

        return array(
            'id'          => $category_info['category_id'],
            'name'        => $category_info['title'],
            'description' => html_entity_decode($category_info['description'], ENT_QUOTES, 'UTF-8'),
            'parent_id'   => $category_info['parent_id'],
        );
    }

    public function categoryList($args) {
        $this->load->model('extension/'.$this->codename.'/d_blog_module');

        $filter_data = array(
            'start' => ($args['page'] - 1) * $args['size'],
            'limit' => $args['size'],
            'sort' => $args['sort'],
            'order'   => $args['order']
        );

        if ( $args['parent'] !== 0 ) {
            $filter_data['parent'] = $args['parent'];
        }

        $results = $this->model_extension_d_vuefront_d_blog_module->getCategories($filter_data);
        $category_total = $this->model_extension_d_vuefront_d_blog_module->getTotalCategories($filter_data);

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
}