<?php
/**
 * Base Controller
 */
abstract class ControllerBase {
    
    protected $view;
    protected $root;
    protected $path;
    protected $utils;
    protected $errorMessage;
    protected $timezone;

    function __construct()
    {
        $this->view = new View();
        $this->utils = new Utils();
        $this->errorMessage = new ErrorMessage();

        $config = Config::singleton();
        $this->root = $config->get('rootPath');
        $this->path = $config->get('physicalPath');
        $this->timezone = $config->get('timezone');
    }
}
?>