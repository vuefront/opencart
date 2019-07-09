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
        $this->d_blog_module = (file_exists(DIR_SYSTEM . 'library/d_shopunity/extension/d_blog_module.json'));

        if ($this->d_blog_module) {
            $this->load->model('extension/module/d_blog_module');
            $this->config_file = $this->model_extension_module_d_blog_module->getConfigFile('d_blog_module', $this->sub_versions);

            $this->setting = $this->model_extension_module_d_blog_module->getConfigData('d_blog_module', 'd_blog_module_setting', $this->config->get('config_store_id'), $this->config_file);
        }
    }

    public function get($args)
    {
        if ($this->d_blog_module) {
            $this->load->model('extension/d_blog_module/post');
            $this->load->model('extension/d_blog_module/review');
            $this->load->model('extension/' . $this->codename . '/d_blog_module');
            $this->load->model('tool/image');
            $post_info = $this->model_extension_d_blog_module_post->getPost($args['id']);
            $post_keyword = $this->model_extension_d_vuefront_d_blog_module->getPostKeyword($args['id']);

            if (!empty($post_keyword['keyword'])) {
                $keyword = $post_keyword['keyword'];
            } else {
                $keyword = '';
            }

            $width = $this->setting['post']['image_width'];
            $height = $this->setting['post']['image_height'];
            if ($post_info['image']) {
                $image = $this->model_tool_image->resize($post_info['image'], $width, $height);
                $imageLazy = $this->model_tool_image->resize($post_info['image'], 10, ceil(10 * $height / $width));
            } else {
                $image = '';
                $imageLazy = '';
            }

            $review_total_info = $this->model_extension_d_blog_module_review->getTotalReviewsByPostId($post_info['post_id']);
            $rating = (int) $review_total_info['rating'];

            return array(
                'id' => $post_info['post_id'],
                'title' => $post_info['title'],
                'name' => $post_info['title'],
                'description' => html_entity_decode($post_info['description'], ENT_QUOTES, 'UTF-8'),
                'shortDescription' => strip_tags(html_entity_decode($post_info['short_description'], ENT_QUOTES, 'UTF-8')),
                'image' => $image,
                'imageLazy' => $imageLazy,
                'datePublished' => iconv(mb_detect_encoding(strftime($this->setting['post']['date_format'][$this->config->get('config_language_id')], strtotime($post_info['date_published']))), "utf-8//IGNORE", strftime($this->setting['post']['date_format'][$this->config->get('config_language_id')], strtotime($post_info['date_published']))),
                'rating' => $rating,
                'next' => $this->vfload->resolver('blog/post/next'),
                'prev' => $this->vfload->resolver('blog/post/prev'),
                'reviews' => $this->vfload->resolver('blog/review/get'),
                'keyword' => $keyword,
            );
        } else {
            return array();
        }
    }

    public function getList($args)
    {
        if ($this->d_blog_module) {
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
                'limit' => $args['size'],
            );

            if (!empty($args['search'])) {
                $filter_data['filter_name'] = $args['search'];
                $filter_data['filter_description'] = $args['search'];
            }

            $post_total = $this->model_extension_d_blog_module_post->getTotalPosts($filter_data);

            $results = $this->model_extension_d_blog_module_post->getPosts($filter_data);

            foreach ($results as $result) {
                $posts[] = $this->get(array('id' => $result['post_id']));
            }

            return array(
                'content' => $posts,
                'first' => $args['page'] === 1,
                'last' => $args['page'] === ceil($post_total / $args['size']),
                'number' => (int) $args['page'],
                'numberOfElements' => count($posts),
                'size' => (int) $args['size'],
                'totalPages' => (int) ceil($post_total / $args['size']),
                'totalElements' => (int) $post_total,
            );
        } else {
            return array(
                'content' => array(),
                'first' => 1,
                'last' => 1,
                'number' => 0,
                'numberOfElements' => 0,
                'size' => 0,
                'totalPages' => 0,
                'totalElements' => 0,
            );
        }
    }

    public function next($args)
    {
        if ($this->d_blog_module) {
            $this->load->model('extension/d_blog_module_post');
            $post = $args['parent'];
            $next_post_info = $this->model_extension_d_blog_module_post->getNextPost($post['id'], 0);
            if(empty($next_post_info)) {
                return null;
            }
            return $this->get(array('id' => $next_post_info['post_id']));
        } else {
            return array();
        }
    }

    public function prev($args)
    {
        if ($this->d_blog_module) {
            $this->load->model('extension/d_blog_module_post');
            $post = $args['parent'];
            $prev_post_info = $this->model_extension_d_blog_module_post->getPrevPost($post['id'], 0);
            if (empty($prev_post_info)) {
                return null;
            }
            return $this->get(array('id' => $prev_post_info['post_id']));

        } else {
            return array();
        }
    }
}
