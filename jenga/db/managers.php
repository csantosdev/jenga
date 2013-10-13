<?php
namespace Jenga\DB\Managers;
use Jenga\DB\Query\QuerySet;

class BasicModelManager {
	
	private $model; // string
	
	public function __construct($model) {
		$this->model = $model;
	}
	
	public function filter($conditions) {
		return new QuerySet($this->model, $conditions);
	}
	
	public function get($conditions) {
		$qs = new QuerySet($this->model, $conditions);
		return $qs[0];
	}
}