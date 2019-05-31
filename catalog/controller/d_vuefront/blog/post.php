<?php

class ControllerDVuefrontBlogPost extends Controller
{
    private $codename = "d_vuefront";

    public function get($args)
    {
        $this->load->model($this->codename . '/blog');
        $this->load->model('tool/image');
        $post_info = $this->model_d_vuefront_blog->getPost($args['id']);
        $post_keyword = $this->model_d_vuefront_blog->getPostKeyword($args['id']);

        if (!empty($post_keyword['keyword'])) {
            $keyword = $post_keyword['keyword'];
        } else {
            $keyword = '';
        }

        $width = $this->config->get('config_image_product_width');
        $height = $this->config->get('config_image_product_height');

        if ($post_info['image']) {
            $image = $this->model_tool_image->resize($post_info['image'], $width, $height);
            $imageLazy = $this->model_tool_image->resize($post_info['image'], 10, ceil(10 * $height / $width));
        } else {
            $image = '';
            $imageLazy = '';
        }

        return array(
                'id' => $post_info['post_id'],
                'title' => $post_info['title'],
                'description' => html_entity_decode($post_info['description'], ENT_QUOTES, 'UTF-8'),
                'shortDescription' => utf8_substr(trim(strip_tags(html_entity_decode($post_info['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('config_product_description_length')) . '..',
                'image' => $image,
                'imageLazy' => $imageLazy,
                'reviews' => $this->vfload->resolver('blog/review/get'),
                'keyword' => $keyword,
            );
    }

    public function getList($args)
    {
        $this->load->model($this->codename . '/blog');

        $posts = array();

        $filter_data = array(
                'filter_category_id' => $args['category_id'],
                'sort' => $args['sort'],
                'order' => $args['order'],
                'start' => ($args['page'] - 1) * $args['size'],
                'limit' => $args['size'],
            );

        if (!empty($args['search'])) {
            $filter_data['filter_name'] = $args['search'];
            $filter_data['filter_description'] = $args['search'];
        }

        $post_total = $this->model_d_vuefront_blog->getTotalPosts($filter_data);

        $results = $this->model_d_vuefront_blog->getPosts($filter_data);

        foreach ($results as $result) {
            $posts[] = $this->get(array('id' => $result['post_id']));
        }

        return array(
            'content' => $posts,
            'first' => $args['page'] === 1,
            'last' => $args['page'] === ceil($post_total / $args['size']),
            'number' => (int) $args['page'],
            'numberOfElements' => count($posts),
            'size' => (int) $args['size'],
            'totalPages' => (int) ceil($post_total / $args['size']),
            'totalElements' => (int) $post_total,
        );
    }
}
