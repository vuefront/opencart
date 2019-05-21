<?php

class ControllerExtensionDVuefrontBlogCategory extends Controller
{
    private $codename = "d_vuefront";
    private $sub_versions = array('lite', 'light', 'free');
    private $config_file = '';
    private $setting = array();

    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->load->model('extension/module/d_blog_module');
        $this->config_file = $this->model_extension_module_d_blog_module->getConfigFile('d_blog_module', $this->sub_versions);

        $this->setting = $this->model_extension_module_d_blog_module->getConfigData('d_blog_module', 'd_blog_module_setting', $this->config->get('config_store_id'), $this->config_file);
    }

    public function get($args)
    {
        $this->load->model('extension/d_blog_module/category');
        $this->load->model('extension/'.$this->codename.'/d_blog_module');
        $this->load->model('tool/image');

        $category_info = $this->model_extension_d_blog_module_category->getCategory($args['id']);
        $category_keyword = $this->model_extension_d_vuefront_d_blog_module->getCategoryKeyword($args['id']);

        if (!empty($category_keyword['keyword'])) {
            $keyword = $category_keyword['keyword'];
        } else {
            $keyword = '';
        }

        $width = $this->setting['category']['sub_category_image_width'];
        $height = $this->setting['category']['sub_category_image_height'];
        if ($category_info['image']) {
            $image = $this->model_tool_image->resize($category_info['image'], $width, $height);
            $imageLazy = $this->model_tool_image->resize($category_info['image'], 10, ceil(10 * $height / $width));
        } else {
            $image = '';
            $imageLazy = '';
        }

        return array(
            'id'          => $category_info['category_id'],
            'name'        => $category_info['title'],
            'description' => html_entity_decode($category_info['description'], ENT_QUOTES, 'UTF-8'),
            'parent_id'   => $category_info['parent_id'],
            'image' => $image,
            'imageLazy' => $imageLazy,
            'url' => $this->vfload->resolver('blog/category/url'),
            'categories' => $this->vfload->resolver('blog/category/child'),
            'keyword' => $keyword
        );
    }

    public function getList($args)
    {
        $this->load->model('extension/'.$this->codename.'/d_blog_module');

        $filter_data = array(
            'start' => ($args['page'] - 1) * $args['size'],
            'limit' => $args['size'],
            'sort' => $args['sort'],
            'order'   => $args['order']
        );

        if ($args['parent'] !== -1) {
            $filter_data['parent'] = $args['parent'];
        }

        $results = $this->model_extension_d_vuefront_d_blog_module->getCategories($filter_data);
        $category_total = $this->model_extension_d_vuefront_d_blog_module->getTotalCategories($filter_data);

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
        $this->load->model('extension/'.$this->codename.'/d_blog_module');
        $category_info = $data['parent'];
        $results = $this->model_extension_d_vuefront_d_blog_module->getCategories(array('parent' => $category_info['id']));

        $categories = array();

        foreach ($results as $result) {
            $categories[] = $this->get(array('id' => $result['category_id']));
        }

        return $categories;
    }

    public function url($data) {
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
