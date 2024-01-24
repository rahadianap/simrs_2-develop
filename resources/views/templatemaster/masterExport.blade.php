<table id="detail-table">
  <thead>
    <tr>
      @foreach($list[0] as $l)
        <th style="width:120px; font-weight: 200; vertical-align: center; text-align:center; font-size:12px; word-wrap: break-word;">{{ strtoupper($l) }}</th>
      @endforeach
    </tr>
  </thead>
  <tbody>
    @foreach($list[1] as $l)
    <tr>
        <td>{{ $l->coa_id }}</td>
        <td>{{ $l->kode_coa }}</td>
        <td>{{ $l->nama_coa }}</td>
        <td>{{ $l->level_coa }}</td>
        <td>{{ $l->level_nama }}</td>
        <td>{{ $l->coa_header }}</td>
        <td>{{ $l->coa_header_id }}</td>
        <td>{{ $l->nilai_normal }}</td>
        <td>{{ $l->is_valid_coa }}</td>
        <td>{{ $l->is_aktif }}</td>
        <td>{{ $l->client_id }}</td>
    </tr>
    @endforeach
  </tbody>
</table>