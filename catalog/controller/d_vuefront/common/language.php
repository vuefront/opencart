<?php

class ControllerDVuefrontCommonLanguage extends Controller
{
    private $codename = "d_vuefront";

    public function get()
    {
        $this->load->model('localisation/language');

        $languages = array();

        $results = $this->model_localisation_language->getLanguages();

        $siteUrl = $this->request->server['HTTPS'] ? $this->config->get('config_ssl') : $this->config->get('config_url');

        foreach ($results as $result) {
            if ($result['status']) {
                $code = $result['code'];

                if(VERSION < '2.2.0.0') {
                    $code = $code == 'en' ? 'en-gb' : $code;
                    $code = $code == 'ru' ? 'ru-ru' : $code;
                }
                $languages[] = array(
                    'name'        => $result['name'],
                    'code'         => $code,
                    'image' => $siteUrl."catalog/language/".$result['code']."/".$result['code'].".png",
                    'active'  => $this->session->data['language'] == $result['code']
                );
            }
        }

        return $languages;
    }

    public function edit($args)
    {
        $this->session->data['language'] = $args['code'];

        return $this->get();
    }
}
