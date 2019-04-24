<?php

class ControllerExtensionDVuefrontTypeWishlist extends Controller
{
    private $codename = "d_vuefront";

    public function resolver() {
        return array(
            'wishlist' => function($root, $args) {
                return $this->load->controller('extension/' . $this->codename . '/wishlist/wishlist');
            },
            'addToWishlist' => function($root, $args) {
                return $this->load->controller('extension/'.$this->codename.'/wishlist/addToWishlist', $args);
            },
            'removeWishlist' => function($root, $args) {
                return $this->load->controller('extension/'.$this->codename.'/wishlist/removeWishlist', $args);
            }
        );
    }
}
