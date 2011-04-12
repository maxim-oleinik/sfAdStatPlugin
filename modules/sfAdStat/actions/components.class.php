<?php

/**
 * Переходы по рекламным ссылкам
 */
class sfAdStatComponents extends sfComponents
{
    /**
     * Статистика переходов, регистраций, заказов за период по источнику по месяцам
     *
     * @param string   $source
     * @param DateTime|null $fromDate
     * @param DateTime|null $tillDate
     */
    public function executeStatMonthlySource()
    {
        $this->stat = PluginAdStat::getStatMonthlySource($this->source, $this->fromDate, $this->tillDate);
    }
}
