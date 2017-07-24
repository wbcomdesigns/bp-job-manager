<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<div class="wpbpjm-adming-setting">
    <div class="wpbpjm-tab-header">
        <h3><?php _e( 'Have some questions?', WPBPJM_TEXT_DOMAIN );?></h3>
    </div>

    <div class="wpbpjm-admin-settings-block">
        <div id="wpbpjm-settings-tbl">
            <div class="wpbpjm-admin-row">
                <div>
                   <button class="wpbpjm-accordion">
                    <?php _e( 'Is this plugin dependent on any other plugin?', WPBPJM_TEXT_DOMAIN );?>
                    </button>
                    <div class="panel">
                        <p> 
                            <?php _e( 'Since this plugin deals with jobs, in buddypress user profiles, so to have the plugin fully functional, you must have the following plugins:', WPBPJM_TEXT_DOMAIN );?>
                            <ol type="1">
                            	<li>BuddyPress</li>
                            	<li>WordPress Job Manager</li>
                            	<li>WordPress Job Manager - Applications</li>
                            	<li>WordPress Job Manager - Resume Manager</li>
                            </ol>
                        </p>
                    </div>
                </div>
            </div>

            <div class="wpbpjm-admin-row">
                <div>
                   <button class="wpbpjm-accordion">
                    <?php _e( 'What does the plugin setting : <strong>Member Types for Job Management</strong> mean?', WPBPJM_TEXT_DOMAIN );?>
                    </button>
                    <div class="panel">
                        <p> 
                            <?php _e( 'This setting means that any user with the capability/member type saved in this setting will get the <strong>Job</strong> tab in his/her user profile menu. He/She will be acting as the employer and can manage the jobs.', WPBPJM_TEXT_DOMAIN );?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="wpbpjm-admin-row">
                <div>
                   <button class="wpbpjm-accordion">
                    <?php _e( 'What does the plugin setting : <strong>Member Types for Resume Management</strong> mean?', WPBPJM_TEXT_DOMAIN );?>
                    </button>
                    <div class="panel">
                        <p>
                            <?php _e( 'This setting means that any user with the capability/member type saved in this setting will get the <strong>Resume</strong> tab in his/her user profile menu. He/She will be acting as the candidate role and can apply to the jobs posted by the employers.', WPBPJM_TEXT_DOMAIN );
                            ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="wpbpjm-admin-row">
                <div>
                   <button class="wpbpjm-accordion">
                    <?php _e( 'How to go for any custom development?', WPBPJM_TEXT_DOMAIN );?>
                    </button>
                    <div class="panel">
                        <p>
                            <?php _e( 'If you need additional help you can contact us for <a href="https://wbcomdesigns.com/contact/" target="_blank" title="Custom Development by Wbcom Designs">Custom Development</a>.', WPBPJM_TEXT_DOMAIN );
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>