<?php
use jenga\http\Request;
use jenga\template\BasicTemplate;

function index(Request $request) {
	BasicTemplate::render('index.html', ['name'=>'Chris Santos']);
}

function contact(Request $request) {
	
}