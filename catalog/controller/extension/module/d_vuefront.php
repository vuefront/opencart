<?php
require_once DIR_SYSTEM . 'library/d_graphql/vendor/autoload.php';

// use Youshido\GraphQL\Execution\Processor;
// use Youshido\GraphQL\Schema\Schema;
// use Youshido\GraphQL\Type\Object\ObjectType;
// use Youshido\GraphQL\Type\Scalar\StringType;

use GraphQL\GraphQL;
use GraphQL\Utils\BuildSchema;

class ControllerExtensionModuleDVuefront extends Controller
{
    private $codename = "d_vuefront";
    private $route = "extension/module/d_vuefront";

    // public function graphql()
    // {
    //     $this->load->model($this->route);
    //     $rawInput = file_get_contents('php://input');
    //     $input = json_decode($rawInput, true);

    //     $queries = $this->model_extension_module_d_vuefront->getQueries();
    //     $mutations = $this->model_extension_module_d_vuefront->getMutations();

    //     $queryType = new ObjectType(Array(
    //         'name' => 'RootQueryType',
    //         'fields' => $queries
    //     ));
    //     $mutationType = new ObjectType(Array(
    //         'name' => 'RootMutationType',
    //         'fields' => $mutations
    //     ));

    //     $schema = new Schema(Array(
    //         'query' => $queryType,
    //         'mutation' => $mutationType
    //     ));

    //     $processor = new Processor($schema);

    //     if (!empty($input['variables'])) {
    //         $processor->processPayload($input['query'], $input['variables']);

    //     } else {
    //         $processor->processPayload($input['query']);
    //     }

    //     $result = $processor->getResponseData();

    //     $this->response->addHeader('Content-Type: application/json; charset=UTF-8');
    //     $this->response->setOutput(json_encode($result));
    // }

    public function graphql()
    {
        $this->load->model($this->route);
        $resolvers = $this->model_extension_module_d_vuefront->getResolvers();
        try {
            $schema = BuildSchema::build(file_get_contents(__DIR__ . '/schema.graphql'));
            $rawInput = file_get_contents('php://input');
            $input = json_decode($rawInput, true);
            $query = $input['query'];
            $variableValues = isset($input['variables']) ? $input['variables'] : null;
            $result = GraphQL::executeQuery($schema, $query, $resolvers, null, $variableValues);
        } catch (\Exception $e) {
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