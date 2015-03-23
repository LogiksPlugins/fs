<?php
if(!defined('ROOT')) exit('No direct script access allowed');
checkServiceSession();
//isAdminSite();

@ini_set("error_reporting",E_ERROR);

$rootAlias="Home";
$subApp="";
$json=loadFeature("fs");
if(!$json) {
	$json=array();
}
if(SITENAME=='admincp' && $_SESSION["SESS_PRIVILEGE_ID"]<=2) {
	$docroot=ROOT;
	$docurl=SiteLocation;
	$rootAlias="<span style='color:red'>ROOT</span>";
	$json['dirs_access']['default']=array();
} elseif(SITENAME=='cms') {
	if(isset($_REQUEST['forsite'])) {
		$docroot=ROOT.APPS_FOLDER.$_REQUEST['forsite']."/";
		$docurl=SiteLocation.APPS_FOLDER.$_REQUEST['forsite']."/";
		if(isset($_REQUEST["media"])) {
			$docroot.=$_REQUEST["media"]."/";
			$docurl.=$_REQUEST["media"]."/";
		}
		$rootAlias=strtoupper($_REQUEST['forsite']);
		$subApp=$_REQUEST['forsite'];
	} else {
		$docroot=ROOT.APPS_FOLDER.$_SESSION["LGKS_CMS_SITE"]."/";
		$docurl=SiteLocation.APPS_FOLDER.$_SESSION["LGKS_CMS_SITE"]."/";
		if(isset($_REQUEST["media"])) {
			$docroot.=$_REQUEST["media"]."/";
			$docurl.=$_REQUEST["media"]."/";
		}
		$rootAlias=strtoupper($_SESSION["LGKS_CMS_SITE"]);
		$subApp=$_SESSION["LGKS_CMS_SITE"];
	}
} /*elseif(isset($_REQUEST["docroot"])) {
	$docroot=$_REQUEST["docroot"];
	$docurl=SiteLocation.$_REQUEST["docroot"];
	if(isset($_REQUEST["media"])) {
		$docroot.=$_REQUEST["media"]."/";
		$docurl.=$_REQUEST["media"]."/";
	}
} */
else {
	$userDir="userdata/";
	if(isset($json['default_user_folders'][SITENAME])) {
		$ur=$json['default_user_folders'][SITENAME];
		if(is_array($ur)) {
			if(isset($ur[$_SESSION['SESS_PRIVILEGE_ID']])) $userDir=$ur[$_SESSION['SESS_PRIVILEGE_ID']];
			elseif(isset($ur[$_SESSION['SESS_USER_ID']])) $userDir=$ur[$_SESSION['SESS_USER_ID']];
			else {
				exit("No User Access Defined");
			}
		} else {
			$userDir=$ur;
		}
	} else {
		$userDir=$json['default_user_folders']['default'];
	}
	$userDir=str_replace("#userid#",$_SESSION['SESS_USER_ID'],$userDir);
	$userDir=str_replace("#privilgeid#",$_SESSION['SESS_PRIVILEGE_ID'],$userDir);
	if(isset($_REQUEST["media"])) $userDir=str_replace("#media#",$_REQUEST["media"],$userDir);
	else $userDir=str_replace("#media#","",$userDir);

	$userDir=str_replace("//","/",$userDir);

	$docroot=ROOT.APPS_FOLDER.SITENAME."/$userDir";
	$docurl=SiteLocation.APPS_FOLDER.SITENAME."/$userDir";

	$subApp=SITENAME;
}
loadModuleLib("fs","elFinder.class");

$opts = array(
	'root'            => $docroot,                        // path to root directory
	'URL'             => $docurl, // root directory URL
	'rootAlias'       => $rootAlias,       // display this instead of root directory name
	//'uploadAllow'   => array('images/*'),
	//'uploadDeny'    => array('all'),
	//'uploadOrder'   => 'deny,allow'
	//'disabled'     => array(),      // list of not allowed commands
	 'dotFiles'     => false,        // display dot files
	 'showNoAccess' => false,	// display a file even if no access (rw) permissions
	// 'dirSize'      => true,         // count total directories sizes
	 'fileMode'     => 0666,         // new files mode
	 'dirMode'      => 0777,         // new folders mode
	 'mimeDetect'   => 'internal',       // files mimetypes detection method (finfo, mime_content_type, linux (file -ib), bsd (file -Ib), internal (by extensions))
	// 'uploadAllow'  => array(),      // mimetypes which allowed to upload
	// 'uploadDeny'   => array(),      // mimetypes which not allowed to upload
	// 'uploadOrder'  => 'deny,allow', // order to proccess uploadAllow and uploadAllow options
	 'imgLib'       => 'auto',       // image manipulation library (imagick, mogrify, gd)
	 'tmbDir'       => CACHE_FOLDER.'fs/',       // directory name for image thumbnails. Set to "" to avoid thumbnails generation
	 'tmbCleanProb' => 1,            // how frequiently clean thumbnails dir (0 - never, 100 - every init request)
	 'tmbAtOnce'    => 5,            // number of thumbnails to generate per request
	 'tmbSize'      => 48,           // images thumbnails size (px)
	 'tmbCrop'      => true,         // crop thumbnails (true - crop, false - scale image to fit thumbnail size)
	 'fileURL'      => false,         // display file URL in "get info"
	 'dateFormat'   => 'j M Y h:i A',  // file modification date format
	// 'logger'       => null,         // object logger
	 'defaults'     => array(        // default permisions
			'read'   => true,
			'write'  => true,
			'rm'     => true
	   ),
	// 'perms'        => array(),      // individual folders/files permisions
	 'debug'        => false,         // send debug to client
	// 'archiveMimes' => array(),      // allowed archive's mimetypes to create. Leave empty for all available types.
	// 'archivers'    => array()       // info about archivers to use. See example below. Leave empty for auto detect
	// 'archivers' => array(
	// 	'create' => array(
	// 		'application/x-gzip' => array(
	// 			'cmd' => 'tar',
	// 			'argc' => '-czf',
	// 			'ext'  => 'tar.gz'
	// 			)
	// 		),
	// 	'extract' => array(
	// 		'application/x-gzip' => array(
	// 			'cmd'  => 'tar',
	// 			'argc' => '-xzf',
	// 			'ext'  => 'tar.gz'
	// 			),
	// 		'application/x-bzip2' => array(
	// 			'cmd'  => 'tar',
	// 			'argc' => '-xjf',
	// 			'ext'  => 'tar.bz'
	// 			)
	// 		)
	// 	)
);
if(isset($json['params'])) {
	foreach($opts as $a=>$b) {
		if($a=='fileMode') $json['params'][$a]=octdec($json['params'][$a]);
		elseif($a=='dirMode') $json['params'][$a]=octdec($json['params'][$a]);
		if(isset($json['params'][$a])) $opts[$a]=$json['params'][$a];
	}
}
if(isset($json['invisible_dirs']['default'])) {
	foreach($json['invisible_dirs']['default'] as $a=>$b) {
		$opts['perms'][$a]=$b;
	}
}
if(strlen($subApp)>0 && isset($json['invisible_dirs'][$subApp])) {
	foreach($json['invisible_dirs'][$subApp] as $a=>$b) {
		$opts['perms'][$a]=$b;
	}
}
if(SITENAME=='cms') {
	if(strlen($subApp)>0 && isset($json['cms_params'][$subApp])) {
		foreach($json['cms_params'][$subApp] as $a=>$b) {
			$opts[$a]=$b;
		}
	}
} else {
	if(strlen($subApp)>0 && isset($json['app_params'][$subApp])) {
		foreach($json['app_params'][$subApp] as $a=>$b) {
			$opts[$a]=$b;
		}
	}
}

//printArray($opts);exit($subApp);
if(isset($_REQUEST["cmd"]) && $_REQUEST["cmd"]=="editsrc") {
	$fm = new elFinder($opts);
	$dir = $fm->_findDir(trim($_GET['current']));
	$file = $fm->_find(trim($_GET['target']), $dir);

	$file=substr($file,strlen($docroot));

	$lnk="../index.php?site=".SITENAME;
	if(isset($_REQUEST['forsite']))
		$lnk.="&forsite={$_REQUEST['forsite']}";
	$lnk.="&page=codeeditor&file=$file";
	header("Location:$lnk");

	exit();
} else {
	$fm = new elFinder($opts);
	$fm->run();
}
?>
