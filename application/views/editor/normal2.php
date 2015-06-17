<div class="container pubsweet-main" id="normal-container">

    <div class="row-fluid">
        <div id="top"></div>
        <div id="editor" contenteditable="true" data-action-url="<?php echo base_url('chapter/saveContent'); ?>"
            data-chapter="<?php echo $chaptername['id']; ?>">
            <?php echo empty($chaptername['content']) ? $chaptername['title'] : $chaptername['content']; ?>
        </div>
        <div id="bottom">
        <div class="content-Slider">
            <div class="pull-left">
                        <span class="small">
                            A
                        </span>
                        <span class="high">
                            A-
                        </span>
            </div>
            <div id="slider"><span style="margin-left:24%">_</span></div>
            <div class="pull-right">
                        <span class="small">
                            A
                        </span>
                        <span class="high">
                            A+
                        </span>
            </div>
        </div>
        </div>
    </div>

</div>
