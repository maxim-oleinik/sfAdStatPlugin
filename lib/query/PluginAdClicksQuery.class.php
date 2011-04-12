<?php

/**
 * PluginAdClicksQuery
 */
class PluginAdClicksQuery extends PluginAdStatQueryAbstract
{
    /**
     * Создать запрос
     *
     * @return PluginAdClicksQuery
     */
    static public function createQuery()
    {
        return parent::create(null, __CLASS__)
            ->setHydrationMode(Doctrine_Core::HYDRATE_NONE)
            ->from('AdClick ac');
    }

    /**
     * @see parent:getDateColumn()
     */
    public function getDateColumn()
    {
        return 'ac.created_at';
    }
}
