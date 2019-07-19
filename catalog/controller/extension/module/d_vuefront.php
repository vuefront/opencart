<?php
require_once DIR_SYSTEM . 'library/d_graphql/vendor/autoload.php';

use GraphQL\GraphQL;
use GraphQL\Utils\BuildSchema;

class ControllerExtensionModuleDVuefront extends Controller
{
    private $codename = "d_vuefront";
    private $route = "extension/module/d_vuefront";

    public function __construct($registry)
    {
        parent::__construct($registry);

        if (! empty($this->request->get['cors'])) {
            try {
                if (! empty($this->request->server['HTTP_ORIGIN'])) {
                    header('Access-Control-Allow-Origin: ' . $this->request->server['HTTP_ORIGIN']);
                } else {
                    header('Access-Control-Allow-Origin: *');
                }
                header('Access-Control-Allow-Methods: POST, OPTIONS');
                header('Access-Control-Allow-Credentials: true');
                header('Access-Control-Allow-Headers: DNT,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type,Range,Token,token,Cookie,cookie,content-type');
            } catch (Exception $e) {
            }
        }
    }

    public function graphql()
    {
        $this->load->model($this->route);
        $resolvers = $this->model_extension_module_d_vuefront->getResolvers();
        $typeConfigDecorator = function ($typeConfig) {
            switch ($typeConfig['name']) {
                case 'Upload':
                    $typeConfig['parseValue'] = function ($value) {
                        return $value;
                    };
                break;
            }

            return $typeConfig;
        };

        try {
            $schema = BuildSchema::build(file_get_contents(__DIR__ . '/' . $this->codename . '_schema/schema.graphql'), $typeConfigDecorator);

            if (!empty($this->request->server['CONTENT_TYPE']) && strpos($this->request->server['CONTENT_TYPE'], 'multipart/form-data') !== false) {
                $rawInput       = html_entity_decode($this->request->post['operations'], ENT_QUOTES, 'UTF-8');
                $input          = json_decode($rawInput, true);
                $query          = isset($input['query']) ? $input['query'] : '';
                $variableValues = isset($input['variables']) ? $input['variables'] : null;
                $rawMap         = html_entity_decode($this->request->post['map'], ENT_QUOTES, 'UTF-8');
                $map            = json_decode($rawMap, true);
                foreach ($map as $key => $value) {
                    $variableValues[ $value ] = $this->request->files[ $key ];
                }
            } else {
                $rawInput       = file_get_contents('php://input');
                $input          = json_decode($rawInput, true);
                $query          = isset($input['query']) ? $input['query'] : '';
                $variableValues = isset($input['variables']) ? $input['variables'] : null;
            }

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
