jenga
=====

Object Relational Mapper (ORM) modeled after Python's Django framework.

Currently supports:
- MongoDB

In the works:
1. MySQL
2. ElasticSearch
  1. Nested document filtering and sorting.
3. CakePHP Model Importer
  1. Converts the CakePHP ORM's model relationships into a format that Jenga can work with. 


Example:
```
Comment::objects()->filter(['post.comments.active' => true])
```
