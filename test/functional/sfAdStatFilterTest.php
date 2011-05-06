<?php
namespace Test\sfAdStatPlugin;

use sfConfig;


/**
 * Регистрация перехода по рекламной ссылке
 */
abstract class sfAdStatFilterTest extends \myFunctionalTestCase
{
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
            ), 1, $found);

        if (sfConfig::get('app_ad_stat_plugin_use_js_cookie')) {
            $this->browser
                ->with('response')->checkElement('ad_click_script', true);
        } else {
            $this->browser
                ->with('response')->setsCookie(\sfConfig::get('app_ad_stat_plugin_id_cookie_name'), $found[0]->getId());
        }
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


    /**
     * Игнорировать куку для указанных объявлений
     */
    public function testIgnoreCookie()
    {
        $this->browser->getContext(true);
        $this->browser->setConfigValue('app_ad_stat_plugin_ignore_cookie_for', array('yandex'));

        $this->browser
            ->setCookie(\sfConfig::get('app_ad_stat_plugin_id_cookie_name'), '1')
            ->get($this->generateUrl('homepage', array(
                'utm_source'   => $advSource = 'yandex',
                'utm_medium'   => $advMedium = 'referal',
                'utm_content'  => $advContent = 'Adv content',
                'utm_campaign' => $adcCampaign = 'campaign 1',
            )))
            ->with('model')->check('AdClick', array(
                'source' => $advSource,
                'medium' => $advMedium,
                'content' => $advContent,
                'campaign' => $adcCampaign,
            ), 1);
    }


    /**
     * Игнорировать ботов
     */
    public function testIgnoreBotClick()
    {
        $this->assertNotNull(\sfConfig::get('app_ad_stat_plugin_bots'));

        $this->browser
            ->setHttpHeader('User-Agent', 'Mozilla/5.0 (compatible; YandexBot/3.0; +http://yandex.com/bots)')
            ->get($adTarget = $this->generateUrl('homepage', array(
                'utm_source'   => $adSource = 'yandex.ru',
                'utm_medium'   => $adMedium = 'referal',
                'utm_content'  => $adContent = 'Adv content',
                'utm_campaign' => $adCampaign = 'campaign 1',
            )))
            ->with('model')->check('AdClick', array(), 0, $found)
            ->with('response')->checkElement('#ad_click_script', false);
    }


    /**
     * Не GET-запрос
     */
    public function testIgnoreNotGet()
    {
        $this->browser
            ->post($this->generateUrl('homepage', array(
                'utm_source'   => $adSource = 'yandex.ru',
                'utm_medium'   => $adMedium = 'referal',
                'utm_content'  => $adContent = 'Adv content',
                'utm_campaign' => $adCampaign = 'campaign 1',
            )))
            ->with('model')->check('AdClick', array(), 0, $found)
            ->with('response')->checkElement('#ad_click_script', false);
    }

}
