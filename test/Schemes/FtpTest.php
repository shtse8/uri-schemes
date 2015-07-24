<?php

namespace League\Uri\test\Schemes;

use League\Uri\Schemes\Ftp as FtpUri;
use PHPUnit_Framework_TestCase;

/**
 * @group ftp
 */
class FtpTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider validArray
     */
    public function testCreateFromString($expected, $input)
    {
        $this->assertSame($expected, FtpUri::createFromString($input)->__toString());
    }

    public function validArray()
    {
        return [
            'with default port' => [
                'ftp://example.com/foo/bar',
                'ftp://example.com:21/foo/bar',
            ],
            'with user info' => [
                'ftp://login:pass@example.com/',
                'ftp://login:pass@example.com/',
            ],
            'empty URI' => [
                '',
                '',
            ],
        ];
    }

    /**
     * @expectedException InvalidArgumentException
     * @dataProvider isValidProvider
     */
    public function testIsValid($input)
    {
        FtpUri::createFromString($input);
    }

    public function isValidProvider()
    {
        return [
            ['ftp:example.com'],
            ['wss:/example.com'],
            ['http://example.com'],
            ['ftp://example.com:80/foo/bar?foo=bar#content'],
        ];
    }

    /**
     * @dataProvider typecodeProvider
     */
    public function testGetTypecode($input, $expected)
    {
        $this->assertSame($expected, FtpUri::createFromString($input)->getTypecode());
    }

    public function typecodeProvider()
    {
        return [
            'empty typecode' => ['ftp://example.com/foo/bar', ''],
            'empty typecode with directory' => ['ftp://example.com/foo/', ''],
            'typecode a' => ['ftp://example.com/foo/bar;type=a', 'a'],
            'typecode i' => ['ftp://example.com/foo/bar;type=i', 'i'],
            'typecode d' => ['ftp://example.com/foo/bar;type=d', 'd'],
            'typecode is case sensitive' => ['ftp://example.com/foo/bar;type=A', ''],
        ];
    }

    /**
     * @dataProvider typecodeModifierProvider
     */
    public function testWithTypecode($input, $typecode, $expected)
    {
        $this->assertSame($expected, (string) FtpUri::createFromString($input)->withTypecode($typecode));
    }

    public function typecodeModifierProvider()
    {
        return [
            'no modification' => ['ftp://example.com/foo/bar', '', 'ftp://example.com/foo/bar'],
            'no modification' => ['ftp://example.com/foo;type=a/bar', 'd', 'ftp://example.com/foo;type=a/bar;type=d'],
            'adding' => ['ftp://example.com/foo/bar', 'a', 'ftp://example.com/foo/bar;type=a'],
            'adding to empty path' => ['ftp://example.com', 'd', 'ftp://example.com/;type=d'],
            'replacing' => ['ftp://example.com/foo/bar;type=i', 'a', 'ftp://example.com/foo/bar;type=a'],
            'removing' => ['ftp://example.com/foo/bar;type=d', '', 'ftp://example.com/foo/bar'],
            'unable to typecode' => ['ftp://example.com/foo/bar;type=A', '', 'ftp://example.com/foo/bar;type=A'],
        ];
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testWithTypecodeFailsWithInvalidTypecode()
    {
        FtpUri::createFromString('ftp://example.com/foo/bar')->withTypecode('Z');
    }
}
