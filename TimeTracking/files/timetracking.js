$(document).ready(function() {
	/**
	 * Manage collapsed form inputs that are hidden from collapsed divs
	 * These inputs are disables when collapsed, so they are not sent when the form is submittted
	 */
	if( $('.disable-collapsed-inputs').length ) {
		$('.collapse.disable-collapsed-inputs')
			.on('hidden.bs.collapse', function (e) {
				$(e.currentTarget).find(':input').each( function(){
					var input = $(this);
					input.data('previous_disabled', input.prop('disabled') ).prop('disabled', true);
				});
			})
			.on('show.bs.collapse', function (e) {
				$(e.currentTarget).find(':input').each( function(){
					var input = $(this);
					input.prop('disabled', input.data('previous_disabled')).removeData('previous_disabled');
				});
			});
	}
});