<?php

/**
 * Фильтр для учета входящих рекламных кликов
 *
 * Опознает входящую рекламную ссылку и пишет факт клика в базу.
 * Помечает всех входящих пользователей рекламной кукой
 */
class sfAdStatFilter extends sfFilter
{
    protected
        $cookieName,
        $cookieExpires,
        $requestParams;


    /**
     * Конструктор
     */
    public function __construct($context, $parameters = array())
    {
        if (empty($parameters['user_agent_black_list'])) {
            $parameters['user_agent_black_list'] = sfConfig::get('app_ad_stat_plugin_bots', array());
        }
        if (empty($parameters['ip_black_list'])) {
            $parameters['ip_black_list'] = sfConfig::get('app_ad_stat_plugin_ip_black_list', array());
        }

        parent::__construct($context, $parameters);

        $this->cookieName    = sfConfig::get('app_ad_stat_plugin_id_cookie_name');
        $this->cookieExpires = sfConfig::get('app_ad_stat_plugin_id_cookie_ttl');
        $this->requestParams = sfConfig::get('app_ad_stat_plugin_request_params');
    }


    /**
     * Executes the filter chain.
     *
     * @param sfFilterChain $filterChain
     */
    public function execute(sfFilterChain $filterChain)
    {
        $filterChain->execute();

        // Авторизованных не учитываем
        if ($this->context->getUser()->isAuthenticated()) {
            return;
        }

        $request = $this->context->getRequest();

        // Не GET запрос не учитываем
        if (!$request->isMethod('get')) {
            return;
        }

        // Не учитываем тех, у кого есть кука
        if ($request->getCookie($this->cookieName)) {
            $f = false;
            if ($ignore = sfConfig::get('app_ad_stat_plugin_ignore_cookie_for')) {
                foreach ($ignore as $source) {
                    if ($source == $request->getParameter($this->requestParams['source'])) {
                        $f = true;
                        break;
                    }
                }
            }

            if (!$f) {
                return;
            }
        }

        $response = $this->context->getResponse();

        // Проверяем, является ли ссылка рекламной
        // Если это не рекламный клик, тогда просто помечаем пользователя,
        // и последующие рекламные клики от него не учитываем.
        $server = $request->getPathInfoArray();
        $forwardedFor = $request->getForwardedFor();
        $server['REMOTE_ADDR']  = $forwardedFor ? $forwardedFor[0] : $request->getRemoteAddress();
        $server['HTTP_REFERER'] = $request->getReferer();

        if (!$values = $this->checkClick($server, $request->getGetParameters())) {
            $response->setCookie($this->cookieName, 'null', time() + $this->cookieExpires * 86400);
            return;
        }


        // Сохраняем клик и вешаем куку через JS
        $AdClick = new AdClick;
        $AdClick->fromArray(array_merge($values, array(
            'user_agent'  => $server['HTTP_USER_AGENT'],
            'remote_addr' => $server['REMOTE_ADDR'],
            'referer'     => $server['HTTP_REFERER'],
            'request'     => $request->getUri(),
        )));
        $AdClick->save();

        // Куку ставим JS во избежание накрутки
        if (sfConfig::get('app_ad_stat_plugin_use_js_cookie')) {
            $js = "<script type=\"text/javascript\" src=\"/sfAdStatPlugin/js/jquery.cookie.js\"></script>\n".
                  "<script id=\"ad_click_script\" type=\"text/javascript\">\n".
                  "//<[CDATA[\n".
                  "$(function(){\n".
                  "  if(parent.location == document.location){\n".
                  // В JS передаем кол-во дней для `expires`
                  "    $.cookie('{$this->cookieName}', '{$AdClick->getId()}', { expires: {$this->cookieExpires} });\n".
                  "  } else {\n".
                  "    parent.location = document.location;\n".
                  "  }\n".
                  "});\n".
                  "//]]>\n".
                  "</script>\n";
            $content = str_replace('</body>', $js.'</body>', $response->getContent());
            $response->setContent($content);

        } else {
            $response->setCookie($this->cookieName, $AdClick->getId(), time() + $this->cookieExpires * 86400);
        }
    }


    /**
     * Проверить валидность рекламного клика
     */
    public function checkClick(array $server, array $query)
    {
        // Проверяем, является ли ссылка рекламной
        $values = array();
        foreach ($this->requestParams as $origin => $param) {
            if (empty($query[$param])) {
                return false;
            }
            $values[$origin] = $query[$param];
        }


        // Режем ботов
        if (empty($server['HTTP_USER_AGENT'])) {
            return false;
        }
        if ($bots = $this->getParameter('user_agent_black_list')) {
            foreach ($bots as $bot) {
                if (stripos($server['HTTP_USER_AGENT'], $bot) !== false) {
                    return false;
                }
            }
        }
        // Рефа нет + запрещенный IP
        if ($this->hasParameter('ip_black_list') && empty($server['HTTP_REFERER'])) {
            if (in_array($server['REMOTE_ADDR'], $this->getParameter('ip_black_list'))) {
                return false;
            }
        }

        return $values;
    }
}
