@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="card">
        {{-- <div class="card-body">
            Hai, <b>{{ Auth::user()->name }}</b>! Selamat datang di aplikasi Penilaian Estetika Bonsai PPBI.
        </div> --}}
        <div class="d-flex gap-2 px-5 mt-5">
            @foreach ($dataRender as $key => $item)
                <div class="container-card noselect">
                    <div class="canvas">
                        <div class="tracker tr-1"></div>
                        <div class="tracker tr-2"></div>
                        <div class="tracker tr-3"></div>
                        <div class="tracker tr-4"></div>
                        <div class="tracker tr-5"></div>
                        <div class="tracker tr-6"></div>
                        <div class="tracker tr-7"></div>
                        <div class="tracker tr-8"></div>
                        <div class="tracker tr-9"></div>
                        <div class="tracker tr-10"></div>
                        <div class="tracker tr-11"></div>
                        <div class="tracker tr-12"></div>
                        <div class="tracker tr-13"></div>
                        <div class="tracker tr-14"></div>
                        <div class="tracker tr-15"></div>
                        <div class="tracker tr-16"></div>
                        <div class="tracker tr-17"></div>
                        <div class="tracker tr-18"></div>
                        <div class="tracker tr-19"></div>
                        <div class="tracker tr-20"></div>
                        <div class="tracker tr-21"></div>
                        <div class="tracker tr-22"></div>
                        <div class="tracker tr-23"></div>
                        <div class="tracker tr-24"></div>
                        <div class="tracker tr-25"></div>
                        <div id="card" style="background: #{{ $item[1] }}">
                            <p id="prompt">Total {{ $key }}</p>
                            <div class="title">{{ number_format($item[0], 0) }} {{ $key }}</div>
                            {{-- <div class="subtitle">
                            mouse hover tracker
                        </div> --}}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="row">
            <div class="col-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Chart</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="myChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')

    {{-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> --}}
    <script>
        let ctx = document.getElementById('myChart').getContext('2d');
        let myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
                datasets: [{
                    label: '# of Votes',
                    data: [12, 19, 3, 5, 2, 3],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
@endsection
