@extends('layouts.dashboard')

@section('dashboard-content')
<x-table pageName="FAQs" routeCreate="{{ route('faqs.create') }}" :pagination="$faqs" :tableData="$faqs">

    <x-slot:thead>
        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-10">
            Kelola
        </th>
        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 w-40">
            Topik Pertanyaan
        </th>
    </x-slot:thead>

    <x-slot:tbody>
        @foreach ($faqs as $data)
        <tr>
            <td class="text-center">
                <a href="{{ route('faqs.edit', encrypt($data->id)) }}" class="btn btn-info text-xs"
                    data-bs-toggle="tooltip" data-bs-placement="bottom" title="Edit"><i
                        class="bi bi-pencil-square"></i></a>

                <form action="{{ route('faqs.destroy', encrypt($data->id)) }}" method="POST">
                @csrf
                @method('DELETE')
                <button id="button-{{ $data->id }}" class="button-delete btn btn-danger text-xs"
                    data-bs-toggle="tooltip" data-bs-placement="bottom" title="Delete" type="button"><i
                        class="bi bi-trash"></i></button>
                </form>
            </td>
            <td class="text-sm text-center">{{ $data->question }}</td>
        </tr>
        @endforeach
    </x-slot:tbody>
</x-table>
@endsection