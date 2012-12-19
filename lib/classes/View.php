<?php
class View
{
    /**
     * @var Controller
     */
    public $Controller;
    /**
     * @var ElementController
     */
    public $ElementController;
    public $viewFile;
    public $layoutFile;
    public function __construct(&$controller)
    {
        $this->Controller = $controller;
        $this->ElementController = new ElementController($controller);
        $this->Controller->vars['dog'] = 'ok';
    }

    public function element($element)
    {

        $this->ElementController->render($element);
    }

    public function render()
    {
        $viewPath = "views/$this->viewFile.tpl";
        $layoutPath = "views/layout_$this->layoutFile.tpl";
        if (!is_file($viewPath)) {
            exit("Cannot find view file: $viewPath");
        }
        if (!is_file($layoutPath)) {
            exit("Cannot find layout file: $layoutPath");
        }

        extract($this->Controller->vars);
        ob_start();
        require_once($viewPath);
        $content = ob_get_clean();

        extract($this->Controller->vars);
        require_once($layoutPath);
    }

    public function layoutScripts()
    {
        $out = '';
        if (!empty($this->Controller->vars['js'])) {
            foreach($this->Controller->vars['js'] as $script) {
                $out .= "<script type='text/javascript' src='/js/$script'></script>";
            }
        }
        return $out;
    }

    public function runControllerMethod($method)
    {
        $method = "_$method";
        if (!method_exists('Controller', $method)) {
//            throw new
            exit("Cannot find controller method <b>$method</b>");
        }
        $return = $this->controller->$method();
        return $return;
    }
}