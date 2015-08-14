<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * This class contains the logic and the functions 
 * that extend the integrations tab of LifterLMS.
 * This class has functions to add the additional
 * content, save the additional content, and 
 * create a custom template to be used for
 * email the courses.
 */
class LLMS_Settings_Integrations_SurveyMonkey 
{	
	/**
	 * Class constructor. This constructor attaches functions in 
	 * this file to the appropriate actions.
	 *
	 * @since 0.1.0
	 * @version  0.8.1
	 * @return void
	 */
	public function __construct() 
	{
		// Add filter to when settings page is loaded
		add_filter( 'lifterlms_integrations_settings', array( $this, 'get_integration_settings' ), 10, 1);
		
		// Add filter for when settings page is updated
		add_action( 'lifterlms_intergration_settings_update_surveymonkey', array( 'LLMS_SurveyMonkey', 'UpdateSurveys' ), 1 );
		
		// Confirms that the plugin is active and registers hooks for the class
		add_action('init', array( $this, 'Enabled'), 10, 1);
		add_action('init', array( $this, 'register_hooks'), 10, 1);
	}

	/**
	 * This function is responsible for extending the tabs
	 * contained on the integration pane of LifterLMS.
	 * 
	 * @param  array $content Array of field for intergrations
	 * @return array Updated array of fields
	 */
	public function get_integration_settings($content) 
	{
		$content[] = array(
			'type' => 'sectionstart',
			'id' => 'surveymonkey_options', 
			'class' =>'top'
		);
		$content[] = array( 
			'title' => __( 'SurveyMonkey Settings', 'lifterlms' ), 
			'type' => 'title', 
			'desc' => '', 
			'id' => 'surveymonkey_options' 
		);

		$content[] = array(
				'desc' 		=> __( 'Enable SurveyMonkey', 'lifterlms' ),
				'id' 		=> 'lifterlms_surveymonkey_enabled',
				'type' 		=> 'checkbox',
				'default'	=> 'no',
				'desc_tip'	=> true,
		);

		// only show when enabled
		if (self::Enabled() == 'yes') 
		{
			$default_email = 'Congratulations {first_name} {last_name}!

You have successfully completed {course_name}! We hope that it was beneficial for you. If you have the time, we would love to hear your thoughts about the course.

If you have the time, could you please take five minutes to respond to a quick survey about the course? The link to the survey is {survey_link}.

Thank you so much!';

			$content[] = array(
					'title' => __( 'API Key', 'lifterlms' ),
					'desc' 		=> __( 'Api key provided by SurveyMonkey', 'lifterlms' ),
					'id' 		=> 'lifterlms_surveymonkey_apikey',
					'type' 		=> 'text',
					'default'	=> '',
					'desc_tip'	=> true,
			);

			$content[] = array(
					'title' => __( 'Access Token', 'lifterlms' ),
					'desc' 		=> __( 'Access Token provided by SurveyMonkey', 'lifterlms' ),
					'id' 		=> 'lifterlms_surveymonkey_accesstoken',
					'type' 		=> 'text',
					'default'	=> '',
					'desc_tip'	=> true,
			);

			$content[] = array(
				'title' => '',
				'value' => __( 'Update Surveys', 'lifterlms' ),
				'type' 		=> 'button',
			);

			$content[] = array(
					'title' => __( 'Email Message Template', 'lifterlms' ),
					'desc' 		=> __( 'Template to use for emailing survey link after a user has completed a course.  
										You can include any of the following merge fields to customize the email.
										<br>{site_title}
										<br>{user_login}
										<br>{site_url}
										<br>{first_name}
										<br>{last_name}
										<br>{email_address}
										<br>{course_name}
										<br>{survey_link}
										<br>{current_date}</p>', 'lifterlms' ),
					'id' 		=> 'lifterlms_surveymonkey_emailtemplate',
					'type' 		=> 'textarea',
					'default'	=> $default_email,
					'desc_tip'	=> true,
					'class' 	=> 'email-template'
			);
			$content[] = array(
				'title' => '',
				'value' => __( 'Restore Default', 'lifterlms' ),
				'type' 	=> 'button',
			);
			$content[] = array(
				'title' => '',
				'value' => __( 'Restore Default', 'lifterlms' ),
				'type' 	=> 'custom-html',
			);
		}

		$content[] = array( 'type' => 'sectionend', 'id' => 'surveymonkey_options');
		return $content;
	}

	/**
	 * This function checks to see if this plugin is validated.
	 *
	 * @since 0.1.0
	 * @version  0.8.1
	 * @return void
	 */
	public static function Enabled () {

		return get_option('lifterlms_surveymonkey_enabled', '');
	}

	/**
	 * This function registers the hooks that are used in this plugin.
	 * This function is responsible for doing the actions that update the plugin.
	 *
	 * @since 0.1.0
	 * @version  0.8.1
	 * @return void
	 */
	public function register_hooks() 
	{
		if ( isset($_POST['save']) && strtolower($_POST['save']) == 'update surveys') 
		{
			do_action('lifterlms_intergration_settings_update_surveymonkey');
			echo '<div class="updated"><p><strong>Surveys Updated.</strong></p></div>';
		}
	}
}

return new LLMS_Settings_Integrations_SurveyMonkey();