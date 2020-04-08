<?php

class ControllerExtensionDVuefrontStoreCheckout extends Controller {
    public function link() {
        return array(
            'link' => $this->url->link('checkout/checkout')
        );
    }

    public function paymentMethods() {
        $methods = array();

        $methods[] = array(
            'id' => 'paypal_id',
            'codename' => "paypal",
            "name" => "PayPal"
        );

        $methods[] = array(
            'id' => 'credit_card',
            'codename' => "credit_card",
            "name" => "Crefit Card"
        );

        $methods[] = array(
            'id' => 'free',
            'codename' => "free",
            "name" => "Free"
        );

        return $methods;
    }

    public function shippingMethods() {
        $methods = array();
        
        $methods[] = array(
            'id' => 'shipping_1',
            'codename' => "shipping_1",
            "name" => "Shipping method 1"
        );

        $methods[] = array(
            'id' => 'shipping_2',
            'codename' => "shipping_2",
            "name" => "Shipping method 2"
        );

        $methods[] = array(
            'id' => 'shipping_3',
            'codename' => "shipping_3",
            "name" => "Shipping method 3"
        );

        return $methods;
    }

    public function paymentAddress() {
        $fields = array();

        $fields[] = array(
            'type' => 'text',
            'name' => 'firstName',
            'required' => true
        );
        $fields[] = array(
            'type' => 'text',
            'name' => 'lastName',
            'required' => true
        );

        $fields[] = array(
            'type' => 'text',
            'name' => 'email',
            'required' => true
        );

        $fields[] = array(
            'type' => 'text',
            'name' => 'company',
            'required' => false
        );
        $fields[] = array(
            'type' => 'text',
            'name' => 'address1',
            'required' => true
        );
        $fields[] = array(
            'type' => 'text',
            'name' => 'address2',
            'required' => false
        );
        $fields[] = array(
            'type' => 'text',
            'name' => 'city',
            'required' => true
        );
        $fields[] = array(
            'type' => 'text',
            'name' => 'postcode',
            'required' => true
        );

        $fields[] = array(
            'type' => 'country',
            'name' => 'country',
            'required' => true
        );

        $fields[] = array(
            'type' => 'zone',
            'name' => 'zone',
            'required' => true
        );

        // Custom Fields
		$data['custom_fields'] = array();

		$this->load->model('account/custom_field');

		$custom_fields = $this->model_account_custom_field->getCustomFields($this->config->get('config_customer_group_id'));

		foreach ($custom_fields as $custom_field) {
			if ($custom_field['location'] == 'address') {
                $values = array();
                if(!empty($custom_field['custom_field_value'])) {
                    foreach ($custom_field['custom_field_value'] as $custom_field_value) {
                        $values[] = array(
                            'text' => html_entity_decode($custom_field_value['name'], ENT_QUOTES, 'UTF-8'),
                            'value' => $custom_field_value['custom_field_value_id']
                        );
                    }
                }
                $name = explode(' ', html_entity_decode($custom_field['name'], ENT_QUOTES, 'UTF-8'));
                foreach ($name as $key => $value) {
                    $name[$key] = ucfirst($value);
                }

                $name = implode('', $name);
                $name = str_replace('&', '', $name);
                $fields[] = array(
                    'type' => $custom_field['type'],
                    'label' => html_entity_decode($custom_field['name'], ENT_QUOTES, 'UTF-8'),
                    'name' => $name,
                    'required' => $custom_field['required'],
                    'values' => $values
                );
			}
		}

        $agree = null;

        $this->load->language('checkout/checkout');
        if ($this->config->get('config_checkout_id')) {
			$this->load->model('catalog/information');

			$information_info = $this->model_catalog_information->getInformation($this->config->get('config_checkout_id'));

			if ($information_info) {
				$agree = sprintf($this->language->get('text_agree'), $this->url->link('information/information/agree', 'information_id=' . $this->config->get('config_checkout_id'), true), $information_info['title'], $information_info['title']);
			}
		}
        
        

        return array(
            'fields' => $fields,
            'agree' => $agree
        );
    }

    public function shippingAddress() {
        $fields = array();

        $fields[] = array(
            'type' => 'text',
            'name' => 'firstName',
            'required' => true
        );
        $fields[] = array(
            'type' => 'text',
            'name' => 'lastName',
            'required' => true
        );

        $fields[] = array(
            'type' => 'text',
            'name' => 'company',
            'required' => false
        );
        $fields[] = array(
            'type' => 'text',
            'name' => 'address1',
            'required' => true
        );
        $fields[] = array(
            'type' => 'text',
            'name' => 'address2',
            'required' => false
        );
        $fields[] = array(
            'type' => 'text',
            'name' => 'city',
            'required' => true
        );
        $fields[] = array(
            'type' => 'text',
            'name' => 'postcode',
            'required' => true
        );

        $fields[] = array(
            'type' => 'country',
            'name' => 'country',
            'required' => true
        );


        $fields[] = array(
          'type' => 'zone',
          'name' => 'zone',
          'required' => true
      );

        return $fields;
    }
}
