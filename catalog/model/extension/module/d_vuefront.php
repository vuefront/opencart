<?php

use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\BooleanType;
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\ListType\ListType;

class ModelExtensionModuleDVuefront extends Model
{
    private $codename = "d_vuefront";

    public function getQueries() {
        $result = Array();
        $files = glob(DIR_APPLICATION.'controller/extension/'.$this->codename.'_type/*.php', GLOB_BRACE);
        foreach ($files as $file) {
            $filename = basename($file, '.php');
            $output = $this->load->controller('extension/'.$this->codename.'_type/'.$filename.'/query');
            if($output) {
                $result = array_merge($result, $output);
            }
        }

        return $result;
    }

    public function getPagination($type) {
        return new ObjectType([
            'name' => (string)$type.'Result',
            'description' => (string)$type.' List',
            'fields' => [
                'content' => new ListType($type),
                'first' => new BooleanType(),
                'last' => new BooleanType(),
                'number' => new IntType(),
                'numberOfElements' => new IntType(),
                'size' => new IntType(),
                'totalPages' => new IntType(),
                'totalElements' => new IntType()

            ]
        ]);
    }

    public function getProductType() {
        return new ObjectType([
            'name' => 'Product',
            'description' => 'Product',
            'fields' => [
                'id' => new IdType(),
                'thumb' => new StringType(),
                'thumbLazy' => new StringType(),
                'name' => new StringType(),
                'description' => new StringType(),
                'price' => new StringType(),
                'special' => new StringType(),
                'tax' => new StringType(),
                'minimum' => new IntType(),
                'rating' => new IntType()

            ]
        ]);
    }
}