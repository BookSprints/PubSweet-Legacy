/**
 * @license Copyright (c) 2003-2014, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

/**
 * @fileOverview The "colorbutton" plugin that makes it possible to assign
 *               text and background colors to editor contents.
 *
 */
CKEDITOR.plugins.add( 'customflagpicker', {
    requires: 'panelbutton,floatpanel',
    icons: 'icon-flag.png', // %REMOVE_LINE_CORE%
    hidpi: true, // %REMOVE_LINE_CORE%
    init: function( editor ) {
        var config = editor.config,
            lang = editor.lang.colorbutton,
            termId = config['term_id'];

        var clickFn;

        if ( !CKEDITOR.env.hc ) {
            addButton( 'FlagPicker', 'fore', "Flags", 10 );
        }

        function addButton( name, type, title, order ) {
            var style = new CKEDITOR.style( config[ 'colorButton_' + type + 'Style' ] ),
                colorBoxId = CKEDITOR.tools.getNextId() + '_colorBox';

            editor.ui.add( name, CKEDITOR.UI_PANELBUTTON, {
                label: title,
                title: title,
                modes: { wysiwyg: 1 },
                editorFocus: 0,
                toolbar: 'colors,' + order,
                allowedContent: 'img[alt,dir,id,lang,longdesc,!src,title]{*}(*)',
                requiredContent: 'img[alt,src]',
                icon: 'plugins/customflagpicker/icons/icon-flag.png',
                panel: {
                    css: CKEDITOR.skin.getPath( 'editor' ),
                    attributes: { role: 'listbox', 'aria-label': lang.panelTitle }
                },

                onBlock: function( panel, block ) {
                    block.autoSize = true;
                    block.element.addClass( 'cke_colorblock' );
                    block.element.setHtml( renderColors( panel, type, colorBoxId ) );
                    // The block should not have scrollbars (#5933, #6056)
                    block.element.getDocument().getBody().setStyle( 'overflow', 'hidden' );

                    CKEDITOR.ui.fire( 'ready', this );

                    var keys = block.keys;
                    var rtl = editor.lang.dir == 'rtl';
                    keys[ rtl ? 37 : 39 ] = 'next'; // ARROW-RIGHT
                    keys[ 40 ] = 'next'; // ARROW-DOWN
                    keys[ 9 ] = 'next'; // TAB
                    keys[ rtl ? 39 : 37 ] = 'prev'; // ARROW-LEFT
                    keys[ 38 ] = 'prev'; // ARROW-UP
                    keys[ CKEDITOR.SHIFT + 9 ] = 'prev'; // SHIFT + TAB
                    keys[ 32 ] = 'click'; // SPACE
                },

                refresh: function() {
                    if ( !editor.activeFilter.check( style ) )
                        this.setState( CKEDITOR.TRISTATE_DISABLED );
                },

                // The automatic colorbox should represent the real color (#6010)
                onOpen: function() {

                    var selection = editor.getSelection(),
                        block = selection && selection.getStartElement(),
                        path = editor.elementPath( block ),
                        color;

                    if ( !path )
                        return;

                    // Find the closest block element.
                    block = path.block || path.blockLimit || editor.document.getBody();

                    // The background color might be transparent. In that case, look up the color in the DOM tree.
                    do {
                        color = block && block.getComputedStyle( type == 'back' ? 'background-color' : 'color' ) || 'transparent';
                    }
                    while ( type == 'back' && color == 'transparent' && block && ( block = block.getParent() ) );

                    // The box should never be transparent.
                    if ( !color || color == 'transparent' )
                        color = '#ffffff';

//                    this._.panel._.iframe.getFrameDocument().getById( colorBoxId ).setStyle( 'background-color', color );

                    return color;
                }
            } );
        }


        function renderColors( panel, type, colorBoxId ) {
            var output = [],
                colors = config['colorButton_'+type+'colors'].split( ',' );

            var clickFn = CKEDITOR.tools.addFunction( function( color, type ) {
                if ( color == '?' ) {
                    var applyColorStyle = arguments.callee;

                    function onColorDialogClose( evt ) {
                        this.removeListener( 'ok', onColorDialogClose );
                        this.removeListener( 'cancel', onColorDialogClose );

                        evt.name == 'ok' && applyColorStyle( this.getContentElement( 'picker', 'selectedColor' ).getValue(), type );
                    }

                    editor.openDialog( 'colordialog', function() {
                        this.on( 'ok', onColorDialogClose );
                        this.on( 'cancel', onColorDialogClose );
                    } );

                    return;
                }

                editor.focus();

                panel.hide();

                editor.fire( 'saveSnapshot' );

                // Clean up any conflicting style within the range.
                editor.removeStyle( new CKEDITOR.style( config[ 'colorButton_' + type + 'Style' ], { color: 'inherit' } ) );

                if ( color ) {
                    var colorStyle = config[ 'colorButton_' + type + 'Style' ];

                    colorStyle.childRule = type == 'back' ?
                        function( element ) {
                            // It's better to apply background color as the innermost style. (#3599)
                            // Except for "unstylable elements". (#6103)
                            return isUnstylable( element );
                        } : function( element ) {
                        // Fore color style must be applied inside links instead of around it. (#4772,#6908)
                        return !( element.is( 'a' ) || element.getElementsByTag( 'a' ).count() ) || isUnstylable( element );
                    };

                    editor.applyStyle( new CKEDITOR.style( colorStyle, { color: color } ) );
                }

                editor.fire( 'saveSnapshot' );
            } );


            var insertFlag = CKEDITOR.tools.addFunction(function(image, title){

                var oEditor = CKEDITOR.currentInstance;
                var html = '<img src="'+server+'public/uploads/flags/'+image+'" alt="'+title+'"/>'

                var newElement = CKEDITOR.dom.element.createFromHtml( html, oEditor.document );
                oEditor.insertElement( newElement );
            });

            output.push('<table>');
            var flags = CKEDITOR.config.flags;
            for(var i in flags){
                output.push(

                    '<a class="cke_colormore" _cke_focus=1 hidefocus=true' +
                    ' title="', lang.more, '"' +
                    ' onclick="CKEDITOR.tools.callFunction(', insertFlag, ',\'', flags[i].image, '\',\''+flags[i].title+'\' );return false;"' +
                    ' href="javascript:void(\'', lang.more, '\')"', ' role="option">',
                    '<img src="'+server+'public/uploads/flags/'+flags[i].image+'"/>','&nbsp;'+flags[i].title, '</a>'
                ); // tr is later in the code.

            }
            return output.join( '' );
        }

        function isUnstylable( ele ) {
            return ( ele.getAttribute( 'contentEditable' ) == 'false' ) || ele.getAttribute( 'data-nostyle' );
        }

    }
} );

/**
 * Whether to enable the **More Colors*** button in the color selectors.
 *
 *		config.colorButton_enableMore = false;
 *
 * @cfg {Boolean} [colorButton_enableMore=true]
 * @member CKEDITOR.config
 */

/**
 * Defines the colors to be displayed in the color selectors. This is a string
 * containing hexadecimal notation for HTML colors, without the `'#'` prefix.
 *
 * **Since 3.3:** A color name may optionally be defined by prefixing the entries with
 * a name and the slash character. For example, `'FontColor1/FF9900'` will be
 * displayed as the color `#FF9900` in the selector, but will be output as `'FontColor1'`.
 *
 *		// Brazil colors only.
 *		config.colorButton_colors = '00923E,F8C100,28166F';
 *
 *		config.colorButton_colors = 'FontColor1/FF9900,FontColor2/0066CC,FontColor3/F00';
 *
 * @cfg {String} [colorButton_colors=see source]
 * @member CKEDITOR.config
 */
CKEDITOR.config.colorButton_colors = '000,800000,8B4513,2F4F4F,008080,000080,4B0082,696969,' +
    'B22222,A52A2A,DAA520,006400,40E0D0,0000CD,800080,808080,' +
    'F00,FF8C00,FFD700,008000,0FF,00F,EE82EE,A9A9A9,' +
    'FFA07A,FFA500,FFFF00,00FF00,AFEEEE,ADD8E6,DDA0DD,D3D3D3,' +
    'FFF0F5,FAEBD7,FFFFE0,F0FFF0,F0FFFF,F0F8FF,E6E6FA,FFF';

/**
 * Stores the style definition that applies the text foreground color.
 *
 *		// This is actually the default value.
 *		config.colorButton_foreStyle = {
 *			element: 'span',
 *			styles: { color: '#(color)' }
 *		};
 *
 * @cfg [colorButton_foreStyle=see source]
 * @member CKEDITOR.config
 */
CKEDITOR.config.colorButton_foreStyle = {
    element: 'span',
    styles: { 'color': '#(color)' },
    overrides: [ {
        element: 'font', attributes: { 'color': null }
    } ]
};

/**
 * Stores the style definition that applies the text background color.
 *
 *		// This is actually the default value.
 *		config.colorButton_backStyle = {
 *			element: 'span',
 *			styles: { 'background-color': '#(color)' }
 *		};
 *
 * @cfg [colorButton_backStyle=see source]
 * @member CKEDITOR.config
 */
CKEDITOR.config.colorButton_backStyle = {
    element: 'span',
    styles: { 'background-color': '#(color)' }
};
