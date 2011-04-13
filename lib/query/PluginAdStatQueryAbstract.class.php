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
     * @return array
     */
    public function fetchGroupByAdSource()
    {
        $adAlias = $this->getAdAlias();

        return $this
                ->setHydrationMode(Doctrine_Core::HYDRATE_NONE)
                ->select("{$adAlias}.source, count({$this->getRootAlias()}.id)")
                ->groupBy("{$adAlias}.source")
                ->orderBy("{$adAlias}.source")
                ->execute();
    }

    /**
     * Группировка по объявлению
     *
     * @return array
     */
    public function fetchGroupByAdContent()
    {
        $adAlias = $this->getAdAlias();

        return $this
                ->setHydrationMode(Doctrine_Core::HYDRATE_NONE)
                ->select("{$adAlias}.content, count({$this->getRootAlias()}.id)")
                ->groupBy("{$adAlias}.content")
                ->orderBy("{$adAlias}.content")
                ->execute();
    }

    /**
     * Группировка по дням
     *
     * @return array
     */
    public function fetchGroupAdDaily()
    {
        return $this
                ->setHydrationMode(Doctrine_Core::HYDRATE_NONE)
                ->select("DATE_FORMAT({$this->getAdDateColumn()}, '%Y-%m-%d') date, count({$this->getRootAlias()}.id)")
                ->groupBy('date')
                ->orderBy('date')
                ->execute();
    }

    /**
     * Группировка по месяцам
     *
     * @return array
     */
    public function fetchGroupAdMonthly()
    {
        $dateColumn = $this->getAdDateColumn();

        return $this
                ->setHydrationMode(Doctrine_Core::HYDRATE_NONE)
                ->select("DATE_FORMAT({$dateColumn}, '%M') date, count({$this->getRootAlias()}.id)")
                ->groupBy('date')
                ->orderBy($dateColumn)
                ->execute();
    }
}
