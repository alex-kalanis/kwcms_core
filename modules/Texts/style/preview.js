///
/// Preview window made and maintained by JavaScript
/// create an asynchronous request after 10 seconds of writing to display results
///
let PreviewWindow = function() {

	let selfPreview = this;
	this.called = false;
	this.frame = null;

	///
	/// Scroll on preview window
	///
	this.scrollWindows = function() {

		let selfScroll = this;
		this.windowA = '';
		this.windowB = '';
		this.ignoreScroll = false;

		this.initialize = function (windowA, windowB) {
			selfScroll.windowA = $(windowA);
			selfScroll.windowB = $(windowB);
			selfScroll.syncSizes(selfScroll.windowA, selfScroll.windowB);
			selfScroll.windowA.scroll(function() {
				// edit windows scrolls immediately
				selfScroll.scrollWindow(selfScroll.windowA, selfScroll.windowB);
			});
			selfScroll.windowB.contents().scroll(function(e) {
				if (selfPreview.called){
					// scroll back after tick - jump on preview window is bad one
					selfScroll.scrollWindow(selfScroll.windowA, selfScroll.windowB);
				} else {
					selfScroll.scrollTextArea(selfScroll.windowB, selfScroll.windowA);
				}
			});
		};

		this.scrollTextArea = function (which, where) {
			let tmpIgnore = selfScroll.ignoreScroll;
			selfScroll.ignoreScroll = false;
			if (!tmpIgnore) {
				selfScroll.ignoreScroll = true;
				let percents = selfScroll.percentsFrom(
					which[0].contentWindow.document.body.scrollHeight / 2.5,
					which.contents().scrollTop()
				);
				let sizeTo = selfScroll.percentsTo(
					where[0].offsetHeight,
					where[0].scrollHeight,
					percents
				);
				where[0].scrollTo(0, Math.round(sizeTo));
			}
		};

		this.scrollWindow = function (which, where) {
			let tmpIgnore = selfScroll.ignoreScroll;
			selfScroll.ignoreScroll = false;
			if (!tmpIgnore) {
				selfScroll.ignoreScroll = true;
				let percents = selfScroll.percentsFrom(
					which[0].scrollTopMax,
					which[0].scrollTop
				);
				let sizeTo = selfScroll.percentsTo(
					where[0].contentWindow.document.body.scrollHeight * 1.5,
					where.height(),
					percents
				);
				where[0].contentWindow.scrollTo(0, Math.round(100 * sizeTo));
			}
		};

		this.percentsFrom = function(srcHeightTotal, position) {
			return (100 * (position / (srcHeightTotal)));
		};

		this.percentsTo = function(dstHeightTotal, dstHeightWindow, currentPercent) {
			return (dstHeightWindow / dstHeightTotal) * currentPercent
		};

		this.syncSizes = function(which, where) {
			where.css('height', which.height() + 'px');
		};

		return this;
	};

	/// now things in class itself

	this.frameWrite = function(content) {
		// fill data inside the frame
		let doc = selfPreview.frame.contentWindow.document;
		doc.open();
		doc.write(content);
		doc.close();
	};
	this.query = function (ev) {
		// query to preview page
		if (!selfPreview.frame) {
			selfPreview.initialize();
			return;
		}
		selfPreview.frameWrite('<h1>RELOAD</h1>');
		$.post(
			$('#editTargetLink').attr('value'),
			$('#editFileForm').serialize(),
			function (result) {
				selfPreview.frameWrite(result);
				selfPreview.called = false;
				selfPreview.scrollWindows().initialize('#filecontent', '#text_edit_viewer');
			}
		);
	};
	this.tick = function (ev) {
		// call when keyup to set timeout
		if (selfPreview.called) {
			return;
		}
		setTimeout(selfPreview.query, 10000);
		selfPreview.called = true;
	};
	this.initialize = function() {
		let frame = document.createElement('iframe');
		frame.setAttribute('id', 'text_edit_viewer');
		$('#text_edit_content').append(frame);
		selfPreview.frame = frame;
		setTimeout(selfPreview.query, 10);
		selfPreview.frameWrite('<h1>Init</h1>');
	};

	return this;
};

let previewWindow = new PreviewWindow();
