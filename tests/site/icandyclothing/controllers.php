<?php
use Jenga\Http\Request;
use Jenga\Template\BasicTemplate;

function index(Request $request) {
	BasicTemplate::render('index.html', ['name'=>'Chris Santos']);
}

function contact(Request $request) {
	
}