<?php

class ModelExtensionDVuefrontSeo extends Model
{
    public function searchKeyword($keyword)
    {
        $type = '';
        $id = 0;

        if (VERSION >= '3.0.0.0') {
            $parts = explode('/', preg_replace("/^\//", "", $keyword));

            // remove any empty arrays from trailing
            if (utf8_strlen(end($parts)) == 0) {
                array_pop($parts);
            }

            foreach ($parts as $part) {
                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE keyword = '" . $this->db->escape($part) . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "'");

                if ($query->num_rows) {
                    $url = explode('=', $query->row['query']);

                    if ($url[0] == 'bm_category_id') {
                        $type = 'blog-category';
                        $id = $url[1];
                    }

                    if ($url[0] == 'bm_post_id') {
                        $type = 'blog-post';
                        $id = $url[1];
                    }

                    if ($url[0] == 'product_id') {
                        $type = 'product';
                        $id = $url[1];
                    }

                    if ($url[0] == 'category_id') {
                        $type = 'category';
                        $id = $url[1];
                    }

                    if ($url[0] == 'manufacturer_id') {
                        $type = 'manufacturer';
                        $id = $url[1];
                    }

                    if ($url[0] == 'information_id') {
                        $type = 'page';
                        $id = $url[1];
                    }
                } else {
                    break;
                }
            }
        } else {
          $parts = explode('/', preg_replace("/^\//", "", $keyword));

          // remove any empty arrays from trailing
          if (utf8_strlen(end($parts)) == 0) {
              array_pop($parts);
          }

        foreach ($parts as $part) {
              $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE keyword = '" . $this->db->escape($part) . "'");

              if ($query->num_rows) {
                $url = explode('=', $query->row['query']);

                  if ($url[0] == 'bm_category_id') {
                    $type = 'blog-category';
                    $id = $url[1];
}

                if ($url[0] == 'bm_post_id') {
                    $type = 'blog-post';
                    $id = $url[1];
                }

                  if ($url[0] == 'product_id') {
                      $type = 'product';
                      $id = $url[1];
                  }

                  if ($url[0] == 'category_id') {
                      $type = 'category';
                      $id = $url[1];
                  }

                  if ($url[0] == 'manufacturer_id') {
                      $type = 'manufacturer';
                      $id = $url[1];
                  }

                  if ($url[0] == 'information_id') {
                      $type = 'page';
                      $id = $url[1];
                  }
              } else {
                  break;
              }
          }
        }

        return array(
            'type' => $type,
            'id' => $id,
            'url' => $keyword
        );
    }
}
