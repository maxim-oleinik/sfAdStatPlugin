<?php

/**
 * Plugin config
 */
class sfAdStatPluginConfiguration extends sfPluginConfiguration
{
    protected
        $cookieName,
        $clickIdColumn;


    /**
     * @see sfPluginConfiguration
     */
    public function initialize()
    {
        $this->cookieName = sfConfig::get('app_ad_stat_plugin_id_cookie_name');
        $this->clickIdColumn = sfConfig::get('app_ad_stat_plugin_user_click_id_column');

        if ($this->clickIdColumn) {
            $this->dispatcher->connect('sfGuard.register_success', array($this, 'listenToUserRegistration'));
        }

        foreach (array('sfAdStatAdmin', 'sfAdClickAdmin') as $module) {
            if (in_array($module, sfConfig::get('sf_enabled_modules', array()))) {
                $this->dispatcher->connect('routing.load_configuration', array('sfAdStatRouting', 'addRouteFor'.str_replace('sf', '', $module)));
            }
        }
    }


    /**
     * Записать ID клика пользователю при регистрации
     */
    public function listenToUserRegistration(sfEvent $event)
    {
        $request = sfContext::getInstance()->getRequest();
        $clickId = (int) $request->getCookie($this->cookieName);

        if ($clickId && $click = AdClickTable::getInstance()->find($clickId)) {

            $usersCount = sfGuardUserTable::getInstance()
                ->createQuery('u')
                ->where('u.'.$this->clickIdColumn.' = ?', $clickId)
                ->count();

            if (!$usersCount) {
                $user = $event->getSubject();
                $user->set($this->clickIdColumn, $clickId);
                $user->save();
            }
        }

    }

}
