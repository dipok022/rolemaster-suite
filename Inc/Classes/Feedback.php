<?php
namespace ROLEMASTER\Inc\Classes;

use ROLEMASTER\Inc\Classes\Notifications\Base\User_Data;

// No, Direct access Sir !!!
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Feedback
 *
 * @author Jewel Theme <support@jeweltheme.com>
 */
class Feedback {

    use User_Data;

	/**
	 * Construct Method
	 *
	 * @return void
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
    public function __construct(){
        add_action( 'admin_enqueue_scripts' , [ $this,'admin_suvery_scripts'] );
        add_action( 'admin_footer' , [ $this , 'deactivation_footer' ] );
        add_action( 'wp_ajax_rolemaster_suite_deactivation_survey', array( $this, 'rolemaster_suite_deactivation_survey' ) );

    }


    public function proceed(){

        global $current_screen;
        if(
            isset($current_screen->parent_file)
            && $current_screen->parent_file == 'plugins.php'
            && isset($current_screen->id)
            && $current_screen->id == 'plugins'
        ){
           return true;
        }
        return false;

    }

    public function admin_suvery_scripts($handle){
        if('plugins.php' === $handle){
            wp_enqueue_style( 'rolemaster-suite-survey' , ROLEMASTER_ASSETS . 'css/rolemaster-suite-survey.css' );
        }
    }

    /**
     * Deactivation Survey
     */
    public function rolemaster_suite_deactivation_survey(){
        check_ajax_referer( 'rolemaster_suite_deactivation_nonce' );

        $deactivation_reason  = ! empty( $_POST['deactivation_reason'] ) ? sanitize_text_field( wp_unslash( $_POST['deactivation_reason'] ) ) : '';

        if( empty( $deactivation_reason )){
            return;
        }

        $email = get_bloginfo( 'admin_email' );
        $author_obj = get_user_by( 'email', $email );
        $user_id    = $author_obj->ID;
        $full_name  = $author_obj->display_name;

        $response = $this->get_collect_data( $user_id, array(
            'first_name'              => $full_name,
            'email'                   => $email,
            'deactivation_reason'     => $deactivation_reason,
        ) );

        return $response;
    }


    public function get_survey_questions(){

        return [
			'no_longer_needed' => [
				'title' => esc_html__( 'I no longer need the plugin', 'rolemaster-suite' ),
				'input_placeholder' => '',
			],
			'found_a_better_plugin' => [
				'title' => esc_html__( 'I found a better plugin', 'rolemaster-suite' ),
				'input_placeholder' => esc_html__( 'Please share which plugin', 'rolemaster-suite' ),
			],
			'couldnt_get_the_plugin_to_work' => [
				'title' => esc_html__( 'I couldn\'t get the plugin to work', 'rolemaster-suite' ),
				'input_placeholder' => '',
			],
			'temporary_deactivation' => [
				'title' => esc_html__( 'It\'s a temporary deactivation', 'rolemaster-suite' ),
				'input_placeholder' => '',
			],
			'rolemaster_suite_pro' => [
				'title' => sprintf( esc_html__( 'I have %1$s Pro', 'rolemaster-suite' ), ROLEMASTER ),
				'input_placeholder' => '',
				'alert' => sprintf( esc_html__( 'Wait! Don\'t deactivate %1$s. You have to activate both %1$s and %1$s Pro in order for the plugin to work.', 'rolemaster-suite' ), ROLEMASTER ),
			],
			'need_better_design' => [
				'title' => esc_html__( 'I need better design and presets', 'rolemaster-suite' ),
				'input_placeholder' => esc_html__( 'Let us know your thoughts', 'rolemaster-suite' ),
			],
            'other' => [
				'title' => esc_html__( 'Other', 'rolemaster-suite' ),
				'input_placeholder' => esc_html__( 'Please share the reason', 'rolemaster-suite' ),
			],
		];
    }


        /**
         * Deactivation Footer
         */
        public function deactivation_footer(){

        if(!$this->proceed()){
            return;
        }

        ?>
        <div class="rolemaster-suite-deactivate-survey-overlay" id="rolemaster-suite-deactivate-survey-overlay"></div>
        <div class="rolemaster-suite-deactivate-survey-modal" id="rolemaster-suite-deactivate-survey-modal">
            <header>
                <div class="rolemaster-suite-deactivate-survey-header">
                    <img src="<?php echo esc_url(ROLEMASTER_IMAGES . '/menu-icon.png'); ?>" />
                    <h3><?php echo wp_sprintf( '%1$s %2$s', ROLEMASTER, esc_html__( '- Feedback', 'rolemaster-suite' ) ); ?></h3>
                </div>
            </header>
            <div class="rolemaster-suite-deactivate-info">
                <?php echo wp_sprintf( '%1$s %2$s', esc_html__( 'If you have a moment, please share why you are deactivating', 'rolemaster-suite' ), ROLEMASTER ); ?>
            </div>
            <div class="rolemaster-suite-deactivate-content-wrapper">
                <form action="#" class="rolemaster-suite-deactivate-form-wrapper">
                    <?php foreach($this->get_survey_questions() as $reason_key => $reason){ ?>
                        <div class="rolemaster-suite-deactivate-input-wrapper">
                            <input id="rolemaster-suite-deactivate-feedback-<?php echo esc_attr($reason_key); ?>" class="rolemaster-suite-deactivate-feedback-dialog-input" type="radio" name="reason_key" value="<?php echo $reason_key; ?>">
                            <label for="rolemaster-suite-deactivate-feedback-<?php echo esc_attr($reason_key); ?>" class="rolemaster-suite-deactivate-feedback-dialog-label"><?php echo esc_html( $reason['title'] ); ?></label>
							<?php if ( ! empty( $reason['input_placeholder'] ) ) : ?>
								<input class="rolemaster-suite-deactivate-feedback-text" type="text" name="reason_<?php echo esc_attr( $reason_key ); ?>" placeholder="<?php echo esc_attr( $reason['input_placeholder'] ); ?>" />
							<?php endif; ?>
                        </div>
                    <?php } ?>
                    <div class="rolemaster-suite-deactivate-footer">
                        <button id="rolemaster-suite-dialog-lightbox-submit" class="rolemaster-suite-dialog-lightbox-submit"><?php echo esc_html__( 'Submit &amp; Deactivate', 'rolemaster-suite' ); ?></button>
                        <button id="rolemaster-suite-dialog-lightbox-skip" class="rolemaster-suite-dialog-lightbox-skip"><?php echo esc_html__( 'Skip & Deactivate', 'rolemaster-suite' ); ?></button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            var deactivate_url = '#';

            jQuery(document).on('click', '#deactivate-role-master-suite', function(e) {
                e.preventDefault();
                deactivate_url = e.target.href;
                jQuery('#rolemaster-suite-deactivate-survey-overlay').addClass('rolemaster-suite-deactivate-survey-is-visible');
                jQuery('#rolemaster-suite-deactivate-survey-modal').addClass('rolemaster-suite-deactivate-survey-is-visible');
            });

            jQuery('#rolemaster-suite-dialog-lightbox-skip').on('click', function (e) {
                e.preventDefault();
                window.location.replace(deactivate_url);
            });


            jQuery(document).on('click', '#rolemaster-suite-dialog-lightbox-submit', async function(e) {
                e.preventDefault();

                jQuery('#rolemaster-suite-dialog-lightbox-submit').addClass('rolemaster-suite-loading');

                var $dialogModal = jQuery('.rolemaster-suite-deactivate-input-wrapper'),
                    radioSelector = '.rolemaster-suite-deactivate-feedback-dialog-input';
                $dialogModal.find(radioSelector).on('change', function () {
                    $dialogModal.attr('data-feedback-selected', jQuery(this).val());
                });
                $dialogModal.find(radioSelector + ':checked').trigger('change');


                // Reasons for deactivation
                var deactivation_reason = '';
                var reasonData = jQuery('.rolemaster-suite-deactivate-form-wrapper').serializeArray();

                jQuery.each(reasonData, function (reason_index, reason_value) {
                    if ('reason_key' == reason_value.name && reason_value.value != '') {
                        const reason_input_id = '#rolemaster-suite-deactivate-feedback-' + reason_value.value,
                            reason_title = jQuery(reason_input_id).siblings('label').text(),
                            reason_placeholder_input = jQuery(reason_input_id).siblings('input').val(),
                            format_title_with_key = reason_value.value + ' - '  + reason_placeholder_input,
                            format_title = reason_title + ' - '  + reason_placeholder_input;

                        deactivation_reason = reason_value.value;

                        if ('found_a_better_plugin' == reason_value.value ) {
                            deactivation_reason = format_title_with_key;
                        }

                        if ('need_better_design' == reason_value.value ) {
                            deactivation_reason = format_title_with_key;
                        }

                        if ('other' == reason_value.value) {
                            deactivation_reason = format_title_with_key;
                        }
                    }
                });

                await jQuery.ajax({
                        url: '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>',
                        method: 'POST',
                        // crossDomain: true,
                        async: true,
                        // dataType: 'jsonp',
                        data: {
                            action: 'rolemaster_suite_deactivation_survey',
                            _wpnonce: '<?php echo esc_js( wp_create_nonce( 'rolemaster_suite_deactivation_nonce' ) ); ?>',
                            deactivation_reason: deactivation_reason
                        },
                        success:function(response){
                            window.location.replace(deactivate_url);
                        }
                });
                return true;
            });

            jQuery('#rolemaster-suite-deactivate-survey-overlay').on('click', function () {
                jQuery('#rolemaster-suite-deactivate-survey-overlay').removeClass('rolemaster-suite-deactivate-survey-is-visible');
                jQuery('#rolemaster-suite-deactivate-survey-modal').removeClass('rolemaster-suite-deactivate-survey-is-visible');
            });
        </script>
        <?php
    }

}