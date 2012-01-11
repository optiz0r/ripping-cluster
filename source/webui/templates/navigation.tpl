<a class="brand" href="{$base_uri}home/">RippingCluster</a>

<ul class="nav">
    <li {if $requested_page == "home"}class="active"{/if}>
        <a href="{$base_uri}home/" title="Home">Home</a>
    </li>
    
    <li {if $requested_page == "jobs"}class="active"{/if}>
        <a href="{$base_uri}jobs/" title="Jobs">Jobs</a>
    </li>
    
    <li {if $requested_page == "logs"}class="active"{/if}>
        <a href="{$base_uri}logs/" title="Logs">Logs</a>
    </li>
    
    <li class="dropdown {if $requested_page == "sources"}active{/if}" data-dropdown="dropdown">
        <a href="#" class="dropdown-toggle" title="Sources">Sources</a>
        <ul class="dropdown-menu">
            <li><a href="{$base_uri}sources/list/" title="All Sources">All</a></li>
        </ul>
    </li>
    
    <li class="dropdown" data-dropdown="dropdown">
        <a href="#" class="dropdown-toggle" title="Admin">Admin</a>
        <ul class="dropdown-menu">
            <li><a href="{$base_uri}admin/settings/" title="Settings">Settings</a></li>
        </ul>
    </li>
    
</ul>

