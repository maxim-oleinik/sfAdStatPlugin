<?php

/**
 * PluginAdStatQueryAbstract
 */
abstract class PluginAdStatQueryAbstract extends Doctrine_Query
{
    /**
     * Возвращает alias AdClick таблицы
     *
     * @abstract
     * @return string
     */
    abstract public function getAdAlias();

    /**
     * Возвращает колонку с датой
     *
     * @abstract
     * @return string
     */
    abstract public function getAdDateColumn();

    /**
     * Фильтровать по периоду времени
     *
     * @param DateTime|null $fromDate
     * @param DateTime|null $tillDate
     * @return PluginAdStatQueryAbstract
     */
    public function filterAdDateInterval(DateTime $fromDate = null, DateTime $tillDate = null)
    {
        if ($fromDate) {
            $this->andWhere($this->getAdDateColumn(). ' >= ?', $fromDate->format('Y-m-d 00:00:00'));
        }

        if ($tillDate) {
            $this->andWhere($this->getAdDateColumn(). ' <= ?', $tillDate->format('Y-m-d 23:59:59'));
        }

        return $this;
    }

    /**
     * Фильтр по источнику
     *
     * @param  $source
     * @return PluginAdStatQueryAbstract
     */
    public function filterAdSource($source)
    {
        return $this->andWhere($this->getAdAlias(). '.source = ?', $source);
    }

    /**
     * Фильтр по объявлению
     *
     * @param  $content
     * @return PluginAdStatQueryAbstract
     */
    public function filterAdContent($content)
    {
        return $this->andWhere($this->getAdAlias(). '.content = ?', $content);
    }

    /**
     * Группировка по источнику
     *
     * @return PluginAdStatQueryAbstract
     */
    public function groupByAdSource()
    {
        $adAlias = $this->getAdAlias();

        return $this
                ->select("{$adAlias}.source, count({$this->getRootAlias()}.id)")
                ->groupBy("{$adAlias}.source")
                ->orderBy("{$adAlias}.source");
    }

    /**
     * Группировка по объявлению
     *
     * @return PluginAdStatQueryAbstract
     */
    public function groupByAdContent()
    {
        $adAlias = $this->getAdAlias();

        return $this
                ->select("{$adAlias}.content, count({$this->getRootAlias()}.id)")
                ->groupBy("{$adAlias}.content")
                ->orderBy("{$adAlias}.content");
    }

    /**
     * Группировка по дням
     *
     * @return PluginAdStatQueryAbstract
     */
    public function groupAdDaily()
    {
        return $this
                ->select("DATE_FORMAT({$this->getAdDateColumn()}, '%Y-%m-%d') date, count({$this->getRootAlias()}.id)")
                ->groupBy('date')
                ->orderBy('date');
    }

    /**
     * Группировка по месяцам
     *
     * @return PluginAdStatQueryAbstract
     */
    public function groupAdMonthly()
    {
        $dateColumn = $this->getAdDateColumn();

        return $this
                ->select("DATE_FORMAT({$dateColumn}, '%M') date, count({$this->getRootAlias()}.id)")
                ->groupBy('date')
                ->orderBy($dateColumn);
    }
}
