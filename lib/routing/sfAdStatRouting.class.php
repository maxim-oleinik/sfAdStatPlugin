<?php

class sfAdStatRouting
{
    static public function addRouteForAdStatAdmin(sfEvent $event)
    {
        $routing = $event->getSubject();

        $routing->prependRoute('sfAdStatStat', new sfRequestRoute(
            'ad-stat',
            array(
                'module' => 'sfAdStatAdmin',
                'action' => 'stat',
            ),
            array(
                'sf_method' => array('get', 'post'),
            )
        ));

        $routing->prependRoute('sfAdStatStatSource', new sfRequestRoute(
            'ad-stat/source/:source',
            array(
                'module' => 'sfAdStatAdmin',
                'action' => 'statSource',
            ),
            array(
                'sf_method' => array('get', 'post'),
                'source'   => '\w+',
            )
        ));

        $routing->prependRoute('sfAdStatStatDailySource', new sfRequestRoute(
            'ad-stat/daily/source/:source',
            array(
                'module' => 'sfAdStatAdmin',
                'action' => 'statDailySource',
            ),
            array(
                'sf_method' => array('get', 'post'),
                'source'   => '\w+',
            )
        ));

        $routing->prependRoute('sfAdStatStatDailyContent', new sfRequestRoute(
            'ad-stat/daily/content/:content',
            array(
                'module' => 'sfAdStatAdmin',
                'action' => 'statDailyContent',
            ),
            array(
                'sf_method' => array('get', 'post'),
            )
        ));
    }
}
