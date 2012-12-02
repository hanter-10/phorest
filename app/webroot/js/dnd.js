$(function(){
jQuery.event.props.push("dataTransfer");

var
def_opts =
{
    inputID: '',
    url: '',
    accept: '',
    maxfiles: 10,
    maxfilesize: 10, //MB
    drop: empty,
    dragStart: empty,
    dragEnter: empty,
    dragOver: empty,
    dragLeave: empty,
    docEnter: empty,
    docOver: empty,
    docLeave: empty,
    beforeEach: empty,
    afterAll: empty,
    rename: empty,
    error: function(err, file, i) {
    alert(err);
    },
    uploadStarted: empty,
    uploadFinished: empty,
    progressUpdated: empty,
    errors = ["BrowserNotSupported", "TooManyFiles", "FileTooLarge", "FileTypeNotAllowed"],
    files_count = 0,
    files;
}

$.fn.dropfile = function(options){
    var opts = $.extend({}, def_opts, options);
    this.on('drop', drop).on('dragstart', opts.dragStart).on('dragenter', dragEnter).on('dragover', dragOver).on('dragleave', dragLeave);
    $('#' + opts.inputID).change(function(e){
        opts.drop(e)
        files = e.target.files;
        files_count = files.length;
        upload();
    });

    function drop(e){
        opts.drop.call(this, e);
        files = e.dataTransfer.files;
        if (files === null || files === undefined || files.length === 0) {
            opts.error(errors[0]);
            return false;
        }
        files_count = files.length;
        upload();
        e.preventDefault();
        return false;
    }

    function upload(){
        if(opts.accept.push && opts.accept.length){
            for(var fileIndex = files.length; fileIndex--;){
                if( !files[fileIndex].type || $.inArray(files[fileIndex].type, opts.accept < 0) ){
                    opts.error(errors[3], files[fileIndex]);
                }
            }
        }

        send()
    }

    function send(){
        
    }


};

function empty() {}


});