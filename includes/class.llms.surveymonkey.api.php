<?php
if ( ! defined( 'ABSPATH' ) ) exit;

require( LLMSSurveyMonkey_PLUGIN_DIR . 'surveymonkey/SurveyMonkey.class.php' );

if ( ! class_exists( 'SurveyMonkey' ) ) : 
	echo "ERROR: SurveyMonkey API failed to load."; exit;
endif;

/**
 * This class is responsible for extending the SurveyMonkey API.
 * This class contains the logic to make calls the a SM wrapper 
 * when needed to direct resources.
 *
 * @since 0.1.0
 */
class LLMS_SurveyMonkey_API extends SurveyMonkey
{	
	/**
	 * Class variable that holds API key
	 * @var string
	 */
	public $apikey = '';

	/**
	 * Class variable that holds Access Token
	 * @var string
	 */
	public $accesstoken = '';

	/**
	 * Class constructor. Sets the API key when created.
	 */
	public function __construct() 
	{
		$this->set_apikey();
	}

	/**
	 * This function is responsible for making a call to the 
	 * SurveyMonkey API and retrieving the array of surveys registered for this user.
	 *
	 * @since 0.1.0
	 * @version  0.8.1
	 * @return array Array of survey data
	 */
	public function GetSurveys()
	{
		$surveys = array();

		$SM = new SurveyMonkey($this->apikey, $this->accesstoken);
		$result = $SM->getSurveyList();
		if ($result['success'])
		{
			foreach ($result['data']['surveys'] as $survey) {
				$titletemp = $SM->getSurveyDetails($survey['survey_id']);
				$title = $titletemp['data']['title']['text'];

				if ($titletemp['data']['title']['enabled'])
				{
					$surveys[$survey['survey_id']] = $title;
				}				
			}

			return $surveys;
		}
		else
		{
			return $surveys;
		}		
	}

	/**
	 * This function is responsible for making a call to the 
	 * SurveyMonkey API and retrieving the URL of the first 
	 * available web collector.
	 *
	 * @param int $survey ID of survey
	 * 
	 * @since 0.1.0
	 * @version  0.8.1
	 * @return void
	 */
	public function GetWebUrl($survey)
	{
		$SM = new SurveyMonkey($this->apikey, $this->accesstoken);
		$result = $SM->getCollectorList($survey, array('fields' => array('url','open','type')));

		if ($result['success'])
		{
			$collector = $result['data']['collectors'][0];
			if ($collector['open'] && $collector['type'] == 'url')
			{
				return $collector['url'];
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}		

	/**
	 * This function sets the API key and access token
	 * varaibles which are used throughout the script.
	 *
	 * @since 0.1.0
	 * @version  0.8.1
	 * @return void
	 */
	private function set_apikey() 
	{
		$this->apikey = get_option('lifterlms_surveymonkey_apikey', '');
		$this->accesstoken = get_option('lifterlms_surveymonkey_accesstoken', '');
	}
}

return new LLMS_SurveyMonkey_API();