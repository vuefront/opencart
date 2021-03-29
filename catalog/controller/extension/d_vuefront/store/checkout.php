<?php

class ControllerExtensionDVuefrontStoreCheckout extends Controller
{
    public function link()
    {
        return array(
            'link' => $this->url->link('checkout/checkout')
        );
    }

    public function paymentMethods()
    {
        $this->load->model('extension/module/d_vuefront');

        $response = $this->model_extension_module_d_vuefront->requestCheckout(
            '{
                payments {
                    setting
                    codename
                    status
                    name
              }
            }',
            array()
        );

        $methods = array();
        if ($response) {
            foreach ($response['payments'] as $key => $value) {
                if ($value['status']) {
                    $methods[] = array(
                        'id' => $value['codename'],
                        'codename' => $value['codename'],
                        "name" => $value['name']
                    );
                }
            }
        }

        return $methods;
    }

    public function shippingMethods($args)
    {
        $method_data = array();

        $this->load->model('setting/extension');

        $results = $this->model_setting_extension->getExtensions('shipping');

        foreach ($results as $result) {
            if ($this->config->get('shipping_' . $result['code'] . '_status')) {
                $this->load->model('extension/shipping/' . $result['code']);

                $quote = $this->{'model_extension_shipping_' . $result['code']}->getQuote($this->session->data['shipping_address']);

                if ($quote) {
                    $method_data[$result['code']] = array(
                        'id' => $quote['code'],
                        'name'      => $quote['title'],
                        'codename'      => $quote['code'],
                        'sort_order' => $quote['sort_order'],
                        'quote' => $quote['quote']
                    );
                }
            }
        }

        $sort_order = array();

        foreach ($method_data as $key => $value) {
            $sort_order[$key] = $value['sort_order'];
        }

        array_multisort($sort_order, SORT_ASC, $method_data);

        $result = array();

        foreach ($method_data as $quote) {
            foreach ($quote['quote'] as $value) {
                $result[] = array(
                    'id' => $value['code'],
                    'name'      => $value['title'] . " - " . $value['text'],
                    'codename'      => $value['code']
                );
            }
        }

        return $result;
    }

    public function paymentAddress()
    {
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
            'name' => 'country_id',
            'required' => true
        );

        $fields[] = array(
            'type' => 'zone',
            'name' => 'zone_id',
            'required' => true
        );

        // Custom Fields
        $data['custom_fields'] = array();

        $this->load->model('account/custom_field');

        $custom_fields = $this->model_account_custom_field->getCustomFields($this->config->get('config_customer_group_id'));

        foreach ($custom_fields as $custom_field) {
            if ($custom_field['location'] == 'address') {
                $values = array();
                if (!empty($custom_field['custom_field_value'])) {
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
                    'name' => "vfCustomField-".$custom_field["location"].'-'.$custom_field["custom_field_id"],
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

    public function shippingAddress()
    {
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
            'name' => 'country_id',
            'required' => true
        );

        $fields[] = array(
          'type' => 'zone',
          'name' => 'zone_id',
          'required' => true
        );

        $this->load->model('account/custom_field');

        $custom_fields = $this->model_account_custom_field->getCustomFields($this->config->get('config_customer_group_id'));

        foreach ($custom_fields as $custom_field) {
            if ($custom_field['location'] == 'address') {
                $values = array();
                if (!empty($custom_field['custom_field_value'])) {
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
                    'name' => "vfCustomField-".$custom_field["location"].'-'.$custom_field["custom_field_id"],
                    'required' => $custom_field['required'],
                    'values' => $values
                );
            }
        }


        return $fields;
    }

    public function createOrder($args)
    {
        $this->session->data['shipping_address'] = array();

        $this->session->data['shipping_address_id'] = '';

        foreach ($this->shippingAddress() as $value) {
            $this->session->data['shipping_address'][$value['name']] = '';
        }

        $this->session->data['shipping_address_id'] = '';

        $this->session->data['payment_address'] = array(
            'custom_field' => array()
        );

        $paymentAddress = $this->paymentAddress();
        foreach ($paymentAddress['fields'] as $value) {
            $this->session->data['payment_address'][$value['name']] = '';
        }

        $this->session->data['payment_method'] = null;
        $this->session->data['shipping_method'] = null;
        return array('success'=> 'success');
    }

    public function updateOrder($args)
    {
        foreach ($args['paymentAddress'] as $value) {
            if (strpos($value['name'], "vfCustomField-") !== false) {
                if ($value['value']) {
                    $field_name = str_replace("vfCustomField-", "", $value['name']);
                    $field_name = explode('-', $field_name);
                    if (!isset($this->session->data['payment_address']['custom_field'][$field_name[0]])) {
                        $this->session->data['payment_address']['custom_field'][$field_name[0]] = array();
                    }
                    $this->session->data['payment_address']['custom_field'][$field_name[0]][$field_name[1]] = $value['value'];
                }
            } else {
                if ($value['value']) {
                    $this->session->data['payment_address'][$value['name']] = $value['value'];
                }
            }
        }

        foreach ($args['shippingAddress'] as $value) {
            if (strpos($value['name'], "vfCustomField-") !== false) {
                if ($value['value']) {
                    $field_name = str_replace("vfCustomField-", "", $value['name']);
                    $field_name = explode('-', $field_name);
                    if (!isset($this->session->data['shipping_address']['custom_field'][$field_name[0]])) {
                        $this->session->data['shipping_address']['custom_field'][$field_name[0]] = array();
                    }
                    $this->session->data['shipping_address']['custom_field'][$field_name[0]][$field_name[1]] = $value['value'];
                }
            } else {
                if ($value['value']) {
                    $this->session->data['shipping_address'][$value['name']] = $value['value'];
                }
            }
        }

        if (!empty($args['shippingAddressId'])) {
            $this->session->data['shipping_address_id'] = $args['shippingAddressId'];
        } else {
            $this->session->data['shipping_address_id'] = "";
        }
        if (!empty($args['paymentAddressId'])) {
            $this->session->data['payment_address_id'] = $args['paymentAddressId'];
        } else {
            $this->session->data['payment_address_id'] = "";
        }

        if (!empty($args['shippingMethod'])) {
            $shipping = explode('.', $args['shippingMethod']);

            $this->load->model('extension/shipping/'.$shipping[0]);

            $quote = $this->{'model_extension_shipping_' . $shipping[0]}->getQuote($this->session->data['shipping_address']);
            if ($quote) {
                $this->session->data['shipping_method'] = $quote['quote'][$shipping[1]];
            }
        }

        $this->session->data['payment_method'] = $args['paymentMethod'];

        return array(
            'paymentMethods' => $this->vfload->resolver('store/checkout/paymentMethods'),
            'shippingMethods' => $this->vfload->resolver('store/checkout/shippingMethods'),
            'totals' => $this->vfload->resolver('store/checkout/totals'),
        );
    }

    public function orderData()
    {
        $this->load->model('extension/module/d_vuefront');


        var_dump($this->session->data);

        $shippingAddress = $this->session->data['shipping_address'];
        $paymentAddress = $this->session->data['payment_address'];
        $shippingAddressId = $this->session->data['shipping_address_id'];
        $paymentAddressId = $this->session->data['payment_address_id'];

        $shippingMethod = $this->session->data['shipping_method'];

        $order_data = array();

        if($paymentAddressId !== "") {
            $paymentAddress = $this->model_account_address->getAddress($paymentAddressId);

            $paymentAddress['firstName'] = $paymentAddress['firstname'];
            $paymentAddress['lastName'] = $paymentAddress['lastname'];
            $paymentAddress['address1'] = $paymentAddress['address_1'];
            $paymentAddress['address2'] = $paymentAddress['address_2'];
            if ($this->customer->isLogged()) {
              $paymentAddress['email'] = $this->customer->getEmail();
          }
        }

        if($shippingAddressId !== "") {
            $shippingAddress = $this->model_account_address->getAddress($shippingAddressId);
            $shippingAddress['firstName'] = $shippingAddress['firstname'];
            $shippingAddress['lastName'] = $shippingAddress['lastname'];
            $shippingAddress['address1'] = $shippingAddress['address_1'];
            $shippingAddress['address2'] = $shippingAddress['address_2'];
            if ($this->customer->isLogged()) {
              $shippingAddress['email'] = $this->customer->getEmail();
          }
        }

        $order_data['customer_id'] = "0";
        $order_data['customer_group_id'] = '';
        $order_data['firstname'] = $paymentAddress['firstName'];
        $order_data['lastname'] = $paymentAddress['lastName'];
        $order_data['email'] = $paymentAddress['email'];
        $order_data['telephone'] = '';
        $order_data['custom_field'] = array();

        if ($this->customer->isLogged()) {
            $order_data['customer_id'] = $this->customer->getId();
            $order_data['customer_email'] = $this->customer->getEmail();
        }

        $order_data['payment_custom_field'] = (isset($paymentAddress['custom_field']) ? $paymentAddress['custom_field'] : array());

        return $order_data;
    }

    public function confirmOrder()
    {
        $this->load->model('extension/module/d_vuefront');

        $response = $this->model_extension_module_d_vuefront->requestCheckout(
            'query($codename: String){
                payment(codename: $codename) {
                    codename
                    name
                }
            }',
            array(
                'codename' =>  $this->session->data['payment_method']
            )
        );
        $paymentMethod = $response['payment'];

        $shippingAddress = $this->session->data['shipping_address'];
        $paymentAddress = $this->session->data['payment_address'];
        $shippingAddressId = $this->session->data['shipping_address_id'];
        $paymentAddressId = $this->session->data['payment_address_id'];

        $shippingMethod = $this->session->data['shipping_method'];


        $order_data = array();

        $totals = array();
        $taxes = $this->cart->getTaxes();
        $total = 0;

        // Because __call can not keep var references so we put them into an array.
        $total_data = array(
            'totals' => &$totals,
            'taxes'  => &$taxes,
            'total'  => &$total
        );

        $this->load->model('setting/extension');

        $sort_order = array();

        $results = $this->model_setting_extension->getExtensions('total');

        foreach ($results as $key => $value) {
            $sort_order[$key] = $this->config->get('total_' . $value['code'] . '_sort_order');
        }

        array_multisort($sort_order, SORT_ASC, $results);

        foreach ($results as $result) {
            if ($this->config->get('total_' . $result['code'] . '_status')) {
                $this->load->model('extension/total/' . $result['code']);

                $this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
            }
        }

        $sort_order = array();

        foreach ($totals as $key => $value) {
            $sort_order[$key] = $value['sort_order'];
        }

        array_multisort($sort_order, SORT_ASC, $totals);

        $order_data['totals'] = $totals;

        $this->load->language('checkout/checkout');

        $order_data['invoice_prefix'] = $this->config->get('config_invoice_prefix');
        $order_data['store_id'] = $this->config->get('config_store_id');
        $order_data['store_name'] = $this->config->get('config_name');

        if ($order_data['store_id']) {
            $order_data['store_url'] = $this->config->get('config_url');
        } else {
            if ($this->request->server['HTTPS']) {
                $order_data['store_url'] = HTTPS_SERVER;
            } else {
                $order_data['store_url'] = HTTP_SERVER;
            }
        }

        $this->load->model('account/customer');
        $this->load->model('account/address');

        if($paymentAddressId !== "") {
            $paymentAddress = $this->model_account_address->getAddress($paymentAddressId);

            $paymentAddress['firstName'] = $paymentAddress['firstname'];
            $paymentAddress['lastName'] = $paymentAddress['lastname'];
            $paymentAddress['address1'] = $paymentAddress['address_1'];
            $paymentAddress['address2'] = $paymentAddress['address_2'];
            if ($this->customer->isLogged()) {
              $paymentAddress['email'] = $this->customer->getEmail();
          }
        }

        if($shippingAddressId !== "") {
            $shippingAddress = $this->model_account_address->getAddress($shippingAddressId);
            $shippingAddress['firstName'] = $shippingAddress['firstname'];
            $shippingAddress['lastName'] = $shippingAddress['lastname'];
            $shippingAddress['address1'] = $shippingAddress['address_1'];
            $shippingAddress['address2'] = $shippingAddress['address_2'];
            if ($this->customer->isLogged()) {
              $shippingAddress['email'] = $this->customer->getEmail();
          }
        }

        $order_data['customer_id'] = "0";
        $order_data['customer_group_id'] = '';
        $order_data['firstname'] = $paymentAddress['firstName'];
        $order_data['lastname'] = $paymentAddress['lastName'];
        $order_data['email'] = $paymentAddress['email'];
        $order_data['telephone'] = '';
        $order_data['custom_field'] = array();

        if ($this->customer->isLogged()) {
            $order_data['customer_id'] = $this->customer->getId();
            $order_data['customer_email'] = $this->customer->getEmail();
        }

        $this->load->model('localisation/country');
        $this->load->model('localisation/zone');

        $country_payment = $this->model_localisation_country->getCountry($paymentAddress['country_id']);
        $zone_payment = $this->model_localisation_zone->getZone($paymentAddress['zone_id']);

        $order_data['payment_firstname'] = $paymentAddress['firstName'];
        $order_data['payment_lastname'] = $paymentAddress['lastName'];
        $order_data['payment_company'] = $paymentAddress['company'];
        $order_data['payment_address_1'] = $paymentAddress['address1'];
        $order_data['payment_address_2'] = $paymentAddress['address2'];
        $order_data['payment_city'] = $paymentAddress['city'];
        $order_data['payment_postcode'] = $paymentAddress['postcode'];
        $order_data['payment_zone'] = $zone_payment['name'];
        $order_data['payment_zone_id'] = $paymentAddress['zone_id'];
        $order_data['payment_country'] = $country_payment['name'];
        $order_data['payment_country_id'] = $paymentAddress['country_id'];
        $order_data['payment_address_format'] = (isset($paymentAddress['address_format']) ? $paymentAddress['address_format'] : '');
        $order_data['payment_custom_field'] = (isset($paymentAddress['custom_field']) ? $paymentAddress['custom_field'] : array());

        $order_data['payment_method'] = $paymentMethod['name'];
        $order_data['payment_code'] = $paymentMethod['codename'];

        $country_shipping = $this->model_localisation_country->getCountry($shippingAddress['country_id']);
        $zone_shipping = $this->model_localisation_zone->getZone($shippingAddress['zone_id']);

        if ($this->cart->hasShipping()) {
            $order_data['shipping_firstname'] = $shippingAddress['firstName'];
            $order_data['shipping_lastname'] = $shippingAddress['lastName'];
            $order_data['shipping_company'] = $shippingAddress['company'];
            $order_data['shipping_address_1'] = $shippingAddress['address1'];
            $order_data['shipping_address_2'] = $shippingAddress['address2'];
            $order_data['shipping_city'] = $shippingAddress['city'];
            $order_data['shipping_postcode'] = $shippingAddress['postcode'];
            $order_data['shipping_zone'] = $zone_shipping['name'];
            $order_data['shipping_zone_id'] = $shippingAddress['zone_id'];
            $order_data['shipping_country'] = $country_shipping['name'];
            $order_data['shipping_country_id'] = $shippingAddress['country_id'];
            $order_data['shipping_address_format'] = (isset($shippingAddress['address_format']) ? $shippingAddress['address_format'] : '');
            $order_data['shipping_custom_field'] = (isset($shippingAddress['custom_field']) ? $shippingAddress['custom_field'] : array());

            if (isset($this->session->data['shipping_method']['title'])) {
                $order_data['shipping_method'] = $this->session->data['shipping_method']['title'];
            } else {
                $order_data['shipping_method'] = '';
            }

            if (isset($this->session->data['shipping_method']['code'])) {
                $order_data['shipping_code'] = $this->session->data['shipping_method']['code'];
            } else {
                $order_data['shipping_code'] = '';
            }
        } else {
            $order_data['shipping_firstname'] = '';
            $order_data['shipping_lastname'] = '';
            $order_data['shipping_company'] = '';
            $order_data['shipping_address_1'] = '';
            $order_data['shipping_address_2'] = '';
            $order_data['shipping_city'] = '';
            $order_data['shipping_postcode'] = '';
            $order_data['shipping_zone'] = '';
            $order_data['shipping_zone_id'] = '';
            $order_data['shipping_country'] = '';
            $order_data['shipping_country_id'] = '';
            $order_data['shipping_address_format'] = '';
            $order_data['shipping_custom_field'] = array();
            $order_data['shipping_method'] = '';
            $order_data['shipping_code'] = '';
        }

        $order_data['products'] = array();

        foreach ($this->cart->getProducts() as $product) {
            $option_data = array();

            foreach ($product['option'] as $option) {
                $option_data[] = array(
                    'product_option_id'       => $option['product_option_id'],
                    'product_option_value_id' => $option['product_option_value_id'],
                    'option_id'               => $option['option_id'],
                    'option_value_id'         => $option['option_value_id'],
                    'name'                    => $option['name'],
                    'value'                   => $option['value'],
                    'type'                    => $option['type']
                );
            }

            $order_data['products'][] = array(
                'product_id' => $product['product_id'],
                'name'       => $product['name'],
                'model'      => $product['model'],
                'option'     => $option_data,
                'download'   => $product['download'],
                'quantity'   => $product['quantity'],
                'subtract'   => $product['subtract'],
                'price'      => $product['price'],
                'total'      => $product['total'],
                'tax'        => $this->tax->getTax($product['price'], $product['tax_class_id']),
                'reward'     => $product['reward']
            );
        }

        // Gift Voucher
        $order_data['vouchers'] = array();

        if (!empty($this->session->data['vouchers'])) {
            foreach ($this->session->data['vouchers'] as $voucher) {
                $order_data['vouchers'][] = array(
                    'description'      => $voucher['description'],
                    'code'             => token(10),
                    'to_name'          => $voucher['to_name'],
                    'to_email'         => $voucher['to_email'],
                    'from_name'        => $voucher['from_name'],
                    'from_email'       => $voucher['from_email'],
                    'voucher_theme_id' => $voucher['voucher_theme_id'],
                    'message'          => $voucher['message'],
                    'amount'           => $voucher['amount']
                );
            }
        }

        $order_data['comment'] = '';
        $order_data['total'] = $total_data['total'];

        if (isset($this->request->cookie['tracking'])) {
            $order_data['tracking'] = $this->request->cookie['tracking'];

            $subtotal = $this->cart->getSubTotal();

            // Affiliate
            $affiliate_info = $this->model_account_customer->getAffiliateByTracking($this->request->cookie['tracking']);

            if ($affiliate_info) {
                $order_data['affiliate_id'] = $affiliate_info['customer_id'];
                $order_data['commission'] = ($subtotal / 100) * $affiliate_info['commission'];
            } else {
                $order_data['affiliate_id'] = 0;
                $order_data['commission'] = 0;
            }

            // Marketing
            $this->load->model('checkout/marketing');

            $marketing_info = $this->model_checkout_marketing->getMarketingByCode($this->request->cookie['tracking']);

            if ($marketing_info) {
                $order_data['marketing_id'] = $marketing_info['marketing_id'];
            } else {
                $order_data['marketing_id'] = 0;
            }
        } else {
            $order_data['affiliate_id'] = 0;
            $order_data['commission'] = 0;
            $order_data['marketing_id'] = 0;
            $order_data['tracking'] = '';
        }

        $order_data['language_id'] = $this->config->get('config_language_id');
        $order_data['currency_id'] = $this->currency->getId($this->session->data['currency']);
        $order_data['currency_code'] = $this->session->data['currency'];
        $order_data['currency_value'] = $this->currency->getValue($this->session->data['currency']);
        $order_data['ip'] = $this->request->server['REMOTE_ADDR'];

        if (!empty($this->request->server['HTTP_X_FORWARDED_FOR'])) {
            $order_data['forwarded_ip'] = $this->request->server['HTTP_X_FORWARDED_FOR'];
        } elseif (!empty($this->request->server['HTTP_CLIENT_IP'])) {
            $order_data['forwarded_ip'] = $this->request->server['HTTP_CLIENT_IP'];
        } else {
            $order_data['forwarded_ip'] = '';
        }

        if (isset($this->request->server['HTTP_USER_AGENT'])) {
            $order_data['user_agent'] = $this->request->server['HTTP_USER_AGENT'];
        } else {
            $order_data['user_agent'] = '';
        }

        if (isset($this->request->server['HTTP_ACCEPT_LANGUAGE'])) {
            $order_data['accept_language'] = $this->request->server['HTTP_ACCEPT_LANGUAGE'];
        } else {
            $order_data['accept_language'] = '';
        }

        $this->load->model('checkout/order');

        $this->session->data['order_id'] = $this->model_checkout_order->addOrder($order_data);

        $response = $this->model_extension_module_d_vuefront->requestCheckout(
            'mutation($paymentMethod: String, $total: Float, $callback: String, $customerId: String, $customerEmail: String) {
                createOrder(paymentMethod: $paymentMethod, total: $total, callback: $callback, customerId: $customerId, customerEmail: $customerEmail) {
                    url
                }
            }',
            array(
                'paymentMethod' => $paymentMethod['codename'],
                'customerId' => $order_data['customer_id'],
                'customerEmail' => $order_data['customer_email'],
                'total' => floatval($total_data['total']),
                'callback' => $this->url->link('extension/d_vuefront/store/checkout/callback', 'order_id='.$this->session->data['order_id'], true)
            )
        );

        return array(
            'url' => $response['createOrder']['url'],
            'order' => array(
                'id' => $this->session->data['order_id']
            )
        );
    }

    public function callback()
    {
        $this->load->model('checkout/order');

        $rawInput = file_get_contents('php://input');

        $input = json_decode($rawInput, true);
        if ($input['status'] == 'COMPLETE') {
            $order_status_id = $this->config->get('config_order_status_id');

            $this->model_checkout_order->addOrderHistory($this->request->get['order_id'], $order_status_id);
        }
    }

    public function totals()
    {
        $totals = array();
        $taxes = $this->cart->getTaxes();
        $total = 0;

        // Because __call can not keep var references so we put them into an array.
        $total_data = array(
            'totals' => &$totals,
            'taxes'  => &$taxes,
            'total'  => &$total
        );

        $this->load->model('setting/extension');

        $sort_order = array();

        $results = $this->model_setting_extension->getExtensions('total');

        foreach ($results as $key => $value) {
            $sort_order[$key] = $this->config->get('total_' . $value['code'] . '_sort_order');
        }

        array_multisort($sort_order, SORT_ASC, $results);

        foreach ($results as $result) {
            if ($this->config->get('total_' . $result['code'] . '_status')) {
                $this->load->model('extension/total/' . $result['code']);
                // We have to put the totals in an array so that they pass by reference.
                $this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
            }
        }

        $sort_order = array();

        foreach ($totals as $key => $value) {
            $sort_order[$key] = $value['sort_order'];
        }

        array_multisort($sort_order, SORT_ASC, $totals);

        $result = array();

        foreach ($totals as $total) {
            $result[] = array(
                'title' => $total['title'],
                'text'  => $this->currency->format($total['value'], $this->session->data['currency'])
            );
        }

        return $result;
    }
}
