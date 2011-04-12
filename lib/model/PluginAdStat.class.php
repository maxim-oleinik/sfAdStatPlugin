<?php

class PluginAdStat
{
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
                ->filterDateInterval($fromDate, $tillDate)
                ->groupBySource()
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
                ->filterDateInterval($fromDate, $tillDate)
                ->filterSource($source)
                ->groupByContent()
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
                ->filterDateInterval($fromDate, $tillDate)
                ->filterSource($source)
                ->groupDaily()
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
                ->filterDateInterval($fromDate, $tillDate)
                ->filterSource($source)
                ->groupMonthly()
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
                ->filterDateInterval($fromDate, $tillDate)
                ->filterContent($content)
                ->groupDaily()
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
    private static function getStatQueries()
    {
        return array(
            'clicks'        => PluginAdClickQuery::createQuery(),
            'registrations' => PluginAdRegistrationsQuery::createQuery(),
            'orders'        => PluginAdOrdersQuery::createQuery(),
        );
    }

    /**
     * Перегруппировать массив со статистикой
     *
     * @static
     * @param  array $stat
     * @return array
     */
    private static function formatStat(array $stat)
    {
        $return = array();

        foreach ($stat as $type => $typeRow) {
            foreach ($typeRow as $row) {
                $return[$row[0]][$type] = $row[1];
            }
        }

        foreach ($return as &$counts) {
            foreach (array('clicks', 'registrations', 'orders') as $key) {
                if (!isset($counts[$key])) {
                    $counts[$key] = 0;
                }
            }
        }

        return $return;
    }
}
