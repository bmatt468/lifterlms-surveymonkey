<?php
/**
* Plugin Name: LifterLMS SurveyMonkey Integration
* Plugin URI: http://smawk.net/
* Description: Connect LifterLMS to SurveyMonkey.
* Version: 0.1.0
* Author: SMAWK
* Author URI: http://smawk.net
*
*
* @package 		LifterLMS
* @category 	Core
* @author 		codeBOX
*/

// Restrict direct access
if ( ! defined( 'ABSPATH' ) ) : exit;
endif;

// make sure class loads
if ( ! class_exists( 'LLMS_SurveyMonkey') ) :

	class LLMS_SurveyMonkey
	{		
		/**
		 * This function is called when the plugin is 
		 * instantiated. It creates the needed actions and 
		 * hooks for the plugin.
		 */
		public function __construct() 
		{			
			// Define class constants
			$this->define_constants();

			// add hooks here
			add_action( 'plugins_loaded', array($this, 'includes') );
			add_action( 'plugins_loaded', array($this, 'Init') );			
		}

		
		public function Init() 
		{
			// only load plugin if LifterLMS class exists.
			if ( class_exists( 'LifterLMS') ) 
			{
				add_action( 'lifterlms_course_completed_notification', array( $this, 'MaybeSendSurvey' ), 10, 2 );
			}
			else 
			{
				add_action( 'admin_init', array($this,'DeactivatePlugin'));
          		add_action( 'admin_notices', array($this,'DeactivatePluginNotice'));
			}			
		}

		
		public function DeactivatePlugin() 
		{
			deactivate_plugins( plugin_basename( __FILE__ ) );
		}

		
		public function DeactivatePluginNotice() 
		{
			echo '<div class="error"><p><strong>LifterLMS</strong> is not active; <strong>LifterLMS SurveyMonkey Integration</strong> has been <strong>deactivated</strong>.</p></div>';
			if ( isset( $_GET['activate'] ) )
			unset( $_GET['activate'] );
		}
		
		private function define_constants() 
		{
			
			if ( ! defined( 'LLMSSurveyMonkey_PLUGIN_FILE' ) ) 
			{
				define( 'LLMSSurveyMonkey_PLUGIN_FILE', __FILE__ );

			}

			
			if ( ! defined( 'LLMSSurveyMonkey_PLUGIN_DIR' ) ) 
			{
				define( 'LLMSSurveyMonkey_PLUGIN_DIR', WP_PLUGIN_DIR . "/" . plugin_basename( dirname(__FILE__) ) . '/');
			}
		}
		
		public static function GetSurveys() 
		{
			return (new LLMS_SurveyMonkey_API)->GetSurveys();
		}

		
		public function includes() {			
			if ( class_exists( 'LifterLMS') ) {
				include_once('includes/class.llms.surveymonkey.api.php');
				include_once( 'includes/class.llms.settings.surveymonkey.php' );
				include_once('includes/class.llms.course.surveymonkey.php');
			} 
		}

		public static function UpdateSurveys()
		{
			$surveys = self::GetSurveys();
			asort($surveys);
			update_option('llms_surveymonkey_surveys', $surveys);
		}

		public function MaybeSendSurvey($person_id, $courseID)
		{
			if (get_option($courseID,'_post_course_survey') != '_post_course_survey')
			{
				
			}
		}
	}

endif;
return new LLMS_SurveyMonkey();