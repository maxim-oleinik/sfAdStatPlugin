<?php
namespace Test\sfAdStatPlugin\Functional\sfAdClickAdmin;

require_once dirname(__FILE__).'/../../bootstrap/all.php';

/**
 * sfAdClickAdmin
 */
class ActionsTest extends \myFunctionalTestCase
{
    protected $app = 'admin';


    /**
     * SetUp
     */
    protected function _start()
    {
        $this->authenticateAdmin();
    }


    /**
     * Показать список
     */
    public function testShowList()
    {
        $plan = array(
            'source' => array(
                'yandex' => array(
                    $this->helper->makeAdClick(array('source' => 'yandex', 'medium' => 'other')),
                    $this->helper->makeAdClick(array('source' => 'yandex', 'medium' => 'other')),
                ),
                'begun' => array(
                    $this->helper->makeAdClick(array('source' => 'begun', 'medium' => 'other')),
                ),
            ),
            'medium' => array(
                'banner' => array(
                    $this->helper->makeAdClick(array('medium' => 'banner', 'source' => 'other')),
                ),
                'cpc' => array(
                    $this->helper->makeAdClick(array('medium' => 'cpc', 'source' => 'other')),
                    $this->helper->makeAdClick(array('medium' => 'cpc', 'source' => 'other')),
                ),
            ),
        );

        // все
        $this->browser
            ->getAndCheck('sfAdClickAdmin', 'index', $this->generateUrl('sfAdClick'), 200)
            ->with('response')->checkElement('.sf_admin_list tbody tr', 6);

        // отфильтрованные
        foreach ($plan as $column => $subplan) {
            foreach ($subplan as $value => $adClicks) {
                $this->browser
                    ->post($this->generateUrl('sfAdClick_collection', array('action' => 'filter', '_reset' => '')))
                    ->getAndCheck('sfAdClickAdmin', 'index', $this->generateUrl('sfAdClick'), 200)
                    ->click('Фильтр', array(
                        'ad_click_filters' => array(
                            $column => array('text' => $value),
                        ),
                    ))
                    ->with('response')
                        ->checkRedirect(302, $this->generateUrl('sfAdClick'))
                    ->getAndCheck('sfAdClickAdmin', 'index', $this->generateUrl('sfAdClick'), 200)
                    ->with('response')
                        ->checkElement('.sf_admin_list tbody tr', count($adClicks));
            }
        }
    }
}
