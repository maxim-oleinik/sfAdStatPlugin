<?php

/**
 * PluginAdRegistrationsQuery
 */
class PluginAdRegistrationsQuery extends PluginAdStatQueryAbstract
{
    /**
     * Создать запрос
     *
     * @return PluginAdRegistrationsQuery
     */
    static public function createQuery()
    {
        return parent::create(null, __CLASS__)
            ->setHydrationMode(Doctrine_Core::HYDRATE_NONE)
            ->from('sfGuardUser u')
            ->innerJoin('u.AdClick ac');
    }

    /**
     * @see parent:getDateColumn()
     */
    public function getDateColumn()
    {
        return 'u.created_at';
    }
}
