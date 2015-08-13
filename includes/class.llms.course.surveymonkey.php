<?php
if ( ! defined( 'ABSPATH' ) ) exit;


class LLMS_Settings_Course_SurveyMonkey {

	public static $prefix = '_';

	
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
	
	public function CustomEngagement($engagements)
	{
		/*$engagements['survey'] = 'Send Survey';
		return $engagements;*/
	}
	
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