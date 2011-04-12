<?php

/**
 * PluginAdOrdersQuery
 */
class PluginAdOrdersQuery extends PluginAdStatQueryAbstract
{
    /**
     * Создать запрос
     *
     * @return PluginAdOrdersQuery
     */
    static public function createQuery()
    {
        return parent::create(null, __CLASS__)
            ->setHydrationMode(Doctrine_Core::HYDRATE_NONE)
            ->from('Order o')
            ->innerJoin('o.User u')
            ->innerJoin('u.AdClick ac');
    }

    /**
     * @see parent:getDateColumn()
     */
    public function getDateColumn()
    {
        return 'o.created_at';
    }
}
