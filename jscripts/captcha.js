var captcha = {
	init: function()
	{
	},

	refresh: function()
	{
		var image_hash = $('image_hash').value;
		this.spinner = new ActivityIndicator("body", {image: "/images/spinner_big.gif"});
		new ajax('/captcha/refresh?image_hash='+image_hash, {method: 'get', onComplete: function(request) { captcha.refresh_complete(request); }});
		return false;
	},

	refresh_complete: function(request)
	{
		if(request.responseText.match(/<error>(.*)<\/error>/))
		{
			message = request.responseText.match(/<error>(.*)<\/error>/);
			if(!message[1])
			{
				message[1] = "An unknown error occurred.";
			}
			alert('There was an error fetching the new captcha.\n\n'+message[1]);
		}
		else if(request.responseText)
		{
			$('captcha_img').src = "/captcha/"+request.responseText;
			$('image_hash').value = request.responseText;
		}
		this.spinner.destroy();
		this.spinner = '';
		$('image_string').focus();
	}
};