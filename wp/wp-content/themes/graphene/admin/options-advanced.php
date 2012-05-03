<?php
function graphene_options_advanced() { 
    global $graphene_settings;
    ?>
        
    <input type="hidden" name="graphene_advanced" value="true" />    
    
    <?php /* Site Preview */ ?>
    <div class="postbox">
        <div class="head-wrap">
            <div title="Click to toggle" class="handlediv"><br /></div>
            <h3 class="hndle"><?php _e( 'Preview', 'graphene' ); ?></h3>
        </div>
        <div class="panel-wrap inside">
            <table class="form-table">
                <tr>
                    <td>
                        <input type="checkbox" name="graphene_settings[enable_preview]" id="enable_preview" <?php checked( $graphene_settings['enable_preview'] ); ?> value="true" />
                        <label for="enable_preview"><?php _e( 'Enable preview of your site on the Graphene Theme Options page', 'graphene' ); ?></label>
                    </td>
                </tr>
            </table>
        </div>
    </div>  
    
    
    <?php /* Action hooks widgets areas */ ?>
    <div class="postbox">
        <div class="head-wrap">
            <div title="Click to toggle" class="handlediv"><br /></div>
            <h3 class="hndle"><?php _e( 'Action Hooks Widget Areas', 'graphene' ); ?></h3>
        </div>
        <div class="panel-wrap inside">
        	<p><?php _e("This option enables you to place virtually any content to every nook and cranny in the theme, by attaching widget areas to the theme's action hooks.", 'graphene' ); ?></p>
            <p><?php _e("All action hooks available in the Graphene Theme are listed below. Click on the filename to display all the action hooks available in that file. Then, tick the checkbox next to an action hook to make a widget area available for that action hook.", 'graphene' ); ?></p>
            
            <ul class="graphene-action-hooks">    
                <?php                
                $actionhooks = graphene_get_action_hooks();
                foreach ( $actionhooks as $actionhook) : 
                    $file = $actionhook['file']; 
                ?>
                    <li>
                        <p class="hooks-file"><a href="#" class="toggle-widget-hooks" title="<?php _e( 'Click to show/hide the action hooks for this file', 'graphene' ); ?>"><?php echo $file; ?></a></p>
                        <ul class="hooks-list">
                            <li class="widget-hooks<?php if(count(array_intersect( $actionhook['hooks'], $graphene_settings['widget_hooks'] ) ) == 0) echo ' hide'; ?>">
                    <?php foreach ( $actionhook['hooks'] as $hook) : ?>
                                <input type="checkbox" name="graphene_settings[widget_hooks][]" value="<?php echo $hook; ?>" id="hook_<?php echo $hook; ?>" <?php if ( in_array( $hook, $graphene_settings['widget_hooks'] ) ) echo 'checked="checked"'; ?> /> <label for="hook_<?php echo $hook; ?>"><?php echo $hook; ?></label><br />
                    <?php endforeach; ?>
                            </li>
                        </ul>
                    </li>
                <?php endforeach; ?>
            </ul>
            
            <p style="padding-top: 10px;text-align: right;"><a href="themes.php?page=graphene_options&tab=advanced&rescan_hooks=true" class="button-secondary"><?php _e( 'Rescan action hooks', 'graphene' ); ?></a></p>
        </div>
    </div>
    
<?php } // Closes the graphene_options_advanced() function definition