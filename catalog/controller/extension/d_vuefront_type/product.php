<?php

class ControllerExtensionDVuefrontTypeProduct extends Controller
{
    private $codename = "d_vuefront";

    public function resolver() {
        return array(
            'productsList' => function($root, $args) {
                $product =  $this->load->controller('extension/' . $this->codename . '/product/products', $args);

                return $product;
            },
            'product' => function($root, $args) {
                return $this->load->controller('extension/' . $this->codename . '/product/product', $args);
            },
            'addReview' => function($root, $args) {
                return $this->load->controller('extension/'.$this->codename.'/product/addReview', $args);
            }
        );
    }
}
