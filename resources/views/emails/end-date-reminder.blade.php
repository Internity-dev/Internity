<x-mail::message>
    Yth {{ $teacher->name }},
    <br>
    <br>
    Kami mengingatkan Anda bahwa siswa bernama <strong>{{ $student->name }}</strong> akan segera mengakhiri masa PKL pada tanggal <strong>{{  \Carbon\Carbon::parse($end)->format('d F Y') }}</strong> dari <strong>{{ $company->name }}</strong> yang beralamat di {{ $company->address }}
    <br>
    <br>
    Anda dipersilakan untuk mengatur jadwal penjemputan untuk siswa tersebut.
    <br>
    <br>
    Terima kasih.
</x-mail::message>
<x-mail::footer>
    © {{ date('Y') }} {{ config('app.name') }}. @lang('All rights reserved.')
</x-mail::footer>
