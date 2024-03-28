<?php
use PHPUnit\Framework\TestCase;

class CacheTest extends TestCase
{
    protected $dir;
    protected $inCache;
    protected $inCacheId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dir = APPLICATION_PATH . "/../data/cache";
        $this->inCache = "save text in cache";
        $this->inCacheId = "idForCachedRecord";
    }

    public function testGetInstance()
    {
        Application_Model_Cache::init(true, $this->dir);

        $instance = Application_Model_Cache::getInstance();
        $this->assertTrue($instance instanceof Zend_Cache_Core);
        return $instance;
    }

    public function testSave()
    {
        $this->assertTrue(Application_Model_Cache::save($this->inCacheId, $this->inCache));
    }

    public function testLoad()
    {
        $this->assertEquals(Application_Model_Cache::load($this->inCacheId), $this->inCache);
    }

    public function testClean()
    {
        Application_Model_Cache::clean();
        $this->assertFalse(Application_Model_Cache::load($this->inCacheId));
    }

    public function testGetInstanceCacheNotEnabled()
    {
        Application_Model_Cache::init(false, $this->dir);
        $this->assertFalse(Application_Model_Cache::getInstance());
    }

    public function testSaveCacheNotEnabled()
    {
        Application_Model_Cache::init(false, $this->dir);
        $this->assertTrue(Application_Model_Cache::save($this->inCacheId, $this->inCache));
    }

    public function testLoadCacheNotEnabled()
    {
        Application_Model_Cache::init(false, $this->dir);
        $this->assertFalse(Application_Model_Cache::load($this->inCacheId), $this->inCache);
    }

    public function testCleanCacheNotEnabled()
    {
        Application_Model_Cache::init(false, $this->dir);
        $this->assertNull(Application_Model_Cache::clean());
    }
}
