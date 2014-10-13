<script src="<?php echo base_url();?>public/js/jquery-2.0.2.min.js"></script>

<?php if($draft):?>

    <script src="<?php echo base_url();?>public/js/annotator-full.min.js"></script>
    <script>
        !function(){
            var $body = $('body'),
                bookId = $body.data('book-id');
            $body.annotator()
                .annotator('addPlugin', 'Store', {
                    prefix: '/annotations',
                    urls: {
                        // These are the default URLs.
                        create:  '/create/'+bookId,
                        read:    '/read/'+bookId+'/:id',
                        update:  '/update/:id',
                        destroy: '/destroy/:id',
                        search:  '/search'
                    }
                });
        }();

    </script>
<?php endif;?>
</body>
</html>