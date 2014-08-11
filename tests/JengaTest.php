<?php
use Realtime\Track\Models\Product;
use Jenga\Db\Query\Filters\Filter as F;

class JengaTest extends PHPUnit_Framework_TestCase {

    public static function setUpBeforeClass() {

        define('APP', realpath(__DIR__ . '/../')  . '/app');
        $loader = require realpath(APP . '/../') . '/vendor/autoload.php';
        $loader->add('Jenga', APP . '/libs/');
        $loader->add('Realtime', APP . '/libs/');

        $m = new MongoClient();
        $m->dropDb('test');
        $db = $m->selectDB('test');
        $collection = $db->selectCollection('posts');

        $collection->insert(array(
            'title' => 'Post #1',
            'category' => 'Sports',
            'comments' => array(
                array('id' => 1, 'body' => 'Comment #1'),
                array('id' => 2, 'body' => 'Comment #2'),
            ),
            'comment_count' => 2,
            'meta' => array(
                array(
                    'name' => 'Color',
                    'value' => 'Blue'
                ),
                array(
                    'name' => 'Age',
                    'value' => 27
                )
            ),
            'statuses' => array(
                array(
                    'name' => 'Approved',
                    'types' => array(
                        array(
                            'id' => 1,
                            'parent_id' => 5
                        )
                    )
                )
            )
        ));
        $collection->insert(array(
            'title' => 'Post #2',
            'category' => 'Other',
            'meta' => array(
                array(
                    'name' => 'Color',
                    'value' => 'Red'
                ),
                array(
                    'name' => 'ShadeColor',
                    'value' => 'Blue'
                )
            ),
            'statuses' => array(
                array(
                    'name' => 'Approved',
                    'types' => array(
                        array(
                            'id' => 2,
                            'parent_id' => 5
                        )
                    )
                )
            )
        ));
    }

    /**
     * Test adding a database configuration into Jenga.
     */
    public function testAddDatabaseConfiguration() {

        $conf = array(
            'host' => 'localhost',
            'port' => 27017,
            'engine' => 'Jenga\Db\Engines\Mongo\Mongo'
        );

        Jenga\Configurations\Database::addConfiguration('mongo', $conf);
        $this->assertNotEmpty(Jenga\Configurations\Database::getDatabaseEngine('mongo'));
    }

    /**
     * Tests that you can load and connect to a Mongo database based on a mongo configuration.
     */
    public function testLoadMongoBackend() {

        $mongo = Jenga\Configurations\Database::getDatabaseEngine('mongo');
        $this->assertInstanceOf('Jenga\Db\Engines\Mongo\Mongo', $mongo);
    }

    /**
     * Test that the filter() method works on the Manager object.
     */
    public function testEmbeddedDocumentFilter() {

        $products = Realtime\Track\Models\Product::objects()->filter(array('comments.id' => 2));
        $this->assertEquals('Post #1', $products[0]['title']);
        $this->assertEquals('Comment #2', $products[0]['comments'][1]['body']);
    }

    public function testElemMatchEmbeddedDocument() {

        // Must use the NestedFilter() method within the filter()
    }

    public function testLessThanFilter() {

        $posts = Product::objects()->filter(array('comment_count__lt' => 3));
        $this->assertEquals('Post #1', $posts[0]['title']);

        $posts = Product::objects()->filter(array('comment_count__lt' => 0));
        $this->assertEquals(0, count($posts));
    }

    public function testLessThanOrEqualFilter() {

        $posts = Product::objects()->filter(array('comment_count__lte' => 3));
        $this->assertEquals('Post #1', $posts[0]['title']);

        $posts = Product::objects()->filter(array('comment_count__lte' => 0));
        $this->assertEquals(0, count($posts));

        $posts = Product::objects()->filter(array('comment_count__lte' => 2));
        $this->assertEquals('Post #1', $posts[0]['title']);
    }

    public function testGreaterThanFilter() {

        $posts = Product::objects()->filter(array('comment_count__gt' => 1));
        $this->assertEquals('Post #1', $posts[0]['title']);

        $posts = Product::objects()->filter(array('comment_count__gt' => 3));
        $this->assertEquals(0, count($posts));
    }

    public function testGreaterThanOrEqualFilter() {

        $posts = Product::objects()->filter(array('comment_count__gte' => 1));
        $this->assertEquals('Post #1', $posts[0]['title']);

        $posts = Product::objects()->filter(array('comment_count__gte' => 3));
        $this->assertEquals(0, count($posts));

        $posts = Product::objects()->filter(array('comment_count__gte' => 2));
        $this->assertEquals('Post #1', $posts[0]['title']);
    }

    public function testWhereInFilter() {

        $categories = array('Sports', 'Food');

        $posts = Product::objects()->filter(array('category' => $categories));
        $this->assertEquals('Post #1', $posts[0]['title']);

        $posts = Product::objects()->filter(array('category__in' => $categories));
        $this->assertEquals('Post #1', $posts[0]['title']);

        $categories = array('Food', 'Shopping');

        $posts = Product::objects()->filter(array('category' => $categories));
        $this->assertEquals(0, count($posts));

        $posts = Product::objects()->filter(array('category__in' => $categories));
        $this->assertEquals(0, count($posts));
    }

    public function testWhereNotInFilter() {

        $categories = array('Food', 'Sports');

        $posts = Product::objects()->filter(array('category__nin' => $categories));
        $this->assertEquals('Post #2', $posts[0]['title']);

        $categories = array('Food');

        $posts = Product::objects()->filter(array('category__nin' => $categories));
        $this->assertEquals('Post #1', $posts[0]['title']);
        $this->assertEquals('Post #2', $posts[1]['title']);
    }

    public function testNestedQuery() {

        $posts = Product::objects()->filter(array(
            'meta.value' => 'Blue'
        ));
        $this->assertEquals('Post #1', $posts[0]['title']);
        $this->assertEquals('Post #2', $posts[1]['title']);

        $posts = Product::objects()->filter(array(
            'meta' => F::Nested(array(
                'name' => 'Color',
                'value' => 'Blue'
        ))));
        $this->assertEquals('Post #1', $posts[0]['title']);
        $this->assertEquals(1, count($posts));
    }

    public function testNestedNestedQuery() {

        $posts = Product::objects()->filter(array(
            'statuses' => F::Nested(array(
                'name' => 'Approved',
                'types' => F::Nested(array(
                    'id' => 1,
                    'parent_id' => 5
        ))))));
        $this->assertEquals('Post #1', $posts[0]['title']);
        $this->assertEquals(1, count($posts));

        $posts = Product::objects()->filter(array(
            'statuses' => F::Nested(array(
                'name' => 'Approved',
                'types' => F::Nested(array(
                    'parent_id' => 5
        ))))));
        $this->assertEquals('Post #1', $posts[0]['title']);
        $this->assertEquals('Post #2', $posts[1]['title']);
        $this->assertEquals(2, count($posts));

        $posts = Product::objects()->filter(array(
            'statuses' => F::Nested(array(
                'name' => 'Approved',
                'types.parent_id' => 5
        ))));
        $this->assertEquals('Post #1', $posts[0]['title']);
        $this->assertEquals('Post #2', $posts[1]['title']);
        $this->assertEquals(2, count($posts));
    }

    public function testGroupedQuery() {

        $posts = Product::objects()->filter(array(
            'title' => 'Post #1',
            array('meta.name' => 'Color')
        ));

        $this->assertEquals('Post #1', $posts[0]['title']);
    }

    public function testOrQuery() {

        $posts = Product::objects()->filter(array(
            array('title' => 'Post #1'),
            'OR',
            array('title' => 'Post #2')
        ));
        $this->assertEquals('Post #1', $posts[0]['title']);
        $this->assertEquals('Post #2', $posts[1]['title']);
    }

    public function testMultipleOrQuery() {

        $posts = Product::objects()->filter(array(
            array('title' => 'Post #1'),
            'OR',
            array('title' => 'Post #2'),
            'OR',
            'category' => 'Sports'
        ));
        $this->assertEquals('Post #1', $posts[0]['title']);
        $this->assertEquals('Post #2', $posts[1]['title']);
    }

    public function testNestedOrQuery() {

        $posts = Product::objects()->filter(array(
            array('meta' => F::Nested(array(
                'name' => 'Color',
                'value' => 'Blue'
            ))),
            'OR',
            array('meta' => F::Nested(array(
                'name' => 'ShadeColor',
                'value' => 'Blue'
            )))
        ));
        $this->assertEquals('Post #1', $posts[0]['title']);
        $this->assertEquals('Post #2', $posts[1]['title']);
    }
}
