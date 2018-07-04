<div class="tab-page" id="tabChunks">
    <h2 class="tab"><i class="fa fa-th-large"></i> {{ ManagerTheme::getLexicon('manage_htmlsnippets') }}</h2>
    <script type="text/javascript">tpResources.addTabPage(document.getElementById('tabChunks'))</script>

    <div id="chunks-info" class="msg-container" style="display:none">
        <div class="element-edit-message-tab">{{ ManagerTheme::getLexicon('htmlsnippet_management_msg') }}</div>
        <p class="viewoptions-message">{{ ManagerTheme::getLexicon('view_options_msg') }}</p>
    </div>

    <div id="_actions">
        <form class="btn-group form-group form-inline">
            <div class="input-group input-group-sm">
                <input class="form-control filterElements-form" type="text" size="30" placeholder="{{ ManagerTheme::getLexicon('element_filter_msg') }}" id="site_htmlsnippets_search" />
                <div class="input-group-btn">
                    <a class="btn btn-success" href="index.php?a=77"><i class="fa fa-plus-circle"></i> <span>{{ ManagerTheme::getLexicon('new_htmlsnippet') }}</span></a>
                    <a class="btn btn-secondary" href="javascript:;" id="chunks-help"><i class="fa fa-question-circle"></i> <span>{{ ManagerTheme::getLexicon('help') }}</span></a>
                    <a class="btn btn-secondary switchform-btn" href="javascript:;" data-target="switchForm_site_htmlsnippets"><i class="fa fa-bars"></i> <span>{{ ManagerTheme::getLexicon('btn_view_options') }}</span></a>
                </div>
            </div>
        </form>
    </div>

    @include('manager::page.resources.helper.switchButtons', [
        'tabName' => $tabName
    ])

    <div class="clearfix"></div>
    <div class="panel-group no-transition">
        <div id="{{ $tabName }}" class="resourceTable panel panel-default">
            @if($outCategory->count() > 0)
                @component('manager::partials.panelCollapse', ['name' => $tabName, 'id' => 0, 'title' => ManagerTheme::getLexicon('no_category')])
                    <ul class="elements">
                        @foreach($outCategory as $item)
                            @include('manager::page.resources.elements.chunk', ['item' => $item])
                        @endforeach
                    </ul>
                @endcomponent
            @endif

            @foreach($categories as $cat)
                @component('manager::partials.panelCollapse', ['name' => $tabName, 'id' => $cat->id, 'title' => $cat->category])
                    <ul class="elements">
                        @foreach($cat->chunks as $item)
                            @include('manager::page.resources.elements.chunk', ['item' => $item])
                        @endforeach
                    </ul>
                @endcomponent
            @endforeach
        </div>
    </div>
    <div class="clearfix"></div>
</div>

@push('scripts.bot')
    <script>
        initQuicksearch('site_htmlsnippets_search', 'site_htmlsnippets');
        initViews('ch', 'chunks', 'site_htmlsnippets')
    </script>
@endpush
