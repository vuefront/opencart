<?php

class ControllerExtensionDVuefrontTypeCurrency extends Controller
{
    private $codename = "d_vuefront";

    public function resolver() {
        return array(
            'currency' => function() {
                return $this->load->controller('extension/' . $this->codename . '/currency/currency');
            },
            'editCurrency' => function($root, $args) {
                return $this->load->controller('extension/'.$this->codename.'/currency/edit', $args);
            }
        );
    }
}
