<?php

class HandBrakeCluster_RequestParser {

    private $request_string;
    private $page = 'home';
    private $vars = array();

    public function __construct($request_string) {
        $this->request_string = $request_string;

        $this->parse();
    }

    public function parse() {
        if (!$this->request_string) {
            return;
        }

        $components = explode('/', $this->request_string);
        if (!$components) {
            return;
        }

        // The first token is the page to execute
        $this->page = array_shift($components);

        // The subsequent tokens are parameters for this page in key value pairs
        while ($components) {
            $this->vars[array_shift($components)] = $components ? array_shift($components) : null;
        }
    }

    public function page() {
        return $this->page;
    }

    public function get($key) {
        if (isset($this->vars[$key])) {
            return $this->vars[$key];
        }

        return null;
    }

};

?>
