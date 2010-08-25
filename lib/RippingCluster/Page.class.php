<?php

class RippingCluster_Page {

    private $smarty;
    private $request;

    private $page;

    public function __construct(Smarty $smarty, RippingCluster_RequestParser $request) {
        $this->smarty = $smarty;
        $this->request = $request;
        $this->page = $request->page();
    }

    public function page() {
        return $this->page;
    }

    public function template_filename() {
        return $this->page . '.tpl';
    }

    public function evaluate($template_variables = array()) {
        $code_filename     = $this->page . '.php';
        $template_filename = $this->template_filename();

        try {
            $this->render($template_filename, $code_filename, $template_variables);
        } catch (RippingCluster_Exception_AbortEntirePage $e) {
            return false;
        } catch (RippingCluster_Exception_FileNotFound $e) {
            $this->render('errors/404.tpl', 'errors/404.php');
        } catch (RippingCluster_Exception $e) {
            $this->render('errors/unhandled-exception.tpl', 'errors/unhandled-exception.php', array(
                'exception' => $e,
            ));
        } 
        
        return true;
    }
    
    protected function render($template_filename, $code_filename = null, $template_variables = array()) {
        if ( ! $this->smarty->template_exists($template_filename)) {
            throw new RippingCluster_Exception_FileNotFound($template_filename);
        }
        
        // Copy all the template variables into the namespace for this function,
        // so that they are readily available to the template
        foreach ($template_variables as $__k => $__v) {
            $$__k = $__v;
        }
        
        // Include the template code file, which will do all the work for this page
        $real_code_filename = 'pages/' . $code_filename;
        if ($code_filename && file_exists($real_code_filename)) {
            include $real_code_filename;
        }
        
        // Now execute the template itself, which will render the results of the code file
        $this->smarty->assign('page_content', $this->smarty->fetch($template_filename));
    }
    
    public static function redirect($relative_url) {
        $absolute_url = RippingCluster_Main::instance()->absoluteUrl($relative_url);
        
        header("Location: $absolute_url");
        
        throw new RippingCluster_Exception_AbortEntirePage();
    }

};

?>
