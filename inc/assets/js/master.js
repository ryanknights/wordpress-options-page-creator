(function (window, document, $)
{	
	"use strict";

	function removeImage (e)
	{
		e.preventDefault();

		var btn    = $(this),
			parent = btn.parent('[data-image-parent]');

		parent.children('input[type="text"]').val('');
		parent.children('img').attr('src', '');
	}

	function renderMediaUploader (e)
	{	
		e.preventDefault();

		var fileFrame,
			imageData,
			btn,
			container;

		btn = $(this);
		container = btn.parent('[data-image-parent]');

		if (fileFrame !== undefined)
		{
			fileFrame.open();
			return;
		}

		fileFrame = wp.media.frames.fileFrame = wp.media(
		{
			'frame'    : 'post',
			'state'    : 'insert',
			'multiple' : false
		});

		fileFrame.on('insert', function (e)
		{
			var json = fileFrame.state().get('selection').first().toJSON();

			if (json.url.length)
			{
				container.children('input[type="text"]').val(json.url);
				container.children('img').attr('src', json.url);
			}

		});

		fileFrame.open();
	}

	$(function ()
	{
		$('button[data-image-upload]').on('click', renderMediaUploader);
		$('button[data-remove-image]').on('click', removeImage);
	});

}(window, document, jQuery));