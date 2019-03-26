<?php
require_once DIR_SYSTEM . 'library/d_graphql/vendor/autoload.php';

use Youshido\GraphQL\Execution\Processor;
use Youshido\GraphQL\Schema\Schema;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\StringType;

class ControllerExtensionModuleDVuefront extends Controller
{
    private $codename = "d_vuefront";
    private $route = "extension/module/d_vuefront";

    public function graphql()
    {
        $this->load->model($this->route);
        $rawInput = file_get_contents('php://input');
        $input = json_decode($rawInput, true);
        $query = $input['query'];

        $queries = $this->model_extension_module_d_vuefront->getQueries();

        $queryType = new ObjectType(Array(
            'name' => 'Query',
            'fields' => $queries
        ));

        $schema = new Schema(Array(
            'query' => $queryType
        ));

        $processor = new Processor($schema);

        $processor->processPayload($query);

        $result = $processor->getResponseData();

        $this->response->addHeader('Content-Type: application/json; charset=UTF-8');
        $this->response->setOutput(json_encode($result));
    }
}