<?php

class ModelExtensionDVuefrontCart extends Model {
    public function prepareCart() {
        $cart = array();
        $results = $this->cart->getProducts();

        $cart['products'] = array();

        foreach ($results as $product) {

			if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
				$unit_price = $this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax'));

				$price = $this->currency->format($unit_price, $this->session->data['currency']);
				$total = $this->currency->format($unit_price * $product['quantity'], $this->session->data['currency']);
			} else {
				$price = false;
				$total = false;
            }

            $option_data = array();

            foreach ($product['option'] as $option) {
				$option_data[] = array(
                    'option_id' => $option['option_id'],
                    'option_value_id' => $option['option_value_id'],
				);
			}

            $product_info = $this->vfload->data('store/product/get', array('id' => $product['product_id']));
            $product_info['price'] = $price;

            $cart_id = VERSION > '2.0.3.1' ? $product['cart_id'] : $product['key'];

            $cart['products'][] = array(
                'key' => $cart_id,
                'product' => array(
                    'product_id' => $product_info['id'],
                    'price' => $product_info['price'],
                ),
                'quantity' => (int)$product['quantity'],
                'option' => $option_data,
                'total' => $total
            );
        }

        $cart['total'] =  $this->currency->format($this->cart->getTotal(),  $this->session->data['currency']);

        return $cart;
    }

    public function getProduct($key) {
        $product = null;

        foreach ($this->cart->getProducts() as $value) {
            if ($value['cart_id'] == $key) {
                $product = $value;
                break;
            }
        }
        if (is_null($product)) {
            return null;
        }

        return $product;
    }
}
