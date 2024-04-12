
@extends('layouts.message')
@section('content')


<style>
    td{
        paddding: 8px;
        border: 1px solid gray;
    }
    tr{
        padding-bottom: 6px;
    }
</style>

<div class="m-6 flex flex-col items-center rounded-xl bg-green-50 p-4 text-slate-800 shadow-sm dark:bg-slate-900 dark:text-slate-400 md:p-12">
    <h2 class="text-2xl underline">Payments for donation</h2>
    <table class="inline-flex items-center p-3" border="1">
            <tr class="p-3 border border-gray-50">
                <th class="p-3">S/N</th>
                <th class="p-3">Name</th>
                <th class="p-3">Email</th>
                <th class="p-3">Status</th>
                <th class="p-3">Amount</th>
                <th class="p-3">Date</th>
            </tr>
            @forelse ($payments as $payment)
            <tr class="p-3">
                <td class="p-3">{{ $payment->id }}</td>
                <td class="p-3">{{ $payment->user->name }}</td>
                <td class="p-3">{{ $payment->user->email }}</td>
                <td class="p-3"> {{ $payment->currency }} {{ $payment->amount }}</td>
                <td class="p-3">{{ $payment->status? "sucessful" : "processing" }}</td>
                <td class="p-3">{{ $payment->created_at->diffForHumans() }}</td>
            </tr>
            @empty
            <tr>
                <td class="text-center" colspan="6">No records</td>
            </tr>
            @endforelse
    </table>

    <div class="p-3">
        {{ $payments->withQueryString()->links() }}
    </div>
</div>
    
@endsection