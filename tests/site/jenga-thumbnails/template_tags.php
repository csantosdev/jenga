<?php
namespace JengaThumbnails;
use Jenga\Template\BasicTemplate as Template;

function thumbnail_by_url($params, $template) {
	echo " INSIDE TEMPLATE TAG ";
	$obj = ['object data'];
	$template->assign('myvar', $obj);
}

Template::registerTag('thumbnail', 'JengaThumbnails\thumbnail_by_url');
