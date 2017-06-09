<?php
require_once('src/PostcodeLoader.php');

use johnmccuk\PostcodeLoader;
use PHPUnit\Framework\TestCase;

class PostcodeLoaderTest extends TestCase
{
    public function __construct()
    {
        $this->mockedPostcodeLoader = $this->getMockBuilder('johnmccuk\PostcodeLoader')
            ->setConstructorArgs([$this->path])
            ->setMethods(['retrieveFileData'])
            ->getMock();

        $this->mockedPostcodeLoader->method('retrieveFileData')
             ->willReturn(['M32 0JG', 'OX49 5NU', 'NE30 1DP']);
    }

    public function testPostcodeLoaderIstheRightClass()
    {
        $postcodeLoader = new PostcodeLoader('some/path');
        $this->assertEquals('johnmccuk\PostcodeLoader', get_class($postcodeLoader));
    }

    /**
     * @expectedException Exception
     */
    public function testRetrievePostcodesThrowsExceptionIfFileMissing()
    {
        $postcodeLoader = new PostcodeLoader('some/path');
        $result = $postcodeLoader->retrievePostcodes();
    }

    public function testPostcodeLoaderReturnsACollctionClass()
    {
        $result = $this->mockedPostcodeLoader->retrievePostcodes();
        $this->assertEquals('Doctrine\Common\Collections\ArrayCollection', get_class($result));
    }

    public function testPostcodeLoaderReturnsCorrectValues()
    {
        $result = $this->mockedPostcodeLoader->retrievePostcodes();
        $this->assertEquals('M320JG', $result->first());
        $this->assertEquals('NE301DP', $result->last());
    }
}
