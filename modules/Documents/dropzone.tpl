<form action="index.php" class="dropzone" id="module-dropzone">
    <input type="hidden" name="module" value="{$MODULE}">
    <input type="hidden" name="action" value="{$MODULE}Ajax">
    {*<input type="hidden" name="file" value="UploadAttach">*}
    <input type="hidden" name="record" value="{$ID}">
    <div class="dz-message">
        <span><img alt="{'Drag file here or click to upload'|@getTranslatedString}" src="include/dropzone/upload_120.png"></span>
        <span>{'Drag file here or click to upload'|@getTranslatedString}</span>
    </div>
</form>
<script>
    var moduleDropzone = new Dropzone('#module-dropzone', {
        url: 'index.php?module=Utilities&action=UtilitiesAjax&file=ExecuteFunctions&mode=ajax&functiontocall=saveAttachment',
        paramName: 'qqfile',
        parallelUploads: 1,
        addRemoveLinks: false,
        createImageThumbnails: true,
        uploadMultiple: false,
{*        clickable: ['#module-dropzone-message', '#module-dropzone']*}
    });


</script>