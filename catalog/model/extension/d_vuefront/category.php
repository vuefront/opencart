<?php
class ModelExtensionDVuefrontCategory extends Model
{
    public function getCategoryKeyword($category_id)
    {
        if (VERSION >= '3.0.0.0') {
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE store_id = '" . (int)$this->config->get('config_store_id') . "' AND language_id='".(int)$this->config->get('config_language_id')."' AND `query` LIKE 'category_id=".(int)$category_id."'");
        } else {
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE `query` LIKE 'category_id=".(int)$category_id."'");
        }
        return $query->row;
    }

    public function getCategories($data = array())
    {
        $sql = "SELECT cp.category_id AS category_id, GROUP_CONCAT(cd1.name ORDER BY cp.level SEPARATOR '&nbsp;&nbsp;&gt;&nbsp;&nbsp;') AS name, c1.parent_id, c1.sort_order FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "category c1 ON (cp.category_id = c1.category_id) LEFT JOIN " . DB_PREFIX . "category c2 ON (cp.path_id = c2.category_id) LEFT JOIN " . DB_PREFIX . "category_description cd1 ON (cp.path_id = cd1.category_id) LEFT JOIN " . DB_PREFIX . "category_description cd2 ON (cp.category_id = cd2.category_id) WHERE c1.status = '1' AND cd1.language_id = '" . (int)$this->config->get('config_language_id') . "' AND cd2.language_id = '" . (int)$this->config->get('config_language_id') . "'";

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
            FROM ".DB_PREFIX."category c
            LEFT JOIN ".DB_PREFIX."category_description cd ON (c.category_id = cd.category_id)
            WHERE c.status = '1' AND cd.language_id='" . (int)$this->config->get('config_language_id') . "'";

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
