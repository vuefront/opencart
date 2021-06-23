<?php

use GraphQL\Error\ClientAware;

class MySafeException extends \Exception implements ClientAware
{
    public function isClientSafe()
    {
        return true;
    }

    public function getCategory()
    {
        return 'businessLogic';
    }
}
class VfLoad
{
    private $registry;
    private $codename = 'd_vuefront';

    public function __construct($registry)
    {
        $this->registry = $registry;
    }

    public function resolver($route)
    {
        $that = $this;

        return function ($root, $args) use ($that, $route) {
            return $that->registry->get('load')->controller('extension/'.$this->codename.'/'.$route, [
                'parent' => $root,
                'args' => $args,
            ]);
        };
    }

    public function data($route, $data = [])
    {
        return $this->registry->get('load')->controller('extension/'.$this->codename.'/'.$route, $data);
    }
}
class ModelExtensionModuleDVuefront extends Model
{
    private $codename = 'd_vuefront';

    public function __construct($registry)
    {
        parent::__construct($registry);
        $registry->set('vfload', new VfLoad($registry));
    }

    public function getResolvers()
    {
        $rawMapping = file_get_contents(DIR_APPLICATION.'controller/extension/module/'.$this->codename.'_schema/mapping.json');
        $mapping = json_decode($rawMapping, true);
        $result = [];
        foreach ($mapping as $key => $value) {
            $that = $this;
            $result[$key] = function ($root, $args, $context) use ($value, $that) {
                try {
                    return $that->load->controller('extension/'.$this->codename.'/'.$value, $args);
                } catch (Exception $e) {
                    throw new MySafeException($e->getMessage());
                }
            };
        }

        return $result;
    }

    public function getJwt($codename)
    {
        $this->load->model('setting/setting');

        $setting = $this->model_setting_setting->getSetting('d_vuefront');

        $result = false;
        
        if (!empty($setting['d_vuefront_apps'])) {
            foreach ($setting['d_vuefront_apps'] as $key => $value) {
                if ($value['codename'] == $codename) {
                    $result = $value['jwt'];
                }
            }
        }

        return $result;
    }

    public function pushEvent($name, $data)
    {
        $apps = $this->getAppsForEvent();

        foreach ($apps as $key => $value) {
            $output = $this->request($value['eventUrl'], [
                'name' => $name,
                'data' => $data,
            ]);
            if ($output) {
                $data = $output;
            }
        }

        return $data;
    }

    public function checkAccess()
    {
        $this->load->model('setting/setting');

        if (!isset($this->request->get['accessKey'])) {
            return false;
        }

        $setting = $this->model_setting_setting->getSetting('d_vuefront');

        $result = false;
        if (!empty($setting['d_vuefront_apps'])) {
            foreach ($setting['d_vuefront_apps'] as $value) {
                if (!empty($value['accessKey']) && $this->request->get['accessKey'] == $value['accessKey']) {
                    $result = true;
                }
            }
        }
        if (!empty($setting['d_vuefront_settings']) && !empty($setting['d_vuefront_settings']['accessKey'])) {
            if ($this->request->get['accessKey'] == $setting['d_vuefront_settings']['accessKey']) {
                $result = true;
            }
        }

        return $result;
    }

    public function editApp($name, $appSetting)
    {
        $appSetting['codename'] = $name;
        $this->load->model('setting/setting');

        $setting = $this->model_setting_setting->getSetting('d_vuefront');

        $app = $this->getApp($name);

        if (!isset($setting['d_vuefront_apps'])) {
            $setting['d_vuefront_apps'] = array();
        }
        if (!is_array($setting['d_vuefront_apps'])) {
            $setting['d_vuefront_apps'] = array();
        }
        if (!empty($app)) {
            foreach ($setting['d_vuefront_apps'] as $key => $value) {
                if ($value['codename'] == $name) {
                    $setting['d_vuefront_apps'][$key] = $appSetting;
                }
            }
        } else {
            $setting['d_vuefront_apps'][] = $appSetting;
        }

        $this->editSettingValue('d_vuefront', 'd_vuefront_apps', $setting['d_vuefront_apps']);
    }

    public function editSettingValue($code = '', $key = '', $value = '', $store_id = 0)
    {
        $this->db->query('DELETE FROM '.DB_PREFIX.'setting WHERE `code` = \''.$this->db->escape($code).'\' AND `key` = \''.$this->db->escape($key).'\' AND store_id = \''.(int) $store_id.'\'');
        if (!is_array($value)) {
            $this->db->query('INSERT INTO '.DB_PREFIX."setting SET `value` = '".$this->db->escape($value)."', serialized = '0'  , `code` = '".$this->db->escape($code)."', `key` = '".$this->db->escape($key)."', store_id = '".(int) $store_id."'");
        } else {
            $value = json_encode($value);
            if (VERSION < '2.1.0.0') {
                $value = serialize($value);
            }
            $this->db->query('INSERT INTO '.DB_PREFIX."setting SET `value` = '".$this->db->escape($value)."', serialized = '1' , `code` = '".$this->db->escape($code)."', `key` = '".$this->db->escape($key)."', store_id = '".(int) $store_id."'");
        }
    }

    public function getSetting($name)
    {
        $this->load->model('setting/setting');

        $setting = $this->model_setting_setting->getSetting('d_vuefront');

        return !empty($setting['d_vuefront_settings']) ? $setting['d_vuefront_settings'] : [];
    }

    public function getApp($name)
    {
        $this->load->model('setting/setting');

        $setting = $this->model_setting_setting->getSetting('d_vuefront');
        if (!empty($setting['d_vuefront_apps'])) {
            foreach ($setting['d_vuefront_apps'] as $value) {
                if ($value['codename'] == $name) {
                    return $value;
                }
            }
        }

        return false;
    }

    public function getAppsForEvent()
    {
        $this->load->model('setting/setting');

        $setting = $this->model_setting_setting->getSetting('d_vuefront');

        $result = [];
        if (!empty($setting['d_vuefront_apps'])) {
            foreach ($setting['d_vuefront_apps'] as $value) {
                if (!empty($value['eventUrl'])) {
                    $result[] = $value;
                }
            }
        }

        return $result;
    }

    public function request($url, $data, $token = false)
    {
        $ch = curl_init();
        $headers = [];

        $headers[] = 'Content-type: application/json';
        if ($token) {
            $headers[] = 'Authorization: Bearer '.$token;
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_URL, $url);

        $result = curl_exec($ch);

        $error = curl_error($ch);

        if ($error) {
            throw new Exception($error);
        }

        $result = json_decode($result, true);

        return $result;
    }

    public function requestCheckout($query, $variables)
    {
        $jwt = $this->getJwt('vuefront-checkout-app');

        $ch = curl_init();

        $requestData = [
            'operationName' => null,
            'variables' => $variables,
            'query' => $query,
        ];

        $headr = [];

        $headr[] = 'Content-type: application/json';
        $headr[] = 'Authorization: '.$jwt;

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headr);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData, JSON_FORCE_OBJECT));
        // curl_setopt($ch, CURLOPT_URL, 'http://localhost:3005/graphql');
        curl_setopt($ch, CURLOPT_URL, 'https://api.checkout.vuefront.com/graphql');

        $result = curl_exec($ch);

        $result = json_decode($result, true);

        return $result['data'];
    }

    public function mergeSchemas($files)
    {
        $rootQueryType = '';
        $types = '';
        $rootMutationType = '';
        foreach ($files as $value) {
            preg_match('/type\s+RootQueryType\s\{\s*\n([^\}]+)/', $value, $matched);
            if (!empty($matched[1])) {
                $rootQueryType = $rootQueryType.PHP_EOL.$matched[1];
            }
            preg_match('/type\s+RootMutationType\s\{\s*\n([^\}]+)/', $value, $mutationMatched);
            if (!empty($mutationMatched[1])) {
                $rootMutationType = $rootMutationType.PHP_EOL.$mutationMatched[1];
            }
            preg_match('/([a-zA-Z0-9\=\s\}\_\-\@\{\:\[\]\(\)\!\"]+)type RootQueryType/', $value, $typesMatched);
            if (!empty($typesMatched[1])) {
                $types = $types.PHP_EOL.$typesMatched[1];
            }
        }

        return "${types}".PHP_EOL.'type RootQueryType {'.PHP_EOL."${rootQueryType}".PHP_EOL.'}'.PHP_EOL.'type RootMutationType {'.PHP_EOL."${rootMutationType}".PHP_EOL.'}';
    }
}
