<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Nilai</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        th,
        td {
            border: 1px solid black;
            padding: 6px;
            text-align: center;
            word-wrap: break-word;
        }

        th {
            font-weight: bold;
        }

        h3 {
            text-align: center;
            font-size: 18px;
        }

        .text-left {
            text-align: left;
        }

        .keterangan {
            font-size: 12px;
            margin-top: 10px;
        }

        @page {
            size: A4 landscape;
            margin: 1cm 3.5cm;
        }

        .col-no {
            width: 10%;
        }

        .col-kemampuan {
            width: 60%;
        }

        .col-nilai-angka {
            width: 15%;
        }

        .col-nilai-huruf {
            width: 15%;
        }
    </style>
</head>

<body>
    <h3>DAFTAR NILAI<br>PRAKTIK KERJA LAPANGAN (PKL) / MAGANG TAHUN PELAJARAN 2024/2025</h3>

    <h4>A. ASPEK TEKNIS</h4>
    <table>
        <tr>
            <th class="col-no" rowspan="2">No.</th>
            <th class="col-kemampuan" rowspan="2">Kemampuan</th>
            <th class="col-nilai-angka" colspan="2">Nilai</th>
        </tr>
        <tr>
            <th>Angka</th>
            <th>Huruf</th>
        </tr>
        @foreach ($technicalScores as $index => $score)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td class="text-left">{{ $score->name }}</td>
            <td>{{ $score->score }}</td>
            <td>{{ $score->letter }}</td>
        </tr>
        @endforeach
    </table>

    <h4>B. ASPEK NONTEKNIS</h4>
    <table>
        <tr>
            <th class="col-no" rowspan="2">No.</th>
            <th class="col-kemampuan" rowspan="2">Kemampuan</th>
            <th class="col-nilai-angka" colspan="2">Nilai</th>
        </tr>
        <tr>
            <th>Angka</th>
            <th>Huruf</th>
        </tr>
        @foreach ($nonTechnicalScores as $index => $score)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td class="text-left">{{ $score->name }}</td>
            <td>{{ $score->score }}</td>
            <td>{{ $score->letter }}</td>
        </tr>
        @endforeach
        <tr>
            <th colspan="2">Jumlah Nilai Rata-Rata</th>
            <th colspan="2">{{ $avgScore }}</th>
        </tr>
    </table>

    <p class="keterangan">
        <strong>Keterangan:</strong><br>
        Sangat Baik: 90 - 100 <br>
        Baik: 75 - 89 <br>
        Cukup: 60 - 74 <br>
        Kurang: &lt; 59
    </p>
    
</body>

</html>