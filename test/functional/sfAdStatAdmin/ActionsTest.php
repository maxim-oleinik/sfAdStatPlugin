<?php
namespace Test\sfAdStatsPlugin\Functional\sfAdStatAdmin;

require_once dirname(__FILE__).'/../../bootstrap/all.php';


/**
 * sfAdStatAdmin
 */
class ActionsTest extends \myFunctionalTestCase
{
    protected $app = 'admin';

    /**
     * Создать фикстуры
     */
    private function _makeFixtures()
    {
        foreach (array(0, 15, 25, 45) as $daysAgo) {
            $date = date('Y-m-d', TIME - 86400 * $daysAgo);

            $this->helper->makeAdClick(array(
                'source' => 'yandex',
                'content' => 'yandex_cpc',
                'created_at' => $date,
            ));

            $this->helper->makeAdClick(array(
                'source' => 'yandex',
                'content' => 'yandex_banner',
                'created_at' => $date,
            ));

            if ($daysAgo > 30) {
                $this->helper->makeAdClick(array(
                    'source' => 'begun',
                    'content' => 'begun_' . $daysAgo,
                    'created_at' => $date,
                ));
            }
        }
    }

    /**
     * Показать статистику
     */
    public function testStat()
    {
        $this->authenticateAdmin();

        $this->_makeFixtures();

        // за весь период
        $this->browser
            ->getAndCheck('sfAdStatAdmin', 'stat', $this->generateUrl('sfAdStatStat', array('_period' => 'all')), 200)
            ->with('response')
                ->checkElement('.sf_admin_list tbody tr', 2);

        // за месяц
        $this->browser
            ->getAndCheck('sfAdStatAdmin', 'stat', $this->generateUrl('sfAdStatStat', array('_period' => 'month')), 200)
            ->with('response')
                ->checkElement('.sf_admin_list tbody tr', 1);

        // за неделю
        $this->browser
            ->getAndCheck('sfAdStatAdmin', 'stat', $this->generateUrl('sfAdStatStat', array('_period' => 'week')), 200)
            ->with('response')
                ->checkElement('.sf_admin_list tbody tr', 1);

        // произвольный период (нет переходов)
        $from = TIME - 86400 * 60;
        $to = TIME - 86400 * 50;
        $filter = array(
            'ad_click_filters' => array(
                'created_at' => array(
                    'from' => array(
                        'day'   => date('d', $from),
                        'month' => date('m', $from),
                        'year'  => date('Y', $from),
                    ),
                    'to' => array(
                        'day'   => date('d', $to),
                        'month' => date('m', $to),
                        'year'  => date('Y', $to),
                    ),
                ),
            ),
        );

        $this->browser
            ->getAndCheck('sfAdStatAdmin', 'stat', $this->generateUrl('sfAdStatStat'), 200)
            ->click('.sf_admin_filter form input[type=submit]', $filter)
                ->with('response')
                    ->checkRedirect(302, $this->generateUrl('sfAdStatStat'))
            ->getAndCheck('sfAdStatAdmin', 'stat', $this->generateUrl('sfAdStatStat'), 200)
                ->with('response')
                    ->checkElement('.sf_admin_list tbody tr', 0);
    }

    /**
     * Показать статистику по источнику
     */
    public function testStatSource()
    {
        $this->authenticateAdmin();

        $this->_makeFixtures();

        $source = 'yandex';

        // за весь период
        $this->browser
                ->getAndCheck('sfAdStatAdmin', 'statSource', $this->generateUrl('sfAdStatStatSource', array('source' => $source, '_period' => 'all')), 200)
                ->with('response')
                ->checkElement('.sf_admin_list tbody tr', 2);

        // за месяц
        $this->browser
            ->getAndCheck('sfAdStatAdmin', 'statSource', $this->generateUrl('sfAdStatStatSource', array('source' => $source, '_period' => 'month')), 200)
            ->with('response')
                ->checkElement('.sf_admin_list tbody tr', 2);

        // за неделю
        $this->browser
            ->getAndCheck('sfAdStatAdmin', 'statSource', $this->generateUrl('sfAdStatStatSource', array('source' => $source, '_period' => 'week')), 200)
            ->with('response')
                ->checkElement('.sf_admin_list tbody tr', 2);


        // произвольный период (нет переходов)
        $from = TIME - 86400 * 60;
        $to = TIME - 86400 * 50;
        $filter = array(
            'ad_click_filters' => array(
                'created_at' => array(
                    'from' => array(
                        'day'   => date('d', $from),
                        'month' => date('m', $from),
                        'year'  => date('Y', $from),
                    ),
                    'to' => array(
                        'day'   => date('d', $to),
                        'month' => date('m', $to),
                        'year'  => date('Y', $to),
                    ),
                ),
            ),
        );

        $this->browser
            ->getAndCheck('sfAdStatAdmin', 'statSource', $this->generateUrl('sfAdStatStatSource', array('source' => $source)), 200)
            ->click('.sf_admin_filter form input[type=submit]', $filter)
                ->with('response')
                    ->checkRedirect(302, $this->generateUrl('sfAdStatStatSource', array('source' => $source)))
            ->getAndCheck('sfAdStatAdmin', 'statSource', $this->generateUrl('sfAdStatStatSource', array('source' => $source)), 200)
                ->with('response')
                    ->checkElement('.sf_admin_list tbody tr', 0);
    }

    /**
     * Показать статистику по объявлению
     */
    public function testStatDailyContent()
    {
        $this->authenticateAdmin();

        $this->_makeFixtures();

        $content = 'yandex_cpc';

        // за весь период
        $this->browser
                ->getAndCheck('sfAdStatAdmin', 'statDailyContent', $this->generateUrl('sfAdStatStatDailyContent', array('content' => $content, '_period' => 'all')), 200)
                ->with('response')
                ->checkElement('.sf_admin_list tbody tr', 4);

        // за месяц
        $this->browser
            ->getAndCheck('sfAdStatAdmin', 'statDailyContent', $this->generateUrl('sfAdStatStatDailyContent', array('content' => $content, '_period' => 'month')), 200)
            ->with('response')
                ->checkElement('.sf_admin_list tbody tr', 3);

        // за неделю
        $this->browser
            ->getAndCheck('sfAdStatAdmin', 'statDailyContent', $this->generateUrl('sfAdStatStatDailyContent', array('content' => $content, '_period' => 'week')), 200)
            ->with('response')
                ->checkElement('.sf_admin_list tbody tr', 1);


        // произвольный период (нет переходов)
        $from = TIME - 86400 * 60;
        $to = TIME - 86400 * 50;
        $filter = array(
            'ad_click_filters' => array(
                'created_at' => array(
                    'from' => array(
                        'day'   => date('d', $from),
                        'month' => date('m', $from),
                        'year'  => date('Y', $from),
                    ),
                    'to' => array(
                        'day'   => date('d', $to),
                        'month' => date('m', $to),
                        'year'  => date('Y', $to),
                    ),
                ),
            ),
        );

        $this->browser
            ->getAndCheck('sfAdStatAdmin', 'statDailyContent', $this->generateUrl('sfAdStatStatDailyContent', array('content' => $content)), 200)
            ->click('.sf_admin_filter form input[type=submit]', $filter)
                ->with('response')
                    ->checkRedirect(302, $this->generateUrl('sfAdStatStatDailyContent', array('content' => $content)))
            ->getAndCheck('sfAdStatAdmin', 'statDailyContent', $this->generateUrl('sfAdStatStatDailyContent', array('content' => $content)), 200)
                ->with('response')
                    ->checkElement('.sf_admin_list tbody tr', 0);
    }

    /**
     * Показать статистику по объявлению
     */
    public function testStatDailySource()
    {
        $this->authenticateAdmin();

        $this->_makeFixtures();

        $source = 'yandex';

        // за весь период
        $this->browser
                ->getAndCheck('sfAdStatAdmin', 'statDailySource', $this->generateUrl('sfAdStatStatDailySource', array('source' => $source, '_period' => 'all')), 200)
                ->with('response')
                ->checkElement('.sf_admin_list tbody tr', 4);

        // за месяц
        $this->browser
            ->getAndCheck('sfAdStatAdmin', 'statDailySource', $this->generateUrl('sfAdStatStatDailySource', array('source' => $source, '_period' => 'month')), 200)
            ->with('response')
                ->checkElement('.sf_admin_list tbody tr', 3);

        // за неделю
        $this->browser
            ->getAndCheck('sfAdStatAdmin', 'statDailySource', $this->generateUrl('sfAdStatStatDailySource', array('source' => $source, '_period' => 'week')), 200)
            ->with('response')
                ->checkElement('.sf_admin_list tbody tr', 1);


        // произвольный период (нет переходов)
        $from = TIME - 86400 * 60;
        $to = TIME - 86400 * 50;
        $filter = array(
            'ad_click_filters' => array(
                'created_at' => array(
                    'from' => array(
                        'day'   => date('d', $from),
                        'month' => date('m', $from),
                        'year'  => date('Y', $from),
                    ),
                    'to' => array(
                        'day'   => date('d', $to),
                        'month' => date('m', $to),
                        'year'  => date('Y', $to),
                    ),
                ),
            ),
        );

        $this->browser
            ->getAndCheck('sfAdStatAdmin', 'statDailySource', $this->generateUrl('sfAdStatStatDailySource', array('source' => $source)), 200)
            ->click('.sf_admin_filter form input[type=submit]', $filter)
                ->with('response')
                    ->checkRedirect(302, $this->generateUrl('sfAdStatStatDailySource', array('source' => $source)))
            ->getAndCheck('sfAdStatAdmin', 'statDailySource', $this->generateUrl('sfAdStatStatDailySource', array('source' => $source)), 200)
                ->with('response')
                    ->checkElement('.sf_admin_list tbody tr', 0);
    }
}
