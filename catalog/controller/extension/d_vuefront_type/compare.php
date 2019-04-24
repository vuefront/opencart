<?php

class ControllerExtensionDVuefrontTypeCompare extends Controller
{
    private $codename = "d_vuefront";

    public function resolver() {
        return array(
            'compare' => function() {
                return $this->load->controller('extension/' . $this->codename . '/compare/compare');
            },
            'addToCompare'  => function($root, $args) {
                return $this->load->controller('extension/'.$this->codename.'/compare/addToCompare', $args);
            },
            'removeCompare'  => function($root, $args) {
                return $this->load->controller('extension/'.$this->codename.'/compare/removeCompare', $args);
            },
        );
    }
}
