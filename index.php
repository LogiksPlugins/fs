<?php
if(!defined('ROOT')) exit('No direct script access allowed');
session_check(true);
$webPath=getWebPath(__FILE__);

$extraURL="";
/*

if(isAdminSite() && isset($_REQUEST['forsite'])) {
	$extraURL="&forsite=".$_REQUEST['forsite'];
}
*/
if(isset($_REQUEST["media"])) {
	$extraURL="&media={$_REQUEST["media"]}";
}
?>
<link rel="stylesheet" href="<?=$webPath?>css/elfinder.css" type="text/css" media="screen" title="no title" charset="utf-8">

<script type="text/javascript" src="<?=$webPath?>js/elfinder.full.js" charset="utf-8"></script>

<style type="text/css">
	#finder {width:100%;}
	body {overflow:hidden;}
</style>
<div id="finder"></div>
<script>
jQuery(function() {
	//alert(getServiceCMD("fs"));
	$("body").attr("oncontextmenu",'return false');
	var f = $('#finder').elfinder({
			url : getServiceCMD("fs")+"<?=$extraURL?>",//'services/?scmd=fs&site=< ?=SITENAME.$extraURL?>',
			lang : 'en',
			docked : true,
			height:$(window).height()-60,
			dialog : {
			 	title : 'File manager',
			 	height : 500
			},
			toolbar:[
					['back', 'reload'],
					['select', 'open'],
					['mkdir', 'mkfile', 'upload'],
					['copy', 'paste', 'rm'],
					['rename', 'edit', 'editsrc'],
					['info', 'quicklook', 'resize'],
					['icons', 'list'],
				],
			onOpen:"openLink",
			onEditSrc:"openEditor",
			//Callback example
			//editorCallback : function(url) {
			//	if (window.console && window.console.log) {
			//		window.console.log(url);
			//	} else {
			//		alert(url);
			//	}
			//},
			//closeOnEditorCallback : true
		});
});
function openLink(lnk,title) {
	lgksOverlayFrame(lnk,"File Viewer");
}
function openEditor(lnk,title) {
	parent.openInNewTab(title,lnk);
}
</script>
