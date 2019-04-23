<?php

class ControllerExtensionDVuefrontBlogPost extends Controller
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

    public function post($args)
    {
        $this->load->model('extension/d_blog_module/post');
        $this->load->model('tool/image');
        $post_info = $this->model_extension_d_blog_module_post->getPost($args['id']);

        $width = $this->setting['post']['image_width'];
        $height = $this->setting['post']['image_height'];
        if ($post_info['image']) {
            $image = $this->model_tool_image->resize($post_info['image'], $width, $height);
            $imageLazy = $this->model_tool_image->resize($post_info['image'], 10, ceil(10 * $height / $width));
        } else {
            $image = '';
            $imageLazy = '';
        }

        return array(
            'id'          => $post_info['post_id'],
            'title'        => $post_info['title'],
            'description' => html_entity_decode($post_info['description'], ENT_QUOTES, 'UTF-8'),
            'shortDescription' => strip_tags(html_entity_decode($post_info['short_description'], ENT_QUOTES, 'UTF-8')),
            'image' => $image,
            'imageLazy' => $imageLazy
        );
    }

    public function postList($args)
    {
        $this->load->model('extension/d_blog_module/post');

        if (in_array($args['sort'], array('sort_order', 'model', 'quantity', 'price', 'date_added'))) {
            $args['sort'] = 'p.' . $args['sort'];
        } elseif (in_array($args['sort'], array('name'))) {
            $args['sort'] = 'pd.' . $args['sort'];
        }

        $posts = array();

        $filter_data = array(
            'filter_category_id' => $args['category_id'],
            'sort' => $args['sort'],
            'order' => $args['order'],
            'start' => ($args['page'] - 1) * $args['size'],
            'limit' => $args['size']
        );

        if (!empty($args['search'])) {
            $filter_data['filter_name'] = $args['search'];
            $filter_data['filter_description'] = $args['search'];
        }
        
        $post_total = $this->model_extension_d_blog_module_post->getTotalPosts($filter_data);

        $results = $this->model_extension_d_blog_module_post->getPosts($filter_data);

        foreach ($results as $result) {
            $posts[] = $this->post(array('id' => $result['post_id']));
        }

        return array(
            'content' => $posts,
            'first' => $args['page'] === 1,
            'last' => $args['page'] === ceil($post_total / $args['size']),
            'number' => (int)$args['page'],
            'numberOfElements' => count($posts),
            'size' => (int)$args['size'],
            'totalPages' => (int)ceil($post_total / $args['size']),
            'totalElements' => (int)$post_total,
        );
    }

    public function postReview($data) {
        $post = $data['parent'];

        $this->load->model('extension/d_blog_module/review');

        $results = $this->model_extension_d_blog_module_review->getReviewsByPostId($post['id']);

        $reviews = array();

        foreach ($results as $result) {
            $reviews[] = array(
                'author' => $result['author'],
                'author_email' => $result['guest_email'],
                'content' => $result['description'],
                'created_at' => $result['date_added'],
                'rating' => (float)$result['rating']
            );
        }

        return $reviews;
    }

    public function addReview($args)
    {
        $this->load->model('extension/d_blog_module/review');

        $reviewData = array(
            'author' => $args['author'],
            'image' => '',
            'description' => $args['content'],
            'rating' => $args['rating']
        );

        $reviewData['status'] = 0;

        if(!$this->setting['review']['moderate']){
            $reviewData['status'] = 1;
        }

        $this->model_extension_d_blog_module_review->addReview($args['id'], $reviewData);

        return $this->post($args);
    }
}
