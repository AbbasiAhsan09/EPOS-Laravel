<h6>
    {{ConfigHelper::getStoreConfig()["app_title"] ?? ""}}
    @if (request()->from && request()->to)
        From : {{date('m-d-Y',strtotime(request()->from))}} - To : {{date('m-d-Y',strtotime(request()->to))}}
    @endif
</h6>