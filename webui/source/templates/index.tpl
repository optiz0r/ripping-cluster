<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <title>Ripping Cluster WebUI</title>
        <script lang="javascript">
        </script>
        <link rel="stylesheet" type="text/css" href="{$base_uri}styles/normal.css" />
        
        <script type="text/javascript">
            var base_uri = "{$base_uri|escape:'quote'}";
            var base_url = "{$base_url|escape:'quote'}";
        </script>
        
        <link type="text/css" href="{$base_uri}styles/3rdparty/jquery-ui/smoothness/jquery-ui-1.8.custom.css" rel="Stylesheet" />	
		<script type="text/javascript" src="{$base_uri}scripts/3rdparty/jquery-1.4.2.js"></script>
		<script type="text/javascript" src="{$base_uri}scripts/3rdparty/jquery-ui-1.8.custom.min.js"></script>
		<script type="text/javascript" src="{$base_uri}scripts/main.js"></script>
    </head>
    <body>

        <div id="container">

            <div id="banner">
                <h1>Ripping Cluster WebUI</h1>
            </div>

            <div id="navigation">
                {include file="navigation.tpl"}
            </div>

            <div id="page-container">
            
                <div id="sidebar">
                    {include file="sidebar.tpl"}
                </div>

                <div id="page">

                    {if $messages}
                        <div id="messages">
                            {foreach from=$messages item=message}
                                {$message}
                            {/foreach}
                        </div>
                    {/if}

                    {$page_content}

                </div>

            </div>

            <div id="footer">
                Powered by RippingCluster WebUI {$version}. Written by Ben Roberts.
            </div>

        </div>
        
        <div id="centrepoint">
            <div id="dialog">
                <div id="dialogheader">
                    <div id="dialogheadertitle">Dialog</div>
                    <div id="dialogheaderclose">X</div>
                </div>
                <div id="dialogcontent"></div>
                <div id="dialogfooter">
                    <div id="dialogfooterok" class="dialogfooterbuttonset">
                        <fieldset>
                            <input type="button" class="dialogbutton" id="dialogfooterok" value="Ok" />
                        </fieldset>
                    </div>
                    <div id="dialogfooterokcancel" class="dialogfooterbuttonset">
                        <fieldset>
                            <input type="button" class="dialogbutton" id="dialogfooterokcancel_ok" value="Ok" />
                            <input type="button" class="dialogbutton" id="dialogfooterokcancel_cancel" value="Cancel" />
                        </fieldset>
                    </div>
                    <div id="dialogfooteryesno" class="dialogfooterbuttonset">
                        <fieldset>
                            <input type="button" class="dialogbutton" id="dialogfooteryes" value="Yes" />
                            <input type="button" class="dialogbutton" id="dialogfooterno" value="No" />
                        </fieldset>
                    </div>
                </div>
            </div>
        </div>

    </body>
</html>
