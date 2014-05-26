<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Jarbit
 * Date: 29/07/13
 * Time: 11:23
 * To change this template use File | Settings | File Templates.
 */
?>

<div class="container pubsweet-main">
    <div class="result">

    </div>
    <table class="table table-bordered review-stats">
        <thead>
            <tr>
                <th rowspan="2">Users</th>
                <th colspan="3">Reviews</th>
                <th colspan="3">Approvals</th>
            </tr>
            <tr>
                <th>Total</th>
                <th>Last 7 days</th>
                <th>Last 24 hours</th>
                <th>Total</th>
                <th>Last 7 days</th>
                <th>Last 24 hours</th>
            </tr>
        </thead>

        <tbody>
        <?php foreach ($users as $item): ?>
            <tr>
                <td><?php echo empty($item['names'])?$item['username']:$item['names']; ?></td>
                <td><?php if(isset($allComments[$item['user_id']])) echo $allComments[$item['user_id']]['count'];?></td>
                <td><?php if(isset($last7Days[$item['user_id']]['count'])) echo $last7Days[$item['user_id']]['count'];?></td>
                <td><?php if(isset($last24Hours[$item['user_id']]['count'])) echo $last24Hours[$item['user_id']]['count'];?></td>

                <td><?php if(isset($allApprovals[$item['user_id']])) echo $allApprovals[$item['user_id']]['count'];?></td>
                <td><?php if(isset($last7DaysApprovals[$item['user_id']]['count'])) echo $last7DaysApprovals[$item['user_id']]['count'];?></td>
                <td><?php if(isset($last24HoursApprovals[$item['user_id']]['count'])) echo $last24HoursApprovals[$item['user_id']]['count'];?></td>
            </tr>
        <?php endforeach;?>

        </tbody>
    </table>
    <h2 class="text-center">All reviews visualization</h2>
    <svg id="barchart"></svg>
    <svg id="piechart"></svg>
</div>