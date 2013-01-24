$(function(){
jQuery.event.props.push("dataTransfer");

var
def_opts =
{
    inputID:        '',
    url:            '',
    accept:         '',
    maxfiles:       10, //ファイルのアップロード最大個数
    maxfilesize:    10, //ファイルの最大サイズ、単位はMB
    drop:           empty,
    dragStart:      empty,
    dragEnter:      empty,
    dragOver:       empty,
    dragLeave:      empty,
    load:           empty,
    allLoaded:      empty,
    error: function(err, file, i) {
    alert(err);
    },
    progress: empty
},
errors = ["BrowserNotSupported", "TooManyFiles", "FileTooLarge", "FileTypeNotAllowed"],
workQueue   = [],
sending     = false;

$.fn.dropfile = function(options){
    var opts = $.extend({}, def_opts, options);
    this.on('drop', drop).on('dragstart', opts.dragStart).on('dragenter', dragEnter).on('dragover', dragOver).on('dragleave', dragLeave);
    

    function getPassedFiles(files){
        //ファイルがセットされているか
        if( !hasFile(files) ) return false; 

        //最大アップロード数を超えたら
        if(workQueue.length + files.length > opts.maxfiles){
            opts.error(errors[1]);
            return false;
        }
        
        //ファイルの中から合格したファイルだけを抽出
        passedFiles = $.map(files,function(file,index){
            if ( verify(file) ) return file;
        });
        if(passedFiles.length===0) return false;
        return passedFiles;
    }

    $('#' + opts.inputID).change(function(e){
        e.preventDefault();
        var 
        files = e.target.files,
        passedFiles = getPassedFiles(files);
        if(!passedFiles) return false;
        opts.drop.call(this,passedFiles, upload);
    });

    function drop(e){
        e.preventDefault();
        var 
        files = e.dataTransfer.files,
        passedFiles = getPassedFiles(files);
        if(!passedFiles) return false;
        opts.drop.call(this,passedFiles, upload);
    }


    //実際に送信する前に、送信予定のファイルたちを待ち行列に詰め込む
    function upload(file,data){
        workQueue.push({file:file, data:data});
        send();
    }

    function send(){
        if(sending===true) return;
        sending = true;
        var
        file    = workQueue[0].file,
        data    = workQueue[0].data,
        xhr     = new XMLHttpRequest(),
        upload  = xhr.upload,
        fd      = new FormData();

        fd.append('file',file);
        fd.append('fileName',file.name);
        upload.file = file;
        upload.data = data;
        upload.addEventListener("progress", progress, false);
        xhr.addEventListener('load',function(e){
            var 
            serverResponse  = null,
            responseText = this.responseText;
            sending = false; //今回の送信が完了しました
            if(responseText){
                try {
                    serverResponse = jQuery.parseJSON(responseText);
                }catch (e) {
                    serverResponse = responseText;
                }
            }

            workQueue.splice(0,1); //待ち行列から成功したファイルを削除
            e.data = this.upload.data;
            e.file = this.upload.file;
            opts.load(e,serverResponse);
            
            if(workQueue.length!=0)  //まだ送信されてないファイルがあるなら、自身をコールしてファイルを送信する
            {send();}
            else
            {opts.allLoaded();}  //全てが完了したら
        },false);
        xhr.open("POST", opts.url, true);
        xhr.send(fd);
    }

    function progress(e)
    {
        if (e.lengthComputable){
            var percentage = Math.round((e.loaded * 100) / e.total);
            e.data = this.data;
            e.file = this.file;
            opts.progress(e,percentage);
        }
    }




    function dragEnter(e) {
      e.preventDefault();
      opts.dragEnter.call(this, e);
    }
    function dragOver(e) {
      e.preventDefault();
      opts.dragOver.call(this, e);
    }
    function dragLeave(e) {
      opts.dragLeave.call(this, e);
      e.stopPropagation();
    }


    function hasFile(files)
    {
        //ファイルがあるかを調べる
        if (files === null || files === undefined || files.length === 0) {
            opts.error(errors[0]); 
            return false;
        }
        return true;
    }

    function verify(file)
    {
        //拡張の指定があれば
        if(opts.accept.push && opts.accept.length){ 
            if( !file.type || $.inArray(file.type, opts.accept) < 0 ){
                opts.error(errors[3], file);
                return false;
            }
        }

        //サイズの指定があれば
        max_file_size = 1048576 * opts.maxfilesize;
        if (file.size > max_file_size) {
            opts.error(errors[2], file);
            return false;
        }
        return true;
    }


};


function empty() {}

});