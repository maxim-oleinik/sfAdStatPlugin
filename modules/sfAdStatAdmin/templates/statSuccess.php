<?php
/**
 * Статистика переходов, регистраций, заказов за период
 *
 * @param DateTime $fromDate
 * @param DateTime $tillDate
 * @param array  $stat
 * @param sfFormFilter $filter
 */
?>

<div id="sf_admin_container">
    <h1>Статистика рекламы (<?php echo ($fromDate) ?  $fromDate->format('d.m.Y') : '?' ?> &mdash; <?php echo ($tillDate) ? $tillDate->format('d.m.Y') : '?' ?>)</h1>

    <?php include_partial('filter', array('route' => 'sfAdStatStat', 'filter' => $filter)) ?>

    <div id="sf_admin_content">
        <div class="sf_admin_list">
            <table cellspacing="0">
                <?php include_partial('table_head', array('title' => 'Источник')) ?>
                <tbody>
                <?php foreach ($stat as $source => $row): ?>
                    <tr>
                        <td><?php echo link_to($source, 'sfAdStatStatSource', array('source' => $source)) ?></td>
                        <?php include_partial('table_row', array('row' => $row)) ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php include_partial('chart_column', array('stat' => $stat, 'fromDate' => $fromDate, 'tillDate' => $tillDate)) ?>
    </div>
</div>