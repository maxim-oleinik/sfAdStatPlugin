<?php
/**
 * Строка таблицы статистики
 *
 * @param string $clicks
 * @param string $registrations
 * @param string $orders
 */

use_helper('sfAdStat');

?>

<td><?php echo $clicks ?></td>
<td><?php echo $registrations ?></td>
<td><?php echo conversion($registrations, $clicks) ?></td>
<td><?php echo $orders ?></td>
<td><?php echo conversion($orders, $registrations) ?></td>
<td><?php echo conversion($orders, $clicks) ?></td>
 
