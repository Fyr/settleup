<?php

class PluginsTest extends BaseTestCase
{
    use Application_Plugin_Messager;

    //Application_Plugin_Messager tests
    public function testPluginMessager()
    {
        $this->assertEquals('%s', $this->getMessageTemplate());
        $this->assertEquals('%s', $this->getHeaderMessageTemplate());
        $this->assertEquals('param', $this->setHeaderMessage('param', 'namespace'));
        $this->assertEquals(null, $this->getHeaderMessage());
        $this->assertEquals('param', $this->getHeaderMessage('namespace'));
        $this->assertEquals('param', $this->addMessage('param', 'namespace', '2'));
        $this->assertEquals('param', $this->getMessages('namespace', '2'));
        $this->assertEquals(false, $this->removeMessage('2'));
        $this->assertEquals(true, $this->removeMessage('2', 'namespace'));
    }
}
