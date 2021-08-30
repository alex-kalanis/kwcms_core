///
/// Preview window made and maintained by JavaScript
/// create an asynchronous request after 10 seconds of writing to display results
///
let PreviewWindow = function() {
	this.called = false;
	this.frame = null;
	this.frameWrite = function(content) {
		// fill data inside the frame
		let doc = previewWindow.frame.contentWindow.document;
		doc.open();
		doc.write(content);
		doc.close();
	};
	this.query = function (ev) {
		// query to preview page
		if (!previewWindow.frame) {
			previewWindow.initialize();
			return;
		}
		previewWindow.frameWrite('<h1>RELOAD</h1>');
		$.post(
			$('#editTargetLink').attr('value'),
			$('#editFileForm').serialize(),
			function (result) {
				previewWindow.frameWrite(result);
				previewWindow.called = false;
			}
		);
	};
	this.tick = function (ev) {
		// call when keyup to set timeout
		if (previewWindow.called) {
			return;
		}
		setTimeout(previewWindow.query, 10000);
		previewWindow.called = true;
	};
	this.initialize = function() {
		let frame = document.createElement('iframe');
		frame.setAttribute('id', 'text_edit_viewer');
		$('#text_edit_content').append(frame);
		previewWindow.frame = frame;
		setTimeout(previewWindow.query, 10);
		previewWindow.frameWrite('<h1>Init</h1>');
	};
	return this;
};

var previewWindow = new PreviewWindow();
