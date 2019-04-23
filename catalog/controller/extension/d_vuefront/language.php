<?php

class ControllerExtensionDVuefrontLanguage extends Controller
{
    private $codename = "d_vuefront";

    public function language()
    {
        $this->load->model('localisation/language');

        $languages = array();

        $results = $this->model_localisation_language->getLanguages();

        $siteUrl = $this->request->server['HTTPS'] ? $this->config->get('config_ssl') : $this->config->get('config_url');

        foreach ($results as $result) {
            if ($result['status']) {
                $languages[] = array(
                    'name'        => $result['name'],
                    'code'         => $result['code'],
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

        return $this->language();
    }
}
