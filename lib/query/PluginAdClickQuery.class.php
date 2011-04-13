<?php

/**
 * PluginAdClickQuery
 */
class PluginAdClickQuery extends PluginAdStatQueryAbstract
{
    /**
     * Создать запрос для статистики
     *
     * @return PluginAdClickQuery
     */
    static public function createAdStatQuery()
    {
        return parent::create(null, __CLASS__)
            ->from('AdClick ac');
    }

    /**
     * @see parent:getAdDateColumn()
     */
    public function getAdDateColumn()
    {
        return 'ac.created_at';
    }

    /**
     * @see parent:getAdAlias()
     */
    public function getAdAlias()
    {
        return 'ac';
    }
}
