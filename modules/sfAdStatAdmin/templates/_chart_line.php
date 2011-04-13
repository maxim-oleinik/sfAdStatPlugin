<?php
/**
 * Графики статистики переходов, регистраций, заказов за период
 *
 * @param array  $stat
 * @param DateTime $fromDate
 * @param DateTime $tillDate
 */

use_javascript('/sfAdStatPlugin/js/highcharts.js');

$stat = $stat->getRawValue();

$columns = sfConfig::get('app_ad_stat_plugin_columns');
$chartColumns = sfConfig::get('app_ad_stat_plugin_chart_columns');

?>

<div id="chart_container" style="height: 400px;">
</div>

<script type="text/javascript">
var chart;
$(document).ready(function() {
    chart = new Highcharts.Chart({
        chart: {
            renderTo: 'chart_container',
            defaultSeriesType: 'line',
            marginRight: 130,
            marginBottom: 25
        },
        title: {
            text: 'Статистика рекламы',
            x: -20 //center
        },
        subtitle: {
            text: '<?php echo ($fromDate) ?  $fromDate->format('d.m.Y') : '?' ?> - <?php echo ($tillDate) ? $tillDate->format('d.m.Y') : '?' ?>',
            x: -20
        },
        xAxis: {
            categories: ['<?php echo join("', '", array_keys($stat)) ?>']
        },
        yAxis: {
            title: {
                text: 'Количество'
            },
            plotLines: [{
                value: 0,
                width: 1,
                color: '#808080'
            }]
        },
        tooltip: {
            formatter: function() {
                    return '<b>'+ this.series.name +'</b><br/>'+
                    this.x +': '+ this.y;
            }
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'top',
            x: -10,
            y: 100,
            borderWidth: 0
        },
        series: [
            <?php foreach ($chartColumns as $column): ?>
            {
                name: '<?php echo isset($columns[$column]['title']) ? $columns[$column]['title'] : $column ?>',
                data: [
                    <?php foreach ($stat as $row): ?>
                        <?php echo $row[$column] ?>,
                    <?php endforeach ?>
                ]
            },
            <?php endforeach; ?>
        ]
    });
});
</script>