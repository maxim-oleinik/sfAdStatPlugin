<?php

/**
 * PluginAdRegistrationsQuery
 */
class PluginAdRegistrationsQuery extends PluginAdStatQueryAbstract
{
    /**
     * Создать запрос для статистики
     *
     * @return PluginAdRegistrationsQuery
     */
    static public function createAdStatQuery()
    {
        return parent::create(null, __CLASS__)
            ->setHydrationMode(Doctrine_Core::HYDRATE_NONE)
            ->from('sfGuardUser u')
            ->innerJoin('u.AdClick ac');
    }

    /**
     * @see parent:getAdDateColumn()
     */
    public function getAdDateColumn()
    {
        return 'u.created_at';
    }

    /**
     * @see parent:getAdClickAlias()
     */
    public function getAdAlias()
    {
        return 'ac';
    }
}
