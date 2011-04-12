<?php

/**
 * PluginAdOrdersQuery
 */
class PluginAdOrdersQuery extends PluginAdStatQueryAbstract
{
    /**
     * Создать запрос для статистики
     *
     * @return PluginAdOrdersQuery
     */
    static public function createAdStatQuery()
    {
        return parent::create(null, __CLASS__)
            ->setHydrationMode(Doctrine_Core::HYDRATE_NONE)
            ->from('Order o')
            ->innerJoin('o.User u')
            ->innerJoin('u.AdClick ac');
    }

    /**
     * @see parent:getAdDateColumn()
     */
    public function getAdDateColumn()
    {
        return 'o.created_at';
    }

    /**
     * @see parent:getAdAlias()
     */
    public function getAdAlias()
    {
        return 'ac';
    }
}
