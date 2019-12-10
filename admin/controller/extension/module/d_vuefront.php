<?php

/**
 * location: admin/controller
 */
class ControllerExtensionModuleDVuefront extends Controller
{
    private $codename = 'd_vuefront';
    private $route = 'extension/module/d_vuefront';
    private $error = [];

    public function __construct($registry)
    {
        parent::__construct($registry);

        $this->load->language($this->route);
        $this->load->model('extension/d_opencart_patch/url');
        $this->load->model('extension/d_opencart_patch/user');
        $this->load->model('extension/d_opencart_patch/load');

        $this->d_shopunity = (file_exists(DIR_SYSTEM . 'library/d_shopunity/extension/d_shopunity.json'));
        $this->d_blog_module = (file_exists(DIR_SYSTEM . 'library/d_shopunity/extension/d_blog_module.json'));
        $this->d_twig_manager = (file_exists(DIR_SYSTEM . 'library/d_shopunity/extension/d_twig_manager.json'));
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

        $app = json_decode(file_get_contents(DIR_APPLICATION . 'view/javascript/d_vuefront/manifest.json'), true);
        $current_chunk = $app['files'];
        while (!empty($current_chunk)) {
            foreach ($current_chunk['js'] as $value) {
                $this->document->addScript('view/javascript/d_vuefront/' . basename($value));
            }
            foreach ($current_chunk['css'] as $value) {
                $this->document->addStyle('view/javascript/d_vuefront/' . basename($value));
            }
            $current_chunk = $current_chunk['next'];
        }

        $data['baseUrl'] = HTTP_SERVER;

        $url_params = [];
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
        $data['breadcrumbs'] = [];

        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_home'),
            'href' => $this->model_extension_d_opencart_patch_url->link('common/dashboard'),
        ];

        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_module'),
            'href' => $this->model_extension_d_opencart_patch_url->getExtensionLink('module'),
        ];

        $data['breadcrumbs'][] = [
            'text' => $this->language->get('heading_title_main'),
            'href' => $this->model_extension_d_opencart_patch_url->link($this->route, $url),
        ];

        if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
            $data['catalog'] = HTTPS_CATALOG . 'index.php?route=extension/module/d_vuefront/graphql';
        } else {
            $data['catalog'] = HTTP_CATALOG . 'index.php?route=extension/module/d_vuefront/graphql';
        }

        //action
        $data['cancel'] = $this->model_extension_d_opencart_patch_url->getExtensionLink('module');
        $data['tokenUrl'] = $this->model_extension_d_opencart_patch_user->getUrlToken();

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->model_extension_d_opencart_patch_load->view($this->route, $data));
    }

    public function vf_update()
    {
        try {
            $rootFolder = realpath(DIR_APPLICATION . '../');
            $tmpFile = tempnam(sys_get_temp_dir(), 'TMP_');
            rename($tmpFile, $tmpFile .= '.tar');
            file_put_contents($tmpFile, file_get_contents($this->request->post['url']));
            $this->removeDir($rootFolder . '/vuefront');

            $phar = new PharData($tmpFile);
            $phar->extractTo($rootFolder . '/vuefront');

            if (strpos($_SERVER["SERVER_SOFTWARE"], "Apache") !== false) {
                if (file_exists($rootFolder . '/.htaccess')) {
                    $inserting = "# VueFront scripts, styles and images
    RewriteCond %{REQUEST_URI} .*(_nuxt)
    RewriteCond %{REQUEST_URI} !.*vuefront/_nuxt
    RewriteRule ^([^?]*) vuefront/$1
    
    # VueFront pages
    
    # VueFront home page
    RewriteCond %{REQUEST_URI} !.*(images|index.php|.html|admin|.js|.css|.png|.jpeg|.ico|wp-json|wp-admin|checkout)
    RewriteCond %{QUERY_STRING} !.*(rest_route)
    RewriteCond %{DOCUMENT_ROOT}/vuefront/index.html -f
    RewriteRule ^$ vuefront/index.html [L]
    
    RewriteCond %{REQUEST_URI} !.*(images|index.php|.html|admin|.js|.css|.png|.jpeg|.ico|wp-json|wp-admin|checkout)
    RewriteCond %{QUERY_STRING} !.*(rest_route)
    RewriteCond %{DOCUMENT_ROOT}/vuefront/index.html !-f
    RewriteRule ^$ vuefront/200.html [L]
    
    # VueFront page if exists html file
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} !.*(images|index.php|.html|admin|.js|.css|.png|.jpeg|.ico|wp-json|wp-admin|checkout)
    RewriteCond %{QUERY_STRING} !.*(rest_route)
    RewriteCond %{DOCUMENT_ROOT}/vuefront/$1.html -f
    RewriteRule ^([^?]*) vuefront/$1.html [L,QSA]
    
    # VueFront page if not exists html file
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} !.*(images|index.php|.html|admin|.js|.css|.png|.jpeg|.ico|wp-json|wp-admin|checkout)
    RewriteCond %{QUERY_STRING} !.*(rest_route)
    RewriteCond %{DOCUMENT_ROOT}/vuefront/$1.html !-f
    RewriteRule ^([^?]*) vuefront/200.html [L,QSA]";

                    $content = file_get_contents($rootFolder . '/.htaccess');

                    if(!is_dir(DIR_APPLICATION . 'controller/extension/module/d_vuefront')) {
                        mkdir(DIR_APPLICATION . 'controller/extension/module/d_vuefront');
                    }

                    file_put_contents(DIR_APPLICATION . 'controller/extension/module/d_vuefront/.htaccess.txt', $content);

                    preg_match('/# VueFront pages/m', $content, $matches);

                    if (count($matches) == 0) {
                        $content = preg_replace_callback('/RewriteBase\s.*$/m', function ($matches) use ($inserting) {
                            return $matches[0] . PHP_EOL . $inserting . PHP_EOL;
                        }, $content);

                        file_put_contents($rootFolder . '/.htaccess', $content);
                    }
                }
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

        $this->vf_information();
    }

    public function vf_turn_off()
    {
        $rootFolder = realpath(DIR_APPLICATION . '../');
        if (strpos($_SERVER["SERVER_SOFTWARE"], "Apache") !== false) {
            if (file_exists(DIR_APPLICATION . 'controller/extension/module/d_vuefront/.htaccess.txt')) {
                $content = file_get_contents(DIR_APPLICATION . 'controller/extension/module/d_vuefront/.htaccess.txt');
                file_put_contents($rootFolder . '/.htaccess', $content);
                unlink(DIR_APPLICATION . 'controller/extension/module/d_vuefront/.htaccess.txt');
            }
        }
        $this->removeDir($rootFolder . '/vuefront');

        $this->vf_information();
    }

    private function removeDir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir . "/" . $object) && !is_link($dir . "/" . $object)) {
                        $this->removeDir($dir . "/" . $object);
                    } else {
                        unlink($dir . "/" . $object);
                    }
                }
            }
            rmdir($dir);
        }
    }

    public function vf_information()
    {
        $catalog = '';
        if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
            $catalog = HTTPS_CATALOG . 'index.php?route=extension/module/d_vuefront/graphql';
        } else {
            $catalog = HTTP_CATALOG . 'index.php?route=extension/module/d_vuefront/graphql';
        }

        $extensions = [];

        if ($this->d_blog_module) {
            $blog_config = json_decode(file_get_contents(DIR_SYSTEM . 'library/d_shopunity/extension/d_blog_module.json'), true);
            $extensions[] = [
                'name' => $this->language->get('text_blog_module'),
                'version' => $blog_config['version'],
                'status' => $this->d_blog_module
            ];
        } else {
            $extensions[] = [
                'name' => $this->language->get('text_blog_module'),
                'version' => '',
                'status' => $this->d_blog_module
            ];
        }


        $status = file_exists(DIR_APPLICATION . 'controller/extension/module/d_vuefront/.htaccess.txt') && is_dir(realpath(DIR_APPLICATION . '../vuefront'));

        $this->response->setOutput(json_encode([
            'apache' => strpos($this->request->server["SERVER_SOFTWARE"], "Apache") !== false,
            'status' => $status,
            'phpversion' => phpversion(),
            'plugin_version' => $this->extension['version'],
            'extensions' => $extensions,
            'cmsConnect' => $catalog
        ]));
    }

    public function proxy()
    {
        $body = $_POST;
        if (!function_exists('getallheaders')) {
            function getallheaders()
            {
                $headers = [];
                foreach ($_SERVER as $name => $value) {
                    if (substr($name, 0, 5) == 'HTTP_') {
                        $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                    }
                }
                return $headers;
            }
        }
        $headers = getallheaders();

        $cHeaders = ['Content-Type: application/json'];

        if (!empty($headers['Token'])) {
            $cHeaders[] = 'token: ' . $headers['Token'];
        }
        $rawInput = file_get_contents('php://input');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.vuefront.com/graphql');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $rawInput);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $cHeaders);
        $result = curl_exec($ch);
        curl_close($ch);

        $this->response->addHeader('Content-Type: application/json; charset=UTF-8');
        $this->response->setOutput($result);
    }

    public function install()
    {
        if ($this->d_shopunity) {
            $this->load->model('extension/d_shopunity/mbooth');
            $this->model_extension_d_shopunity_mbooth->installDependencies('d_vuefront');
        }
    }
}
