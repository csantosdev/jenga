<?php /* Smarty version Smarty-3.1.15, created on 2013-10-07 23:08:39
         compiled from "/var/www/Jenga/tests/site/icandyclothing/templates/index.html" */ ?>
<?php /*%%SmartyHeaderCode:699506027524f53049fa0c6-39164149%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6e6306677160311a35b1bab84e4ed79e38514e84' => 
    array (
      0 => '/var/www/Jenga/tests/site/icandyclothing/templates/index.html',
      1 => 1381201717,
      2 => 'file',
    ),
    'd9cd2318bde0a85f27961e1935af663c903aed71' => 
    array (
      0 => '/var/www/Jenga/tests/site/icandyclothing/templates/base.html',
      1 => 1380930911,
      2 => 'file',
    ),
    'f142462a56dd44d82b02b97973838b815d57e969' => 
    array (
      0 => '/var/www/Jenga/tests/site/icandyclothing/templates/header.html',
      1 => 1380930326,
      2 => 'file',
    ),
    'a59bf412e26dcdfee373ced76279c544d36538b5' => 
    array (
      0 => '/var/www/Jenga/tests/site/icandyclothing/templates/footer.html',
      1 => 1380930281,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '699506027524f53049fa0c6-39164149',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.15',
  'unifunc' => 'content_524f5304a6a0c6_58547943',
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_524f5304a6a0c6_58547943')) {function content_524f5304a6a0c6_58547943($_smarty_tpl) {?><!doctype html>
<html>
	
<head>
	
</head>
<body>
	
	<?php /*  Call merged included template "header.html" */
$_tpl_stack[] = $_smarty_tpl;
 $_smarty_tpl = $_smarty_tpl->setupInlineSubTemplate("header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0, '699506027524f53049fa0c6-39164149');
content_525377379c27b9_37709761($_smarty_tpl);
$_smarty_tpl = array_pop($_tpl_stack); 
/*  End of included template "header.html" */?>

	
	<div>Name: <?php echo $_smarty_tpl->tpl_vars['name']->value;?>
</div>
	<?php echo JengaThumbnails\thumbnail_by_url(array(),$_smarty_tpl);?>

	<?php echo $_smarty_tpl->tpl_vars['myvar']->value[0];?>


	
	<?php /*  Call merged included template "footer.html" */
$_tpl_stack[] = $_smarty_tpl;
 $_smarty_tpl = $_smarty_tpl->setupInlineSubTemplate("footer.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0, '699506027524f53049fa0c6-39164149');
content_52537737afdf86_84919650($_smarty_tpl);
$_smarty_tpl = array_pop($_tpl_stack); 
/*  End of included template "footer.html" */?>

</body>
</html><?php }} ?>
<?php /* Smarty version Smarty-3.1.15, created on 2013-10-07 23:08:39
         compiled from "/var/www/Jenga/tests/site/icandyclothing/templates/header.html" */ ?>
<?php if ($_valid && !is_callable('content_525377379c27b9_37709761')) {function content_525377379c27b9_37709761($_smarty_tpl) {?><div>{{ HEADER }}</div><?php }} ?>
<?php /* Smarty version Smarty-3.1.15, created on 2013-10-07 23:08:39
         compiled from "/var/www/Jenga/tests/site/icandyclothing/templates/footer.html" */ ?>
<?php if ($_valid && !is_callable('content_52537737afdf86_84919650')) {function content_52537737afdf86_84919650($_smarty_tpl) {?><div>{{ FOOTER }}</div>
<?php }} ?>
