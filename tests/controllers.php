<?php
namespace iCandyClothing;

function shop_view($item_id) {
	echo 'Item ID: ' . $item_id;
}

function shop_category_view($category_slug, $page) {
	echo 'Category: ' . $category_slug . ' Page: ' . $page;
}