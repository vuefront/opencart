<?php

class ModelExtensionDVuefrontCountry extends Model
{
    public function getCountry($country_id)
    {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "country c WHERE c.country_id = '" . (int)$country_id . "'");

        return $query->row;
    }

    public function getCountries($data = array())
    {
        $sql = "SELECT * FROM " . DB_PREFIX . "country c";

        $implode = array();

        if (!empty($data['filter_name'])) {
            $implode[] = "c.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
        }

        if(!empty($implode)) {
            $sql .= ' WHERE '.implode(' AND ', $implode);
        }

        $sort_data = array(
                'c.name'
            );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY c.name";
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

    public function getTotalCountries($data = array())
    {
        $sql = "SELECT count(*) as total FROM " . DB_PREFIX . "country c";

        $implode = array();

        if (!empty($data['filter_name'])) {
            $implode[] = "c.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
        }

        if(!empty($implode)) {
            $sql .= ' WHERE '.implode(' AND ', $implode);
        }

        $query = $this->db->query($sql);

        return $query->row['total'];
    }
}
