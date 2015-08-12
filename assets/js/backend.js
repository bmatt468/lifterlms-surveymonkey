jQuery(document).ready(function($) 
{
	$('.button-primary').live('click', function(event) {
		if ($(this).val() === 'Restore Default')
		{
			event.preventDefault();
			$('#lifterlms_surveymonkey_emailtemplate').val('Congratulations {first_name} {last_name}!\n\n' +
				'You have successfully completed {course_name}! We hope that it was beneficial for you.\n\n' +
				'If you have the time, we would love to hear your thoughts about the course. ' +
				'If possible, could you please take five minutes to respond to a quick survey about the course? ' +
				'The link to the survey is {survey_link}.\n\n' +
				'Thank you so much!'
			);
		}
	});
});