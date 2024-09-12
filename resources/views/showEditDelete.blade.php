@vite(['resources/js/app.js'])

<div id="app1">
    <file-or-form
        :original-name='@json($originalName)'
        :size='@json($size)'
        :upload-date='@json($uploadDate)'
        :description='@json($description)'
        :security-status='@json($securityStatus)'
        :download-link='@json($downloadLink)'
        :csrf-token='@json($csrfToken)'
        :file-id='@json($fileId)'
    ></file-or-form>
</div>
