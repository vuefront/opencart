<?php

class ControllerExtensionDVuefrontTypeAccount extends Controller
{
    private $codename = "d_vuefront";

    public function resolver() {
        return array(
            'accountLogin' => function($root, $args) {
                return $this->load->controller('extension/'.$this->codename.'/account/login', $args);
            },
            'accountLogout'  => function($root, $args) {
                return $this->load->controller('extension/'.$this->codename.'/account/logout', $args);
            },
            'accountRegister' => function ($store, $args) {
                return $this->load->controller('extension/'.$this->codename.'/account/register', $args);
            },
            'accountEdit' => function ($store, $args) {
                return $this->load->controller('extension/'.$this->codename.'/account/edit', $args);
            },
            'accountEditPassword' => function ($store, $args) {
                return $this->load->controller('extension/'.$this->codename.'/account/editPassword', $args);
            },
            'accountCheckLogged' => function ($store, $args) {
                return $this->load->controller('extension/'.$this->codename.'/account/isLogged', $args);
            }
        );
    }
}
