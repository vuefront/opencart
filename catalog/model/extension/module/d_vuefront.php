<?php

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
        $rawMapping = file_get_contents(DIR_APPLICATION.'controller/extension/module/'.$this->codename.'/mapping.json');
        $mapping = json_decode( $rawMapping, true );
        $result = array();
        foreach ($mapping as $key => $value) {
            $that = $this;
            $result[$key] = function($root, $args, $context) use ($value, $that) {
                return $that->load->controller('extension/'.$this->codename.'/'.$value, $args);
            };
        }

        return $result;
    }
}