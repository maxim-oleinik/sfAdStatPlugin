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
    public function execute($filterChain)
    {
        $filterChain->execute();

        // Авторизованных не учитываем
        if ($this->context->getUser()->isAuthenticated()) {
            return;
        }


        $request = $this->context->getRequest();
        $response = $this->context->getResponse();

        // Не учитываем тех, у кого есть кука
        if ($request->getCookie($this->cookieName)) {
            return;
        }


        // Проверяем, является ли ссылка рекламной
        $values = array();
        foreach ($this->requestParams as $origin => $param) {
            if (! $values[$origin] = $request->getParameter($param)) {
                // Если это не рекламный клик, тогда просто помечаем пользователя,
                // и последующие рекламные клики от него не учитываем.
                $response->setCookie($this->cookieName, 'null', time() + $this->cookieExpires * 86400);
                return;
            }
        }


        // Режем ботов
        $pathInfo = $request->getPathInfoArray();
        $userAgent = $pathInfo['HTTP_USER_AGENT'];

        $bots = sfConfig::get('app_ad_stat_plugin_bots', array());

        foreach ($bots as $bot) {
            if (stripos($userAgent, $bot) !== false) {
                return;
            }
        }


        // Сохраняем клик и вешаем куку через JS
        $forwardedFor = $request->getForwardedFor();
        $remoteAddress = $forwardedFor ? $forwardedFor[0] : $request->getRemoteAddress();

        $AdClick = new AdClick;
        $AdClick->fromArray(array_merge($values, array(
            'user_agent'  => $userAgent,
            'remote_addr' => $remoteAddress,
            'referer'     => $request->getReferer(),
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

}
