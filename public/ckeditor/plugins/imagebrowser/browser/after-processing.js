/**
 * Created by jgutix on 5/6/15.
 */
window.MyGlobal = {
    init: function(){
        $('a.thumbnail img').each(function(){
            var $img = $(this);
            $img.append($('<span></span>', {text: $img.src}));
        });
    }
};
