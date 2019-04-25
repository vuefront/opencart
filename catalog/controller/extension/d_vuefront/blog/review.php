<?php

class ControllerExtensionDVuefrontBlogReview extends Controller
{
    private $codename = "d_vuefront";

    
    public function add($args)
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

        return $this->vfload->data('blog/post/get', $args);
    }

    public function get($data) {
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
}
