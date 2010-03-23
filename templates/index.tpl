<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <title>HandBrake Cluster WebUI</title>
        <script lang="javascript">
        </script>
        <link rel="stylesheet" type="text/css" href="{$base_uri}styles/normal.css" />
        
        <link type="text/css" href="{$base_uri}styles/3rdparty/jquery-ui/smoothness/jquery-ui-1.8.custom.css" rel="Stylesheet" />	
		<script type="text/javascript" src="{$base_uri}scripts/3rdparty/jquery-1.4.2.js"></script>
		<script type="text/javascript" src="{$base_uri}scripts/3rdparty/jquery-ui-1.8.custom.min.js"></script>
    </head>
    <body>

        <div id="container">

            <div id="banner">
                <h1>HandBrake Cluster WebUI</h1>
            </div>

            <div id="navigation">
                {include file=navigation.tpl}
            </div>

            <div id="page-container">
            
                <div id="sidebar">
                    {include file=sidebar.tpl}
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
                Powered by HandBrakeCluster WebUI {$version}. Written by Ben Roberts.
            </div>

        </div>

    </body>
</html>
