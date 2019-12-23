<?php

class ControllerDVuefrontBlogPost extends Controller
{
    private $codename = "d_vuefront";
    private $blog = false;
    private $model_blog = false;

    public function __construct( $registry ) {
        parent::__construct($registry);
        $this->load->model('module/'.$this->codename);
        $this->blog = $this->model_module_d_vuefront->detectBlog();

        if($this->blog) {
            $this->load->model($this->codename.'/blog_'.$this->blog);
            $this->model_blog = $this->{'model_'.$this->codename.'_blog_'.$this->blog};
        }
    }

    public function get($args)
    {
        if ($this->blog) {
            $this->load->model('tool/image');
            $post_info = $this->model_blog->getPost($args['id']);
            $post_keyword = $this->model_blog->getPostKeyword($args['id']);

            if (!empty($post_keyword['keyword'])) {
                $keyword = $post_keyword['keyword'];
            } else {
                $keyword = '';
            }

            $width = $this->config->get('config_image_product_width');
            $height = $this->config->get('config_image_product_height');

            if ($post_info['image']) {
                $image = $this->model_tool_image->resize($post_info['image'], $width, $height);
                $imageLazy = $this->model_tool_image->resize($post_info['image'], 10, ceil(10 * $height / $width));
            } else {
                $image = '';
                $imageLazy = '';
            }

            $review_total_info = $this->model_blog->getTotalReviewsByPostId($post_info['post_id']);
            $rating = (int) $review_total_info['rating'];

            $date_format = '%A %d %B %Y';

            return array(
                'id' => $post_info['post_id'],
                'name' => $post_info['title'],
                'title' => $post_info['title'],
                'description' => html_entity_decode($post_info['description'], ENT_QUOTES, 'UTF-8'),
                'shortDescription' => utf8_substr(trim(strip_tags(html_entity_decode($post_info['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('config_product_description_length')) . '..',
                'image' => $image,
                'imageLazy' => $imageLazy,
                'datePublished' => iconv(mb_detect_encoding(strftime($date_format, strtotime($post_info['date_published']))), "utf-8//IGNORE", strftime($date_format, strtotime($post_info['date_published']))),
                'reviews' => $this->vfload->resolver('blog/review/get'),
                'rating' => $rating,
                'keyword' => $keyword,
                'categories' => $this->vfload->resolver('blog/post/categories'),
                'next' => $this->vfload->resolver('blog/post/next'),
                'prev' => $this->vfload->resolver('blog/post/prev'),
                'meta' => array(
                    'title' => html_entity_decode($post_info['meta_title'], ENT_QUOTES, 'UTF-8'),
                    'description' => html_entity_decode($post_info['meta_description'], ENT_QUOTES, 'UTF-8'),
                    'keyword' => $post_info['meta_keyword']
                )
            );
        } else {
          return array();
        }
    }

    public function getList($args)
    {
        if ($this->blog) {
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

            $post_total = $this->model_blog->getTotalPosts($filter_data);

            $results = $this->model_blog->getPosts($filter_data);

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
            'first' => true,
            'last' => true,
            'number' => 1,
            'numberOfElements' => 0,
            'size' => 0,
            'totalPages' => 0,
            'totalElements' => 0
          );
        }
    }

    public function next($args)
    {
        if ($this->blog) {
            $post = $args['parent'];
            $next_post_info = $this->model_blog->getNextPost($post['id']);
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
        if ($this->blog) {
            $post = $args['parent'];
            $prev_post_info = $this->model_blog->getPrevPost($post['id']);
            if (empty($prev_post_info)) {
                return null;
            }
            return $this->get(array('id' => $prev_post_info['post_id']));

        } else {
            return array();
        }
    }
    public function categories($args)
    {
        if ($this->blog) {
            $post = $args['parent'];

            $result = $this->model_blog->getCategoryByPostId($post['id']);
            $categories = array();
            foreach ($result as $category) {
                $categories[] =$this->vfload->data('blog/category/get', array('id' => $category['category_id']));
            }
            return $categories;
        } else {
            return array();
        }
    }
}
