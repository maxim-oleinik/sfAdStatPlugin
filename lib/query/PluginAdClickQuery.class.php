<?php

/**
 * PluginAdClickQuery
 */
class PluginAdClickQuery extends PluginAdStatQueryAbstract
{
    /**
     * Создать запрос
     *
     * @return PluginAdClickQuery
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
