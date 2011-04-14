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
                ->fetchGroupByAdSource();
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
                ->fetchGroupByAdContent();
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
                ->fetchGroupAdDaily();
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
                ->fetchGroupAdMonthly();
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
                ->fetchGroupAdDaily();
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
     * @param  array $stat array(
     *                         'clicks' => array(
     *                             array('yandex', 54),
     *                             array('begun',  11),
     *                         ),
     *                         'registrations' => array(
     *                             array('yandex', 17),
     *                         ),
     *                     )
     *
     * @return array       array(
     *                         'yandex' => array(
     *                             'clicks'        => 54,
     *                             'registrations' => 17,
     *                         ),
     *                         'begun' => array(
     *                             'clicks'        => 11,
     *                             'registrations' => 0,
     *                         ),
     *                     )
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
