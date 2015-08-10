<?php
if ( ! defined( 'ABSPATH' ) ) exit;


class LLMS_Settings_Integrations_SurveyMonkey 
{
	
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
		}

		$content[] = array( 'type' => 'sectionend', 'id' => 'surveymonkey_options');
		return $content;
	}

	public static function Enabled () {

		return get_option('lifterlms_surveymonkey_enabled', '');
	}

	
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