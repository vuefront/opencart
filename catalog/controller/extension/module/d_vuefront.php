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
            $schema = BuildSchema::build(file_get_contents(__DIR__ . '/'.$this->codename.'/schema.graphql'));
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
        $this->response->addHeader('Content-Type: application/json; charset=UTF-8');
        $this->response->setOutput(json_encode($result));
    }
}