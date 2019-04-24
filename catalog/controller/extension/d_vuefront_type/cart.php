<?php

class ControllerExtensionDVuefrontTypeCart extends Controller
{
    private $codename = "d_vuefront";

    public function resolver() {
        return array(
            'cart' => function($root, $args) {
                return $this->load->controller('extension/'.$this->codename.'/cart/cart');
            },
            'addToCart'  => function($root, $args) {
                return $this->load->controller('extension/'.$this->codename.'/cart/addToCart', $args);
            },
            'updateCart'  => function($root, $args) {
                return $this->load->controller('extension/'.$this->codename.'/cart/updateCart', $args);
            },
            'removeCart'  => function($root, $args) {
                return $this->load->controller('extension/'.$this->codename.'/cart/removeCart', $args);
            },
        );
    }
}
