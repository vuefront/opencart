<?php

class ControllerDVuefrontStoreWishlist extends Controller
{
    private $codename = "d_vuefront";

    public function getList($args)
    {
        $this->load->model($this->codename."/wishlist");
        $wishlist = array();
        $results = $this->model_d_vuefront_wishlist->getWishlist();

        foreach ($results as $product_id) {
            $wishlist[] = $this->vfload->data('store/product/get', array('id' => $product_id));
        }

        return $wishlist;
    }

    public function add($args)
    {
        $this->request->post['product_id'] = $args['id'];

        if(VERSION < '1.5.5.0') {
            $this->vfload->controller('account/wishlist/update', array(), true);
        } else {
            $this->vfload->controller('account/wishlist/add', array(), true);
        }

        return $this->getList(array());
    }

    public function remove($args)
    {
        $this->load->model($this->codename."/wishlist");
        $this->model_d_vuefront_wishlist->deleteWishlist($args['id']);

        return $this->getList(array());
    }
}
