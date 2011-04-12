<?php

/**
 * Переходы по рекламным ссылкам
 */
class sfAdStatAdminActions extends sfActions
{
    /**
     * Статистика переходов, регистраций, заказов за период
     */
    public function executeStat(sfWebRequest $request)
    {
        $this->init('sfAdStatStat', $request);

        $this->stat = PluginAdStat::getStat($this->fromDate, $this->tillDate);
    }

    /**
     * Статистика переходов, регистраций, заказов за период по источнику
     */
    public function executeStatSource(sfWebRequest $request)
    {
        $this->forward404Unless($this->source = $request->getParameter('source'));

        $this->init('@sfAdStatStatSource?source='.$this->source, $request);

        $this->stat = PluginAdStat::getStatSource($this->source, $this->fromDate, $this->tillDate);
    }

    /**
     * Статистика переходов, регистраций, заказов за период по источнику по дням
     */
    public function executeStatDailySource(sfWebRequest $request)
    {
        $this->forward404Unless($this->source = $request->getParameter('source'));

        $this->init('@sfAdStatDailyStatSource?source='.$this->source, $request);

        $this->stat = PluginAdStat::getStatDailySource($this->source, $this->fromDate, $this->tillDate);
    }

    /**
     * Статистика переходов, регистраций, заказов за период по объявлению по дням
     */
    public function executeStatDailyContent(sfWebRequest $request)
    {
        $this->forward404Unless($this->content = $request->getParameter('content'));

        $this->init('@sfAdStatDailyStatContent?content='.$this->content, $request);

        $this->stat = PluginAdStat::getStatDailyContent($this->content, $this->fromDate, $this->tillDate);
    }

    /**
     * Инициализация фильтра, интервала дат
     *
     * @param string       $route
     * @param sfWebRequest $request
     * @return void
     */
    protected function init($route, sfWebRequest $request)
    {
        if ($request->hasParameter('_period'))
        {
            $this->setFilters($this->getFilterDefaults($request->getParameter('_period')));
        }

        $this->filter = new sfAdStatAdminFormFilter($filters = $this->getFilters());

        if ($request->isMethod('post')) {
            $this->filter->bind($request->getParameter($this->filter->getName()));
            if ($this->filter->isValid())
            {
                $this->setFilters($filters = $this->filter->getValues());
            }
            $this->redirect($route);
        }

        if (null !== $filters['created_at']['from']) {
            $this->fromDate = new DateTime($filters['created_at']['from']);
        } else {
            $this->fromDate = null;
        }

        if (null !== $filters['created_at']['to']) {
            $this->tillDate = new DateTime($filters['created_at']['to']);
        } else {
            $this->tillDate = null;
        }
    }

    /**
     * Возвращает параметры фильтра
     *
     * @return array
     */
    protected function getFilters()
    {
        return $this->getUser()->getAttribute('sfAdStatAdmin.filters', $this->getFilterDefaults(), 'admin_module');
    }

    /**
     * Устанавливает параметры фильтра
     *
     * @param array $filters
     * @return array
     */
    protected function setFilters(array $filters)
    {
        return $this->getUser()->setAttribute('sfAdStatAdmin.filters', $filters, 'admin_module');
    }

    /**
     * Возвращает параметры фильтра по умолчанию
     *
     * @param string $period
     * @return array
     */
    protected function getFilterDefaults($period = 'month')
    {
        $defaults = array(
            'week' => array(
                'created_at' => array(
                    'from' => date('Y-m-d 00:00:00', TIME - 86400 * 7),
                    'to'   => date('Y-m-d 23:59:59', TIME),
                ),
            ),
            'month' => array(
                'created_at' => array(
                    'from' => date('Y-m-d 00:00:00', TIME - 86400 * 30),
                    'to'   => date('Y-m-d 23:59:59', TIME),
                ),
            ),
            'all' => array(
                'created_at' => array(
                    'from' => null,
                    'to'   => null,
                ),
            ),
        );

        return isset($defaults[$period]) ? $defaults[$period] : $defaults['month'];
    }
}
