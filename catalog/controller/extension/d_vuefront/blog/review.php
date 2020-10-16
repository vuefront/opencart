<?php

class ControllerExtensionDVuefrontBlogReview extends Controller
{
    private $codename = "d_vuefront";

    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->d_blog_module = (file_exists(DIR_SYSTEM . 'library/d_shopunity/extension/d_blog_module.json'));
    }

    public function add($args)
    {
        if ($this->d_blog_module) {
            $this->load->model('extension/d_blog_module/review');

            $reviewData = array(
                'author' => $args['author'],
                'image' => '',
                'description' => $args['content'],
                'rating' => $args['rating'],
            );

            $reviewData['status'] = 0;

            if (!$this->setting['review']['moderate']) {
                $reviewData['status'] = 1;
            }

            $this->model_extension_d_blog_module_review->addReview($args['id'], $reviewData);

            return $this->vfload->data('blog/post/get', $args);
        } else {
            return null;
        }
    }

    public function get($data)
    {
        if ($this->d_blog_module) {
            $post = $data['parent'];

            $this->load->model('extension/d_blog_module/review');

            $results = $this->model_extension_d_blog_module_review->getReviewsByPostId($post['id']);
            $total = $this->model_extension_d_blog_module_review->getTotalReviewsByPostId($post['id']);

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
                'content'=> $reviews,
                'totalElements' => $total['total']
            );
        } else {
            return array();
        }
    }
}
