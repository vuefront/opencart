<?php

class ModelExtensionDVuefrontDBlogModule extends Model
{
    public function getCategories($data = array())
    {
        $sql = "SELECT cp.category_id AS category_id, c1.status, "
            . "GROUP_CONCAT(cd1.title ORDER BY cp.level SEPARATOR '&nbsp;&nbsp;&gt;&nbsp;&nbsp;') AS title, c1.parent_id, c1.sort_order "
            . "FROM " . DB_PREFIX . "bm_category_path cp "
            . "LEFT JOIN " . DB_PREFIX . "bm_category c1 ON (cp.category_id = c1.category_id) "
            . "LEFT JOIN " . DB_PREFIX . "bm_category c2 ON (cp.path_id = c2.category_id) "
            . "LEFT JOIN " . DB_PREFIX . "bm_category_description cd1 ON (cp.path_id = cd1.category_id) "
            . "LEFT JOIN " . DB_PREFIX . "bm_category_description cd2 ON (cp.category_id = cd2.category_id) "
            . "WHERE cd1.language_id = '" . (int)$this->config->get('config_language_id')
            . "' AND cd2.language_id = '" . (int)$this->config->get('config_language_id') . "'";


        if (!empty($data['filter_name'])) {
            $sql .= " AND cd2.title LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        }

        if (isset($data['parent'])) {
            $sql .= " AND c1.parent_id = '" . (int)$data['parent'] . "'";
        }

        $sql .= " GROUP BY cp.category_id";

        $sort_data = array(
            'title',
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
        $sql = "SELECT count(*) as total "
            . "FROM " . DB_PREFIX . "bm_category c "
            . "LEFT JOIN " . DB_PREFIX . "bm_category_description cd ON (c.category_id = cd.category_id) "
            . "WHERE cd.language_id = '" . (int)$this->config->get('config_language_id') . "'";


        if (!empty($data['filter_name'])) {
            $sql .= " AND cd.title LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        }

        if (isset($data['parent'])) {
            $sql .= " AND c.parent_id = '" . (int)$data['parent'] . "'";
        }

        $query = $this->db->query($sql);

        return $query->row['total'];
    }
}