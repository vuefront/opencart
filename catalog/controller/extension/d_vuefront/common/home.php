<?php

class ControllerExtensionDVuefrontCommonHome extends Controller
{
    public function get()
    {
        return [
            'meta' => [
                'title' => $this->config->get('config_meta_title'),
                'description' => $this->config->get('config_meta_description'),
                'keyword' => $this->config->get('config_meta_keyword'),
            ],
        ];
    }

    public function searchUrl($args)
    {
        $this->load->model('extension/d_vuefront/seo');

        $result = $this->model_extension_d_vuefront_seo->searchKeyword($args['url']);

        return $result;
    }

    public function updateApp($args)
    {
        $this->load->model('extension/module/d_vuefront');
        $this->model_extension_module_d_vuefront->editApp($args['name'], $args['settings']);

        return $this->model_extension_module_d_vuefront->getApp($args['name']);
    }

    public function updateSite($args)
    {
      try {
        $rootFolder = realpath(DIR_APPLICATION.'../');
        $tmpFile = tempnam(sys_get_temp_dir(), 'TMP_');
        rename($tmpFile, $tmpFile .= '.tar');
        file_put_contents($tmpFile, file_get_contents("https://vuefront2019.s3.amazonaws.com/sites/".$args['number']."/vuefront-app.tar"));
        $this->removeDir($rootFolder.'/vuefront');
        $phar = new PharData($tmpFile);
        $phar->extractTo($rootFolder.'/vuefront');
        return true;
      } catch (\Exception $e) {
      }
      return false;
    }

    private function removeDir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != '.' && $object != '..') {
                    if (is_dir($dir.'/'.$object) && !is_link($dir.'/'.$object)) {
                        $this->removeDir($dir.'/'.$object);
                    } else {
                        unlink($dir.'/'.$object);
                    }
                }
            }
            rmdir($dir);
        }
    }

    public function authProxy($args)
    {
        $this->load->model('extension/module/d_vuefront');

        if (!$this->customer->isLogged()) {
            return;
        }
        $app_info = $this->model_extension_module_d_vuefront->getApp($args['app']);
        $url = str_replace(':id', $this->customer->getId(), $app_info['authUrl']);
        $result = $this->model_extension_module_d_vuefront->request($url, [
            'customer_id' => $this->customer->getId(),
        ], $app_info['jwt']);

        if (!$result) {
            return '';
        }

        return $result['token'];
    }
}
