<?php

class ControllerDVuefrontBlogReview extends Controller
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
    public function add($args)
    {
        if ($this->blog) {

            $reviewData = array(
                'author' => $args['author'],
                'image' => '',
                'description' => $args['content'],
                'rating' => $args['rating'],
            );

            $reviewData['status'] = 0;

            $this->model_blog->addReview($args['id'], $reviewData);

            return $this->vfload->data('blog/post/get', $args);
        } else {
          return array();
        }
    }

    public function get($data)
    {

        if ($this->blog) {
            $post = $data['parent'];

            $results = $this->model_blog->getReviewsByPostId($post['id']);
            $reviews = array();

            foreach ($results as $result) {
                $reviews[] = array(
                    'author' => $result['author'],
                    'author_email' => $result['guest_email'],
                    'content' => $result['description'],
                    'created_at' => $result['date_added'],
                    'rating' => (float) $result['rating'],
                );
            }


            return array(
                'content' => $reviews,
                'totalElements' => count($reviews)
            );
        } else {
          return array(
            'content' => array(),
            'totalElements' => 0
          );
        }
    }
}
