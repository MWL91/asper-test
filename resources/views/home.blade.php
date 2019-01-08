@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Imported data</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Years</th>
                                <th @click="orderBy('object')"><a href="javascript:void(0);"><span class="caret">@{{ (order=='DESC') ? '&uarr;' : '&darr;' }}</span> Object</a></th>
                                <th @click="orderBy('city')"><a href="javascript:void(0);"><span class="caret">@{{ (order=='DESC') ? '&uarr;' : '&darr;' }}</span> City</a></th>
                                <th @click="orderBy('street')"><a href="javascript:void(0);"><span class="caret">@{{ (order=='DESC') ? '&uarr;' : '&darr;' }}</span> Street</a></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="object in laravelData.data" :key="object.id">
                                <td>@{{ object.years.map(function(elem){ return elem.year; }).join(", ") }}</td>
                                <td>@{{ object.name }}</td>
                                <td>@{{ object.city }}</td>
                                <td>@{{ object.street }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <pagination :data="laravelData" @pagination-change-page="getResults"></pagination>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
