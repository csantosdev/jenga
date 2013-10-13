<?php
namespace Jenga\Db\Models\Managers;
use Jenga\Db\Query\QuerySet;

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