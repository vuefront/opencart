<?php

class ControllerDVuefrontStoreCheckout extends Controller {
    public function link() {
        return array(
            'link' => $this->url->link('checkout/checkout')
        );
    }
}