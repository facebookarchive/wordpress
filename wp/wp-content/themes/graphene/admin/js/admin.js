jQuery(document).ready(function($){
        $('.meta-box-sortables .head-wrap').click(function(){
                $(this).next().toggle();
                return false;
        }).next().hide();

        // Toggle all
        $('.toggle-all').click(function(){
                $('.meta-box-sortables .head-wrap').next().toggle();
                return false;
        })

        // Show/Hide the slider_type specific options
        $('input[name="graphene_settings\\[slider_type\\]"]').change(function(){
                $('[class*="row_slider_type"]').hide();
                $('.row_slider_type_'+$(this).val()).fadeIn();
        });

        // Show/Hide home page panes specific options
        $('input[name="graphene_settings\\[show_post_type\\]"]').change(function(){
                $('[id*="row_show_post_type"]').hide();
                $('#row_show_post_type_'+$(this).val()).fadeIn();
                if ($(this).val()=='cat-latest-posts'){
                        $('#row_show_post_type_latest-posts').fadeIn();
                }
        });

        // To hide/show complete section
        $('input[data-toggleOptions]').change(function(){
                var target = $(this).attr('rel');
                if ( target )
                        $('.'+target).fadeToggle();
                else
                        $(this).closest('table').next().fadeToggle();
        });

        // To Show/Hide the widget hooks
        $('a.toggle-widget-hooks').click(function(){
                $(this).closest('li').find('li.widget-hooks').fadeToggle();
                return false;
        });
        
        // Non-essential options display settings
        /* Disabled for now, until proper API is implemented for feature pointers in WordPress core
        var nonEssentialOptions = grapheneGetCookie('graphene-neod'); // neod = Non Essential Options Display
        if (nonEssentialOptions == 'true'){
                $('.non-essential-option, .toggle-essential-options, .nav-tab-advanced').show();
                $('.toggle-all-options').hide();
        } else {
                $('.non-essential-option, .toggle-essential-options, .nav-tab-advanced').hide();
                $('.toggle-all-options').show();
        }

        $('.toggle-essential-options').click(function(){
                grapheneSetCookie('graphene-neod', false, 100);
                $('.non-essential-option, .toggle-essential-options, .nav-tab-advanced').hide();
                $('.toggle-all-options').show();
                return false;
        });
        $('.toggle-all-options').click(function(){
                grapheneSetCookie('graphene-neod', true, 100);
                $('.non-essential-option, .toggle-essential-options, .nav-tab-advanced').show();
                $('.toggle-all-options').hide();
                return false;
        });
        */
       
        // Remember the opened options panes
        $('.meta-box-sortables .head-wrap, .toggle-all').click(function(){
                var postboxes = $('.left-wrap .postbox');
                var openboxes = new Array();
                $('.left-wrap .panel-wrap:visible').each(function(index){   
                        var openbox = $(this).parent();
                        openboxes.push(postboxes.index(openbox));                        
                });                    
                grapheneSetCookie('graphene-tab-'+graphene_tab+'-boxes', openboxes.join(','), 100);                    
        });

        // reopen the previous options panes
        var oldopenboxes = grapheneGetCookie('graphene-tab-'+graphene_tab+'-boxes');                
        if (oldopenboxes != null && oldopenboxes != '') {
                var boxindexes = oldopenboxes.split(',');                    
                for (var boxindex in boxindexes){                            
                        $('.left-wrap .postbox:eq('+boxindexes[boxindex]+')').find('.panel-wrap').show();
                }
        }
        
        
        // To support the Media Uploader/Gallery picker in the theme options
        var uploadparent = 0;
        var old_send_to_editor = window.send_to_editor;
        var old_tb_remove = window.tb_remove;

        jQuery('.upload_image_button').click(function(){
            uploadparent = jQuery(this).closest('td');
            tb_show('', 'media-upload.php?post_id=0&amp;type=image&amp;TB_iframe=true');
            return false;
        });

        window.tb_remove = function() {
            uploadparent = 0;
            old_tb_remove();
        }

        window.send_to_editor = function(html) {
            if(uploadparent){              
                imgurl = jQuery('img',html).attr('src');
                uploadparent.find('input[type="text"]').attr('value', imgurl);
                tb_remove();
            } else {
                old_send_to_editor();
            }
        }
        
                
        /* Reordering and admin for the social profile links */
        if (graphene_tab == 'general'){
							
                $('#socialprofile-sortable').sortable({ items: '.socialprofile-table', placeholder: 'socialprofile-dragging', opacity: .8 });
                function delete_socialprofile() {
                        $(this).closest('table').remove();
                        return false;
                }
                $('.socialprofile-del').bind('click', delete_socialprofile);                
                $('#new-socialprofile-type').change(function(){                    
                        if ($('#new-socialprofile-type').val() != 'custom') {
                                $('#new-socialprofile-iconurl').closest('tr').hide(); // the the custom icon url input
                                $('#new-socialprofile-title').val($('#new-socialprofile-type option').filter(":selected").text()); // prefill the title for the user                        
                        } 
                        else {
                                $('#new-socialprofile-iconurl').closest('tr').show();
                        }
                        if ($('#new-socialprofile-type').val() != 'rss') { $('#new-socialprofile-url-description').hide(); } 
                        else { $('#new-socialprofile-url-description').show(); }
                });
                $('#new-socialprofile-add').click(function(){
                        var spData = $('#new-socialprofile-data').data();
                        var $spType = $('#new-socialprofile-type');
                        var $spName = $('#new-socialprofile-type option').filter(":selected").html();
                        var $spTitle = $('#new-socialprofile-title');
                        var $spUrl = $('#new-socialprofile-url');
                        var $spIconUrl = $('#new-socialprofile-iconurl');
                        if ($spType.val() == '-') { $spType.focus(); }
                        else if (!$spTitle.val()) { $spTitle.focus(); }
                        else if ($spType.val() != 'rss' && !$spUrl.val()) { $spUrl.focus(); }
                        else if ($spType.val() == 'custom' && !$spIconUrl.val()){ $spIconUrl.focus(); }
                        else {
                                var ix = $('#socialprofile-next-index').val();

                                var i18n_title = $spName;
                                var rowspan = 2;
                                if ( $spType.val() != 'custom' )
                                        var icon_url = spData.iconUrl + $spType.val() + '.png';
                                else
                                        var icon_url = $spIconUrl.val();
                                var icon = '<div class="mysocial social-'+$spType.val()+'"><img class="mysocial-icon" src="' + icon_url + '" alt="" /></div>';
                                var extraCustom = '';
                                if ($spType.val() == 'rss'){
                                        extraCustom = '<br /><span class="description">'+ $('#new-socialprofile-url-description').text() + '</span>';
                                }
                                else if ($spType.val() == spData.customTitle){
                                        rowspan = 3;
                                        icon = '<img class="mysocial-icon" src="'+$spIconUrl.val()+' " />';
                                        extraCustom = '\
                                                        </td>\
                                                </tr>\
                                                <tr>\
                                                        <th class="small-row">'+spData.textIconUrl+'</th>\
                                                        <td><input type="text" name="graphene_settings[social_profiles]['+ix+'][icon_url]" value="'+$spIconUrl.val()+'" class="widefat code" />';
                                }

                                $('#socialprofile-sortable').append(
                                        '<table class="form-table socialprofile-table">\
                                                <tr>\
                                                        <th scope="row" rowspan="'+rowspan+'" class="small-row">\
                                                                '+i18n_title+'<br />\
                                                                <input type="hidden" name="graphene_settings[social_profiles]['+ix+'][type]" value="'+$spType.val()+'" />\
                                                                <input type="hidden" name="graphene_settings[social_profiles]['+ix+'][name]" value="'+$spName+'" />\
                                                                '+icon+'\
                                                        </th>\
                                                        <th class="small-row">'+spData.textTitleAttr+'</th>\
                                                        <td><input type="text" name="graphene_settings[social_profiles]['+ix+'][title]" value="'+$spTitle.val()+'"  class="widefat code"/>\
                                                </tr>\
                                                <tr>\
                                                        <th class="small-row">'+spData.textUrl+'</th>\
                                                        <td>\
                                                                <input type="text" name="graphene_settings[social_profiles]['+ix+'][url]" value="'+$spUrl.val()+'" class="widefat code" />\
                                                                '+extraCustom+'\
                                                                <br /><span class="delete"><a href="#" class="socialprofile-del">'+spData.textDelete+'</a></span>\
                                                        </td>\
                                                </tr>\
                                        </table>'
                                );

                                // reset the new form
                                $('#socialprofile-next-index').val(ix+1);
                                $('option:first', $spType).attr('selected', 'selected');
                                $spTitle.val('');
                                $spUrl.val('');
                                $spIconUrl.val('').closest('tr').hide();
                                // rebind the del click event
                                $('.socialprofile-del').unbind('click');
                                $('.socialprofile-del').bind('click', delete_socialprofile);                        
                        }
                        return false;
                });
        } // end of graphene_tab 'general'
                               		
        /* jQuery UI Slider for the column widths options */
        if (graphene_tab == 'display') {
                                              		
                var gutter = 10;
                var grid_width = graphene_settings.grid_width;
                var container_width = graphene_settings.container_width;
                var container = container_width - gutter * 2;
                var content_2col = graphene_settings.column_width.two_col.content;
                var sidebar_left_3col = graphene_settings.column_width.three_col.sidebar_left;
                var sidebar_right_3col = graphene_settings.column_width.three_col.sidebar_right;

                /* Container */
                $( '#container_width-slider' ).slider({
                        min: 800,
                        max: 1400,
                        step: 5,
                        value: container_width,
                        slide: function( event, ui ) {
                                $( '#container_width' ).val( ui.value );
                                container_width = ui.value;
                                $( '.column_width-max-legend' ).html( ui.value + ' px' );
                                grid_width = (ui.value - gutter * 32) / 16;
                                $( '#grid_width' ).val( grid_width );
                                container = container_width - gutter * 2;

                                sidebar_2col = grid_width * 5 + gutter * 8;
                                sidebar_3col = grid_width * 4 + gutter * 6;

                                /* Update the two-column width settings */
                                $( "#column_width_2col-slider" ).slider( "option", "max", container - gutter );
                                $( "#column_width_2col-slider" ).slider( "option", "value", container - sidebar_2col - gutter );
                                $( "#column_width_2col_sidebar" ).val( sidebar_2col );
                                $( "#column_width_2col_content" ).val( container - sidebar_2col - gutter * 2 );

                                /* Update the three-column width settings */
                                $( "#column_width-slider" ).slider( "option", "max", ui.value - gutter * 2 );
                                $( "#column_width-slider" ).slider( "option", "values", [ sidebar_3col, ui.value - sidebar_3col - gutter * 2] );
                                $( "#column_width_sidebar_left" ).val( sidebar_3col );
                                $( "#column_width_sidebar_right" ).val( sidebar_3col );
                                $( "#column_width_content" ).val( grid_width * 8 + gutter * 14 );
                        }	
                });

                /* Two-column mode */
                $( '#column_width_2col-slider' ).slider({
                        min: gutter,
                        max: container - gutter,
                        value: content_2col + gutter,
                        step: 5,
                        slide: function( event, ui ) {
                                sidebar_2col = container - ui.value - gutter;
                                content_2col = ui.value - gutter;

                                $( "#column_width_2col_sidebar" ).val( sidebar_2col );
                                $( "#column_width_2col_content" ).val( content_2col );
                        }
                });

                /* Three-column mode */
                $( '#column_width-slider' ).slider({
                        range: true,
                        min: 0,
                        max: container,
                        values: [ sidebar_left_3col, container - sidebar_right_3col ],
                        step: 5,
                        slide: function( event, ui ) {
                                sidebar_left = ui.values[0];
                                sidebar_right = container - ui.values[1];
                                content = container - sidebar_left - sidebar_right - gutter * 4;

                                $( "#column_width_sidebar_left" ).val( sidebar_left );
                                $( "#column_width_sidebar_right" ).val( sidebar_right );
                                $( "#column_width_content" ).val( content );
                        }
                });
                
                
                /* Farbtastic colour picker */
                $('div.colorpicker').each(function(){
                    var $this = $(this);
                    $this.hide();                    
                    $this.farbtastic($this.siblings('input.color'));
                });                    
                $('input.color')
                    .focusin(function(){ $(this).siblings('div.colorpicker').show(); })
                    .focusout(function(){ $(this).siblings('div.colorpicker').hide(); });    
                
                $('.clear-color').click(function(){
                    $(this).siblings('input.color').attr('value', '').removeAttr('style');
                    return false;
                });

                // The widget preview
                $('#picker_bg_widget_header_border div, #picker_bg_widget_title div, #picker_bg_widget_title_textshadow div, #picker_bg_widget_header_top div, #picker_bg_widget_header_bottom div, #bg_widget_header_border, #bg_widget_title, #bg_widget_title_textshadow, #bg_widget_header_top, #bg_widget_header_bottom').bind('mouseup keyup', function(){
                        var borderColor = $.farbtastic('#picker_bg_widget_header_border').color;
                        var titleColor = $.farbtastic('#picker_bg_widget_title').color;
                        var shadowColor = $.farbtastic('#picker_bg_widget_title_textshadow').color;
                        var topColor = $.farbtastic('#picker_bg_widget_header_top').color;
                        var bottomColor = $.farbtastic('#picker_bg_widget_header_bottom').color;                        
                        $('.sidebar-wrap h3').attr('style', '\
                                background: ' + topColor + ';\
                                background: -moz-linear-gradient(' + topColor + ', ' + bottomColor + ');\
                                background: -webkit-linear-gradient(' + topColor + ', ' + bottomColor + ');\
                                background: linear-gradient(' + topColor + ', ' + bottomColor + ');\
                                border-color: ' + borderColor + ';\
                                color: ' + titleColor + ';\
                                text-shadow: 0 -1px 0 ' + shadowColor + ';\
                        ');
                });
                $('#picker_bg_widget_item div, #picker_bg_widget_box_shadow div, #bg_widget_item, #bg_widget_box_shadow').bind('mouseup keyup', function(){
                        var bgColor = $.farbtastic('#picker_bg_widget_item').color;
                        var shadowColor = $.farbtastic('#picker_bg_widget_box_shadow').color;
                        $('.sidebar-wrap').attr('style', '\
                                background: ' + bgColor + ';\
                                -moz-box-shadow: 0 0 5px ' + shadowColor + ';\
                                -webkit-box-shadow: 0 0 5px ' + shadowColor + ';\
                                box-shadow: 0 0 5px ' + shadowColor + ';\
                        ');
                });
                $('#picker_bg_widget_list div, #bg_widget_list').bind('mouseup keyup', function(){
                        $('.sidebar ul li').attr('style', 'border-color: ' + $.farbtastic('#picker_bg_widget_list').color + ';');
                });

                // The slider background preview
                $('#picker_bg_slider_top div, #picker_bg_slider_bottom div, #bg_slider_top, #bg_slider_bottom').bind('mouseup keyup', function(){
                        var colorTop = $.farbtastic('#picker_bg_slider_top').color;
                        var colorBottom = $.farbtastic('#picker_bg_slider_bottom').color;
                        $('#grad-box').attr('style', '\
                                background: ' + colorTop + ';\
                                background: linear-gradient(left top, ' + colorTop + ', ' + colorBottom + ');\
                                background: -moz-linear-gradient(left top, ' + colorTop + ', ' + colorBottom + ');\
                                background: -webkit-linear-gradient(left top, ' + colorTop + ', ' + colorBottom + ');\
                        ');
                });

                // Block button preview
                $('#picker_bg_button div, #picker_bg_button_label div, #picker_bg_button_label_textshadow div, #picker_bg_button_box_shadow div, #bg_button, #bg_button_label, #bg_button_label_textshadow, #bg_button_box_shadow').bind('mouseup keyup', function(){
                        var bgColor = $.farbtastic('#picker_bg_button').color;
                        var textColor = $.farbtastic('#picker_bg_button_label').color;
                        var textshadowColor = $.farbtastic('#picker_bg_button_label_textshadow').color;
                        var boxshadowColor = $.farbtastic('#picker_bg_button_box_shadow').color;
                        R = hexToR(bgColor) - 35;
                        G = hexToG(bgColor) - 35;
                        B = hexToB(bgColor) - 35;
                        color_bottom = 'rgb(' + R + ', ' + G + ', ' + B + ')';

                        $('.block-button').attr('style', '\
                                        background: ' + bgColor + ';\
                                        background: -moz-linear-gradient(' + bgColor + ', ' + color_bottom + ');\
                                        background: -webkit-linear-gradient(' + bgColor + ', ' + color_bottom + ');\
                                        background: linear-gradient(' + bgColor + ', ' + color_bottom + ');\
                                        border-color: ' + color_bottom + ';\
                                        text-shadow: 0 -1px 0 ' + textshadowColor + ';\
                                        color: ' + textColor + ';\
                                        -moz-box-shadow: 0 0 5px ' + boxshadowColor + ';\
                                        -webkit-box-shadow: 0 0 5px ' + boxshadowColor + ';\
                                        box-shadow: 0 0 5px ' + boxshadowColor + ';\
                        ');
                });

                // Archive title preview
                $('#picker_bg_archive_left div, #picker_bg_archive_right div, #bg_archive_left, #bg_archive_right').bind('mouseup keyup', function(){
                        var leftColor = $.farbtastic('#picker_bg_archive_left').color;
                        var rightColor = $.farbtastic('#picker_bg_archive_right').color;
                        $('.page-title').attr('style', '\
                                background: ' + leftColor + ';\
                                background: linear-gradient(left top, ' + leftColor + ', ' + rightColor + ');\
                                background: -moz-linear-gradient(left top, ' + leftColor + ', ' + rightColor + ');\
                                background: -webkit-linear-gradient(left top, ' + leftColor + ', ' + rightColor + ');\
                        ');
                });
                $('#picker_bg_archive_text div, #bg_archive_text').bind('mouseup keyup', function(){
                        $('.page-title span').css('color', $.farbtastic('#picker_bg_archive_text').color);
                });
                $('#picker_bg_archive_label div, #bg_archive_label').bind('mouseup keyup', function(){
                        $('.page-title').css('color', $.farbtastic('#picker_bg_archive_label').color);
                });
                $('#picker_bg_archive_textshadow div, #bg_archive_textshadow').bind('mouseup keyup', function(){
                        $('.page-title').css('text-shadow', '0 -1px 0 ' + $.farbtastic('#picker_bg_archive_textshadow').color);
                });


                // Colour presets
                var colour_presets = new Object();
                colour_presets.default = '{"bg_content_wrapper":"#e3e3e3","bg_content":"#fff","bg_meta_border":"#e3e3e3","bg_post_top_border":"#d8d8d8","bg_post_bottom_border":"#ccc","bg_widget_item":"#fff","bg_widget_list":"#e3e3e3","bg_widget_header_border":"#195392","bg_widget_title":"#fff","bg_widget_title_textshadow":"#555","bg_widget_header_bottom":"#1f6eb6","bg_widget_header_top":"#3c9cd2","bg_widget_box_shadow":"#BBBBBB","bg_slider_top":"#0F2D4D","bg_slider_bottom":"#2880C3","bg_button":"#2982C5","bg_button_label":"#fff","bg_button_label_textshadow":"#16497E","bg_button_box_shadow":"#555555","bg_archive_left":"#0F2D4D","bg_archive_right":"#2880C3","bg_archive_label":"#E3E3E3","bg_archive_text":"#fff","bg_archive_textshadow":"#333","content_font_colour":"#2c2b2b","title_font_colour":"#1772af","link_colour_normal":"#1772af","link_colour_visited":"#1772af","link_colour_hover":"#074d7c","bg_comments":"#E9ECF5","comments_text_colour":"#2C2B2B","threaded_comments_border":"#DDDDDD","bg_author_comments":"#FFFFFF","bg_author_comments_border":"#CCCCCC","author_comments_text_colour":"#2C2B2B","bg_comment_form":"#EEEEEE","comment_form_text":"#2C2B2B"}';
                colour_presets.dream_magnet = '{"bg_content_wrapper":"#e3e3e3","bg_content":"#fff","bg_meta_border":"#e3e3e3","bg_post_top_border":"#d8d8d8","bg_post_bottom_border":"#ccc","bg_widget_item":"#fff","bg_widget_list":"#e3e3e3","bg_widget_header_border":"#022328","bg_widget_title":"#fff","bg_widget_title_textshadow":"#04343a","bg_widget_header_bottom":"#06454c","bg_widget_header_top":"#005F6B","bg_widget_box_shadow":"#BBBBBB","bg_slider_top":"#06454c","bg_slider_bottom":"#005F6B","bg_button":"#005F6B","bg_button_label":"#fff","bg_button_label_textshadow":"#053a41","bg_button_box_shadow":"#555555","bg_archive_left":"#06454c","bg_archive_right":"#005F6B","bg_archive_label":"#b6d2d5","bg_archive_text":"#eae9e9","bg_archive_textshadow":"#033c42","content_font_colour":"#2c2b2b","title_font_colour":"#008C9E","link_colour_normal":"#008C9E","link_colour_visited":"#008C9E","link_colour_hover":"#005F6B","bg_comments":"#E9ECF5","comments_text_colour":"#2C2B2B","threaded_comments_border":"#DDDDDD","bg_author_comments":"#FFFFFF","bg_author_comments_border":"#005F6B","author_comments_text_colour":"#2C2B2B","bg_comment_form":"#EEEEEE","comment_form_text":"#2C2B2B"}';
                colour_presets.curiosity_killed = '{"bg_content_wrapper":"#DCE9BE","bg_content":"#fff","bg_meta_border":"#ffffff","bg_post_top_border":"#ffffff","bg_post_bottom_border":"#ffffff","bg_widget_item":"#fff","bg_widget_list":"#e3e3e3","bg_widget_header_border":"#640822","bg_widget_title":"#fff","bg_widget_title_textshadow":"#402222","bg_widget_header_bottom":"#74122e","bg_widget_header_top":"#99173C","bg_widget_box_shadow":"#BBBBBB","bg_slider_top":"#74122e","bg_slider_bottom":"#99173C","bg_button":"#99173C","bg_button_label":"#fff","bg_button_label_textshadow":"#59071e","bg_button_box_shadow":"#555555","bg_archive_left":"#74122e","bg_archive_right":"#99173C","bg_archive_label":"#fdd2de","bg_archive_text":"#fff","bg_archive_textshadow":"#6d0d0d","content_font_colour":"#2c2b2b","title_font_colour":"#99173C","link_colour_normal":"#99173C","link_colour_visited":"#99173C","link_colour_hover":"#5b0820","bg_comments":"#f3fedd","comments_text_colour":"#2C2B2B","threaded_comments_border":"#DCE9BE","bg_author_comments":"#fee6ed","bg_author_comments_border":"#99173C","author_comments_text_colour":"#2C2B2B","bg_comment_form":"#fff","comment_form_text":"#696969"}';

                // Apply colour preset                
                $('select.colour-presets').bind('mouseup keyup change', function(){                        
                        var presetName = $('.colour-presets').val().replace( '-', '_' );
                        colour_preset = $.parseJSON( colour_presets[presetName] );
                        for ( var option_name in colour_preset ){
                                $elm = $('#' + option_name).siblings('.colorpicker');
                                $.farbtastic($elm).setColor(colour_preset[option_name]);                                
                        }
                        $('.colorpicker div').trigger('mouseup');
                });

        } // end of graphene_tab 'display'
});


function hexToR(h) {
    if ( h.length == 4 )
        return parseInt((cutHex(h)).substring(0,1)+(cutHex(h)).substring(0,1),16);
    if ( h.length == 7 )
        return parseInt((cutHex(h)).substring(0,2),16);
}
function hexToG(h) {
    if ( h.length == 4 )
        return parseInt((cutHex(h)).substring(1,2)+(cutHex(h)).substring(1,2),16);
    if ( h.length == 7 )
        return parseInt((cutHex(h)).substring(2,4),16);
}
function hexToB(h) {
    if ( h.length == 4 )
        return parseInt((cutHex(h)).substring(2,3)+(cutHex(h)).substring(2,3),16);
    if ( h.length == 7 )
        return parseInt((cutHex(h)).substring(4,6),16);
}
function cutHex(h) {return (h.charAt(0)=="#") ? h.substring(1,7):h}

function grapheneSetCookie(name,value,days) {
    if (days) {
        var date = new Date();
        date.setTime(date.getTime()+(days*24*60*60*1000));
        var expires = "; expires="+date.toGMTString();
    }
    else var expires = "";
    document.cookie = name+"="+value+expires+"; path=/";
}

function grapheneGetCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}

function grapheneDeleteCookie(name) {
    grapheneSetCookie(name,"",-1);
}