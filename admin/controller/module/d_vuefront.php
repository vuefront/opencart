<?php

/**
* location: admin/controller
*/
class ControllerModuleDVuefront extends Controller
{
    private $codename = 'd_vuefront';
    private $route = 'module/d_vuefront';
    private $error = array();

    public function __construct($registry)
    {
        parent::__construct($registry);

        $this->load->language($this->route);

        $this->extension = json_decode(file_get_contents(DIR_APPLICATION.'mbooth/xml/d_vuefront.xml'), true);
    }

    public function index()
    {

        $url_params = array();
        $url = '';

        $url = ((!empty($url_params)) ? '&' : '') . http_build_query($url_params);

        $this->document->setTitle($this->language->get('heading_title_main'));
        $this->data['heading_title'] = $this->language->get('heading_title_main');

        $this->data['text_edit'] = $this->language->get('text_edit');
        $this->data['text_title'] = $this->language->get('text_title');
        $this->data['text_description'] = $this->language->get('text_description');

        // Button
        $this->data['button_cancel'] = $this->language->get('button_cancel');

        // Variable
        $this->data['version'] = $this->extension['version'];

        //support
        $this->data['text_powered_by'] = $this->language->get('text_powered_by');

        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        // Breadcrumbs
        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => false
            );
    
        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_module'),
            'href' => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
            );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title_main'),
            'href' => $this->url->link($this->route,'token=' . $this->session->data['token'].'&'.$url, 'SSL'),
            'separator' => ' :: '
            );
            
        if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
            $this->data['catalog'] = HTTPS_CATALOG.'index.php?route=module/d_vuefront/graphql';
        } else {
            $this->data['catalog'] = HTTP_CATALOG.'index.php?route=module/d_vuefront/graphql';
        }

        //action
        $this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');

        $this->template = 'module/d_vuefront.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);

        $this->response->setOutput($this->render());
    }
}
