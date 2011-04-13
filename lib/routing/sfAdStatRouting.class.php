<?php
namespace sfAdStatPlugin\Routing;

use sfEvent, sfRequestRoute, sfDoctrineRouteCollection;


/**
 * Роутинг плагина
 */
class sfAdStatRouting
{
    /**
     * Статистика
     */
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


    /**
     * Клики
     */
    static public function addRouteForAdClickAdmin(sfEvent $event)
    {
        $routing = $event->getSubject();

        $routing->prependRoute('sf_guard_user', new sfDoctrineRouteCollection(array(
            'name'                 => 'sfAdClick',
            'model'                => 'AdClick',
            'module'               => 'sfAdClickAdmin',
            'prefix_path'          => 'ad-click',
            'with_show'            => false,
            'with_wildcard_routes' => true,
            'with_wildcard_object' => false,
            'actions'              => array('list'),
            'collection_actions'   => array(),
            'requirements'         => array(),
        )));
    }
}
