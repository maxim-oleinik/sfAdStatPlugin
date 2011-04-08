<?php
namespace Test\sfAdStatPlugin;


/**
 * Регистрация перехода по рекламной ссылке
 */
abstract class sfAdStatFilterTest extends \myFunctionalTestCase
{
    /**
     * Авторизован, переход по рекламной ссылке не учитываем
     */
    public function testAuthenticated()
    {
        $this->authenticateUser();
        $this->browser
            ->get($this->generateUrl('homepage', array(
                'utm_source'   => $advSource = 'yandex.ru',
                'utm_medium'   => $advMedium = 'referal',
                'utm_content'  => $advContent = 'Adv content',
                'utm_campaign' => $adcCampaign = 'campaign 1',
            )))
            ->with('model')->check('AdClick', array(), 0)
            ->with('response')->checkElement('#ad_click_script', false);
    }


    /**
     * Cookie не установлена, переход по рекламной ссылке
     */
    public function testWithNoCookieAndValidUrl()
    {
        $this->browser
            ->setHttpHeader('Referer', $adReferer = 'http://yandex.ru')
            ->setHttpHeader('User-Agent', $adUserAgent = 'Mozilla/Firefox 4.0 Windows 7 x86_64')
            ->setHttpHeader('X-Forwarded-For', $adRemoteAddr = '208.67.222.222')
            ->get($adTarget = $this->generateUrl('homepage', array(
                'utm_source'   => $adSource = 'yandex.ru',
                'utm_medium'   => $adMedium = 'referal',
                'utm_content'  => $adContent = 'Adv content',
                'utm_campaign' => $adCampaign = 'campaign 1',
            )))
            ->with('model')->check('AdClick', array(
                'source'   => $adSource,
                'medium'   => $adMedium,
                'content'  => $adContent,
                'campaign' => $adCampaign,
                'user_agent'  => $adUserAgent,
                'remote_addr' => $adRemoteAddr,
                'referer'  => $adReferer,
                'request'  => 'http://localhost'. $adTarget,
            ), 1, $found)
            ->with('response')->checkElement('#ad_click_script', true);
    }


    /**
     * Cookie не установлена, ссылка не содержит необходимых параметров
     */
    public function testWithNoCookieAndInvalidUrl()
    {
        $this->browser
            ->get($this->generateUrl('homepage', array(
                'utm_source'   => 'yandex.ru',
                'utm_medium'   => 'referal',
                'utm_content'  => 'Adv content',
            )))
            ->with('response')->begin()
                ->setsCookie(\sfConfig::get('app_ad_stat_plugin_id_cookie_name'), 'null')
            ->end()
            ->with('model')->check('AdClick', array(), 0);
    }


    /**
     * Cookie уже есть
     */
    public function testWithCookieExists()
    {
        $this->assertNotNull($cookieName = \sfConfig::get('app_ad_stat_plugin_id_cookie_name'));

        $this->browser
            ->setCookie($cookieName, '1')
            ->get($this->generateUrl('homepage', array(
                'utm_source'   => $advSource = 'yandex.ru',
                'utm_medium'   => $advMedium = 'referal',
                'utm_content'  => $advContent = 'Adv content',
                'utm_campaign' => $adcCampaign = 'campaign 1',
            )))
            ->with('model')->check('AdClick', array(
                'source' => $advSource,
                'medium' => $advMedium,
                'content' => $advContent,
                'campaign' => $adcCampaign,
            ), 0)
            ->with('response')->checkElement('#ad_click_script', false);
    }

}
