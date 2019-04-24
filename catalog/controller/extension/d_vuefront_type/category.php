<?php

class ControllerExtensionDVuefrontTypeCategory extends Controller
{
    private $codename = "d_vuefront";

    public function resolver() {
        return array(
            'category' => function($root, $args) {
                return $this->load->controller('extension/'.$this->codename.'/category/category', $args);
            },
            'categoriesList'  => function($root, $args) {
                return $this->load->controller('extension/'.$this->codename.'/category/categoryList', $args);
            }
        );
    }
}
