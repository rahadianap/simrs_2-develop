<table id="detail-table">
  <thead>
    <tr>
      @foreach($list as $l)
        <th style="width:120px; font-weight: 200; vertical-align: center; text-align:center; font-size:12px; word-wrap: break-word;">{{ strtoupper($l) }}</th>
      @endforeach
    </tr>
  </thead>
</table>