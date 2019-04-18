<?php

class ControllerExtensionDVuefrontWishlist extends Controller
{
    private $codename = "d_vuefront";

    public function wishlist($args)
    {
        $this->load->model("extension/".$this->codename."/wishlist");
        $wishlist = array();
        $results = $this->model_extension_d_vuefront_wishlist->getWishlist();

        foreach ($results as $product_id) {
            $wishlist[] = $this->load->controller('extension/'.$this->codename.'/product/product', array('id' => $product_id));
        }

        return $wishlist;
    }

    public function addToWishlist($args)
    {
        $this->request->post['product_id'] = $args['id'];

        $this->load->controller('account/wishlist/add');

        return $this->wishlist(array());
    }

    public function removeWishlist($args)
    {
        $this->load->model("extension/".$this->codename."/wishlist");
        $this->model_extension_d_vuefront_wishlist->deleteWishlist($args['id']);

        return $this->wishlist(array());
    }
}
