@vite(['resources/js/app.js'])

<div id="test">
{{--    <test :name='@json($name)'></test>--}}
    <test
        :original-name='@json($originalName)'
        :size='@json($size)'
        :upload-date='@json($uploadDate)'
        :description='@json($description)'
        :security-status='@json($securityStatus)'
        :download-link='@json($downloadLink)'
        :csrf-token='@json($csrfToken)'
        :file-id='@json($fileId)'
    ></test>
</div>
