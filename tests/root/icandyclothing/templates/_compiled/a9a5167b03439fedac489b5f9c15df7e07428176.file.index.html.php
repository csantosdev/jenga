<?php /* Smarty version Smarty-3.1.15, created on 2013-10-12 21:12:26
         compiled from "/var/www/ENV/libs/Jenga/tests/root/icandyclothing/templates/index.html" */ ?>
<?php /*%%SmartyHeaderCode:15651238255259f37a8c70f7-61450009%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a9a5167b03439fedac489b5f9c15df7e07428176' => 
    array (
      0 => '/var/www/ENV/libs/Jenga/tests/root/icandyclothing/templates/index.html',
      1 => 1381201717,
      2 => 'file',
    ),
    '978b7a0c93f8be2dd60859ab58d14805cfa82ae0' => 
    array (
      0 => '/var/www/ENV/libs/Jenga/tests/root/icandyclothing/templates/base.html',
      1 => 1380930911,
      2 => 'file',
    ),
    '5a3171f130f607925d27db1bfcf2309b96fee94f' => 
    array (
      0 => '/var/www/ENV/libs/Jenga/tests/root/icandyclothing/templates/header.html',
      1 => 1380930326,
      2 => 'file',
    ),
    'c0853128fde0462d04c1e1dec3a23d11b213f1b2' => 
    array (
      0 => '/var/www/ENV/libs/Jenga/tests/root/icandyclothing/templates/footer.html',
      1 => 1380930281,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '15651238255259f37a8c70f7-61450009',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.15',
  'unifunc' => 'content_5259f37a9ed8a2_67135141',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5259f37a9ed8a2_67135141')) {function content_5259f37a9ed8a2_67135141($_smarty_tpl) {?><!doctype html>
<html>
	
<head>
	
</head>
<body>
	
	<?php /*  Call merged included template "header.html" */
$_tpl_stack[] = $_smarty_tpl;
 $_smarty_tpl = $_smarty_tpl->setupInlineSubTemplate("header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0, '15651238255259f37a8c70f7-61450009');
content_5259f37a9bf556_33991377($_smarty_tpl);
$_smarty_tpl = array_pop($_tpl_stack); 
/*  End of included template "header.html" */?>

	
	<div>Name: <?php echo $_smarty_tpl->tpl_vars['name']->value;?>
</div>
	<?php echo Thumbnails\thumbnail_by_url(array(),$_smarty_tpl);?>

	<?php echo $_smarty_tpl->tpl_vars['myvar']->value[0];?>


	
	<?php /*  Call merged included template "footer.html" */
$_tpl_stack[] = $_smarty_tpl;
 $_smarty_tpl = $_smarty_tpl->setupInlineSubTemplate("footer.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0, '15651238255259f37a8c70f7-61450009');
content_5259f37a9e77e7_49127850($_smarty_tpl);
$_smarty_tpl = array_pop($_tpl_stack); 
/*  End of included template "footer.html" */?>

</body>
</html><?php }} ?>
<?php /* Smarty version Smarty-3.1.15, created on 2013-10-12 21:12:26
         compiled from "/var/www/ENV/libs/Jenga/tests/root/icandyclothing/templates/header.html" */ ?>
<?php if ($_valid && !is_callable('content_5259f37a9bf556_33991377')) {function content_5259f37a9bf556_33991377($_smarty_tpl) {?><div>{{ HEADER }}</div><?php }} ?>
<?php /* Smarty version Smarty-3.1.15, created on 2013-10-12 21:12:26
         compiled from "/var/www/ENV/libs/Jenga/tests/root/icandyclothing/templates/footer.html" */ ?>
<?php if ($_valid && !is_callable('content_5259f37a9e77e7_49127850')) {function content_5259f37a9e77e7_49127850($_smarty_tpl) {?><div>{{ FOOTER }}</div>
<?php }} ?>
