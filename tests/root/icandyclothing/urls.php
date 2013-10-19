<?php
return [
	'^/shop/(?P<item_id>\d+)$' => 'iCandyClothing\shop_view',
	'^/shop/(?P<category_slug>\w+)/(?P<page>\d+)$' => 'iCandyClothing\shop_category_view',
	'^/image$' => 'image',
	'^/$' => 'index'
];