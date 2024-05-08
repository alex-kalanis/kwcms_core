/** rewrite renderer */
uploaderRenderer.renderFileItem = function(uploadedFile) {
    let progressBasicBox = uploaderRenderer.upQuery.getObjectById(uploaderRenderer.upIdent.baseProgress);
    let progressBox = progressBasicBox.clone(true);
    progressBox.attr(uploaderRenderer.upIdent.localId, uploadedFile.localId);
    progressBox.removeAttr(uploaderRenderer.upIdent.baseProgress);
    let list = uploaderRenderer.upQuery.getObjectById(uploaderRenderer.upIdent.knownBulk);
    list.append(progressBox);
    let fileName = progressBox.find(".filename").first();
    fileName.append(uploadedFile.fileName);
    let buttons_retry = progressBox.find("button.button_retry").first();
    buttons_retry[0].onclick = function() {
        uploaderProcessor.getHandler().retryRead(uploadedFile.localId);
    };
    let buttons_resume = progressBox.find("button.button_resume").first();
    buttons_resume[0].onclick = function() {
        uploaderProcessor.getHandler().resumeRead(uploadedFile.localId);
    };
    let buttons_stop = progressBox.find("button.button_stop").first();
    buttons_stop[0].onclick = function() {
        uploaderProcessor.getHandler().stopRead(uploadedFile.localId);
    };
};
uploaderRenderer.renderReaded = function(uploadedFile) {
    let node = uploaderRenderer.upQuery.getObjectById(uploadedFile.localId);
    let button = node.find("button.button_retry").first();
    button[0].removeAttribute("disabled");
};
uploaderRenderer.renderFinished = function(uploadedFile) {
    uploaderRenderer.upQuery.setObjectContent(uploaderRenderer.upIdent.elapsedTime, uploaderRenderer.formatTime(uploaderRenderer.getElapsedTime(uploadedFile)));
    uploaderRenderer.upQuery.setObjectContent(uploaderRenderer.upIdent.estimatedTimeLeft, uploaderRenderer.calculateEstimatedTimeLeft(uploadedFile, 100));
    uploaderRenderer.upQuery.setObjectContent(uploaderRenderer.upIdent.currentPosition, uploaderRenderer.calculateSize(uploadedFile.fileSize));
    uploaderRenderer.upQuery.setObjectContent(uploaderRenderer.upIdent.totalSize, uploaderRenderer.calculateSize(uploadedFile.fileSize));
    uploaderRenderer.upQuery.setObjectContent(uploaderRenderer.upIdent.estimatedSpeed, uploaderRenderer.calculateSize(0));
    uploaderRenderer.upQuery.setObjectContent(uploaderRenderer.upIdent.percentsComplete, "100%");

    let node = uploaderRenderer.upQuery.getObjectById(uploadedFile.localId);
    let button = node.find("button").first();
    button[0].setAttribute("disabled", "disabled");
};
uploaderRenderer.startRead = function(uploadedFile) {
    uploaderRenderer.startTime = uploaderRenderer.getCurrentTime();
    let node = uploaderRenderer.upQuery.getObjectById(uploadedFile.localId);
    let button = node.find("button.button_resume").first();
    button[0].removeAttribute("disabled");
};
uploaderRenderer.stopRead = function(uploadedFile) {
    let node = uploaderRenderer.upQuery.getObjectById(uploadedFile.localId);
    let button = node.find("button.button_resume").first();
    button[0].setAttribute("disabled", "disabled");
};
uploaderRenderer.resumeRead = function(uploadedFile) {
    let node = uploaderRenderer.upQuery.getObjectById(uploadedFile.localId);
    let button = node.find("button.button_resume").first();
    button[0].removeAttribute("disabled");
};
uploaderRenderer.updateBar = function(uploadedFile) {
    let node = uploaderRenderer.upQuery.getObjectById(uploadedFile.localId);
    let checkedPercent = uploaderRenderer.calculateCheckedPercent(uploadedFile);
    let uploadPercent = uploaderRenderer.calculatePercent(uploadedFile);

    uploaderRenderer.upQuery.setObjectContent(uploadedFile.localId + " .elapsed_time", uploaderRenderer.formatTime(uploaderRenderer.getElapsedTime(uploadedFile)));
    uploaderRenderer.upQuery.setObjectContent(uploadedFile.localId + " .est_time_left", uploaderRenderer.calculateEstimatedTimeLeft(uploadedFile, uploadPercent));
    uploaderRenderer.upQuery.setObjectContent(uploadedFile.localId + " .current_position", uploaderRenderer.calculateSize(uploadedFile.lastKnownPart * uploadedFile.partSize));
    uploaderRenderer.upQuery.setObjectContent(uploadedFile.localId + " .total_kbytes", uploaderRenderer.calculateSize(uploadedFile.fileSize));
    uploaderRenderer.upQuery.setObjectContent(uploadedFile.localId + " .est_speed", uploaderRenderer.calculateSize(uploaderRenderer.calculateSpeed(uploadedFile)));
    uploaderRenderer.upQuery.setObjectContent(uploadedFile.localId + " .percent_complete", uploadPercent.toString() + "%");

    let percentDone = node.find(".progressbar-wrapper .uploaded").first();
    percentDone[0].style.paddingLeft = (uploadPercent - checkedPercent).toString() + "%";

    let percentChecked = node.find(".progressbar-wrapper .checked").first();
    percentChecked[0].style.paddingLeft = checkedPercent.toString() + "%";
};
uploaderRenderer.updateStatus = function(uploadedFile, status) {
    if (status === undefined) {
        status = uploadedFile.errorMessage;
    }
    if (status == null) {
        status = uploadedFile.errorMessage;
    }
    let node = uploaderRenderer.upQuery.getObjectById(uploadedFile.localId);
    let errLog = node.find("." + uploaderRenderer.upIdent.errorLog);
    errLog[0].append(status);
};
uploaderFailure.process = function(uploadedFile, event) {
    if (event === undefined) {
        event = uploadedFile.errorMessage;
    }
    if (event == null) {
        event = uploadedFile.errorMessage;
    }
    let node = uploaderRenderer.upQuery.getObjectById(uploadedFile.localId);
    let errLog = node.find("." + uploaderRenderer.upIdent.errorLog);
    errLog[0].append(event);
};

document.addEventListener('DOMContentLoaded', function () {
    // configs
    if ($ && uploaderReader.canRead(window)) {
        let lang = new UploadTranslations();
        uploaderProcessor.init(uploaderQuery.init($), lang, targetConfig);
    }

    // runner
    if ($ && uploaderReader.canRead(window)) {
        // Success. All the File APIs are supported. And JQuery is here too
        // Add autostart to action handler
        uploaderProcessor.getHandler().handleFileSelection = function (event) {
            uploaderProcessor.getHandler().handleFileSelect(event);
            uploaderProcessor.getHandler().startRead();
        };
        uploaderProcessor.getHandler().handleFileInputs = function (event) {
            uploaderProcessor.getHandler().handleFileInput(event);
            uploaderProcessor.getHandler().startRead();
        };
        // drop zone
        let dropZone = document.getElementById("uparea");
        dropZone.className = 'can_upload';
        // Setup the dnd listeners.
        dropZone.addEventListener('drop', uploaderProcessor.getHandler().handleFileSelection, false);
        dropZone.addEventListener('click', function () {
            // classical input - on click ->> https://stackoverflow.com/questions/16215771/how-to-open-select-file-dialog-via-js
            let dummyInput = document.createElement('input');
            dummyInput.type = 'file';
            dummyInput.onchange = uploaderProcessor.getHandler().handleFileInputs;
            dummyInput.click();
        });

        let buttons_area = $(".upload_buttons").first();
        buttons_area[0].style.visibility = 'visible';
        let buttons_abort = $("button.button_abort").first();
        buttons_abort[0].onclick = function() {
            uploaderProcessor.getHandler().abortRead();
        };
        let buttons_clear = $("button.button_clear").first();
        buttons_clear[0].onclick = function() {
            uploaderProcessor.getHandler().clearList();
        };
    }
});
