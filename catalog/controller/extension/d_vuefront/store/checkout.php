<?php

class ControllerExtensionDVuefrontStoreCheckout extends Controller {
    public function link() {
        return array(
            'link' => $this->url->link('checkout/checkout')
        );
    }
}