<?php
set_time_limit(0);

$strOut = '';
$strOut .= '<html>';
$strOut .= '<head>';
$strOut .= '<script type="text/javascript" src="jquery-1.6.2.js"></script>';
$strOut .= '<script type="text/javascript" >';
$strOut .= '$(document).ready(function(){
	
	$("li").click(function(){
		var obj = $(this).find("ul");
		obj.slideToggle(300);
		return false;
	});

	$("a").click(function(){
		$("#unitContainer").css("background","#cccccc");
		var link = $(this).attr("href");
		link = link.replace(/index.php/g,"runUnit.php");
		$.get(link, function(data) {
		  link = link.replace(/runUnit.php\?/g,"");
		  link = link.replace(/test=/g,"");
		  link = link.replace(/dir=/g,"");
		  $("#unitContainer").html("<br /><h2>"+link+"</h2>"+data);
  		  $("#unitContainer").css("background","#ffffff");
		});
		return false;
	})
	
});
';
$strOut .= '</script>';
$strOut .= '<style>
/*ul,li { list-style-type:none;  margin:0;padding:0;}
li:hover { background: #ffffff; }
li:hover ul li { background: #ff0000; }*/
</style>
';

	

$strOut .= '</head>';
$strOut .= '<body>';
$strOut .= '<table width="100%"><tr><td valign="top">';
$strOut .= '<div class="unittestFolder" style="margin:0 10px;background:#999;border:solid 1px #666;padding:0 20px;min-width:200px">';
$strOut .= '<h2>Auswahl der Unittest</h2>';
$strOut .= '<h3><a href="CheckTests.php">Fehlende Tests</a></h3>';
$strOut .= '<ul style="margin-left:20px;padding:0">';
$strOut .= '<li><a href="index.php?dir=" style="color:navy">Alle Test</a></li>';
$strOut .= searchTests('./jamwork/');
$strOut .= '</ul>';
$strOut .= '</div>';
$strOut .= '</td><td width="90%" valign="top" id="unitContainer"><br />';
$strOut .= '</td></tr></table>';
echo $strOut;
echo '</body>';
echo '</html>';

function searchTests($dir)
{
	$directory = dirname(__FILE__).'/'.$dir;
	$iterator = new \DirectoryIterator($directory);
	
	$strOut = '';
	
	foreach($iterator as $iteration)
	{
		if($iteration->isFile())
		{
			$namespace = str_replace('./jamwork', 'unittest\jamwork', $dir);
			$namespace = str_replace('/', '\\', $namespace);
			$className = $namespace.$iteration->getBasename('Test.php');
			$strOut .= '<li><a href="index.php?test='.$className.'" style="color:navy">'.$iteration->getBasename('Test.php').'</a></li>';
		}
		elseif(!$iteration->isDot())
		{
			$namespaceDir = str_replace('./jamwork/', '', $dir.$iteration->getFilename());
			$namespace = str_replace('/', '\\', $namespaceDir);
			$strOut .= '<li><a href="index.php?dir='.$namespaceDir.'" style="color:navy">'.$namespace.'</a>';
			$strOut .= '<ul  style="margin-left:15px;padding:0">';
			$strOut .= searchTests($dir.$iteration->getFilename().'/');
			$strOut .= '</li></ul>';
		}
	}
	return $strOut;
}
