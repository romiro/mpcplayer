<?php
class ElementController
{
    /**
     * Reference to front controller
     *
     * @var Controller
     */
    private $Controller;

    public function __construct(&$controller)
    {
        $this->Controller = $controller;
    }

    public function addJs($file)
    {
        if (substr($file, -3) !== '.js') {
            $file .= '.js';
        }
        $this->Controller->vars['js'][] = $file;
    }

    public function render($element = '')
    {
        if ( empty($element) ) {
            throw new Exception("Element $element needs to be set!");
        }

        extract($this->Controller->vars);

        if ( method_exists($this, $element) ) {
            extract( (array)$this->$element() );
        }
        require_once(BASEDIR."/views/elements/$element.tpl");
    }

    public function filelist()
    {
        $mp3s = $directories = $current = array();

        if (!empty($_GET['dir'])) {
            $currentDir = urldecode($_GET['dir']);
        }
        else {
            $currentDir = '';
        }

        $contents = mpc::ls($currentDir);
        natcasesort($contents);

        foreach($contents as $value)
        {
            if (strpos($value, '/') > 0) {
                $value = substr($value, strrpos($value, '/')+1);
            }
            if (strtolower(substr($value, -4)) == ".mp3") {
                $mp3s[] = $value;
            }
            else {
                $directories[$value] = $currentDir.'/'.$value;
            }
        }

        if (!empty($currentDir)) {
            $directories = array( 'up' => substr($currentDir, 0, strrpos($currentDir, '/')) ) + $directories;
        }

        return array('files'=>$mp3s, 'directories'=>$directories, 'currentDir'=>$currentDir);
    }

    public function controlpanel()
    {

    }

    public function playlist()
    {
//        $playlists = mpc::getAllPlaylists();
//        foreach($playlists as $key=>$value) {
//            $playlists[$key] = str_replace('_', ' ', $value);
//        }
//        return array('playlists'=>$playlists);
    }
}