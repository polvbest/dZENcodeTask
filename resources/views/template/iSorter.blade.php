{{--<div class="iSorter">--}}
@php
  $sorterFields = [ 'date'  => 'Creation date'
                  , 'name'  => 'User name'
                  , 'email' => 'User e-mail'
                  ];
  $field  = isset($sorter['field']) && array_key_exists($sorter['field'], $sorterFields) ? $sorter['field'] : "date";
  $direct = isset($sorter['direct']) && array_key_exists($sorter['direct'], $sorterFields) ? $sorter['direct'] : -1;
@endphp
<div class="container">
  <div class="btn-group" role="group" aria-label="Select sorted field">
    <button type="button" class="btn btn-secondary action-sort" data-direction="1">A-Z</button>
    <div class="btn-group" role="group">
      <button id="btnGroupSorterFields" type="button" class="btn btn-secondary dropdown-toggle"
              data-toggle="dropdown" data-sort-field="{{ $field }}" data-direction="{{ $direct }}"
              aria-haspopup="true" aria-expanded="false">{{ $sorterFields[$field] }}
      </button>
      <div class="dropdown-menu" aria-labelledby="btnGroupSorterFields">
        <a class="dropdown-item disabled" href="#">Sort by:</a>
        @foreach($sorterFields as $field => $text)
          <a class="dropdown-item" href="#{{ $field }}={{ $direct }}" data-value="{{ $field }}">{{ $text }}</a>
        @endforeach
      </div>
    </div>
    <button type="button" class="btn btn-secondary action-sort" data-direction="-1">Z-A</button>
    <button type="button" class="btn btn-secondary sorter-reset" data-sorter-reset
            style="border-left:1px solid darkgray;">Reset
    </button>
  </div>
</div>
{{--</div>--}}