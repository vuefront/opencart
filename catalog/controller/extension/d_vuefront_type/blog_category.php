<?php

class ControllerExtensionDVuefrontTypeBlogCategory extends Controller
{
    private $codename = "d_vuefront";

    public function resolver() {
        return array(
            'categoryBlog' => function($root, $args) {
                return $this->load->controller('extension/'.$this->codename.'/blog_category/category', $args);
            },
            'categoriesBlogList'  => function($root, $args) {
                return $this->load->controller('extension/'.$this->codename.'/blog_category/categoryList', $args);
            }
        );
    }
}
