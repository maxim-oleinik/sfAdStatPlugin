<?php
namespace Test\sfAdStatPlugin\Unit;

require_once dirname(__FILE__).'/../bootstrap/all.php';

use sfAdStatFilter;


class sfAdStatFilterTest extends \myUnitTestCase
{
    private $filter;
    private $server = array();
    private $query  = array();


    /**
     * SetUp
     */
    public function _start()
    {
        $this->filter = new sfAdStatFilter($this->getContext());

        $this->server['HTTP_USER_AGENT'] = 'Some user agent';
        $this->server['REMOTE_ADDR']     = '12.12.12.12';
        $this->server['HTTP_REFERER']    = 'http://example.org/';

        $this->query  = array(
            'utm_source'   => 'yandex',
            'utm_medium'   => 'cpc',
            'utm_content'  => 'ad-name-1',
            'utm_campaign' => 'campaign-1',
        );
    }


    /**
     * Валидная ссылка
     */
    public function testValidLink()
    {
        $expected  = array(
            'source'   => $this->query['utm_source'],
            'medium'   => $this->query['utm_medium'],
            'content'  => $this->query['utm_content'],
            'campaign' => $this->query['utm_campaign'],
        );
        $this->assertEquals($expected, $this->filter->checkClick($this->server, $this->query));
    }


    /**
     * Ссылка не содержит необходимых параметров
     */
    public function testWithNoCookieAndInvalidUrl()
    {
        foreach ($this->query as $key => $value) {
            $q = $this->query;
            unset($q[$key]);

            $this->assertFalse($this->filter->checkClick($this->server, $q), $key);
        }
    }


    /**
     * User-Agent не указан
     */
    public function testUserAgentNotDefined()
    {
        $this->assertFalse($this->filter->checkClick($server = array(), $this->query));
    }


    /**
     * User-Agent в черном списке
     */
    public function testUserAgentIsBlackListed()
    {
        $filter = new sfAdStatFilter($this->getContext(), array('user_agent_black_list' => array('specific')));

        $server['HTTP_USER_AGENT'] = 'Some Specific user-agent';
        $this->assertFalse($filter->checkClick($server, $this->query));
    }


    /**
     * IP в черном списке
     */
    public function testIpIsBlackListed()
    {
        $filter = new sfAdStatFilter($this->getContext(), array('ip_black_list' => array($this->server['REMOTE_ADDR'])));

        // Referer указан
        $this->assertTrue((bool)$filter->checkClick($this->server, $this->query));

        // Нет рефа и IP запрещен
        $server = $this->server;
        unset($server['HTTP_REFERER']);
        $this->assertFalse($filter->checkClick($server, $this->query));
    }

}
