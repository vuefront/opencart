<?php

class ModelModuleDVuefront extends Model {
    public function detectBlog() {
        $query = $this->db->query("SHOW TABLES LIKE '".DB_PREFIX."blog_article'");
        if($query->num_rows > 0) {
            return 'blog';
        } else {
            $query = $this->db->query("SHOW TABLES LIKE '".DB_PREFIX."sb_news'");
            if($query->num_rows > 0) {
                return 'news';
            } else {
                return false;
            }
        }
    }
}