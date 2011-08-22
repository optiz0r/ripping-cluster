"page_replacements": {
	"source-list": {
		{include file="fragments/source-list.tpl" assign="sources_html"}
		"content": {$sources_html|json_encode}
	}
}