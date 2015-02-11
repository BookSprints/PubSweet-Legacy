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
    <div class="row-fluid">
    <ul class="nav nav-tabs">
        <li class="active"><a href="#content" data-toggle="tab">Content</a></li>
        <li><a href="#history" data-toggle="tab">History</a></li>
        <li><a href="#editorial" data-toggle="tab">Editorial</a></li>

    </ul>

    <div class="tab-content">

        <div class="tab-pane active" id="content">
            <div class="clearfix">
                <div class="pull-right">
                    <div class="btn-group nav" data-toggle="buttons-radio">
                        <a class="btn btn-primary" href="#word-count" data-toggle="tab">Pie</a>
                        <a class="btn btn-primary active" data-toggle="tab" href="#bubble-words">Bubbles</a>
                    </div>
                </div>
            </div>
            <div class="tab-content">
                <div id="word-count" class="tab-pane">
                    <p>Distribution of words per sections and per chapter</p>
                </div>
                <div id="bubble-words" class="tab-pane active">
                    <p>Amount of words in every chapter, circle with same color belong to the same chapter. Hover over
                        to
                        get more info</p>
                </div>
            </div>
            <div id="users-words">
                <p>Amount of words added by every user</p>
            </div>
        </div>
        <div class="tab-pane" id="history">
            <div id="words-history">
                <p>Amount of words added over the time</p>
            </div>
        </div>
        <div class="tab-pane" id="editorial">
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
                        <td><?php echo empty($item['names']) ? $item['username'] : $item['names']; ?></td>
                        <td><?php if (isset($allComments[$item['user_id']])) {
                                echo $allComments[$item['user_id']]['count'];
                            } ?></td>
                        <td><?php if (isset($last7Days[$item['user_id']]['count'])) {
                                echo $last7Days[$item['user_id']]['count'];
                            } ?></td>
                        <td><?php if (isset($last24Hours[$item['user_id']]['count'])) {
                                echo $last24Hours[$item['user_id']]['count'];
                            } ?></td>

                        <td><?php if (isset($allApprovals[$item['user_id']])) {
                                echo $allApprovals[$item['user_id']]['count'];
                            } ?></td>
                        <td><?php if (isset($last7DaysApprovals[$item['user_id']]['count'])) {
                                echo $last7DaysApprovals[$item['user_id']]['count'];
                            } ?></td>
                        <td><?php if (isset($last24HoursApprovals[$item['user_id']]['count'])) {
                                echo $last24HoursApprovals[$item['user_id']]['count'];
                            } ?></td>
                    </tr>
                <?php endforeach; ?>

                </tbody>
            </table>
            <h2 class="text-center">All reviews visualization</h2>
            <svg id="barchart"></svg>
            <svg id="piechart"></svg>
        </div>

    </div>
    </div>
</div>