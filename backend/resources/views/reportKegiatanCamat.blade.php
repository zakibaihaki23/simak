<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
    <title>Download Laporan Keadaan Bencana Alam - SIMAK Kabupaten Pandeglang</title>
</head>
<body>
    <style type="text/css">
.table {
  table-layout: fixed;
  border: solid 1p;
  border-collapse: collapse;
  border-spacing: 0;
}
table tr:not(:first-child) th {
  display: inline-block;
  -webkit-writing-mode: vertical-rl;
  -ms-writing-mode: tb-rl;
  writing-mode: vertical-rl;
}


.table thead th {
  border: solid 1px;
  /*padding: 30px;*/
  text-align: center;
  vertical-align: middle;
  font-size: 11pt;
  width: 1px;
  font-weight: normal;
  white-space: nowrap;
 
}
th span {
    transform: rotate(-90deg)
}

.table tbody td {
  border: solid 1px;
  font-size: 11pt;
  width: 1px;
  vertical-align: middle;
}
td.item {
    text-align: center;
    white-space: normal;
}
@font-face {
    font-family: Arial, Helvetica, sans-serif
}
#rotate
{
  height:200px;
  width: 30px
}


#vertical
{
    -webkit-transform:rotate(-90deg);
    -moz-transform:rotate(-90deg);
    -o-transform: rotate(-90deg);
    margin-left: -50px;
    margin-right: -50px;
}

	</style>
<div>
    
    <center>
        @foreach ($periode as $waktu => $per)
            <li style="font-weight: bolder; font-size: 18px; list-style-type: none;">Laporan Realisasi PBB Tahun {{ \Carbon\Carbon::parse($per->periode)->translatedFormat('Y') }}</li>
            <li style="font-weight: bolder; font-size: 18px; list-style-type: none;">Bulan  {{ \Carbon\Carbon::parse($per->periode)->translatedFormat('F') }}</li>
            <p></p>
    </center>
    <br/>
    <div style="list-style-type: none; font-weight: bold; ">
        @foreach ($kecamatan as $kec)
        <li>Pemerintah Kabupaten Pandeglang</li>
        <li>{{$kec->nama_instansi}}</li>

</div>
<div class="table-responsive" style="margin-top: 20px">
<table class="table table-bordered table-sm" align="center">
    <thead>
        <tr>
            <th>No</th>
            <th>Hari/Tanggal</th>
            <th>Waktu</th>
            <th>Tempat</th>
            <th>Uraian Kegiatan</th>
        </tr>
        <tr style="text-align: center; background-color: #A9A9A9">
            <th>1</th>
            <th>2</th>
            <th>3</th>
            <th>4</th>
            <th>5</th>
        </tr>
    </thead>
    <tbody>
        
        @php $i=1 @endphp
        @foreach($camat as $item)
        <tr style="text-align: center; vertical-align: baseline">
            
            @if($item->waktu == null) {
            
            <td style="font-weight: bold; font-size: 20pt" 
            colspan="5" 
            class="text-center">
            NIHIL
          </td> 
           } @else {
            <td>{{ $i++ }}</td>
            <td>{{ \Carbon\Carbon::parse($item->waktu)->translatedFormat('l, d F Y') }}</td>
            <td>{{ \Carbon\Carbon::parse($item->waktu)->translatedFormat('H:i') }}</td>
            <td>{{$item->tempat}}</td>
            <td>{{$item->uraian_kegiatan}}</td>
           } @endif
            
            
        </tr>
        @endforeach
    </tbody>
</table>
</div>
<div></div>
</div>
<div style="clear:both; position:relative;">
<div style="position:absolute; left:0pt; width:192pt" class="text-center">
        <p>Mengetahui</p>
        <p>Camat {{$kec->nama_instansi}}</p>
    <br>
    <p style="text-decoration: underline;margin-top: 20px;">
    ..................................................
    </p>
    <div class="text-center">
    <p>
    NIP..................................................
    </p>
    </div>
    </div>
    <div class="col text-right">
        <p>{{$kec->nama_instansi}}, 
            {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }} 
        </p>
        <p style="margin-right: 20px">Sekretaris {{$kec->nama_instansi}}</p>
        <br>
        <p style="margin-top: 20px; text-decoration: underline; margin-right: 4px">
        ..................................................
        </p>
    <div class="text-right">
    <p style="margin-right: 8px">
    NIP..................................................
    </p>
    </div>
</div>
@endforeach
@endforeach
</body>
</html>
