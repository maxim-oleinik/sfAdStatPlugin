<?php
namespace Test\sfAdStatPlugin\Unit\Table;

require_once dirname(__FILE__).'/../../bootstrap/all.php';

use PluginAdClickTable, PluginAdClickQuery, sfConfig;


class PluginAdClickTableTest extends \myUnitTestCase
{
    public function _start()
    {
        sfConfig::set('app_ad_stat_plugin_columns', array(
            'clicks' => array('query' => 'PluginAdClickQuery'),
        ));
    }
    /**
     * Создать фикстуры
     */
    private function _makeFixtures()
    {
        foreach (array(0, 15, 30, 31) as $daysAgo) {
            $date = new \DateTime("-{$daysAgo} days");
            $date = $date->format('Y-m-d');

            $this->helper->makeAdClick(array(
                'source' => 'yandex',
                'content' => 'yandex_1',
                'created_at' => $date,
            ));

            $this->helper->makeAdClick(array(
                'source' => 'yandex',
                'content' => 'yandex_2',
                'created_at' => $date,
            ));

            $this->helper->makeAdClick(array(
                'source' => 'begun',
                'content' => 'begun_1',
                'created_at' => $date,
            ));
        };
    }

    /**
     * formatStat
     */
    public function testFormatStat()
    {
        $stat = array(
            'clicks' => array(
                array('yandex', 54),
                array('begun',  11),
            ),
        );

        $expected = array(
            'yandex' => array(
                'clicks' => 54,
            ),
            'begun' => array(
                'clicks' => 11,
            ),
        );

        $this->assertEquals($expected, TestAdClickTable::testFormatStat($stat));
    }

    /**
     * getStat
     */
    public function testGetStat()
    {
        $this->_makeFixtures();

        $tillDate = new \DateTime();

        // статистика за сегодня
        $fromDate = new \DateTime();
        $expected = array(
            'begun'  => array('clicks' => 1),
            'yandex' => array('clicks' => 2),
        );
        $stat = TestAdClickTable::getStat($fromDate, $tillDate);
        $this->assertEquals($expected, $stat);

        // статистика за 15 дней
        $fromDate = new \DateTime('-15 days');
        $expected = array(
            'begun'  => array('clicks' => 2),
            'yandex' => array('clicks' => 4),
        );
        $stat = TestAdClickTable::getStat($fromDate, $tillDate);
        $this->assertEquals($expected, $stat);

        // статистика за 30 дней
        $fromDate = new \DateTime('-30 days');
        $expected = array(
            'begun'  => array('clicks' => 3),
            'yandex' => array('clicks' => 6),
        );
        $stat = TestAdClickTable::getStat($fromDate, $tillDate);
        $this->assertEquals($expected, $stat);
    }

    /**
     * getStatSource
     */
    public function testGetStatSource()
    {
        $this->_makeFixtures();

        $source = 'yandex';
        $tillDate = new \DateTime();

        // статистика за сегодня
        $fromDate = new \DateTime();
        $expected = array(
            'yandex_1' => array('clicks' => 1),
            'yandex_2' => array('clicks' => 1),
        );
        $stat = TestAdClickTable::getStatSource($source, $fromDate, $tillDate);
        $this->assertEquals($expected, $stat);

        // статистика за 15 дней
        $fromDate = new \DateTime('-15 days');
        $expected = array(
            'yandex_1' => array('clicks' => 2),
            'yandex_2' => array('clicks' => 2),
        );
        $stat = TestAdClickTable::getStatSource($source, $fromDate, $tillDate);
        $this->assertEquals($expected, $stat);

        // статистика за 30 дней
        $fromDate = new \DateTime('-30 days');
        $expected = array(
            'yandex_1' => array('clicks' => 3),
            'yandex_2' => array('clicks' => 3),
        );
        $stat = TestAdClickTable::getStatSource($source, $fromDate, $tillDate);
        $this->assertEquals($expected, $stat);
    }

    /**
     * getStatDailySource
     */
    public function testGetStatDailySource()
    {
        $this->_makeFixtures();

        $dates = array();
        $source = 'yandex';
        $tillDate = new \DateTime();

        foreach (array(0, 15, 30) as $daysAgo) {
            $fromDate = new \DateTime("-{$daysAgo} days");
            $dates[] = $fromDate->format('Y-m-d');
            $expected = array();
            foreach ($dates as $date) {
                $expected[$date] = array('clicks' => 2);
            }

            $stat = TestAdClickTable::getStatDailySource($source, $fromDate, $tillDate);
            $this->assertEquals($expected, $stat);
        }
    }

    /**
     * getStatDailyContent
     */
    public function testGetStatDailyContent()
    {
        $this->_makeFixtures();

        $dates = array();
        $content = 'yandex_1';
        $tillDate = new \DateTime();

        foreach (array(0, 15, 30) as $daysAgo) {
            $fromDate = new \DateTime("-{$daysAgo} days");
            $dates[] = $fromDate->format('Y-m-d');
            $expected = array();
            foreach ($dates as $date) {
                $expected[$date] = array('clicks' => 1);
            }

            $stat = TestAdClickTable::getStatDailyContent($content, $fromDate, $tillDate);
            $this->assertEquals($expected, $stat);
        }
    }
    
    /**
     * getStatMonthlySource
     */
    public function testGetStatMonthlySource()
    {
        $date = new \DateTime();
        $date = $date->format('Y-m-d');
        $this->helper->makeAdClick(array(
            'source' => 'yandex',
            'created_at' => $date,
        ));
        $this->helper->makeAdClick(array(
            'source' => 'begun',
            'created_at' => $date,
        ));

        $date = new \DateTime('-1 month');
        $date = $date->format('Y-m-d');
        $this->helper->makeAdClick(array(
            'source' => 'yandex',
            'created_at' => $date,
        ));

        $dates = array();
        $source = 'yandex';
        $tillDate = new \DateTime();

        foreach (array(0, 1) as $monthsAgo) {
            $fromDate = new \DateTime("-{$monthsAgo} months");
            $dates[] = $fromDate->format('F');
            $expected = array();
            foreach ($dates as $date) {
                $expected[$date] = array('clicks' => 1);
            }

            $stat = TestAdClickTable::getStatMonthlySource($source, $fromDate, $tillDate);
            $this->assertEquals($expected, $stat);
        }
    }
}

class TestAdClickTable extends PluginAdClickTable
{
    public static function testFormatStat(array $stat)
    {
        return self::formatStat($stat);
    }
}
