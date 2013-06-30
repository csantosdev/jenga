jenga
=====

ORM and framework modeled after Django.
- MongoDB ORM
- Handles MongoDB joins automatically with ORM
`Comment::objects()->filter(['post.blog.active' => true])`
- Regex Routing
