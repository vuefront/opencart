<?php

class ControllerExtensionDVuefrontStoreCart extends Controller
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
					$value = $option['value'];
				} else {
                    $this->load->model('tool/upload');

                    $upload_info = $this->model_tool_upload->getUploadByCode($option['value']);

					if ($upload_info) {
						$value = $upload_info['name'];
					} else {
						$value = '';
					}
				}

				$option_data[] = array(
					'name'  => $option['name'],
					'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value),
					'type'  => $option['type']
				);
			}
            
            $product_info = $this->vfload->data('store/product/get', array('id' => $product['product_id']));
            $product_info['price'] = $price;

            $cart_id = VERSION > '2.0.3.1' ? $product['cart_id'] : $product['key'];

            $cart['products'][] = array(
                'key' => $cart_id,
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

        $this->load->model('extension/module/d_vuefront');

        $this->model_extension_module_d_vuefront->pushEvent("add_to_cart",  array( "cart" => $this->request->post, "customer_id" => $this->customer->getId(), "guest" => $this->customer->isLogged() ? false : true));
        
        $this->load->controller('checkout/cart/add');

        $result = json_decode($this->response->getOutput(), true);
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

        $this->load->model('extension/module/d_vuefront');
        $this->model_extension_module_d_vuefront->pushEvent("update_cart",  array( "cart" => $args, "customer_id" => $this->customer->getId(), "guest" => $this->customer->isLogged() ? false : true));

        return $this->get(array());
    }

    public function remove($args)
    {
        $this->cart->remove($args['key']);

        $this->load->model('extension/module/d_vuefront');

        $this->model_extension_module_d_vuefront->pushEvent("remove_cart",  array( "cart" => $args, "customer_id" => $this->customer->getId(), "guest" => $this->customer->isLogged() ? false : true));

        return $this->get(array());
    }
}
