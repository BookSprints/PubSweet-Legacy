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

    CKEDITOR.dialog.add( 'customautosaveOptionsDialog', function( editor )
    {
        return {
            title : 'Auto save options',
            minWidth : 400,
            minHeight : 200,
            contents :
                [
                    {
                        id : 'customautosavedialog',
                        label : 'Settings',
                        elements :
                            [
                                {
                                    type : 'select',
                                    id : 'style',
                                    label : 'How often the document will be autosaved?',
                                    items :
                                        [
                                            [ 'Off', '0' ],
                                            [ '30 seconds', '30' ],
                                            [ '1 minute', '60' ],
                                            [ '5 minutes', '300' ],
                                            [ '10 minutes', '600' ]
                                        ],
                                    default: editor.config.autoSaveOptionTime != null ? editor.config.autoSaveOptionTime : 0,
                                    commit : function( data )
                                    {
                                        data.time = this.getValue();
                                    }
                                }
                            ]
                    }

                ],
            onOk: function(){
                var data = {};
                this.commitContent(data);
                $.post(editor.config.autoSaveOptionUrl, data, function(resp){
                    if(resp.ok){
                        editor.config.autoSaveOptionTime = parseInt(data.time);
                        CKEDITOR.config.autoSaveOptionTime = parseInt(data.time);//UI was not refreshing
                        startTimer(editor);
                    }
                }, 'json');
            }
        };
    });

	var pluginName = 'customautosave';

	// Register a plugin named "customautosave".
	CKEDITOR.plugins.add( pluginName, {
//		lang: 'af,ar,bg,bn,bs,ca,cs,cy,da,de,el,en,en-au,en-ca,en-gb,eo,es,et,eu,fa,fi,fo,fr,fr-ca,gl,gu,he,hi,hr,hu,id,is,it,ja,ka,km,ko,ku,lt,lv,mk,mn,ms,nb,nl,no,pl,pt,pt-br,ro,ru,si,sk,sl,sq,sr,sr-latn,sv,th,tr,ug,uk,vi,zh,zh-cn', // %REMOVE_LINE_CORE%
        lang: 'en',
//		icons: 'savebutton', // %REMOVE_LINE_CORE%
		hidpi: true, // %REMOVE_LINE_CORE%
		init: function( editor ) {
            CKEDITOR.config.autoSaveOptionTime = editor.config.autoSaveOptionTime
            editor.addCommand( 'customautosaveOptionsDialog', new CKEDITOR.dialogCommand( 'customautosaveOptionsDialog' ) );

			editor.ui.addButton && editor.ui.addButton( 'CustomautosaveOptions', {
				label: 'Auto Save',
				command: 'customautosaveOptionsDialog',
				toolbar: 'document,10'
			});
            startTimer(editor);
		}
	});

    var timeOutId = 0,
        startTimer = function (editor) {
            if (timeOutId) {
                clearInterval(timeOutId);
            }
            var delay = editor.config.autoSaveOptionTime != null ? editor.config.autoSaveOptionTime : 0;
            if(delay > 0 ){
                timeOutId = setInterval(function(){
                    editor.fire('save');
                }, delay * 1000);
            }

        };

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