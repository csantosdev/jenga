<?php /* Smarty version Smarty-3.1.15, created on 2013-10-12 16:26:36
         compiled from "/var/www/Jenga/icandyclothing/templates/index.html" */ ?>
<?php /*%%SmartyHeaderCode:15160168525259b07c0662c9-63797786%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ff981d414cbcbd927e2f4561d2b54b254f073586' => 
    array (
      0 => '/var/www/Jenga/icandyclothing/templates/index.html',
      1 => 1381201717,
      2 => 'file',
    ),
    '01deb4e2c58404472d3897dcdbf9a93b7a198418' => 
    array (
      0 => '/var/www/Jenga/icandyclothing/templates/base.html',
      1 => 1380930911,
      2 => 'file',
    ),
    'f08afbf752bfd80e2c582bff3d8442b6f85083ce' => 
    array (
      0 => '/var/www/Jenga/icandyclothing/templates/header.html',
      1 => 1380930326,
      2 => 'file',
    ),
    'ea7d3f72a04105c37d5ba13d9f8fbef18221c01a' => 
    array (
      0 => '/var/www/Jenga/icandyclothing/templates/footer.html',
      1 => 1380930281,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '15160168525259b07c0662c9-63797786',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.15',
  'unifunc' => 'content_5259b07c12a907_03693161',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5259b07c12a907_03693161')) {function content_5259b07c12a907_03693161($_smarty_tpl) {?><!doctype html>
<html>
	
<head>
	
</head>
<body>
	
	<?php /*  Call merged included template "header.html" */
$_tpl_stack[] = $_smarty_tpl;
 $_smarty_tpl = $_smarty_tpl->setupInlineSubTemplate("header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0, '15160168525259b07c0662c9-63797786');
content_5259b07c0f1309_19560285($_smarty_tpl);
$_smarty_tpl = array_pop($_tpl_stack); 
/*  End of included template "header.html" */?>

	
	<div>Name: <?php echo $_smarty_tpl->tpl_vars['name']->value;?>
</div>
	<?php echo Thumbnails\thumbnail_by_url(array(),$_smarty_tpl);?>

	<?php echo $_smarty_tpl->tpl_vars['myvar']->value[0];?>


	
	<?php /*  Call merged included template "footer.html" */
$_tpl_stack[] = $_smarty_tpl;
 $_smarty_tpl = $_smarty_tpl->setupInlineSubTemplate("footer.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0, '15160168525259b07c0662c9-63797786');
content_5259b07c122298_75268653($_smarty_tpl);
$_smarty_tpl = array_pop($_tpl_stack); 
/*  End of included template "footer.html" */?>

</body>
</html><?php }} ?>
<?php /* Smarty version Smarty-3.1.15, created on 2013-10-12 16:26:36
         compiled from "/var/www/Jenga/icandyclothing/templates/header.html" */ ?>
<?php if ($_valid && !is_callable('content_5259b07c0f1309_19560285')) {function content_5259b07c0f1309_19560285($_smarty_tpl) {?><div>{{ HEADER }}</div><?php }} ?>
<?php /* Smarty version Smarty-3.1.15, created on 2013-10-12 16:26:36
         compiled from "/var/www/Jenga/icandyclothing/templates/footer.html" */ ?>
<?php if ($_valid && !is_callable('content_5259b07c122298_75268653')) {function content_5259b07c122298_75268653($_smarty_tpl) {?><div>{{ FOOTER }}</div>
<?php }} ?>
