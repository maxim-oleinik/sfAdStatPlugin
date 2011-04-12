<?php

/**
 * PluginAdStatQueryAbstract
 */
abstract class PluginAdStatQueryAbstract extends Doctrine_Query
{
    /**
     * Возвращает колонку с датой
     *
     * @abstract
     * @return string
     */
    abstract public function getDateColumn();

    /**
     * Фильтровать по периоду времени
     *
     * @param DateTime|null $fromDate
     * @param DateTime|null $tillDate
     * @return PluginAdStatQueryAbstract
     */
    public function filterDateInterval(DateTime $fromDate = null, DateTime $tillDate = null)
    {
        if ($fromDate) {
            $this->andWhere($this->getDateColumn(). ' >= ?', $fromDate->format('Y-m-d 00:00:00'));
        }

        if ($tillDate) {
            $this->andWhere($this->getDateColumn(). ' <= ?', $tillDate->format('Y-m-d 23:59:59'));
        }

        return $this;
    }

    /**
     * Фильтр по источнику
     *
     * @param  $source
     * @return PluginAdStatQueryAbstract
     */
    public function filterSource($source)
    {
        return $this->andWhere('ac.source = ?', $source);
    }

    /**
     * Фильтр по объявлению
     *
     * @param  $content
     * @return PluginAdStatQueryAbstract
     */
    public function filterContent($content)
    {
        return $this->andWhere('ac.content = ?', $content);
    }

    /**
     * Группировка по источнику
     *
     * @return PluginAdStatQueryAbstract
     */
    public function groupBySource()
    {
        return $this
            ->select("ac.source, count({$this->getRootAlias()}.id)")
            ->groupBy('ac.source')
            ->orderBy('ac.source');
    }

    /**
     * Группировка по объявлению
     *
     * @return PluginAdStatQueryAbstract
     */
    public function groupByContent()
    {
        return $this
            ->select("ac.content, count({$this->getRootAlias()}.id)")
            ->groupBy('ac.content')
            ->orderBy('ac.content');
    }

    /**
     * Группировка по дням
     *
     * @return PluginAdStatQueryAbstract
     */
    public function groupDaily()
    {
        return $this
            ->select("DATE_FORMAT({$this->getDateColumn()}, '%Y-%m-%d') date, count({$this->getRootAlias()}.id)")
            ->groupBy('date')
            ->orderBy('date');
    }
}
