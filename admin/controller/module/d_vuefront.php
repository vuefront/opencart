<?php

/**
* location: admin/controller
*/
class ControllerModuleDVuefront extends Controller
{
    private $codename = 'd_vuefront';
    private $route = 'module/d_vuefront';
    private $error = array();
    private $blog = false;

    public function __construct($registry)
    {
        parent::__construct($registry);

        $this->load->language($this->route);
        $this->load->model($this->route);
        $this->blog = $this->model_module_d_vuefront->detectBlog();

        $this->extension = simplexml_load_string(file_get_contents(DIR_APPLICATION.'mbooth/xml/d_vuefront.xml'));

        $this->extension = json_encode($this->extension);
        $this->extension = json_decode($this->extension, true);
    }

    public function index()
    {

        $url_params = array();
        $url = '';

        $url = ((!empty($url_params)) ? '&' : '') . http_build_query($url_params);

        $this->document->setTitle($this->language->get('heading_title_main'));
        $this->document->addScript('//cdn.jsdelivr.net/npm/clipboard@2/dist/clipboard.min.js');
        $this->data['heading_title'] = $this->language->get('heading_title_main');

        $this->data['text_edit'] = $this->language->get('text_edit');
        $this->data['text_title'] = $this->language->get('text_title');
        $this->data['text_description'] = $this->language->get('text_description');
        $this->data['text_copy'] = $this->language->get('text_copy');

        $this->data['text_blog_module'] = $this->language->get('text_blog_module');
        $this->data['text_blog_enabled'] = $this->language->get('text_blog_enabled');
        $this->data['text_blog_disabled'] = $this->language->get('text_blog_disabled');
        $link = '2419';
        switch($this->blog) {
            case 'news':
                $link = '2419';
                break;
            case 'blog':
                $link = '4552';
                break;
            default:
                break;
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

        $this->data['baseUrl'] = HTTP_SERVER;

        if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
            $this->data['siteUrl'] = HTTPS_CATALOG;
        } else {
            $this->data['siteUrl'] = HTTP_CATALOG;
        }

        $this->data['tokenUrl'] = 'token='.$this->session->data['token'];

        $this->data['text_blog_description'] = sprintf($this->language->get('text_blog_description'), $link);

        $this->data['blog'] = $this->blog;

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

    public function vf_information()
    {
        $root = realpath(DIR_APPLICATION . '../');
        $catalog = '';
        if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
            $catalog = HTTPS_CATALOG . 'index.php?route=module/d_vuefront/graphql';
        } else {
            $catalog = HTTP_CATALOG . 'index.php?route=module/d_vuefront/graphql';
        }

        $extensions = array();

        if ($this->blog) {
            $extensions[] = [
                'name' => 'Blog',
                'version' => '1.0.0',
                'status' => !!$this->blog
            ];
        } else {
            $extensions[] = [
                'name' => 'Blog',
                'version' => '',
                'status' => !!$this->blog
            ];
        }

        $is_apache = strpos($this->request->server["SERVER_SOFTWARE"], "Apache") !== false;

        $status = false;
        if(file_exists(DIR_APPLICATION . 'controller/module/d_vuefront_backup/.htaccess.txt')) {
            $status = true;
        }
        $this->response->addHeader( 'Content-Type: application/json; charset=UTF-8' );

        $this->response->setOutput(json_encode(array(
            'apache' => $is_apache,
            'backup' => 'admin/module/d_vuefront_backup/.htaccess.txt',
            'htaccess' => file_exists($root . '/.htaccess'),
            'status' => $status,
            'phpversion' => phpversion(),
            'plugin_version' => $this->extension['version'],
            'plugin' => $this->extension,
            'extensions' => $extensions,
            'cmsConnect' => $catalog,
            'server' => $this->request->server["SERVER_SOFTWARE"]
        )));


    }

    public function proxy()
    {
        $body = $_POST;
        if (!function_exists('getallheaders')) {
            function getallheaders()
            {
                $headers = array();
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
        if (!empty($headers['token'])) {
            $cHeaders[] = 'token: ' . $headers['token'];
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

    public function vf_update() {

        try {
            $rootFolder = realpath(DIR_APPLICATION . '../');
            $tmpFile = tempnam(sys_get_temp_dir(), 'TMP_');
            rename($tmpFile, $tmpFile .= '.tar');
            file_put_contents($tmpFile, file_get_contents($this->request->post['url']));
            $this->removeDir($rootFolder . '/vuefront');
            $phar = new PharData($tmpFile);
            $phar->extractTo($rootFolder . '/vuefront');
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

        $this->vf_information();
    }

    public function vf_turn_on()
    {
        $catalog = '';
        if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
            $catalog = HTTPS_CATALOG;
        } else {
            $catalog = HTTP_CATALOG;
        }

        try {
            $rootFolder = realpath(DIR_APPLICATION . '../');

            $catalog_url_info = parse_url($catalog);

            $catalog_path = $catalog_url_info['path'];

            $document_path = $catalog_path;
            if(!empty($this->request->server['DOCUMENT_ROOT'])) {
              $document_path = str_replace(realpath($this->request->server['DOCUMENT_ROOT']), '', $rootFolder) . '/';
            }

            if (strpos($_SERVER["SERVER_SOFTWARE"], "Apache") !== false) {

                if(!file_exists($rootFolder . '/.htaccess')) {
                    file_put_contents($rootFolder.'/.htaccess', "Options +FollowSymlinks
Options -Indexes
<FilesMatch \"(?i)((\.tpl|\.ini|\.log|(?<!robots)\.txt))\">
 Require all denied
</FilesMatch>
RewriteEngine On
RewriteBase ".$catalog_path."
RewriteRule ^sitemap.xml$ index.php?route=extension/feed/google_sitemap [L]
RewriteRule ^googlebase.xml$ index.php?route=extension/feed/google_base [L]
RewriteRule ^system/download/(.*) index.php?route=error/not_found [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_URI} !.*\.(ico|gif|jpg|jpeg|png|js|css)
RewriteRule ^([^?]*) index.php?_route_=$1 [L,QSA]");
                }

                if(!is_writable($rootFolder . '/.htaccess')) {
                    http_response_code(500);
                    $this->response->setOutput(json_encode(array(
                        'error' => 'not_writable_htaccess'
                    )));
                    return;
                }

                if (file_exists($rootFolder . '/.htaccess')) {
                    $inserting = "# VueFront scripts, styles and images
RewriteCond %{REQUEST_URI} .*(_nuxt)
RewriteCond %{REQUEST_URI} !.*/vuefront/_nuxt
RewriteRule ^([^?]*) vuefront/$1

# VueFront sw.js
RewriteCond %{REQUEST_URI} .*(sw.js)
RewriteCond %{REQUEST_URI} !.*/vuefront/sw.js
RewriteRule ^([^?]*) vuefront/$1

# VueFront favicon.ico
RewriteCond %{REQUEST_URI} .*(favicon.ico)
RewriteCond %{REQUEST_URI} !.*/vuefront/favicon.ico
RewriteRule ^([^?]*) vuefront/$1


# VueFront pages

# VueFront home page
RewriteCond %{REQUEST_URI} !.*(images|index.php|.html|admin|.js|.css|.png|.jpeg|.ico|wp-json|wp-admin|checkout)
RewriteCond %{QUERY_STRING} !.*(rest_route)
RewriteCond %{DOCUMENT_ROOT}".$document_path."vuefront/index.html -f
RewriteRule ^$ vuefront/index.html [L]

RewriteCond %{REQUEST_URI} !.*(images|index.php|.html|admin|.js|.css|.png|.jpeg|.ico|wp-json|wp-admin|checkout)
RewriteCond %{QUERY_STRING} !.*(rest_route)
RewriteCond %{DOCUMENT_ROOT}".$document_path."vuefront/index.html !-f
RewriteRule ^$ vuefront/200.html [L]

# VueFront page if exists html file
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_URI} !.*(images|index.php|.html|admin|.js|.css|.png|.jpeg|.ico|wp-json|wp-admin|checkout)
RewriteCond %{QUERY_STRING} !.*(rest_route)
RewriteCond %{DOCUMENT_ROOT}".$document_path."vuefront/$1.html -f
RewriteRule ^([^?]*) vuefront/$1.html [L,QSA]

# VueFront page if not exists html file
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_URI} !.*(images|index.php|.html|admin|.js|.css|.png|.jpeg|.ico|wp-json|wp-admin|checkout)
RewriteCond %{QUERY_STRING} !.*(rest_route)
RewriteCond %{DOCUMENT_ROOT}".$document_path."vuefront/$1.html !-f
RewriteRule ^([^?]*) vuefront/200.html [L,QSA]";

                    $content = file_get_contents($rootFolder . '/.htaccess');

                    if (!is_dir(DIR_APPLICATION . 'controller/module/d_vuefront_backup')) {
                        mkdir(DIR_APPLICATION . 'controller/module/d_vuefront_backup');
                    }

                    file_put_contents(DIR_APPLICATION . 'controller/module/d_vuefront_backup/.htaccess.txt', $content);

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
            if (file_exists(DIR_APPLICATION . 'controller/module/d_vuefront_backup/.htaccess.txt')) {
                if(!is_writable($rootFolder . '/.htaccess') || !is_writable(DIR_APPLICATION . 'controller/module/d_vuefront_backup/.htaccess.txt')) {
                    http_response_code(500);
                    $this->response->setOutput(json_encode(array(
                        'error' => 'not_writable_htaccess'
                    )));
                    return;
                }
                $content = file_get_contents(DIR_APPLICATION . 'controller/module/d_vuefront_backup/.htaccess.txt');
                file_put_contents($rootFolder . '/.htaccess', $content);
                unlink(DIR_APPLICATION . 'controller/module/d_vuefront_backup/.htaccess.txt');
            }
        }


        $this->vf_information();
    }
}
