<?php

class ControllerExtensionDVuefrontCommonHome extends Controller {
    public function get() {
        return array(
            'meta' => array(
                'title' => $this->config->get('config_meta_title'),
                'description' => $this->config->get('config_meta_description'),
                'keyword' => $this->config->get('config_meta_keyword')
            )
        );
    }
}