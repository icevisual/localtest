<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head > 
    <title>Highchart js export module sample</title> 
    <!-- 1.引入jquery库 --> 
    <script src="flatUI/js/jquery-1.8.3.min.js"></script>
    <!-- 2.引入highcharts的核心文件 --> 
    <script src="Highcharts/highcharts.js" type="text/javascript"></script> 
    <!-- 3.引入导出需要的js库文件 --> 
    <script src="Highcharts/modules/exporting.js" type="text/javascript"></script> 
	<script language="javascript" type="text/javascript"> 
    var chart; 
    $(document).ready(function () { 
        chart = new Highcharts.Chart({ 
            chart: { 
                renderTo: 'container', 
                defaultSeriesType: 'line', //图表类别，可取值有：line、spline、area、areaspline、bar、column等 
                marginRight: 130, 
                marginBottom: 25 
            }, 
            title: { 
                text: '<?php echo $chartData['title'];?>', //设置一级标题 
                x: -20 //center 
            }, 
            subtitle: { 
                text: '<?php echo $chartData['subtitle'];?>', //设置二级标题 
                x: -20 
            }, 
            xAxis: <?php echo $chartData['xAxis'];?>, 
            yAxis: { 
                title: { 
                    text: '<?php echo $chartData['yAxis_title'];?>' //设置y轴的标题 
                }, 
                plotLines: [{ 
                    value: 0, 
                    width: 1, 
                    color: '#808080' 
                }] 
            }, 
            tooltip: { 
                formatter: function () { 
                    return '<b>' + this.series.name + '</b><br/>' + 
               this.x + ': ' + this.y + ' ';  //鼠标放在数据点的显示信息，但是当设置显示了每个节点的数据项的值时就不会再有这个显示信息 
                } 
            }, 
            legend: { 
                layout: 'vertical', 
                align: 'right', //设置说明文字的文字 left/right/top/ 
                verticalAlign: 'top', 
                x: -10, 
                y: 100, 
                borderWidth: 0 
            }, 
            exporting: { 
                enabled: true, //用来设置是否显示‘打印’,'导出'等功能按钮，不设置时默认为显示 
                url: "" //导出图片的URL，默认导出是需要连到官方网站去的哦 
            }, 
            plotOptions: { 
                line: { 
                    dataLabels: { 
                        enabled: true //显示每条曲线每个节点的数据项的值 
                    }, 
                    enableMouseTracking: false 
                } 
            }, 
            series: <?php echo $chartData['series'];?> 
        });

    }); 
    </script> 
   </head> 
<body> 
    <form id="form1" runat="server"> 
    <!--5.导入容器用于显示图表--> 
    <div id="container" style="width: 900px;"> 
    </div> 
    </form> 
</body> 
</html>