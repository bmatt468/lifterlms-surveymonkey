<?php
if ( ! defined( 'ABSPATH' ) ) exit;

require( LLMSSurveyMonkey_PLUGIN_DIR . 'surveymonkey/SurveyMonkey.class.php' );

if ( ! class_exists( 'SurveyMonkey' ) ) : 
	echo "ERROR: SurveyMonkey API failed to load."; exit;
endif;

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

	private function set_apikey() 
	{
		$this->apikey = get_option('lifterlms_surveymonkey_apikey', '');
		$this->accesstoken = get_option('lifterlms_surveymonkey_accesstoken', '');
	}
}

return new LLMS_SurveyMonkey_API();