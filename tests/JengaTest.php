<?php
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
            'comment_count' => 2
        ));
        $collection->insert(array(
            'title' => 'Post #2',
            'category' => 'Other'
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

        $posts = Realtime\Track\Models\Product::objects()->filter(array('comment_count__lt' => 3));
        $this->assertEquals('Post #1', $posts[0]['title']);

        $posts = Realtime\Track\Models\Product::objects()->filter(array('comment_count__lt' => 0));
        $this->assertEquals(0, count($posts));
    }

    public function testLessThanOrEqualFilter() {

        $posts = Realtime\Track\Models\Product::objects()->filter(array('comment_count__lte' => 3));
        $this->assertEquals('Post #1', $posts[0]['title']);

        $posts = Realtime\Track\Models\Product::objects()->filter(array('comment_count__lte' => 0));
        $this->assertEquals(0, count($posts));

        $posts = Realtime\Track\Models\Product::objects()->filter(array('comment_count__lte' => 2));
        $this->assertEquals('Post #1', $posts[0]['title']);
    }

    public function testGreaterThanFilter() {

        $posts = Realtime\Track\Models\Product::objects()->filter(array('comment_count__gt' => 1));
        $this->assertEquals('Post #1', $posts[0]['title']);

        $posts = Realtime\Track\Models\Product::objects()->filter(array('comment_count__gt' => 3));
        $this->assertEquals(0, count($posts));
    }

    public function testGreaterThanOrEqualFilter() {

        $posts = Realtime\Track\Models\Product::objects()->filter(array('comment_count__gte' => 1));
        $this->assertEquals('Post #1', $posts[0]['title']);

        $posts = Realtime\Track\Models\Product::objects()->filter(array('comment_count__gte' => 3));
        $this->assertEquals(0, count($posts));

        $posts = Realtime\Track\Models\Product::objects()->filter(array('comment_count__gte' => 2));
        $this->assertEquals('Post #1', $posts[0]['title']);
    }

    public function testWhereInFilter() {

        $categories = array('Sports', 'Food');

        $posts = Realtime\Track\Models\Product::objects()->filter(array('category' => $categories));
        $this->assertEquals('Post #1', $posts[0]['title']);

        $posts = Realtime\Track\Models\Product::objects()->filter(array('category__in' => $categories));
        $this->assertEquals('Post #1', $posts[0]['title']);

        $categories = array('Food', 'Shopping');

        $posts = Realtime\Track\Models\Product::objects()->filter(array('category' => $categories));
        $this->assertEquals(0, count($posts));

        $posts = Realtime\Track\Models\Product::objects()->filter(array('category__in' => $categories));
        $this->assertEquals(0, count($posts));
    }

    public function testWhereNotInFilter() {

        $categories = array('Food', 'Sports');

        $posts = Realtime\Track\Models\Product::objects()->filter(array('category__nin' => $categories));
        $this->assertEquals('Post #2', $posts[0]['title']);

        $categories = array('Food');

        $posts = Realtime\Track\Models\Product::objects()->filter(array('category__nin' => $categories));
        $this->assertEquals('Post #1', $posts[0]['title']);
        $this->assertEquals('Post #2', $posts[1]['title']);
    }
}
