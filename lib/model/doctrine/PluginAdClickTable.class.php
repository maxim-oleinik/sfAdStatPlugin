<?php

/**
 * PluginAdClickTable
 */
class PluginAdClickTable extends Doctrine_Table
{
    protected static $queriesKeys = array();

    /**
     * Получить статистику, группировка по источнику
     *
     * @static
     * @param DateTime $fromDate
     * @param DateTime $tillDate
     * @return array
     */
    public static function getStat(DateTime $fromDate = null, DateTime $tillDate = null)
    {
        $stats = self::getStatQueries();

        foreach ($stats as &$stat) {
            $stat = $stat
                ->filterAdDateInterval($fromDate, $tillDate)
                ->groupByAdSource()
                ->execute();
        }

        return self::formatStat($stats);
    }

    /**
     * Получить статистику по конкретному источнику, группировка по объявлению
     *
     * @static
     * @param string   $source
     * @param DateTime $fromDate
     * @param DateTime $tillDate
     * @return array
     */
    public static function getStatSource($source, DateTime $fromDate = null, DateTime $tillDate = null)
    {
        $stats = self::getStatQueries();

        foreach ($stats as &$stat) {
            $stat = $stat
                ->filterAdDateInterval($fromDate, $tillDate)
                ->filterAdSource($source)
                ->groupByAdContent()
                ->execute();
        }

        return self::formatStat($stats);
    }

    /**
     * Получить статистику по конкретному источнику, группировка по дням
     *
     * @static
     * @param string   $source
     * @param DateTime $fromDate
     * @param DateTime $tillDate
     * @return array
     */
    public static function getStatDailySource($source, DateTime $fromDate = null, DateTime $tillDate = null)
    {
        $stats = self::getStatQueries();

        foreach ($stats as &$stat) {
            $stat = $stat
                ->filterAdDateInterval($fromDate, $tillDate)
                ->filterAdSource($source)
                ->groupAdDaily()
                ->execute();
        }

        return self::formatStat($stats);
    }

    /**
     * Получить статистику по конкретному источнику, группировка по месяцам
     *
     * @static
     * @param string   $source
     * @param DateTime $fromDate
     * @param DateTime $tillDate
     * @return array
     */
    public static function getStatMonthlySource($source, DateTime $fromDate = null, DateTime $tillDate = null)
    {
        $stats = self::getStatQueries();

        foreach ($stats as &$stat) {
            $stat = $stat
                ->filterAdDateInterval($fromDate, $tillDate)
                ->filterAdSource($source)
                ->groupAdMonthly()
                ->execute();
        }

        return self::formatStat($stats);
    }

    /**
     * Получить статистику по конкретному объявлению, групировка по дням
     *
     * @static
     * @param string   $source
     * @param DateTime $fromDate
     * @param DateTime $tillDate
     * @return array
     */
    public static function getStatDailyContent($content, DateTime $fromDate = null, DateTime $tillDate = null)
    {
        $stats = self::getStatQueries();

        foreach ($stats as &$stat) {
            $stat = $stat
                ->filterAdDateInterval($fromDate, $tillDate)
                ->filterAdContent($content)
                ->groupAdDaily()
                ->execute();
        }

        return self::formatStat($stats);
    }

    /**
     * Получить массив запросов
     *
     * @static
     * @return array
     */
    protected static function getStatQueries()
    {
        $columns = sfConfig::get('app_ad_stat_plugin_columns');

        $queries = array();
        self::$queriesKeys = array();
        foreach ($columns as $key => $options) {
            if (isset($options['query'])) {
                self::$queriesKeys[] = $key;
                $queries[$key] = $options['query']::createAdStatQuery();
            }
        }

        return $queries;
    }

    /**
     * Перегруппировать массив со статистикой
     *
     * @static
     * @param  array $stat
     * @return array
     */
    protected static function formatStat(array $stat)
    {
        $return = array();

        foreach ($stat as $type => $typeRow) {
            foreach ($typeRow as $row) {
                $return[$row[0]][$type] = $row[1];
            }
        }

        foreach ($return as &$counts) {
            foreach (self::$queriesKeys as $key) {
                if (!isset($counts[$key])) {
                    $counts[$key] = 0;
                }
            }
        }

        return $return;
    }
}
