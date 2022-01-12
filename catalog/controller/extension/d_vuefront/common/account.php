<?php

class ControllerExtensionDVuefrontCommonAccount extends Controller
{
    private $codename = 'd_vuefront';

    public function customerList($args)
    {
        $this->load->model('extension/'.$this->codename.'/customer');

        $filter_data = [
            'start' => ($args['page'] - 1) * $args['size'],
            'limit' => $args['size'],
            'sort' => $args['sort'],
            'order' => $args['order'],
        ];

        if (!empty($args['search'])) {
            $filter_data['filter_name'] = $args['search'];
        }

        $results = $this->model_extension_d_vuefront_customer->getCustomers($filter_data);
        $customer_total = $this->model_extension_d_vuefront_customer->getTotalCustomers($filter_data);

        $customers = [];

        foreach ($results as $result) {
            $customers[] = $this->getCustomer(['id' => $result['customer_id']]);
        }

        return [
            'content' => $customers,
            'first' => $args['page'] === 1,
            'last' => $args['page'] === ceil($customer_total / $args['size']),
            'number' => (int) $args['page'],
            'numberOfElements' => count($customers),
            'size' => (int) $args['size'],
            'totalPages' => (int) ceil($customer_total / $args['size']),
            'totalElements' => (int) $customer_total,
        ];
    }

    public function getCustomer($args)
    {
        $this->load->model('account/customer');

        $customer_info = $this->model_account_customer->getCustomer($args['id']);

        if (!$customer_info) {
            return [];
        }

        return [
            'id' => $customer_info['customer_id'],
            'firstName' => $customer_info['firstname'],
            'lastName' => $customer_info['lastname'],
            'email' => $customer_info['email'],
            'phone' => $customer_info['telephone'],
        ];
    }

    public function register($args)
    {
        $this->load->model('account/customer');
        $this->load->language('account/register');
        $customer_info = $args['customer'];

        if ($this->model_account_customer->getTotalCustomersByEmail($customer_info['email'])) {
            throw new Exception($this->language->get('error_exists'));
        }
        $customerData = [
            'firstname' => $customer_info['firstName'],
            'lastname' => $customer_info['lastName'],
            'email' => $customer_info['email'],
            'phone' => $customer_info['phone'],
            'password' => $customer_info['password'],
        ];

        if (VERSION < '3.0.0.0') {
            $customerData['fax'] = '';
            $customerData['company'] = '';
            $customerData['address_1'] = '';
            $customerData['address_2'] = '';
            $customerData['city'] = '';
            $customerData['postcode'] = '';
            $customerData['country_id'] = $this->config->get('config_country_id');
            $customerData['zone_id'] = $this->config->get('config_zone_id');
        }

        $customer_id = $this->model_account_customer->addCustomer($customerData);

        $customer_info = $this->model_account_customer->getCustomer($customer_id);

        $this->load->model('extension/module/d_vuefront');

        $this->model_extension_module_d_vuefront->pushEvent('create_customer', $customer_info);

        $this->customer->login($customer_info['email'], $customer_info['password']);

        unset($this->session->data['guest']);

        return [
            'id' => $customer_info['customer_id'],
            'firstName' => $customer_info['firstname'],
            'lastName' => $customer_info['lastname'],
            'email' => $customer_info['email'],
            'phone' => $customer_info['telephone'],
        ];
    }

    public function login($args)
    {
        $this->load->language('account/login');
        $this->load->model('account/customer');
        if ($this->customer->login($args['email'], $args['password'])) {
            $customer_info = $this->model_account_customer->getCustomer($this->customer->getId());

            $this->load->model('extension/module/d_vuefront');

            $this->model_extension_module_d_vuefront->pushEvent('login_customer', $customer_info);

            return [
                'token' => null,
                'customer' => [
                    'id' => $customer_info['customer_id'],
                    'firstName' => $customer_info['firstname'],
                    'lastName' => $customer_info['lastname'],
                    'email' => $customer_info['email'],
                    'phone' => $customer_info['telephone'],
                ],
            ];
        } else {
            throw new Exception($this->language->get('error_login'));
        }
    }

    public function logout()
    {
        $this->load->model('extension/module/d_vuefront');

        $this->model_extension_module_d_vuefront->pushEvent('logout_customer', ['customer_id' => $this->customer->getId()]);

        $this->customer->logout();

        $logged = $this->customer->isLogged();

        return [
            'status' => !empty($logged),
        ];
    }

    public function edit($args)
    {
        $this->load->model('account/customer');
        $customer_info = $args['customer'];
        $customerData = [
            'firstname' => $customer_info['firstName'],
            'lastname' => $customer_info['lastName'],
            'email' => $customer_info['email'],
            'telephone' => $customer_info['phone'],
        ];

        if (VERSION < '3.0.0.0') {
            $customerData['fax'] = '';
        }

        if (VERSION > '3.0.0.0') {
            $this->model_account_customer->editCustomer($this->customer->getId(), $customerData);
        } else {
            $this->model_account_customer->editCustomer($customerData);
        }

        $customer_info = $this->model_account_customer->getCustomer($this->customer->getId());

        return [
            'id' => $customer_info['customer_id'],
            'firstName' => $customer_info['firstname'],
            'lastName' => $customer_info['lastname'],
            'email' => $customer_info['email'],
            'phone' => $customer_info['telephone'],
        ];
    }

    public function editPassword($args)
    {
        $this->load->model('account/customer');

        $this->model_account_customer->editPassword($this->customer->getEmail(), $args['password']);

        $customer_info = $this->model_account_customer->getCustomer($this->customer->getId());

        return [
            'id' => $customer_info['customer_id'],
            'firstName' => $customer_info['firstname'],
            'lastName' => $customer_info['lastname'],
            'email' => $customer_info['email'],
            'phone' => $customer_info['telephone'],
        ];
    }

    public function isLogged()
    {
        $this->load->model('account/customer');
        $customer_info = [];
        $customer = [];
        if ($this->customer->isLogged()) {
            $customer_info = $this->model_account_customer->getCustomer($this->customer->getId());
            $customer = [
                'id' => $customer_info['customer_id'],
                'firstName' => $customer_info['firstname'],
                'lastName' => $customer_info['lastname'],
                'email' => $customer_info['email'],
                'phone' => $customer_info['telephone'],
            ];
        }

        $logged = $this->customer->isLogged();

        return [
            'status' => !empty($logged),
            'customer' => $customer,
        ];
    }

    public function addressList()
    {
        $this->load->model('account/address');

        $results = $this->model_account_address->getAddresses();
        $addresses = [];
        foreach ($results as $result) {
            $addresses[] = $this->address(['id' => $result['address_id']]);
        }

        return $addresses;
    }

    public function address($args)
    {
        $this->load->model('account/address');

        $result = $this->model_account_address->getAddress($args['id']);

        return [
            'id' => $result['address_id'],
            'firstName' => $result['firstname'],
            'lastName' => $result['lastname'],
            'company' => $result['company'],
            'zoneId' => $result['zone_id'],
            'zone' => $this->vfload->resolver('common/account/zone'),
            'countryId' => $result['country_id'],
            'country' => $this->vfload->resolver('common/account/country'),
            'address1' => $result['address_1'],
            'address2' => $result['address_2'],
            'city' => $result['city'],
            'zipcode' => $result['postcode'],
        ];
    }

    public function country($args)
    {
        return $this->vfload->data('common/country/get', ['id' => $args['parent']['countryId']]);
    }

    public function zone($args)
    {
        return $this->vfload->data('common/zone/get', ['id' => $args['parent']['zoneId']]);
    }

    public function addAddress($args)
    {
        $this->load->model('extension/d_vuefront/address');

        $address_id = $this->model_extension_d_vuefront_address->addAddress($this->customer->getId(), $args['address']);

        return $this->address(['id' => $address_id]);
    }

    public function editAddress($args)
    {
        $this->load->model('extension/d_vuefront/address');

        $this->model_extension_d_vuefront_address->editAddress($args['id'], $args['address']);

        return $this->address(['id' => $args['id']]);
    }

    public function removeAddress($args)
    {
        $this->load->model('extension/d_vuefront/address');

        $this->model_extension_d_vuefront_address->deleteAddress($args['id']);

        return $this->addressList($args);
    }
}
