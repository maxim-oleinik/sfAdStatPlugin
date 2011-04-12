<?php
/**
 * Вывод  фильтра
 *
 * @param string $route
 * @param array  $routeParams
 * @param sfAdStatAdminFormFilter $filter
 */

if (! isset($routeParams)) {
    $routeParams = array();
} else {
    $routeParams = $routeParams->getRawValue();
}

?>

<?php echo link_to('За неделю', $route, $routeParams, array('query_string' => '_period=week')) ?> |
<?php echo link_to('За месяц', $route, $routeParams, array('query_string' => '_period=month')) ?> |
<?php echo link_to('За все время', $route, $routeParams, array('query_string' => '_period=all')) ?> |
<a href="#" onclick="$('#sf_admin_bar').slideToggle();return false;">Указать период</a>

<div id="sf_admin_bar"><div class="sf_admin_filter">
    <form action="<?php echo isset($routeParams) ? url_for($route, $routeParams) : url_for($route) ?>" method="post">
        <table cellpadding="0"><tbody><tr><td>
            <?php echo $filter ?>
            <input type="submit" />
        </td></tr></tbody></table>
    </form>
</div></div>