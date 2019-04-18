<?php
class ModelExtensionDVuefrontWishlist extends Model
{
    public function getWishlist()
    {
        $result = array();
        if (VERSION < '2.1.0.0') {
            if (!empty($this->session->data['wishlist'])) {
                $result = $this->session->data['wishlist'];
            }
        } else {
            if ($this->customer->isLogged()) {
                $this->load->model('account/wishlist');
                $result = $this->model_account_wishlist->getWishlist();
            } else {
                if (!empty($this->session->data['wishlist'])) {
                    $result = $this->session->data['wishlist'];
                }
            }
        }
        return $result;
    }

    public function addWishlist($product_id)
    {
        $this->load->model('catalog/product');

        $product_info = $this->model_catalog_product->getProduct($product_id);

        if ($product_info) {
            if (VERSION < '2.1.0.0') {
                if (!isset($this->session->data['wishlist'])) {
                    $this->session->data['wishlist'] = array();
                }
                if (!in_array($product_id, $this->session->data['wishlist'])) {
                    $this->session->data['wishlist'][] = (int)$product_id;
                }
            } else {
                if ($this->customer->isLogged()) {
                    $this->load->model('account/wishlist');
                    $this->model_account_wishlist->addWishlist($product_id);
                } else {
                    if (!isset($this->session->data['wishlist'])) {
                        $this->session->data['wishlist'] = array();
                    }
                    if (!in_array($product_id, $this->session->data['wishlist'])) {
                        $this->session->data['wishlist'][] = (int)$product_id;
                    }
                }
            }
        }
    }

    public function deleteWishlist($product_id)
    {
        if (VERSION < '2.1.0.0') {
            if (!empty($this->session->data['wishlist'])) {
                $key = array_search($product_id, $this->session->data['wishlist']);

                if ($key !== false) {
                    unset($this->session->data['wishlist'][$key]);
                }
            }
        } else {
            if ($this->customer->isLogged()) {
                $this->load->model('account/wishlist');
                $this->model_account_wishlist->deleteWishlist($product_id);
            } else {
                if (!empty($this->session->data['wishlist'])) {
                    $key = array_search($product_id, $this->session->data['wishlist']);

                    if ($key !== false) {
                        unset($this->session->data['wishlist'][$key]);
                    }
                }
            }
        }
    }
}
