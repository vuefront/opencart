<?php
use GraphQL\Error\ClientAware;

class MySafeException extends \Exception implements ClientAware
{
    public function isClientSafe()
    {
        return true;
    }

    public function getCategory()
    {
        return 'businessLogic';
    }
}
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
}
