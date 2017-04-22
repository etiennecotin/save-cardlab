//Overlapping bars on mobile

var data = {
  labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
  series: [
    [5, 4, 3, 7, 5, 10, 3, 4, 8, 10, 6, 8],
    [3, 2, 9, 5, 4, 6, 4, 6, 7, 8, 7, 4],
    [6, 3, 5, 9, 8, 5, 6, 8, 3, 2, 3, 6]
  ]
};

var options = {
  seriesBarDistance: 10
};

var responsiveOptions = [
  ['screen and (max-width: 640px)', {
    seriesBarDistance: 5,
    axisX: {
      labelInterpolationFnc: function (value) {
        return value[0];
      }
    }
  }]
];

new Chartist.Bar('#overlapping-bars', data, options, responsiveOptions);

$('#sparkline1').sparkline([0, 23, 43, 35, 44, 45, 56, 37, 40], {
            type: 'line',
            width: $('#sparkline1').width(),
            height: '180',
            chartRangeMax: 50,
            lineColor: '#5fbeaa',
            fillColor: 'rgba(95, 190, 170, 0.3)',
            highlightLineColor: 'rgba(0,0,0,.1)',
            highlightSpotColor: 'rgba(0,0,0,.2)',
        });

$('#sparkline1').sparkline([25, 23, 26, 24, 25, 32, 30, 24, 19], {
            type: 'line',
            width: $('#sparkline1').width(),
            height: '180',
            chartRangeMax: 40,
            lineColor: '#5d9cec',
            fillColor: 'rgba(93, 156, 236, 0.3)',
            composite: true,
            highlightLineColor: 'rgba(0,0,0,.1)',
            highlightSpotColor: 'rgba(0,0,0,.2)',
        });