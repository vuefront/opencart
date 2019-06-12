<?php

class ControllerExtensionDVuefrontCommonPage extends Controller
{
    private $codename = "d_vuefront";

    public function get($args)
    {
        $this->load->model('extension/'.$this->codename.'/page');
        $information_info = $this->model_extension_d_vuefront_page->getPage($args['id']);
        $category_keyword = $this->model_extension_d_vuefront_page->getPageKeyword($args['id']);

        if (!empty($category_keyword['keyword'])) {
            $keyword = $category_keyword['keyword'];
        } else {
            $keyword = '';
        }

        return array(
            'id'          => $information_info['information_id'],
            'title'        => $information_info['title'],
            'name'        => $information_info['title'],
            'description' => html_entity_decode($information_info['description'], ENT_QUOTES, 'UTF-8'),
            'sort_order' => (int)$information_info['sort_order'],
            'keyword' => $keyword
        );
    }

    public function getList($args)
    {
        $this->load->model('extension/'.$this->codename.'/page');

        if (in_array($args['sort'], array('sort_order', 'title'))) {
            $args['sort'] = 'i.' . $args['sort'];
        } elseif (in_array($args['sort'], array('name'))) {
            $args['sort'] = 'id.' . $args['sort'];
        }

        $posts = array();

        $filter_data = array(
            'sort' => $args['sort'],
            'order' => $args['order'],
            'start' => ($args['page'] - 1) * $args['size'],
            'limit' => $args['size']
        );

        if (!empty($args['search'])) {
            $filter_data['filter_title'] = $args['search'];
            $filter_data['filter_description'] = $args['search'];
        }
        
        $page_total = $this->model_extension_d_vuefront_page->getTotalPages($filter_data);

        $results = $this->model_extension_d_vuefront_page->getPages($filter_data);

        foreach ($results as $result) {
            $posts[] = $this->get(array('id' => $result['information_id']));
        }

        return array(
            'content' => $posts,
            'first' => $args['page'] === 1,
            'last' => $args['page'] === ceil($page_total / $args['size']),
            'number' => (int)$args['page'],
            'numberOfElements' => count($posts),
            'size' => (int)$args['size'],
            'totalPages' => (int)ceil($page_total / $args['size']),
            'totalElements' => (int)$page_total,
        );
    }
}
