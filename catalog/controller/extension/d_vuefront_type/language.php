<?php

class ControllerExtensionDVuefrontTypeLanguage extends Controller
{
    private $codename = "d_vuefront";

    public function resolver() {
        return array(
            'language' => function() {
                return $this->load->controller('extension/' . $this->codename . '/language/language');
            },
            'editLanguage' => function($root, $args) {
                return $this->load->controller('extension/'.$this->codename.'/language/edit', $args);
            }
        );
    }
}
