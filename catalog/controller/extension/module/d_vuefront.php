<?php
require_once DIR_SYSTEM . 'library/d_graphql/vendor/autoload.php';

use GraphQL\GraphQL;
use GraphQL\Utils\BuildSchema;

class ControllerExtensionModuleDVuefront extends Controller
{
    private $codename = "d_vuefront";
    private $route = "extension/module/d_vuefront";
    
    public function graphql()
    {
        $this->load->model($this->route);
        $resolvers = $this->model_extension_module_d_vuefront->getResolvers();

        try {
            $schema = BuildSchema::build(file_get_contents(__DIR__ . '/'.$this->codename.'_schema/schema.graphql'));
            $rawInput = file_get_contents('php://input');
            $input = json_decode($rawInput, true);
            $query = $input['query'];
            $variableValues = isset($input['variables']) ? $input['variables'] : null;
            $result = GraphQL::executeQuery($schema, $query, $resolvers, null, $variableValues);
        } catch (Exception $e) {
            $result = [
                'error' => [
                    'message' => $e->getMessage()
                ]
            ];
        }

        if (!empty($this->request->get['cors'])) {
            $this->response->addHeader('Access-Control-Allow-Origin: '.$this->request->server['HTTP_ORIGIN']);
            $this->response->addHeader('Access-Control-Allow-Methods: POST, OPTIONS');
            $this->response->addHeader('Access-Control-Allow-Credentials: true');
            $this->response->addHeader('Access-Control-Allow-Headers: DNT,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type,Range,Token,token,Cookie,cookie');
        }

        $this->response->addHeader('Content-Type: application/json; charset=UTF-8');
        $this->response->setOutput(json_encode($result));
    }
}
