<div class="container pubsweet-main">
    <h1>Showing logins info for last <?php echo $days;?> days</h1>
    <?php $availableDays = array(7, 15, 30);?>
    <div class="pull-right">Show last:
        <?php foreach($availableDays as $day):?>
            <?php if($days == $day){
                echo $day;continue;
            }?>
        <a href="<?php echo base_url('admin/stats/'.$day);?>">
            <span class="badge badge-success"><?php echo $day;?></span>
        </a>
        <?php endforeach;?> days</div>
    <div id="graph" data-last-days="<?php echo $days;?>"></div>
    <div class="row-fluid">
        <table class="table table-striped">
            <tr>
                <th>Username</th>
                <th>Time</th>
            </tr>
            <?php foreach ($last as $item) : ?>

                <tr>
                    <td><?php echo $item['username']; ?></td>
                    <td><?php echo $item['time']; ?></td>
                </tr>
            <?php endforeach; ?>

        </table>
    </div>

</div>
