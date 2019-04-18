<?php

class ControllerExtensionDVuefrontAccount extends Controller
{
    private $codename = "d_vuefront";
    private $sub_versions = array('lite', 'light', 'free');
    private $config_file = '';
    private $setting = array();

    public function register($args)
    {
        $this->load->model('account/customer');
        $customer_info = $args['customer'];
        $customerData = array(
            'firstname' => $customer_info['firstName'],
            'lastname' => $customer_info['lastName'],
            'email' => $customer_info['email'],
            'telephone' => '',
            'password' => $customer_info['password'],
        );

        $customer_id = $this->model_account_customer->addCustomer($customerData);

        $customer_info = $this->model_account_customer->getCustomer($customer_id);

        $this->customer->login($customer_info['email'], $customer_info['password']);

        unset($this->session->data['guest']);

        return array(
            'id' => $customer_info['customer_id'],
            'firstName' => $customer_info['firstname'],
            'lastName' => $customer_info['lastname'],
            'email' => $customer_info['email'],
        );
    }

    public function login($args)
    {
        $this->load->model('account/customer');
        if ($this->customer->login($args['email'], $args['password'])) {
            $customer_info = $this->model_account_customer->getCustomer($this->customer->getId());

            return array(
                'id' => $customer_info['customer_id'],
                'firstName' => $customer_info['firstname'],
                'lastName' => $customer_info['lastname'],
                'email' => $customer_info['email'],
            );
        }
    }

    public function isLogged()
    {
        $this->load->model('account/customer');
        $customer_info = array();
        $customer = array();
        if ($this->customer->isLogged()) {
            $customer_info = $this->model_account_customer->getCustomer($this->customer->getId());
            $customer = array(
                'id' => $customer_info['customer_id'],
                'firstName' => $customer_info['firstname'],
                'lastName' => $customer_info['lastname'],
                'email' => $customer_info['email'],
            );
        }
        
        $logged = $this->customer->isLogged();

        return array(
            'status' => !empty($logged),
            'customer' => $customer
        );
    }

    public function logout()
    {
        $this->customer->logout();

        $logged = $this->customer->isLogged();

        return array(
            'status' => !empty($logged)
        );
    }
}
