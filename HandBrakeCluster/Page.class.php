<?php

class HandBrakeCluster_Page {

    private $smarty;
    private $request;

    private $page;

    public function __construct(Smarty $smarty, HandBrakeCluster_RequestParser $request) {
        $this->smarty = $smarty;
        $this->request = $request;
        $this->page = $request->page();
    }

    public function page() {
        return $this->page;
    }

    public function template_filename() {
        $template_filename = $this->page . '.tpl';

        if (!$this->smarty->template_exists($template_filename)) {
            $template_filename = 'home.tpl';
        }

        return $template_filename;
    }

    public function evaluate() {
        $code_filename = 'pages/' . $this->page . '.php';

        if (file_exists($code_filename)) {
            eval("include '$code_filename';");
        }
        
    }

};

?>
