<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <title>{$title}</title>

        <!-- JQuery //-->
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js"></script>
        <link type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/themes/smoothness/jquery-ui.css" rel="Stylesheet" />
        <!-- JQuery Plugins //-->
        <script type="text/javascript" src="{$base_uri}scripts/3rdparty/jquery.chained.js"></script>
        <script type="text/javascript" src="{$base_uri}scripts/3rdparty/jquery.jec-1.3.2.js"></script>
        <script type="text/javascript" src="{$base_uri}scripts/3rdparty/jquery.asmselect.js"></script>
        <link type="text/css" href="{$base_uri}styles/3rdparty/jquery.asmselect.css" rel="Stylesheet" />    
        <script type="text/javascript" src="{$base_uri}scripts/3rdparty/jquery.progressbar.min.js"></script>
        
        <!-- Less //-->
        <script type="text/javascript" src="{$base_uri}scripts/3rdparty/less-1.1.5.min.js"></script>
        
        <!-- Bootstrap //-->
        <link rel="stylesheet" href="http://twitter.github.com/bootstrap/1.4.0/bootstrap.min.css">
        <script type="text/javascript" src="{$base_uri}scripts/3rdparty/bootstrap-alerts.js"></script>
        <script type="text/javascript" src="{$base_uri}scripts/3rdparty/bootstrap-twipsy.js"></script>
        <script type="text/javascript" src="{$base_uri}scripts/3rdparty/bootstrap-popover.js"></script>
        <script type="text/javascript" src="{$base_uri}scripts/3rdparty/bootstrap-dropdown.js"></script>
        <script type="text/javascript" src="{$base_uri}scripts/3rdparty/bootstrap-tabs.js"></script>
        <script type="text/javascript" src="{$base_uri}scripts/3rdparty/bootstrap-modal.js"></script>
        
        <!-- Local //-->
        <script type="text/javascript" src="{$base_uri}scripts/sihnon-js-lib/sihnon-framework.js"></script>
        <script type="text/javascript" src="{$base_uri}scripts/main.js"></script>
        
        <link rel="stylesheet/less" href="{$base_uri}less/bootstrap.less" media="all" />
        <link rel="stylesheet" type="text/css" href="{$base_uri}styles/normal.css" />

        <script type="text/javascript">
            var base_uri = "{$base_uri|escape:'quote'}";
            var base_url = "{$base_url|escape:'quote'}";
        </script>
        
    </head>
    <body>
        <div class="topbar no-print">
            <div class="topbar-inner">
                <div class="container-fluid">
                    {$page->include_template('navigation')}
                </div><!-- /tobar-inner -->
            </div><!-- /container-fliud -->
        </div><!-- /topbar -->

        <div class="container">
            <div class="row">
                <div class="span16">
                    <h1>RippingCluster WebUI</h1>
                </div>
            </div>
        
            <div class="row">
                {if ! $messages}
                    {$session = RippingCluster_Main::instance()->session()}
                    {$messages = $session->get('messages')}
                    {$session->delete('messages')}
                {/if}
                {if $messages}
                    <div id="messages">
                        {foreach from=$messages item=message}
                            {if is_array($message)}
                                {$severity=$message['severity']}
                                <div class="alert-message {$severity}">
                                    {$message['content']|escape:html}
                                </div>
                            {else}
                                <div class="alert-message info">
                                    {$message|escape:html}
                                </div>
                            {/if}
                        {/foreach}
                    </div><!-- /messages -->
                {/if}

                <div id="page_content">
                    {$page_content}
                </div>
            </div>
    
            <footer class="no-print">
                <p>
                    Powered by RippingCluster WebUI {$version}. Written by
                    <a xmlns:cc="http://creativecommons.org/ns#" href="http://benroberts.net" property="cc:attributionName" rel="cc:attributionURL">Ben Roberts</a>
                </p>
                <p>
                    This work is licensed under a <a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/3.0/">Creative Commons Attribution-NonCommercial-ShareAlike 3.0 Unported License</a>.
                    <br />
                    
                    <a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/3.0/">
                        <img alt="Creative Commons Licence" style="border-width:0" src="http://i.creativecommons.org/l/by-nc-sa/3.0/88x31.png" />
                    </a>
                </p>
            </footer>

        </div>
        
        <div id="dialog" class="modal hide fade">
            <div class="modal-header">
                <a href="#" class="close">&times;</a>
                <h3 id="dialog-header-title"></h3>
            </div>
            <div id="dialog-body" class="modal-body"></div>
            <div id="dialog-footer" class="modal-footer">
                <div id="dialog-footer-ok" class="dialog-footer-buttonset">
                    <fieldset>
                        <input type="button" class="btn primary" id="dialog-footer-ok-ok" value="Ok" />
                    </fieldset>
                </div>
                <div id="dialog-footer-okcancel" class="dialog-footer-buttonset">
                    <fieldset>
                        <input type="button" class="btn primary" id="dialog-footer-okcancel-ok" value="Ok" />
                        <input type="button" class="btn secondary" id="dialog-footer-okcancel-cancel" value="Cancel" />
                    </fieldset>
                </div>
                <div id="dialog-footer-yesno" class="dialog-footer-buttonset">
                    <fieldset>
                        <input type="button" class="btn primary" id="dialog-footer-yesno-yes" value="Yes" />
                        <input type="button" class="btn secondary" id="dialog-footer-yesno-no" value="No" />
                    </fieldset>
                </div>
            </div>
        </div>

    </body>
</html>
