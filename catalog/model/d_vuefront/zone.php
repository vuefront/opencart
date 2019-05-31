<?php

class ModelDVuefrontZone extends Model
{
    public function getZone($zone_id)
    {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "zone z WHERE z.zone_id = '" . (int)$zone_id . "'");

        return $query->row;
    }

    public function getZones($data = array())
    {
        $sql = "SELECT * FROM " . DB_PREFIX . "zone z";

        $implode = array();

        if (!empty($data['filter_name'])) {
            $implode[] = "z.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
        }
        if (!empty($data['filter_country_id'])) {
            $implode[] = "z.country_id = '" . (int)$data['filter_country_id'] . "'";
        }

        if(!empty($implode)) {
            $sql .= ' WHERE '.implode(' AND ', $implode);
        }

        $sort_data = array(
                'z.name'
            );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY z.name";
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

    public function getTotalZones($data = array())
    {
        $sql = "SELECT count(*) as total FROM " . DB_PREFIX . "zone z";

        $implode = array();

        if (!empty($data['filter_name'])) {
            $implode[] = "z.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
        }

        if (!empty($data['filter_country_id'])) {
            $implode[] = "z.country_id = '" . (int)$data['filter_country_id'] . "'";
        }

        if(!empty($implode)) {
            $sql .= ' WHERE '.implode(' AND ', $implode);
        }

        $query = $this->db->query($sql);

        return $query->row['total'];
    }
}
