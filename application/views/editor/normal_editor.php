<div id="form-editor">
    <div class="container pubsweet-main" id="normal-container">

        <div class="row-fluid">
            <form action="<?php echo base_url('chapter/saveContent'); ?>" method="post" id="form-save-content"
                onsubmit="return false;">
                <input type="hidden" name="id" value="<?php echo $chaptername['id']; ?>"/>
                <textarea name="content" id="editor" cols="30" rows="10" class="hide"
                          autofocus="autofocus" >
                    <?php echo empty($chaptername['content']) ? '<h1>' . $chaptername['title'] . '</h1>' : $chaptername['content']; ?></textarea>
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
            </form>
        </div>
    </div>
</div>