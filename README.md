jenga
=====

Object Relational Mapper (ORM) modeled after Python's Django framework.

Currently supports:
- MongoDB

In the works:
- MySQL
- ElasticSearch
- CakePHP Model Importer

Example:
`Comment::objects()->filter(['post.comments.active' => true])`
