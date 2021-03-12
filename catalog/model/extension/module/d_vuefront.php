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
class VfLoad {
    private $registry;
    private $codename = "d_vuefront";
    public function __construct($registry)
    {
        $this->registry = $registry;
    }

    public function resolver($route) {
        $that = $this;
        return function($root, $args) use ($that, $route) {
            return $that->registry->get('load')->controller('extension/'.$this->codename.'/'.$route, array(
                'parent' => $root,
                'args' => $args
            ));
        };
    }

    public function data($route, $data = array()) {
        return $this->registry->get('load')->controller('extension/'.$this->codename.'/'.$route, $data);
    }
}
class ModelExtensionModuleDVuefront extends Model
{
    private $codename = "d_vuefront";

    public function __construct($registry)
    {
        parent::__construct($registry);
        $registry->set('vfload', new VfLoad($registry));
    }

    public function getResolvers()
    {
        $rawMapping = file_get_contents(DIR_APPLICATION.'controller/extension/module/'.$this->codename.'_schema/mapping.json');
        $mapping = json_decode( $rawMapping, true );
        $result = array();
        foreach ($mapping as $key => $value) {
            $that = $this;
            $result[$key] = function($root, $args, $context) use ($value, $that) {
                try {
                    return $that->load->controller('extension/'.$this->codename.'/'.$value, $args);
                } catch(Exception $e) {
                    throw new MySafeException($e->getMessage());
                }
            };
        }

        return $result;
    }

    public function getJwt($codename) {
        $this->load->model('setting/setting');

        $setting = $this->model_setting_setting->getSetting('d_vuefront');

        $result = false;

        foreach ($setting['d_vuefront_apps'] as $key => $value) {
            if($value['codename'] == $codename) {
                $result = $value['jwt'];
            }
        }

        return $result;
    }

    public function pushEvent($name, $data) {
        $apps = $this->getAppsForEvent();

        foreach ($apps as $key => $value) {
            $this->request($value['eventUrl'], array(
                'name' => $name,
                'data' => $data
            ));
        }
    }

    public function checkAccess() {
        $this->load->model('setting/setting');

        if (!isset($this->request->get['accessKey'])) {
            return false;
        }

        $setting = $this->model_setting_setting->getSetting('d_vuefront');

        $result = false;

        foreach ($setting['d_vuefront_apps'] as $value) {
            if(!empty($value['accessKey']) && $this->request->get['accessKey'] == $value['accessKey']) {
                $result = true;
            }
        }

        return $result;
    }

    public function getAppsForEvent() {
        $this->load->model('setting/setting');

        $setting = $this->model_setting_setting->getSetting('d_vuefront');

        $result = [];

        foreach ($setting['d_vuefront_apps'] as $value) {
            if(!empty($value['eventUrl'])) {
                $result[] = $value;
            }
        }

        return $result;
    }

    public function request($url, $data) {
        $ch = curl_init();
        $headr = array();

        $headr[] = 'Content-type: application/json';

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headr);
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

    public function requestCheckout($query, $variables) {
        $jwt = $this->getJwt('vuefront-checkout-app');

        $ch = curl_init();

        $requestData = array(
            'operationName' => null,
            'variables' => $variables,
            'query' => $query
        );

        $headr = array();

        $headr[] = 'Content-type: application/json';
        $headr[] = 'Authorization: '.$jwt;

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$headr);
        curl_setopt($ch, CURLOPT_POST,true);
        curl_setopt($ch, CURLOPT_POSTFIELDS,     json_encode($requestData, JSON_FORCE_OBJECT) );
        // curl_setopt($ch, CURLOPT_URL, 'http://localhost:3005/graphql');
        curl_setopt($ch, CURLOPT_URL, 'https://api.checkout.vuefront.com/graphql');

        $result = curl_exec($ch);

        $result = json_decode($result, true);

        return $result['data'];
    }
    public function mergeSchemas($files) {
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

        return "${types}".PHP_EOL."type RootQueryType {".PHP_EOL."${rootQueryType}".PHP_EOL."}".PHP_EOL."type RootMutationType {".PHP_EOL."${rootMutationType}".PHP_EOL."}";
    }
}
