/**
 * Created with JetBrains PhpStorm.
 * User: jgutix
 * Date: 08-26-13
 * Time: 10:01 PM
 */
/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

/**
 * @fileOverview The Save plugin.
 */

(function() {
	var savebuttonCmd = {
		readOnly: 1,

		exec: function( editor ) {
			if ( editor.fire( 'savebutton' ) ) {
                var $this = $(editor.element.$);
                $.post($this.data('action-url'), {'content': $this.html(), 'id': $this.data('chapter')}, function(resp){
                    if(resp.ok){
                        alert('Well done');
                    }
                },'json')
			}
		}
	};
    var backCmd = {
		readOnly: 1,

		exec: function( editor ) {
			 editor.fire( 'back' );
            window.location.href = $(editor.element.$).data('back-url');
		}
	};

	var pluginName = 'savebutton';

	// Register a plugin named "save".
	CKEDITOR.plugins.add( pluginName, {
//		lang: 'af,ar,bg,bn,bs,ca,cs,cy,da,de,el,en,en-au,en-ca,en-gb,eo,es,et,eu,fa,fi,fo,fr,fr-ca,gl,gu,he,hi,hr,hu,id,is,it,ja,ka,km,ko,ku,lt,lv,mk,mn,ms,nb,nl,no,pl,pt,pt-br,ro,ru,si,sk,sl,sq,sr,sr-latn,sv,th,tr,ug,uk,vi,zh,zh-cn', // %REMOVE_LINE_CORE%
        lang: 'en',
//		icons: 'savebutton', // %REMOVE_LINE_CORE%
		hidpi: true, // %REMOVE_LINE_CORE%
		init: function( editor ) {
			// Save plugin is for replace mode only.
//			if ( editor.elementMode != CKEDITOR.ELEMENT_MODE_REPLACE )
//				return;

			editor.addCommand( pluginName, savebuttonCmd );
			editor.addCommand( 'back', backCmd );

			editor.ui.addButton && editor.ui.addButton( 'Savebutton', {
				label: 'Save',
				command: pluginName,
				toolbar: 'document,10'
			});
            editor.ui.addButton && editor.ui.addButton( 'Backbutton', {
				label: 'Back',
				command: 'back',
				toolbar: 'document,10'
			});
		}
	});
})();

/**
 * Fired when the user clicks the Save button on the editor toolbar.
 * This event allows to overwrite the default Save button behavior.
 *
 * @since 4.2
 * @event save
 * @member CKEDITOR.editor
 * @param {CKEDITOR.editor} editor This editor instance.
 */