<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * This class contains the logic that adds the additional 
 * meta box to the course page. Along with adding the 
 * meta box, this class contains a function that is responsible 
 * for saving the data on a $_POST request
 *
 * @since 0.1.0
 */
class LLMS_Settings_Course_SurveyMonkey {

	public static $prefix = '_';
	
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
		// This confirms that the plugin is activated before running
		if (LLMS_Settings_Integrations_SurveyMonkey::Enabled() == 'yes') 
		{
			// Adds a filters that runs whenever the course page is loaded
			add_filter( 'llms_meta_fields_course_main', array( $this, 'ExtendSettings' ));
			//add_filter( 'lifterlms_engagement_types', array( $this, 'CustomEngagement' ));

			// Adds an action that comes whenever a page is loaded
			add_action( 'save_post', array($this, 'PostUpdateHandler') );
		}
	}
	
	/**
	 * This function will be used down the road to extend
	 * the engagements to include sending a survey.
	 *
	 * @since 0.1.0
	 * @version  0.8.1
	 * @return void
	 */
	public function CustomEngagement($engagements)
	{
		/*$engagements['survey'] = 'Send Survey';
		return $engagements;*/
	}
	
	/**
	 * This function is used to extend the meta box settings of 
	 * the course. It takes in the existing meta box array, 
	 * modifies it, and then returns the updated array.
	 *
	 * @param array $content Array of metabox fields
	 *
	 * @since 0.1.0
	 * @version  0.8.1
	 * @return array Updated array of metabox fields
	 */
	public function ExtendSettings($content) 
	{		
		$surveys = array();
		$surveyOptions = get_option('llms_surveymonkey_surveys',array());
		foreach ($surveyOptions as $surveyID => $surveyName) 
		{
			$surveys[] = array(
				'key' => $surveyID,
				'title' => $surveyName,
			);
		}

		// Create array of content to be passed back to the 
		// course page. This array will be appended to the 
		// end of the current meta box.
		$metaBoxTab = array(			
			'title' => 'SurveyMonkey Settings',
			'fields' => array(
				array(
						'type'		=> 'select',
						'label'		=> 'Post-Course Survey',
						'desc' 		=> 'Select a survey to send to users once they have completed the course.',
						'id' 		=> self::$prefix . 'post_course_survey',
						'class' 	=> 'input-full',
						'value' 	=> $surveys,
						'desc_class'=> 'd-all',
						'group' 	=> '',
				),			
			),
		);

		array_push($content, $metaBoxTab);
		return $content;
	}

	/**
	 * This function is the save function. This function is
	 * called whenever a post is updated. This functions checks 
	 * to see if any of the survey monkey fields need to be updated
	 *
	 * @since 0.1.0
	 * @version  0.8.1
	 * @return void
	 */
	public function PostUpdateHandler()
	{	
		if (isset($_POST['post_ID'])) 
		{
			$svy = isset($_POST['_post_course_survey']) ? $_POST['_post_course_survey'] : '';
			update_post_meta($_POST['post_ID'], '_post_course_survey', $svy);
		}
	}
}
return new LLMS_Settings_Course_SurveyMonkey();