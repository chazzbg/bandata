var $ = require('jquery');

require('bootstrap-sass');
require('bootstrap-datepicker');

var moment = require("moment");
require("moment-timezone");

var Highcharts = require('highcharts');

Highcharts.setOptions({
    global: {
        /**
         * Use moment-timezone.js to return the timezone offset for individual
         * timestamps, used in the X axis labels and the tooltip header.
         */
        getTimezoneOffset: function (timestamp) {
            var zone = 'Europe/Sofia';
            return -moment.tz(timestamp, zone).utcOffset();
        }
    }
});

var options = {
    chart: {
        type: 'container'
    },
    title: {
        text: 'Heart rate'
    },
    yAxis: {
        title: {
            text: 'BPM'
        }
    },
    xAxis: {
        type: 'datetime',
        dateTimeLabelFormats: {
            millisecond: '%H:%M:%S.%L',
            second: '%H:%M:%S',
            minute: '%H:%M',
            hour: '%H:%M',
            day: '%d %b %Y',
            week: '%e. %b',
            month: '%b \'%y',
            year: '%Y'
        }
    },
    plotOptions: {
        series: {
            dataGrouping: {
                enabled: true,
                forced: true
            }
        }
    },
    series: [{
        color: "#ff0000",
        name: 'Heart rate',
        type: 'column',
        dataGrouping: {
            approximation: function () {
                console.log(
                    'dataGroupInfo:',
                    this.dataGroupInfo,
                    'Raw data:',
                    this.options.data.slice(this.dataGroupInfo.start, this.dataGroupInfo.start + this.dataGroupInfo.length)
                );
                return this.dataGroupInfo.length;
            },
            forced: true
        },
        data: []
    }]

};

var chart = Highcharts.chart('chart', options);


var fetchResults = function (from, to) {
    $.get('/heartrate/data/' + from + '/' + to, function (data) {
        options.series[0].data = data;
        chart = Highcharts.chart('chart', options);
    });


    $.get('/heartrate/aggregates/' + from + '/' + to, function (data) {
        $.each(data, function (type, stat) {
            $("#" + type + "_stat").text(stat);
        })
    });
};

$('document').ready(function () {

    $("#datepicker").datepicker({
        todayBtn: "linked"
    }).on('changeDate', function (e) {
        var date = moment(e.date).hour(0).minute(0);

        var from = date.format('X');
        date.add(1, 'days');
        var to = date.format('X');
        fetchResults(from, to);
    });


    $("#lastday").on('click', function (e) {
        var date = moment();
        var to = date.format('X');
        date.subtract(1, 'days');
        var from = date.format('X');
        fetchResults(from, to);
    });

    $("#lasthour").on('click', function (e) {
        var date = moment();
        var to = date.format('X');
        date.subtract(1, 'hours');
        var from = date.format('X');
        fetchResults(from, to);
    }).trigger('click');
});
