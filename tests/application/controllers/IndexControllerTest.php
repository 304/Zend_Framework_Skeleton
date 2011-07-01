<?php
include_once 'ControllerTestCase.php';
class IndexControllerTest extends ControllerTestCase
{

    public function testRouteIndex()
    {
        $this->dispatch('/');
        $this->assertAction("index");
        $this->assertController("index");
        $this->assertRoute('main-page');
    }
}
