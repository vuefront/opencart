<?php

/**
 * location: admin/controller
 */
class ControllerExtensionModuleDVuefront extends Controller
{
    private $codename = 'd_vuefront';
    private $route = 'extension/module/d_vuefront';
    private $error = array();

    public function __construct($registry)
    {
        parent::__construct($registry);

        $this->load->language($this->route);
        $this->load->model('extension/d_opencart_patch/url');
        $this->load->model('extension/d_opencart_patch/load');

        $this->d_shopunity = (file_exists(DIR_SYSTEM . 'library/d_shopunity/extension/d_shopunity.json'));
        $this->d_blog_module = (file_exists(DIR_SYSTEM . 'library/d_shopunity/extension/d_blog_module.json'));
        $this->extension = json_decode(file_get_contents(DIR_SYSTEM . 'library/d_shopunity/extension/d_vuefront.json'), true);
        $this->d_admin_style = (file_exists(DIR_SYSTEM . 'library/d_shopunity/extension/d_admin_style.json'));
    }

    public function index()
    {
        if ($this->d_shopunity) {
            $this->load->model('extension/d_shopunity/mbooth');
            $this->model_extension_d_shopunity_mbooth->validateDependencies('d_vuefront');
        }

        if ($this->d_twig_manager) {
            $this->load->model('extension/module/d_twig_manager');
            $this->model_extension_module_d_twig_manager->installCompatibility();
        }

        if ($this->d_admin_style) {
            $this->load->model('extension/d_admin_style/style');
            $this->model_extension_d_admin_style_style->getStyles('light');
        }

        $this->document->addScript('view/javascript/d_vuefront/clipboard/clipboard.min.js');

        $url_params = array();
        $url = '';

        $url = ((!empty($url_params)) ? '&' : '') . http_build_query($url_params);

        $this->document->setTitle($this->language->get('heading_title_main'));
        $data['heading_title'] = $this->language->get('heading_title_main');
        $data['d_blog_module'] = $this->d_blog_module;

        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_copy'] = $this->language->get('text_copy');
        $data['text_title'] = $this->language->get('text_title');
        $data['text_description'] = $this->language->get('text_description');

        $data['text_blog_module'] = $this->language->get('text_blog_module');
        $data['text_blog_enabled'] = $this->language->get('text_blog_enabled');
        $data['text_blog_disabled'] = $this->language->get('text_blog_disabled');
        $data['text_blog_description'] = $this->language->get('text_blog_description');

        // Button
        $data['button_cancel'] = $this->language->get('button_cancel');

        // Variable
        $data['version'] = $this->extension['version'];

        //support
        $data['text_powered_by'] = $this->language->get('text_powered_by');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        // Breadcrumbs
        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->model_extension_d_opencart_patch_url->link('common/dashboard'),
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_module'),
            'href' => $this->model_extension_d_opencart_patch_url->getExtensionLink('module'),
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title_main'),
            'href' => $this->model_extension_d_opencart_patch_url->link($this->route, $url),
        );

        if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
            $data['catalog'] = HTTPS_CATALOG . 'index.php?route=extension/module/d_vuefront/graphql';
        } else {
            $data['catalog'] = HTTP_CATALOG . 'index.php?route=extension/module/d_vuefront/graphql';
        }

        //action
        $data['cancel'] = $this->model_extension_d_opencart_patch_url->getExtensionLink('module');

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->model_extension_d_opencart_patch_load->view($this->route, $data));
    }

    public function install()
    {
        if ($this->d_shopunity) {
            $this->load->model('extension/d_shopunity/mbooth');
            $this->model_extension_d_shopunity_mbooth->installDependencies('d_vuefront');
        }
    }
}
