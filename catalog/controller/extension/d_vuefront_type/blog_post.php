<?php

class ControllerExtensionDVuefrontTypeBlogPost extends Controller
{
    private $codename = "d_vuefront";

    public function resolver() {
        return array(
            'post' => function($root, $args) {
                return $this->load->controller('extension/' . $this->codename . '/blog_post/post', $args);
            },
            'postsList'  => function($root, $args) {
                return $this->load->controller('extension/' . $this->codename . '/blog_post/postList', $args);
            },
            'addBlogPostReview'  => function($root, $args) {
                return $this->load->controller('extension/' . $this->codename . '/blog_post/addReview', $args);
            },
        );
    }
}