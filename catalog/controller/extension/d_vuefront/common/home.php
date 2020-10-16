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

    public function searchUrl($args) {
        $this->load->model('extension/d_vuefront/seo');

        $result = $this->model_extension_d_vuefront_seo->searchKeyword($args['url']);


        return $result;
    }
}
