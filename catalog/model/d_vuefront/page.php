<?php

class ModelDVuefrontPage extends Model
{
    public function getPageKeyword($information_id) {
        if (VERSION >= '3.0.0.0') {
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE store_id = '" . (int)$this->config->get('config_store_id') . "' AND language_id='".(int)$this->config->get('config_language_id')."' AND `query` LIKE 'information_id=".(int)$information_id."'");
        } else {
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE `query` LIKE 'information_id=".(int)$information_id."'");
        }
        return $query->row;
    }
    public function getPage($information_id)
    {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "information i LEFT JOIN " . DB_PREFIX . "information_description id ON (i.information_id = id.information_id) WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "' AND i.information_id = '" . (int)$information_id . "'");

        return $query->row;
    }

    public function getPages($data = array())
    {
        $sql = "SELECT * FROM " . DB_PREFIX . "information i LEFT JOIN " . DB_PREFIX . "information_description id ON (i.information_id = id.information_id) WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "'";

        if (!empty($data['filter_title'])) {
            $sql .= " AND id.title LIKE '%" . $this->db->escape($data['filter_title']) . "%'";
        }
        if (!empty($data['filter_description'])) {
            $sql .= " AND id.description LIKE '%" . $this->db->escape($data['filter_description']) . "%'";
        }

        $sort_data = array(
                'id.title',
                'i.sort_order'
            );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY id.title";
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

    public function getTotalPages($data = array())
    {
        $sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "information i LEFT JOIN " . DB_PREFIX . "information_description id ON (i.information_id = id.information_id) WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "'";

        if (!empty($data['filter_title'])) {
            $sql .= " AND id.title LIKE '%" . $this->db->escape($data['filter_title']) . "%'";
        }
        if (!empty($data['filter_description'])) {
            $sql .= " AND id.description LIKE '%" . $this->db->escape($data['filter_description']) . "%'";
        }

        $query = $this->db->query($sql);

        return $query->row['total'];
    }
}
