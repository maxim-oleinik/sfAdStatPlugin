<?php
namespace Test\sfAdStatPlugin\Unit\Query;

require_once dirname(__FILE__).'/../../bootstrap/all.php';

use PluginAdClickQuery;

class PluginAdClickQueryTest extends \myUnitTestCase
{
    /**
     * Выполнить запрос и проверить найденные записи
     */
    private function assertFoundClicks(array $clicks, array $foundNames, PluginAdClickQuery $q)
    {
        $found = $q->execute();

        $this->assertEquals(count($foundNames), $found->count());
        foreach ($foundNames as $i => $name) {
            $this->assertModels($clicks[$name], $found[$i], "[{$i}] {$name}");
        }
    }


    // Test
    // -------------------------------------------------------------------------
    public function testFilterDateInterval()
    {
        $dates = array(
            date('Y-m-d H:i:s'),
            date('Y-m-d H:i:s', TIME - 86400 * 15),
            date('Y-m-d H:i:s', TIME - 86400 * 30),
        );

        $clicks = array(
            1 => $this->helper->makeAdClick(array('created_at' => $dates[0])),
                 $this->helper->makeAdClick(array('created_at' => $dates[0])),
                 $this->helper->makeAdClick(array('created_at' => $dates[1])),
                 $this->helper->makeAdClick(array('created_at' => $dates[1])),
                 $this->helper->makeAdClick(array('created_at' => $dates[2])),
                 $this->helper->makeAdClick(array('created_at' => $dates[2])),
        );

        $plan = array(
            array(array(1, 2),             new \DateTime($dates[0]), new \DateTime($dates[0])),
            array(array(3, 4),             new \DateTime($dates[1]), new \DateTime($dates[1])),
            array(array(5, 6),             new \DateTime($dates[2]), new \DateTime($dates[2])),
            array(array(1, 2, 3, 4),       new \DateTime($dates[1]), new \DateTime($dates[0])),
            array(array(3, 4, 5, 6),       new \DateTime($dates[2]), new \DateTime($dates[1])),
            array(array(1, 2, 3, 4, 5, 6), new \DateTime($dates[2]), new \DateTime($dates[0])),
            array(array(1, 2, 3, 4, 5, 6), null, null),
        );

        foreach ($plan as $case) {
            $this->assertFoundClicks($clicks, $case[0], PluginAdClickQuery::createAdStatQuery()->filterAdDateInterval($case[1], $case[2]));
        }
    }

    public function testFilterAdSource()
    {
        $sources = array(
            'yandex',
            'google',
        );

        $clicks = array(
            1 => $this->helper->makeAdClick(array('source' => $sources[0])),
                 $this->helper->makeAdClick(array('source' => $sources[0])),
                 $this->helper->makeAdClick(array('source' => $sources[0])),
                 $this->helper->makeAdClick(array('source' => $sources[1])),
                 $this->helper->makeAdClick(array('source' => $sources[1])),
                 $this->helper->makeAdClick(array('source' => $sources[1])),
        );

        $plan = array(
            array(array(1, 2, 3), $sources[0]),
            array(array(4, 5, 6), $sources[1]),
        );

        foreach ($plan as $case) {
            $this->assertFoundClicks($clicks, $case[0], PluginAdClickQuery::createAdStatQuery()->filterAdSource($case[1]));
        }
    }

    public function testFilterAdContent()
    {
        $contents = array(
            'yandex',
            'google',
        );

        $clicks = array(
            1 => $this->helper->makeAdClick(array('content' => $contents[0])),
                 $this->helper->makeAdClick(array('content' => $contents[0])),
                 $this->helper->makeAdClick(array('content' => $contents[0])),
                 $this->helper->makeAdClick(array('content' => $contents[1])),
                 $this->helper->makeAdClick(array('content' => $contents[1])),
                 $this->helper->makeAdClick(array('content' => $contents[1])),
        );

        $plan = array(
            array(array(1, 2, 3), $contents[0]),
            array(array(4, 5, 6), $contents[1]),
        );

        foreach ($plan as $case) {
            $this->assertFoundClicks($clicks, $case[0], PluginAdClickQuery::createAdStatQuery()->filterAdContent($case[1]));
        }
    }

    public function testFetchGroupByAdSource()
    {
         $sources = array(
            'yandex',
            'google',
        );

        $clicks = array(
             $this->helper->makeAdClick(array('source' => $sources[0])),
             $this->helper->makeAdClick(array('source' => $sources[0])),
             $this->helper->makeAdClick(array('source' => $sources[1])),
             $this->helper->makeAdClick(array('source' => $sources[1])),
             $this->helper->makeAdClick(array('source' => $sources[1])),
             $this->helper->makeAdClick(array('source' => $sources[1])),
        );

        $expected = array(
            array('google', 4),
            array('yandex', 2),
        );

        $this->assertEquals($expected, PluginAdClickQuery::createAdStatQuery()->fetchGroupByAdSource());
    }
    
    public function testFetchGroupByAdContent()
    {
         $contents = array(
            'yandex',
            'google',
        );

        $clicks = array(
             $this->helper->makeAdClick(array('content' => $contents[0])),
             $this->helper->makeAdClick(array('content' => $contents[0])),
             $this->helper->makeAdClick(array('content' => $contents[1])),
             $this->helper->makeAdClick(array('content' => $contents[1])),
             $this->helper->makeAdClick(array('content' => $contents[1])),
             $this->helper->makeAdClick(array('content' => $contents[1])),
        );

        $expected = array(
            array('google', 4),
            array('yandex', 2),
        );

        $this->assertEquals($expected, PluginAdClickQuery::createAdStatQuery()->fetchGroupByAdContent());
    }

    public function testFetchGroupAdDaily()
    {
         $days = array(
            date('Y-m-d'),
            date('Y-m-d', TIME - 86400),
        );

        $clicks = array(
             $this->helper->makeAdClick(array('created_at' => $days[0])),
             $this->helper->makeAdClick(array('created_at' => $days[0])),
             $this->helper->makeAdClick(array('created_at' => $days[1])),
             $this->helper->makeAdClick(array('created_at' => $days[1])),
             $this->helper->makeAdClick(array('created_at' => $days[1])),
             $this->helper->makeAdClick(array('created_at' => $days[1])),
        );

        $expected = array(
            array($days[1], 4),
            array($days[0], 2),
        );

        $this->assertEquals($expected, PluginAdClickQuery::createAdStatQuery()->fetchGroupAdDaily());
    }

    public function testFetchGroupAdMonthly()
    {
         $days = array(
            date('Y-m-d'),
            date('Y-m-d', TIME - 86400 * 45),
        );

        $clicks = array(
             $this->helper->makeAdClick(array('created_at' => $days[0])),
             $this->helper->makeAdClick(array('created_at' => $days[0])),
             $this->helper->makeAdClick(array('created_at' => $days[1])),
             $this->helper->makeAdClick(array('created_at' => $days[1])),
             $this->helper->makeAdClick(array('created_at' => $days[1])),
             $this->helper->makeAdClick(array('created_at' => $days[1])),
        );

        $expected = array(
            array(date('F', TIME - 86400 * 45), 4),
            array(date('F'), 2),
        );

        $this->assertEquals($expected, PluginAdClickQuery::createAdStatQuery()->fetchGroupAdMonthly());
    }
}
