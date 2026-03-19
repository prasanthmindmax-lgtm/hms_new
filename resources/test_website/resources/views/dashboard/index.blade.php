@extends('layouts.app')

@section('content')

<div class="page-titles">

    <h2> {{ $pageTitle }} <small> Dashboard </small></h2>

</div>


<script src="https://code.highcharts.com/highcharts.js"></script>

<script src="https://code.highcharts.com/highcharts-more.js"></script>

<script type="text/javascript">

  // Data retrieved from http://vikjavev.no/ver/index.php?spenn=2d&sluttid=16.06.2015.

  $(function () {

    Highcharts.chart('chartdiv', {

        chart: {

            type: 'areaspline'

        },

        title: {

            text: 'Monthly Statistic'

        },

        legend: {

            layout: 'vertical',

            align: 'left',

            verticalAlign: 'top',

            x: 150,

            y: 100,

            floating: true,

            borderWidth: 1,

            backgroundColor:

            Highcharts.defaultOptions.legend.backgroundColor || '#FFFFFF'

        },

        xAxis: {

            categories: [

            'Monday',

            'Tuesday',

            'Wednesday',

            'Thursday',

            'Friday',

            'Saturday',

            'Sunday'

            ],

        plotBands: [{ // visualize the weekend

            from: 4.5,

            to: 6.5,

            color: 'rgba(68, 170, 213, .2)'

        }]

    },

    yAxis: {

        title: {

            text: 'Pegawai'

        }

    },

    tooltip: {

        shared: true,

        valueSuffix: ' Orang'

    },

    credits: {

        enabled: false

    },

    plotOptions: {

        areaspline: {

            fillOpacity: 0.5

        }

    },

    series: [{

        name: 'Kehadiran',

        data: [3, 4, 3, 5, 4, 10, 12]

    }, {

        name: 'Alpa',

        data: [1, 3, 4, 3, 3, 5, 4]

    }

    , {

        name: 'Cuti',

        data: [4, 5,1, 6, 12, 3, 1]

    }]

});



});







</script>            

@stop