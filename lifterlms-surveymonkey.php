<?php
/**
* Plugin Name: LifterLMS SurveyMonkey Integration
* Plugin URI: http://smawk.net/
* Description: Connect LifterLMS to SurveyMonkey.
* Version: 0.8.0
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

	/**
	 * This is the main class for the plugin. It contains the constructor
	 * for the plugin, as well as the initialization methods.
	 *
	 * @since 0.1.0 
	 */
	class LLMS_SurveyMonkey
	{		
		/**
		 * This function is called when the plugin is 
		 * instantiated. It creates the needed actions and 
		 * hooks for the plugin.
		 *
		 * @since 0.1.0
		 * @version 0.8.1
		 * @return void
		 */
		public function __construct() 
		{			
			////////////////////////////
			// Define class constants //
			////////////////////////////
			$this->define_constants();

			////////////////////////////////////////
			// Add appropriate hook to the plugin //
			////////////////////////////////////////
			add_action( 'plugins_loaded', array($this, 'includes') );
			add_action( 'plugins_loaded', array($this, 'Init') );
			add_action('admin_enqueue_scripts',array($this,'AddStyles'));			
		}

		/**
		 * This function is called when the plugin is loaded.
		 * It is reponsible for making sure that LifterLMS is 
		 * installed; if so, this function takes care off adding 
		 * the appropriate functions to the Lifter-specific hooks.
		 *
		 * @since 0.1.0
		 * @version 0.8.1
		 * @return void
		 */
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

		
		/**
		 * This function is responsbile for deactivating the plugin should 
		 * the need arise.
		 *
		 * @since 0.1.0
		 * @version 0.8.1
		 * @return void
		 */
		public function DeactivatePlugin() 
		{
			deactivate_plugins( plugin_basename( __FILE__ ) );
		}

		
		/**
		 * This function is responsible for displaying the banner 
		 * across the top of the admin page when the plugin is deactivated.
		 *
		 * @since 0.1.0
		 * @version 0.8.1
		 * @return void
		 */
		public function DeactivatePluginNotice() 
		{
			echo '<div class="error"><p><strong>LifterLMS</strong> is not active; <strong>LifterLMS SurveyMonkey Integration</strong> has been <strong>deactivated</strong>.</p></div>';
			if ( isset( $_GET['activate'] ) )
			unset( $_GET['activate'] );
		}
		
		/**
		 * This function is used to define the constants used in the plugin.
		 *
		 * @since 0.1.0
		 * @version 0.8.1
		 * @return void
		 */
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
		
		/**
		 * This function makes a call through the SurveyMonkey API
		 * class to fetch the array of surveys. It returns that array.
		 *
		 * @since 0.1.0
		 * @version 0.8.1
		 * @return array Array of ID's of surveys
		 */
		public static function GetSurveys() 
		{
			return (new LLMS_SurveyMonkey_API)->GetSurveys();
		}

		/**
		 * This function is responsible for including the appropriate files for the plugin.
		 * This function first makes sure that LifterLMS is activated, then includes
		 * the files for the plugin.
		 *
		 * @since 0.1.0
		 * @version 0.8.1
		 * @return void
		 */
		public function includes() {			
			if ( class_exists( 'LifterLMS') ) {
				include_once('includes/class.llms.surveymonkey.api.php');
				include_once( 'includes/class.llms.settings.surveymonkey.php' );
				include_once('includes/class.llms.course.surveymonkey.php');
			} 
		}

		/**
		 * This function is responsible for enqueuing the custom styles
		 * and scripts that are used in this plugin.
		 *
		 * @since 0.1.0
		 * @version 0.8.1
		 * @return void
		 */
		public function AddStyles()
		{
			wp_enqueue_style('llms_surveymonkey', plugins_url('/assets/css/style.css',__FILE__));
			wp_enqueue_script('llms_surveymonkey_scripts', plugins_url('/assets/js/backend.js',__FILE__));
		}

		/**
		 * This function is responsible for updating the array of surveys
		 * that is stored in the options table. It first makes the call
		 * to fetch the array of surveys, the sorts that array alphabetically
		 * before inserting it into the options table.
		 *
		 * @since 0.1.0
		 * @version 0.8.1
		 * @return void
		 */
		public static function UpdateSurveys()
		{
			$surveys = self::GetSurveys();
			asort($surveys);
			update_option('llms_surveymonkey_surveys', $surveys);
		}

		/**
		 * This function is responsible for obtaining the URL of the 
		 * web catch from MailChimp. It makes a call through the API
		 * to get the open web collector.
		 *
		 * @since 0.1.0
		 * @version 0.8.1
		 * @return string URL of open web collector
		 */
		public static function GetWebUrl($survey)
		{
			return (new LLMS_SurveyMonkey_API)->GetWebUrl($survey);
		}

		/**
		 * This function is responsible for determining wheter or not
		 * to send an email containing the information about the survey.
		 * If the survey is active, this function will grab the template 
		 * from the main settings, and then send an email to the user 
		 * containing the survey information.
		 *
		 * @since 0.1.0
		 * @version 0.8.1
		 * @return void
		 */
		public function MaybeSendSurvey($person_id, $courseID)
		{
			if (get_post_meta($courseID,'_post_course_survey',true) != '')
			{
				$data = get_userdata($person_id);

				$url = self::GetWebUrl(get_post_meta($courseID,'_post_course_survey',true));

				$tokens = array(
					'{site_title}',
					'{user_login}',
					'{site_url}',
					'{first_name}',
					'{last_name}',
					'{email_address}',
					'{course_name}',
					'{survey_link}',
					'{current_date}',
				);

				$updatedTokens = array(
					get_bloginfo('name'),
					$data->user_login,
					get_bloginfo('url'),
					$data->first_name,
					$data->last_name,
					$data->user_email,
					get_the_title($courseID),
					$url,
					date(DATE_RSS),
				);
				
				$email = str_replace($tokens, $updatedTokens, get_option('lifterlms_surveymonkey_emailtemplate'));
				

				if ($data->user_email && $url)
				{
					wp_mail($data->user_email, get_the_title($courseID) . ' survey request', $email);
				}					
			}
		}
	}

endif;
return new LLMS_SurveyMonkey();