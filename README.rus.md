sfAdStatPlugin
==============

Плагин для учета входящих рекламных кликов и оценки эффективности рекламной кампании.


Установка и настройка
---------------------

  * Установка как submodule для Git-репозитария:

        cd /my/project/dir
        git submodule add git://github.com:maxim-oleinik/sfAdStatPlugin.git plugins/sfAdStatPlugin

  * Запуск тестов:

        phpunit plugins/sfAdStatPlugin/test/AllTests.php

  * Подключить тесты плагина в общий набор: test/AllTests.php

        [php]
        require_once __DIR__.'/../plugins/sfAdStatPlugin/test/AllTests.php';

        class AllTests extends PHPUnit_Framework_TestSuite
        {
            public static function suite()
            {
                $suite = new AllTests('PHPUnit');
                ...
                $suite->addTest(\Test\sfAdStatPlugin\AllTests::suite());
                return $suite;
            }
        }

  * ProjectConfiguration

        $this->enablePlugins('sfAdStatPlugin', ...);

  * Подключить JS

        ./symfony plugin:publish-assets

  * Создать таблицу `sf_ad_clicks`

        [php]
        public function migrate($upDown)
        {
            $this->table($upDown, 'sf_ad_clicks',
            array(
                'id' => array(
                    'type' => 'integer',
                    'length' => 4,
                    'autoincrement' => true,
                    'primary' => true,
                ),
                'campaign' => array(
                    'type' => 'string',
                    'length' => 255,
                ),
                'source' => array(
                    'type' => 'string',
                    'length' => 255,
                ),
                'medium' => array(
                    'type' => 'string',
                    'length' => 255,
                ),
                'content' => array(
                    'type' => 'string',
                    'length' => 255,
                ),
                'user_agent' => array(
                    'type'   => 'string',
                    'length' => 255,
                ),
                'remote_addr' => array(
                    'type'   => 'string',
                    'length' => 15,
                ),
                'referer' => array(
                    'type'   => 'string',
                    'length' => 255,
                ),
                'request' => array(
                    'type'   => 'string',
                    'length' => 255,
                ),
                'created_at' => array(
                    'type'   => 'timestamp',
                    'length' => '25',
                    'notnull'  => true,
                ),
            ),
            array(
                'type' => 'INNODB',
                'charset' => 'utf8',
            ));
        }

  * Подключить фильтр sfAdStatFilter в filters.yml

        [yml]
        # insert your own filters here

        ad_stat:
          class: sfAdStatFilter

  * Конфигурация плагина в app.yml

        [yml]
        ad_stat_plugin:
          id_cookie_name:       ad     # Название куки для хранения id клика
          id_cookie_ttl:        183    # Дней
          user_click_id_column: false  # Колонка в sfGuardUser для записи id клика
          request_params:
            source:   utm_source
            medium:   utm_medium
            content:  utm_content
            campaign: utm_campaign


При регистрации пользователя сохранить ему click_id
---------------------------------------------------

  * schema.yml

        [yml]
        sfGuardUser:
          columns:
            click_id:   { type: integer(4), unique: true }
          relations:
            AdClick:
              local: click_id
              foreign: id
              autoComplete: false


  * В таблицу `sf_guard_user` добавить колонку `click_id`

        [php]
        public function migrate($upDown)
        {
            $this->column($upDown, 'sf_guard_user', 'click_id', 'integer', 4);

            $this->index($upDown, 'sf_guard_user', 'click_id', array(
                'fields' => array(click_id),
                'type'   => 'unique',
            ));

            $this->foreignKey($upDown, 'sf_guard_user', 'User_VS_AdClick', array(
                'local'        => 'click_id',
                'foreign'      => 'id',
                'foreignTable' => 'sf_ad_clicks',
                'onDelete'     => 'SET NULL',
            ));
        }


  * В app.yml указать название колонки `click_id`

        [yml]
        ad_stat_plugin:
          user_click_id_column: click_id


Модули статистики
---------------------------------------------------

  * settings.yml

        [yml]
        enabled_modules: [..., sfAdStatAdmin, sfAdClickAdmin]

  * security.yml

        [yml]
        default:
          credentials: super_admin

  * Ссылку в меню sfAdminDashPlugin в app.yml

        [yml]
        categories:
          AdStat:
            credentials: super_admin
            items:
              "Клики":
                url:    sfAdClick
