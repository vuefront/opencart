<?php

class VfLoad
{
    private $registry;
    private $codename = "d_vuefront";
    public function __construct($registry)
    {
        $this->registry = $registry;
    }
    

    public function controller($route, $args, $root = false)
    {
        if(!$root) {
            $route = $this->codename . '/'.$route;
        }
        $action = new Action($route, $args);

        if (file_exists($action->getFile())) {
            require_once($action->getFile());

            $class = $action->getClass();

            $controller = new $class($this->registry);

            return $controller->{$action->getMethod()}($action->getArgs());
        } else {
            trigger_error('Error: Could not load controller ' . $route . '!');
            exit();
        }
    }

    public function resolver($route)
    {
        $that = $this;
        return function ($root, $args) use ($that, $route) {
            $action = new Action($this->codename . '/'.$route, array(
                'parent' => $root,
                'args' => $args
            ));

            if (file_exists($action->getFile())) {
                require_once($action->getFile());
    
                $class = $action->getClass();
    
                $controller = new $class($this->registry);
    
                return $controller->{$action->getMethod()}($action->getArgs());
            } else {
                trigger_error('Error: Could not load controller ' . $route . '!');
                exit();
            }
        };
    }

    public function data($route, $data = array())
    {
        return $this->controller($route, $data);
    }
}
class ModelModuleDVuefront extends Model
{

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
