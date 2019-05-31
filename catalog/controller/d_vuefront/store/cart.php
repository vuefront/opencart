<?php

class ControllerDVuefrontStoreCart extends Controller
{
    private $codename = "d_vuefront";

    public function get($args)
    {
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
                if ($option['type'] != 'file') {
                    $value = $option['option_value'];
                } else {
                    $filename = $this->encryption->decrypt($option['option_value']);

                    $value = utf8_substr($filename, 0, utf8_strrpos($filename, '.'));
                }

                $option_data[] = array(
                    'name'  => $option['name'],
                    'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value),
                    'type' => $option['type']
                );
			}
            
            $product_info = $this->vfload->data('store/product/get', array('id' => $product['product_id']));
            $product_info['price'] = $price;

            $cart['products'][] = array(
                'key' => $product['key'],
                'product' => $product_info,
                'quantity' => (int)$product['quantity'],
                'option' => $option_data,
                'total' => $total
            );
        }

        $cart['total'] =  $this->currency->format($this->cart->getTotal(),  $this->session->data['currency']);

        return $cart;
    }

    public function add($args)
    {
        $this->request->post['product_id'] = $args['id'];
        $this->request->post['quantity'] = $args['quantity'];
        $this->request->post['option'] = array();

        foreach ($args['options'] as $option) {
            $this->request->post['option'][$option['id']] = strpos( $option['value'], '|') == false ? $option['value'] : explode('|', $option['value']);
        }

        $this->vfload->controller('checkout/cart/add', array(), true);

        ob_start();
        $this->response->output();
        $result = json_decode(ob_get_contents(), true);
        ob_end_clean();

        if(!empty($result['error'])) {
            if(!empty($result['error']['option'])) {
                throw new Exception(reset($result['error']['option']));
            } else if(!empty($result['error']['recurring'])) {
                throw new Exception($result['error']['recurring']);
            } else {
                throw new Exception('error cart');
            }
        }

        return $this->get(array());
    }

    public function update($args)
    {
        $this->cart->update($args['key'], $args['quantity']);

        return $this->get(array());
    }

    public function remove($args)
    {
        $this->cart->remove($args['key']);

        return $this->get(array());
    }
}
