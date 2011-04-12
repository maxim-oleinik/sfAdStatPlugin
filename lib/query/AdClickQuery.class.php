<?php

/**
 * AdClickQuery
 */
class AdClickQuery extends Doctrine_Query
{

    /**
     * Создать запрос
     *
     * @param  string $alias
     * @return AdClickQuery
     */
    static public function createQuery($alias = 'a')
    {
        return parent::create(null, __CLASS__)
            ->from('AdClick '.$alias);
    }


    /**
     * Статистика переходов по месяцам
     *
     * @return array
     */
    public function getMonthlyStats()
    {
        $alias = $this->getRootAlias();

        $rows = $this
            ->setHydrationMode(Doctrine::HYDRATE_NONE)
            ->select("COUNT({$alias}.id), DATE_FORMAT({$alias}.created_at, '%M') month")
            ->groupBy('month')
            ->orderBy("{$alias}.created_at")
            ->execute();

        $visits = array();
        foreach ($rows as $row) {
            $visits[$row[1]] = $row[0];
        }
        return $visits;
    }


    /**
     * Выборка по рефералу
     *
     * @param sfGuardUser $user
     * @return AdClickQuery
     */
    public function filterByReferal(sfGuardUser $user)
    {
        return $this->andWhere($this->getRootAlias() .'.source = ?', $user->getId());
    }


    /**
     * Выборка по датам
     *
     * @param DateTime $fromDate
     * @param DateTime $tillDate
     */
    public function filterByDates(DateTime $fromDate, DateTime $tillDate)
    {
        return $this->andWhere($this->getRootAlias(). '.created_at BETWEEN ? AND ?', array(
            $fromDate->format('Y-m-d 00:00:00'),
            $tillDate->format('Y-m-d 23:59:59'),
        ));
    }


    /**
     * Выборка по ID
     *
     * @todo вынести в базовый класс
     * @param array $ids
     * @return AdClickQuery
     */
    public function filterByIds(array $ids)
    {
        $ids[] = 0;
        return $this->andWhereIn($this->getRootAlias().'.id', $ids);
    }


    /**
     * Получить массив ID объявлений
     *
     * @todo вынести в базовый класс
     * @return array
     */
    public function getPrimaryKeys()
    {
        $rows = $this->select($this->getRootAlias(). '.id')
            ->execute(array(), Doctrine::HYDRATE_NONE);

        $ids = array();
        foreach ($rows as $row) {
            $ids[] = $row[0];
        }
        return $ids;
    }

}
