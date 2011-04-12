<?php

/**
 * Форма фильтра для статистики
 */
class sfAdStatAdminFormFilter extends PluginAdClickFormFilter
{
    public function configure()
    {
        $this->useFields(array('created_at'));

        $this->widgetSchema['created_at'] = new sfWidgetFormFilterDate(array(
            'from_date' => new sfWidgetFormI18nDate(array('culture' => 'ru')),
            'to_date' => new sfWidgetFormI18nDate(array('culture' => 'ru')),
            'with_empty' => false,
            'template' => '%from_date% &mdash; %to_date%',
        ));
        $this->widgetSchema['created_at']->setLabel(' ');
    }
}
