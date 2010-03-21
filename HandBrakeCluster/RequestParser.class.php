<?php

class HandBrakeCluster_RequestParser {

    private $request_string;
    private $page = array();
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

        // Read through the components list looking for elements matching known directories and files
        // to determine which page this request is for
        $base_dir = 'templates';
        while (true) {
            if ($components && ! $components[0]) {
                // Skip over any empty components before we find a page
                array_shift($components);
            }
            
            if ($components && is_dir($base_dir . '/' . $components[0])) {
                $base_dir .= '/' . $components[0];
                array_push($this->page, array_shift($components));
            } elseif ($components && is_file($base_dir . '/' . $components[0] . '.tpl')) {
                // We have found a valid page, so break the loop here,
                // leaving the remaining components as key/value pairs
                array_push($this->page, array_shift($components));
                break;
            } else {
                // See if we've already seen a component and assumed it referred to a dir when a file of the same name exists
                if ($this->page && is_file($base_dir . '.tpl')) {
                    break;
                } elseif ( ! $components && is_file($base_dir . '/index.tpl')) {
                    // The last component in the path was a valid directory, and a directory index exists
                    array_push($this->page, 'index');
                    break; 
                } else {
                    // No valid page was found, so display an error page
                    $this->page = array('404');
                    return;
                }
            }
        }

        // The subsequent tokens are parameters for this page in key value pairs
        while ($components) {
            $this->vars[array_shift($components)] = $components ? array_shift($components) : null;
        }
    }

    public function page() {
        return join('/', $this->page);
    }

    public function get($key) {
        if (isset($this->vars[$key])) {
            return $this->vars[$key];
        }

        return null;
    }
    
    public function request_string() {
        return $this->request_string;
    }

};

?>
