<div class="col-md-12">
    <table class="table table-stripped">
        <thead>
            <tr>
                <th>Kode Matakuliah</th>
                <th>Matakuliah</th>
                <th>Hari</th>
                <th>Jam</th>
                <th>Ruangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($jadwal as $row)
                <tr>
                    <td>{{ $row->kode_mata_kuliah }}</td>
                    <td>{{ $row->nama_mata_kuliah }}</td>
                    <td>{{ $row->hari }}</td>
                    <td>{{ $row->sesi }}</td>
                    <td>{{ $row->ruang }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</div>
