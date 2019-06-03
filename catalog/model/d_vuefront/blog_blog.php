<?php

class ModelDVuefrontBlogBlog extends Model
{
    public function getCategoryKeyword($category_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE `query` LIKE 'blog_category_id=".(int)$category_id."'");
        return $query->row;
    }

    public function getPostKeyword($post_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE `query` LIKE 'blog_article_id=".(int)$post_id."'");

        return $query->row;
    }

    public function getCategory($category_id)
    {
        $sql = "SELECT *, c.category_id as category_id, cd.name as title FROM " . DB_PREFIX . "blog_category c "
            . "LEFT JOIN " . DB_PREFIX . "blog_category_description cd ON (c.category_id = cd.category_id) "
            . "WHERE cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c.category_id = '".(int)$category_id."'";

        $sql .= " GROUP BY c.category_id";

        $query = $this->db->query($sql);

        return $query->row;
    }

    public function getCategories($data = array())
    {
        $sql = "SELECT c.category_id AS category_id, c.status, "
            . "cd.name AS title, c.parent_id, c.sort_order "
            . "FROM " . DB_PREFIX . "blog_category c "
            . "LEFT JOIN " . DB_PREFIX . "blog_category_description cd ON (c.category_id = cd.category_id) "
            . "WHERE cd.language_id = '" . (int)$this->config->get('config_language_id') . "'";


        if (!empty($data['filter_name'])) {
            $sql .= " AND cd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        }

        if (isset($data['parent'])) {
            $sql .= " AND c.parent_id = '" . (int)$data['parent'] . "'";
        }

        $sql .= " GROUP BY c.category_id";

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
        $sql = "SELECT count(*) as total "
        . "FROM " . DB_PREFIX . "blog_category c "
        . "LEFT JOIN " . DB_PREFIX . "blog_category_description cd ON (c.category_id = cd.category_id) "
        . "WHERE cd.language_id = '" . (int)$this->config->get('config_language_id') . "'";


        if (!empty($data['filter_name'])) {
            $sql .= " AND cd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        }

        if (isset($data['parent'])) {
            $sql .= " AND c.parent_id = '" . (int)$data['parent'] . "'";
        }

        $sql .= " GROUP BY c.category_id";

        $query = $this->db->query($sql);

        return $query->row['total'];
    }

    public function getPost($category_id)
    {
        $sql = "SELECT *, n.article_id as post_id FROM " . DB_PREFIX . "blog_article n "
            . "LEFT JOIN " . DB_PREFIX . "blog_article_description nd ON (n.article_id = nd.article_id) "
            . "WHERE nd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND n.article_id = '".(int)$category_id."'";

        $sql .= " GROUP BY n.article_id";

        $query = $this->db->query($sql);

        return $query->row;
    }

    public function getPosts($data = array())
    {
        $sql = "SELECT n.article_id AS post_id ";
        if (!empty($data['filter_category_id'])) {
            $sql .= " FROM " . DB_PREFIX . "blog_article_to_category n2c";
            $sql .= " LEFT JOIN " . DB_PREFIX . "blog_article n ON (n2c.article_id = n.article_id)";
        } else {
            $sql .= " FROM " . DB_PREFIX . "blog_article n ";
        }

        $sql .= "LEFT JOIN " . DB_PREFIX . "blog_article_description nd ON (n.article_id = nd.article_id) "
            . "WHERE nd.language_id = '" . (int)$this->config->get('config_language_id') . "'";


        if (!empty($data['filter_name']) && !empty($data['filter_description'])) {
            $sql .= " AND ( nd.title LIKE '%" . $data['filter_name'] . "%' OR nd.description LIKE '%" . $data['filter_description'] . "%' )";
        } else {
            if (!empty($data['filter_name'])) {
                $sql .= " AND nd.title LIKE '%" . $data['filter_name'] . "%'";
            }
    
            if (!empty($data['filter_description'])) {
                $sql .= " AND nd.description LIKE '%" . $data['filter_description'] . "%'";
            }
        }

        if (!empty($data['filter_category_id'])) {
            $sql .= " AND n2c.category_id = '" . (int)$data['filter_category_id'] . "'";
        }

        $sql .= " GROUP BY n.article_id";

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

    public function getTotalPosts($data = array())
    {
        $sql = "SELECT count(*) as total ";
        if (!empty($data['filter_category_id'])) {
            $sql .= " FROM " . DB_PREFIX . "blog_article_to_category n2c";
            $sql .= " LEFT JOIN " . DB_PREFIX . "blog_article n ON (n2c.article_id = n.article_id)";
        } else {
            $sql .= " FROM " . DB_PREFIX . "blog_article n ";
        }

        $sql .= "LEFT JOIN " . DB_PREFIX . "blog_article_description nd ON (n.article_id = nd.article_id) "
            . "WHERE nd.language_id = '" . (int)$this->config->get('config_language_id') . "'";


        if (!empty($data['filter_name']) && !empty($data['filter_description'])) {
            $sql .= " AND ( nd.title LIKE '%" . $data['filter_name'] . "%' OR nd.description LIKE '%" . $data['filter_description'] . "%' )";
        } else {
            if (!empty($data['filter_name'])) {
                $sql .= " AND nd.title LIKE '%" . $data['filter_name'] . "%'";
            }
    
            if (!empty($data['filter_description'])) {
                $sql .= " AND nd.description LIKE '%" . $data['filter_description'] . "%'";
            }
        }

        if (!empty($data['filter_category_id'])) {
            $sql .= " AND n2c.category_id = '" . (int)$data['filter_category_id'] . "'";
        }

        $query = $this->db->query($sql);

        return $query->row['total'];
    }

    public function getReviewsByPostId($article_id)
    {

        $sql = "SELECT *, c.comment_id, c.name as author, 0 AS rating, c.content AS description, c.email AS guest_email, n.article_id AS post_id, nd.title, n.image, c.created AS date_added "
        . "FROM " . DB_PREFIX . "blog_comment c "
        . "LEFT JOIN " . DB_PREFIX . "blog_article n ON (c.article_id = n.article_id) "
        . "LEFT JOIN " . DB_PREFIX . "blog_article_description nd "
        . "ON (n.article_id = nd.article_id) "
        . "WHERE n.article_id = '" . (int)$article_id . "' "
        . "AND n.status = '1' "
        . "AND c.status = '1' "
        . "AND nd.language_id = '" . (int)$this->config->get('config_language_id') . "' "
        . "ORDER BY c.created DESC";
        $query = $this->db->query($sql);


        return $query->rows;
    }

    public function addReview($post_id, $data) {
        $sql = "INSERT INTO " . DB_PREFIX . "blog_comment "
        . "SET name = '" . $this->db->escape($data['author']) . "', "
        . "article_id = '" . (int)$post_id . "', "
        . "status = ". (int)$data['status'] .", "
        . "text = '" . $this->db->escape($data['description']) . "', "
        . "date_added = NOW(), date_modified = NOW()";
        $this->db->query($sql);
        $review_id = $this->db->getLastId();
    }
}
