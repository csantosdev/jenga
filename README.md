jenga
=====

Object Relational Mapper (ORM) modeled after Python's Django framework.

Currently supports:
- MongoDB

In the works:
- MySQL
- ElasticSearch
-  Nested document filtering and sorting.
- CakePHP Model Importer
	- Converts the CakePHP ORM's model relationships into a format that Jenga can work with. 


## Example:
```
Comment::objects()->filter(['post.comments.active' => true])
// Nested Document
Comment::objects()->filter([
	'meta' => F::Nested([
		'name' => 'ip_address',
		'value' => '127.0.0.1'
	])
);
```
