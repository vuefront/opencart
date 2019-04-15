<?php

use Youshido\GraphQL\Type\Scalar\FloatType;
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\BooleanType;
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\ListType\ListType;

class ModelExtensionModuleDVuefront extends Model
{
    private $codename = "d_vuefront";

    public function getQueries()
    {
        $result = Array();
        $files = glob(DIR_APPLICATION . 'controller/extension/' . $this->codename . '_type/*.php', GLOB_BRACE);
        foreach ($files as $file) {
            $filename = basename($file, '.php');
            $output = $this->load->controller('extension/' . $this->codename . '_type/' . $filename . '/query');
            if ($output) {
                $result = array_merge($result, $output);
            }
        }

        return $result;
    }

    public function getMutations()
    {
        $result = Array();
        $files = glob(DIR_APPLICATION . 'controller/extension/' . $this->codename . '_type/*.php', GLOB_BRACE);
        foreach ($files as $file) {
            $filename = basename($file, '.php');
            $output = $this->load->controller('extension/' . $this->codename . '_type/' . $filename . '/mutation');
            if ($output) {
                $result = array_merge($result, $output);
            }
        }

        return $result;
    }

    public function getPagination($type)
    {
        return new ObjectType([
            'name' => (string)$type . 'Result',
            'description' => (string)$type . ' List',
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

    public function getCategories($data = array())
    {
        $sql = "SELECT cp.category_id AS category_id, GROUP_CONCAT(cd1.name ORDER BY cp.level SEPARATOR '&nbsp;&nbsp;&gt;&nbsp;&nbsp;') AS name, c1.parent_id, c1.sort_order FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "category c1 ON (cp.category_id = c1.category_id) LEFT JOIN " . DB_PREFIX . "category c2 ON (cp.path_id = c2.category_id) LEFT JOIN " . DB_PREFIX . "category_description cd1 ON (cp.path_id = cd1.category_id) LEFT JOIN " . DB_PREFIX . "category_description cd2 ON (cp.category_id = cd2.category_id) WHERE cd1.language_id = '" . (int)$this->config->get('config_language_id') . "' AND cd2.language_id = '" . (int)$this->config->get('config_language_id') . "'";

        if (isset($data['parent'])) {
            $sql .= " AND c1.parent_id = '" . (int)$data['parent'] . "'";
        }

        if (!empty($data['filter_name'])) {
            $sql .= " AND cd2.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
        }

        $sql .= " GROUP BY cp.category_id";

        $sort_data = array(
            'name',
            'sort_order'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY sort_order";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getTotalCategories($data = array())
    {
        $sql = "SELECT COUNT(c.category_id) AS total
            FROM oc_category c
            LEFT JOIN oc_category_description cd ON (c.category_id = cd.category_id)
            WHERE cd.language_id='" . (int)$this->config->get('config_language_id') . "'";

        if (isset($data['parent'])) {
            $sql .= " AND c.parent_id = '" . (int)$data['parent'] . "'";
        }

        if (!empty($data['filter_name'])) {
            $sql .= " AND cd.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
        }

        $query = $this->db->query($sql);

        return $query->row['total'];
    }
}