/**
 * Created with JetBrains PhpStorm.
 * User: juancarlosg
 * Date: 9/3/13
 * Time: 10:58 AM
 */
/**
 * @license Copyright © 2013 Stuart Sillitoe <stuart@vericode.co.uk>
 * strInsert - A Custom Dropdown in CKEditor 4
 * This is open source, can modify it as you wish.
 *
 * strInsert by Stuart Sillitoe
 * stuartsillitoe.co.uk
 *
 */
CKEDITOR.plugins.add('customlanguage',
{
    requires : ['richcombo'],
    init : function( editor )
    {

        $.ajax({
            type: "GET",
            url: editor.config.server+'language/all',
            async: false,
            data: {},
            dataType: 'JSON',
            success: function(resp)
            {
                var resp = resp;
                //  array of strings to choose from that'll be inserted into the editor

                /*strings.push(['@@FAQ::displayList()@@', 'FAQs', 'FAQs']);
                 strings.push(['@@Glossary::displayList()@@', 'Glossary', 'Glossary']);
                 strings.push(['@@CareerCourse::displayList()@@', 'Career Courses', 'Career Courses']);
                 strings.push(['@@CareerProfile::displayList()@@', 'Career Profiles', 'Career Profiles']);*/

                // add the menu to the editor
                editor.ui.addRichCombo('customlanguage',
                    {
                        label:     'Language',
                        title:     'Language',
                        voiceLabel: 'Language',
                        className:   'cke_format',
                        multiSelect:false,
                        panel:
                        {
                            css: [ editor.config.contentsCss, CKEDITOR.skin.getPath('editor') ],
                            voiceLabel: editor.lang.panelVoiceLabel
                        },

                        init: function()
                        {
                            this.startGroup( "Language" );
                            for (var i in resp)
                            {
                                /* value, text, title*/
                                this.add(resp[i]['iso_code'], resp[i]['code_language'], resp[i]['english_name']);
                            }
                        },

                        onClick: function( value )
                        {
                            editor.fire( 'changeLanguage', value );

                        }
                    });

            }

        });

    }
});
