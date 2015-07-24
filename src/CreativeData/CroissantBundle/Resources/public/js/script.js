$(document).ready(function () {

    $.ajax({
        method: "POST",
        url: "statsToChose"
    }).done(function (msg) {
                serie = jQuery.parseJSON( msg);

                $("#graphtochose").highcharts({
                    chart: {
                        plotBackgroundColor: null,
                        plotBorderWidth: null,
                        plotShadow: false
                    },
                     credits: {
                          enabled: false
                    }, 
                    title: {
                        text: 'Probabilité d\'être selectionné'
                    },
                    tooltip: {
                        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                    },
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: true,
                                format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                                style: {
                                    color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                                }
                            }
                        }
                    },
                    series: [{
                            type: 'pie',
                            name: 'Utilisateur',
                            data: serie
                        }]
                });
            });

    $.ajax({
        method: "POST",
        url: "statsChosen"
    })
            .done(function (msg) {
                serie = jQuery.parseJSON( msg);
                $("#graphchosen").highcharts({
                    chart: {
                        plotBackgroundColor: null,
                        plotBorderWidth: null,
                        plotShadow: false
                    },
                     credits: {
                         enabled: false
                    },
                    title: {
                        text: 'Personnes selectionnées'
                    },
                    tooltip: {
                        pointFormat: '{series.name}: <b>{point.y}</b>'
                    },
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: true,
                                format: '<b>{point.name}</b>: {point.y} ',
                                style: {
                                    color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                                }
                            }
                        }
                    },
                    series: [{
                            type: 'pie',
                            name: 'Utilisateur',
                            data: serie
                        }]
                });

            });
});