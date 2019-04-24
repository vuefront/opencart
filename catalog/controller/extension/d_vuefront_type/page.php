<?php

class ControllerExtensionDVuefrontTypePage extends Controller
{
    private $codename = "d_vuefront";

    public function resolver() {
        return array(
            'page' => function($root, $args) {
                return $this->load->controller('extension/' . $this->codename . '/page/page', $args);
            },
            'pagesList' => function($root, $args) {
                return $this->load->controller('extension/' . $this->codename . '/page/pageList', $args);
            }
        );
    }
}