<?php

if(!isset($_SESSION)) 
{ 
    session_start(); 
}

include 'db.php';

if(isset($_SESSION['user_name']) && $_SESSION['role'] == 'admin') {

$user_id=$_SESSION['user_id'];  

?>
<!-- Header.php. Contains header content -->
<?php include 'template/header.php';?>
<link rel="stylesheet"
    href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="https://code.jquery.com/ui/1.10.2/themes/smoothness/jquery-ui.css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
<script src="https://code.jquery.com/ui/1.10.2/jquery-ui.js"></script>
<script src="https://d3js.org/d3.v3.min.js"></script>
 <!-- CUSTOM GRAPH CSS -->
 <style>
    #svg-chart-container path.link {
      fill: none;
      stroke-width: 1.5px;
    }

    #svg-chart-container circle {
      fill: #ccc;
      stroke: #fff;
      stroke-width: 1.5px;
    }

    #svg-chart-container text {
      fill: #000;
      font: 15px sans-serif;
      pointer-events: none;
    }

    .left-panel {
      position: absolute;
      top: 60px;
      left: 20px;
      z-index: 1;
      border: 1px solid gray;
      border-radius: 5px;
      background-color: rgba(30, 55, 62, 0.9);
      max-width: 500px;
      z-index: 10;
    }

    .insights-wrapper {
      border-radius: 5px;
    }

    #left-panel-table tbody tr th {
      border-right: 1px solid gray;
      color: gray;
    }

    .title-center {
      text-align: center;
      margin: 5px;
      font-size: 16px;
      font-weight: lighter;
      color: gray;
    }

    .hidden {
      display: none;
    }

    .absolute-positioned {
      position: absolute;
      left: 10px;
    }

    #left-panel-table td {
      padding: 0.75rem;
    }

    #toggle-connections {
      position: absolute;
    }

    #slider-range {
      position: absolute;
      left: 20px;
      width: 350px;
      top: 85%;
    }

    #slider-paragraph {
      margin-top: 10px;
    }
  </style>
<body class="hold-transition skin-black-light sidebar-mini">
<div class="wrapper">

<?php include 'template/main-header.php';?>
 <!-- Left side column. contains the logo and sidebar -->
<?php include 'template/main-sidebar.php';?>
 <!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <h1>
          Attack Graph
          <small>Click on attacker to view details!</small>
        </h1>
        <ol class="breadcrumb">
          <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
          <li><a href="#">Attack Graph</a></li>
        </ol>
      </section>

      <!-- Main content -->
      <section class="content">

        <!-- Default box -->
        <div class="box">
          <div id="slider-range"></div>
          <div id="chart-container"></div>

          <div class="left-panel">
            <div id="node-details-title-center" class="title-center">
              <i style="color: red; cursor: pointer;" class="fa fa-plus maximize hidden"></i>
              <i style="color: red; cursor: pointer;" class="fa fa-minus minimize hidden absolute-positioned"></i>
              Attacker Details
            </div>
            <div class="insights-wrapper">
              <table id="left-panel-table" class="table table-borderless">
              </table>
            </div>
          </div>

          <div id="toggle-connections"></div>

        </div>
        <!-- /.box -->

      </section>
      <!-- /.content -->
    </div>
    <!-- /.content-wrapper --> 

    <script>
    var decoyGroups, decoyNames, nodes, links, graphData, path, node;

    var enableAnimation = true;
    var container = d3.select('#chart-container').node().parentNode;

    var width = container.getBoundingClientRect().width,
      height = window.innerHeight - 150;
    color = d3.scale.category20c();

    d3.json("/Decoify/graph/getAlertsGraph.php", function (error, graphDataGlobal) {  // main api

      renderChart(graphDataGlobal, null, null);

      //load chart every 5 minutes
      setInterval(() => {
        renderChart(graphDataGlobal, null, null);
      }, 360000);
    });

    function renderChart(graphDataGlobal, startDateGlobal, endDateGlobal) {
      var anglesGlobal = [];

      d3.select('#svg-chart-container').remove();

      graphData = filterBySlider(graphDataGlobal);

      links = generateLinks(graphData);

      nodes = {};

      // Compute the distinct nodes from the links.
      links.forEach(function (link) {
        link.source = nodes[link.source] ||
          (nodes[link.source] = { name: link.source, type: link.source_type, timestamp: link.timestamp });
        link.target = nodes[link.target] ||
          (nodes[link.target] = { name: link.target, type: link.target_type, timestamp: link.timestamp });
        link.value = +link.value;
      });

      setCircularValues();

      var attackersSize = getAttackerImageSize();

      var force = d3.layout.force()
        .gravity(-0.05)
        .nodes(d3.values(nodes))
        .links(filteredLinks())
        .size([width, height])
        .linkDistance(function (d) {
          var x = width / 15;
          return x;
        })
        .charge(-200)
        .on("tick", tick)
        .start();

      // Set the range
      var v = d3.scale.linear().range([0, 100]);

      // Scale the range of the data
      v.domain([0, d3.max(links, function (d) { return d.value; })]);

      // asign a type per value to encode opacity
      links.forEach(function (link) {
        if (v(link.value) <= 25) {
          link.type = "25_percent";
        } else if (v(link.value) <= 50 && v(link.value) > 25) {
          link.type = "50_percent";
        } else if (v(link.value) <= 75 && v(link.value) > 50) {
          link.type = "75_percent";
        } else if (v(link.value) <= 100 && v(link.value) > 75) {
          link.type = "100_percent";
        }

        //change link size here, possible options -> 25_percent, 50_percent, 75_percent, 100_percent
        link.type = "50_percent";

      });

      var svg = d3.select("#chart-container").append("svg")
        .attr("width", width)
        .attr("height", height)
        .attr('id', 'svg-chart-container');

      // build the arrow.
      svg.append("svg:defs").selectAll("marker")
        .data(["end"])      // Different link/path types can be defined here
        .enter().append("svg:marker")    // This section adds in the arrows
        .attr("id", String)
        .attr("viewBox", "0 -5 10 10")
        .attr("refX", 15)
        .attr("refY", -1.5)
        .attr("markerWidth", function (d) { if (d.type == "dejavu") return "0"; else return "10"; })
        .attr("markerHeight", function (d) { if (d.type == "dejavu") return "0"; else return "10"; })
        .attr("orient", "auto")
        .append("svg:path")
        .attr("d", "M0,-5L10,0L0,5");

      //black background
      let backgroundRect = svg
        .append('rect')
        .classed('background-rect', true)
        .attr('width', '100%')
        .attr('height', '100%')
        .attr('fill', 'white')
        .on('click', function (d) {
          d3.select('#toggle-connections').style('display', 'none');
        });

      var g = svg.append('g').attr('transform', 'translate(0,0)')
        .attr('scale', 1);

      // add the links and the arrows
      path = g.append("svg:g").selectAll("path")
        .data(links)
        .enter().append("svg:path")
        .attr("class", function (d) { return "link " + d.type; })
        .attr("marker-end", "url(#end)")
        .style('stroke', function (x) {
          // change link colors here

          var color = 'rgb(102,102,102)'; //gray by default

          if (x.source_type == 'attacker') color = 'red'; //red links for attackers

          var currentDaySeconds = new Date().setHours(0, 0, 0, 0);

          if (x.timestamp) {
            var timestampSeconds = new Date(x.timestamp);

            if (timestampSeconds > currentDaySeconds) // if timestamp date is today, or future date
              color = 'red';
          }

          return color;
        });

      if (enableAnimation)
        path.style('opacity', 0);
      else
        path.style('opacity', function (x) {
          var className = d3.select(this).attr('class');

          if (className.includes('25_percent')) return 0.25;
          if (className.includes('50_percent')) return 0.5;
          if (className.includes('75_percent')) return 0.75;

          return 1;
        });

      var layoutNodes = force.nodes();

      // define the nodes
      node = g.selectAll(".node")
        .data(layoutNodes)
        .enter().append("g")
        .attr("class", "node")
        .on('click', function (d) {
          if (d3.event.defaultPrevented) return;

          if (d.type == 'attacker')
            updatePanelInfo(d)
        })
        .on("contextmenu", function (d, i) {
          d3.event.preventDefault();
          rightClickAction(d);
        })
        .on('mouseenter', function (d) {
          nodeHoverAction(this, d);
        })
        .on('mouseleave', function (d) {
          nodeUnhoverAction(this, d);
        })
        .call(force.drag)
        .attr('cursor', 'pointer')

      if (enableAnimation) {
        node.style('opacity', 0);
        animateGraph(node, path);
      }

      // add title for node
      node.append("title").text(function (d) { return 'qada' });

      // add the nodes
      node.append("circle")
        .attr("r", function (d) { if (d.type == "dejavu") return "25"; else return "10"; })
        .style("fill", function (d) { if (d.type == "dejavu") return "red"; else return color(d.name); })

      // add node image
      node.append("image")
        .attr("xlink:href", function (d) {
          if (d.type == "dejavu") return "images/computer.png";
          if (decoyNames.includes(d.name)) return "images/honeypot1.png";
          if (decoyGroups.includes(d.name)) return "images/location.png";
          else return "images/attacker.png";
        })
        .attr("width", function (d) {
          if (d.type == "dejavu") d.imageWidth = 100;

          else {
            if (d.type == 'attacker') d.imageWidth = attackersSize;
            else d.imageWidth = 50;
          }

          return d.imageWidth;
        })
        .attr("height", function (d) {
          if (d.type == "dejavu") d.imageHeight = 100;

          else {
            if (d.type == 'attacker') d.imageHeight = attackersSize;
            else d.imageHeight = 50;
          }

          return d.imageHeight;
        })
        .attr("x", function (d) { return -d.imageWidth / 2 })
        .attr("y", function (d) { return -d.imageHeight / 2 });

      // add the text 
      node.append("text")
        .attr("x", function (d) {
          return 0;
        })
        .attr("y", function (d) { return d.imageHeight / 2 + 10 })
        .attr("dy", ".35em")
        .attr('text-anchor', 'middle')
        .classed('node-name', true)
        .text(function (d) { return d.name; })
        .attr('display', function (d) {
          return (graphData.length > 50 && d.type == 'attacker') ? 'none' : null;
        });

      d3.select('#slider-container').remove();

      var sliderContainer = d3.select('#slider-range').append('div')
        .attr('id', 'slider-container')
        .style('width', '100%');

      addSlider();

      panelButtonsDisplay();

      //add zoom capabilities 
      var zoom_handler = d3.behavior.zoom()
        .on("zoom", zoom_actions)

      zoom_handler(svg);

      svg.on("mousedown.zoom", null)
        .on("touchstart.zoom", null)
        .on("touchmove.zoom", null)
        .on("touchend.zoom", null);

      function addSlider() {
        // A slider (using only d3 and HTML5) that removes nodes below the input threshold.
        var sliderParagraph = sliderContainer.append('p').attr('id', 'slider-paragraph');
        var sliderLabel = sliderParagraph.append('label').attr('for', 'amount').style('font-size', '14px').text('Attack Date:   ');
        var sliderInput = sliderParagraph.append('input').attr('id', 'amount').style('font-size', '14px').style('border', 0).style('color', '#f6931f').style('font-weight', 'bold').attr('size', 27);

        var minDateSeconds = d3.min(graphDataGlobal.map(x => new Date(x.LogInsertedTimeStamp).getTime()));
        var maxDateSeconds = d3.max(graphDataGlobal.map(x => new Date(x.LogInsertedTimeStamp).getTime()));

        var maxDate = new Date(maxDateSeconds);
        var start = startDateGlobal || new Date(maxDate.setDate(maxDate.getDate() - 14));
        var end = endDateGlobal || new Date(maxDateSeconds);

        $("#slider-container").slider({
          range: true,
          min: minDateSeconds / 1000,
          max: maxDateSeconds / 1000,
          step: 86400,
          values: [start.getTime() / 1000, end.getTime() / 1000],
          slide: function (event, ui) {
            ui.values[0] += 86400;

            var startDate = new Date(ui.values[0] * 1000);
            var endDate = new Date(ui.values[1] * 1000);

            start.setDate(start.getDate() + 1)

            $("#amount").val((parseDate(startDate)) + " - " + parseDate(endDate));
          },
          stop: function (event, ui) {
            ui.values[0] += 86400;

            enableAnimation = false;
            var startDate = new Date(ui.values[0] * 1000);
            var endDate = new Date(ui.values[1] * 1000);

            renderChart(graphDataGlobal, startDate, endDate);
          }
        });

        $("#amount").val((parseDate(start)) + " - " + parseDate(end));
      }

      function parseDate(date) {
        return date.toDateString().split(' ').slice(1).join(' ');
      }

      function animateGraph(node, path) {
        //dejavu
        node.filter(x => x.type == 'dejavu').transition().duration(800).delay(0).style('opacity', 1);

        //decoy_group
        node.filter(x => x.type == 'decoy_group').transition().duration(800).delay(800).style('opacity', 1);

        //decoy
        node.filter(x => x.type == 'decoy_name').transition().duration(800).delay(1600).style('opacity', 1);

        //attacker
        node.filter(x => x.type == 'attacker').transition().duration(800).delay(2400).style('opacity', 1);

        path.transition().duration(800).delay(2400).style('opacity', function (x) {
          var className = d3.select(this).attr('class');

          if (className.includes('25_percent')) return 0.25;
          if (className.includes('50_percent')) return 0.5;
          if (className.includes('75_percent')) return 0.75;
          return 1;
        });
      }

      function setCircularValues() {
        var dejavu = { x: width / 2 - 7, y: height / 2 - 7 };
        var minSize = d3.min([width, height]);

        if (nodes['DEJAVU']) {
          nodes['DEJAVU'].fixed = true;
          nodes['DEJAVU'].x = dejavu.x;
          nodes['DEJAVU'].y = dejavu.y;
        }

        var firstLeveLRadius = minSize / 6;
        var secondLeveLRadius = minSize / 3

        anglesGlobal.forEach(function (record) {
          var innerNode = nodes[record.name];
          var radius = innerNode.type == 'decoy_group' ? firstLeveLRadius : secondLeveLRadius;

          var attackersCount = links.filter(x => x.target.name == record.name).length;
          if (attackersCount > 50) radius += attackersCount / 2;

          innerNode.fixed = true;
          innerNode.x = getX(dejavu.x, radius, record.angle);
          innerNode.y = getY(dejavu.y, radius, record.angle);
        });
      }

      function getX(centerX, radius, angle) {
        let radian = angle * Math.PI / 180;
        let x = radius * Math.cos(radian);
        return centerX + x;
      }

      function getY(centerY, radius, angle) {
        let radian = angle * Math.PI / 180;
        let y = radius * Math.sin(radian);
        return centerY + y;
      }


      function filteredLinks() {
        var attackerLinks = links.filter(x => x.source_type == 'attacker');

        var duplicatedAttackerNames = [];

        attackerLinks.forEach(function (x) {
          if (attackerLinks.filter(d => d.source.name == x.source.name).length > 1) duplicatedAttackerNames.push(x.source.name);
        });

        duplicatedAttackerNames = Array.from(new Set(duplicatedAttackerNames));

        var singleLinks = [];

        var reversedLinks = links.slice().reverse()

        duplicatedAttackerNames.forEach(function (name) { singleLinks.push(reversedLinks.find(x => x.source_type == 'attacker' && x.source.name == name)); });

        return links.filter(x => !(x.source_type == 'attacker' && duplicatedAttackerNames.includes(x.source.name))).concat(singleLinks);
      }

      function getAttackerImageSize() {
        var recordsCount = graphData.length;
        var size = 0;
        if (recordsCount > 150) return 20;
        if (recordsCount > 100) return 25;
        if (recordsCount > 80) return 30;
        if (recordsCount > 60) return 35;
        if (recordsCount > 50) return 40;
        return 45;
      }

      function panelButtonsDisplay() {
        $('.minimize').click(function () {
          minimizeData();
        });

        $('.maximize').click(function () {
          maximizeData();
        });
      }

      function rightClickAction(d) {
        let div = d3.select('#toggle-connections');

        let content = `<div style='background: #f6f5f3; width:195px; font-family: sans-serif; box-shadow:3px 3px 5px #797878; height:50px; padding:3%; border-radius: 3px;'>
                     <input type='checkbox' id='tooltip-checkbox'/> show connected nodes 
                     </div>`;

        div.html(content)
          .style("left", (d3.event.pageX + 10) + "px")
          .style("top", (d3.event.pageY) + "px")
          .style("display", "block");

        div.select('input#tooltip-checkbox').on('click', function () {
          if (this.checked)
            showOneNodeConnections(d);
          else
            showAllConnections();
        });
      }

      function showOneNodeConnections(d) {
        showAllConnections();

        switch (d.type) {
          case 'dejavu':
            dejavuRightClick(d);
            break;
          case 'decoy_group':
            groupRightClick(d);
            break;
          case 'decoy_name':
            decoyRightClick(d);
            break;
          case 'attacker':
            attackerRightClick(d);
            break;

          default:
            break;
        }
      }

      function dejavuRightClick(d) {
        let currentNodeLinks = d3.selectAll('.link').filter(x => x.source.name == d.name || x.target.name == d.name);
        let nodeNames = currentNodeLinks.data().map(x => [x.source.name, x.target.name]).flat();
        d3.selectAll('.link').filter(x => x.source.name != d.name && x.target.name != d.name).attr('display', 'none');
        d3.selectAll('.node').filter(x => !nodeNames.includes(x.name) && x.name.toLowerCase() != 'dejavu').attr('display', 'none');
      }

      function groupRightClick(d) {
        let connectedNodeNames = [d.name].concat(Array.from(new Set(graphData.filter(x => x.Decoy_Group == d.name).map(x => x.Decoy_Name))));
        let currentNodeLinks = d3.selectAll('.link').filter(x => connectedNodeNames.includes(x.source.name) || connectedNodeNames.includes(x.target.name));
        let nodeNames = currentNodeLinks.data().map(x => [x.source.name, x.target.name]).flat();
        d3.selectAll('.link').filter(x => !connectedNodeNames.includes(x.source.name) && !connectedNodeNames.includes(x.target.name)).attr('display', 'none');
        d3.selectAll('.node').filter(x => !nodeNames.includes(x.name) && x.name.toLowerCase() != 'dejavu').attr('display', 'none');
      }

      function decoyRightClick(d) {
        let connectedNodeNames = [d.name]
        let groupName = graphData.find(x => x.Decoy_Name == d.name).Decoy_Group;

        let currentNodeLinks = d3.selectAll('.link').filter(function (x) {
          let condition1 = connectedNodeNames.includes(x.source.name) || connectedNodeNames.includes(x.target.name);
          let condition2 = x.source.name == 'DEJAVU' && x.target.name == groupName;
          return condition1 || condition2;
        });

        let nodeNames = currentNodeLinks.data().map(x => [x.source.name, x.target.name]).flat();

        d3.selectAll('.link').filter(function (x) {
          let condition1 = connectedNodeNames.includes(x.source.name) || connectedNodeNames.includes(x.target.name);
          let condition2 = x.source.name == 'DEJAVU' && x.target.name == groupName;
          return !condition1 && !condition2;
        }).attr('display', 'none');

        d3.selectAll('.node').filter(x => !nodeNames.includes(x.name) && x.name.toLowerCase() != 'dejavu').attr('display', 'none');
      }

      function attackerRightClick(d) {
        let attackerIP = d.name;
        let decoys = graphData.filter(x => x.Attacker_IP == attackerIP).map(x => x.Decoy_Name);
        let groups = graphData.filter(x => decoys.includes(x.Decoy_Name)).map(x => x.Decoy_Group)
        let nodeNames = Array.from(new Set([attackerIP].concat(decoys).concat(groups)));

        d3.selectAll('.link').filter(function (x) {
          let condition1 = decoys.includes(x.target.name);
          let condition2 = groups.includes(x.source.name) || x.source.name == attackerIP;
          return !condition1 || !condition2
        })
          .attr('display', 'none');

        d3.selectAll('.link').filter(function (x) {
          return x.source.name == 'DEJAVU' && groups.includes(x.target.name);
        }).attr('display', null);

        d3.selectAll('.node').filter(x => !nodeNames.includes(x.name) && x.name.toLowerCase() != 'dejavu').attr('display', 'none');
      }

      function showAllConnections() {
        d3.selectAll('.link').attr('display', null);
        d3.selectAll('.node').attr('display', null);
      }

      function minimizeData() {
        $('.insights-wrapper').addClass('hidden');
        $('.minimize').addClass('hidden');
        $('.maximize').removeClass('hidden');
      }

      function maximizeData() {
        $('.insights-wrapper').removeClass('hidden');
        $('.maximize').addClass('hidden');
        $('.minimize').removeClass('hidden');
      }

      function nodeHoverAction(node, data) {
        d3.select(node).select('.node-name').attr('display', null);
      }

      function nodeUnhoverAction(node, data) {
        d3.select(node).select('.node-name')
          .attr('display', (graphData.length > 50 && data.type == 'attacker' && parseFloat(g.attr('scale')) <= 1.2) ? 'none' : null);
      }

      //send request to an api
      function updatePanelInfo(node) {
        $.ajax({
          url: '/Decoify/graph/getEventsJSON.php', // replace this url with your api
          type: "get",
          dataType: "json",
          contentType: "application/json",
          success: function (response) {
            var nodeEvents = response.filter(x => x.Attacker_IP == node.name).sort(function (a, b) { return new Date(b['max(timestamp)']) - new Date(a['max(timestamp)']) });

            if (nodeEvents.length == 0) return;

            var panelJson = {
              'Attacker IP': node.name,
              'Last Attack Time': nodeEvents[0]['max(timestamp)'],
              'Attacks Carried OUT': getAttacksCountString(nodeEvents),
              'Link to Attacker': '<a href="search.php?attackerIP=' + node.name + '">Link</a>'
            }

            var leftPanelTable = $('#left-panel-table');
            $("#left-panel-table tbody").remove();

            for (var key in panelJson) {
              leftPanelTable.append('<tbody><tr><th style="border-right: 1px solid gray; color: gray;">' + key + '</th>' + '<td style="color: gray; word-break:break-all;">' + panelJson[key] + '</td></tr></tbody>');
            };

            maximizeData();

          }
        });

      }

      function getAttacksCountString(events) {
        var nested = d3.nest()
          .key((d, i) => d.EventType)
          .entries(events);

        let attacksCountArray = [];

        nested.forEach(function (record) {
          attacksCountArray.push(record.key);
        })

        return attacksCountArray.join(', ') + ' (' + attacksCountArray.length + ' Attempts)'
      }

      //Zoom functions 
      function zoom_actions() {
        g.attr("transform", function (d) {
          let x = d3.event.translate[0];
          let y = d3.event.translate[1];
          let scale = d3.event.scale;
          return 'translate(' + x + ',' + y + ') scale(' + scale + ')';
        })
          .attr('scale', d3.event.scale);

        d3.selectAll('.node').filter(x => x.type == 'attacker')
          .selectAll('.node-name')
          .attr('display', d3.event.scale > 1.2 ? null : 'none');
      }

      // add the curvy lines
      function tick() {
        path.attr("d", function (d) {
          return "M" + d.source.x + "," + d.source.y + "L" + d.target.x + "," + d.target.y;

        });

        node.attr("transform", function (d) {
          let props = this.getBoundingClientRect();
          return "translate(" + (d.x = Math.max(props.width, Math.min(width - props.width, d.x))) + ","
            + (d.y = Math.max(props.height, Math.min(height - props.height, d.y))) + ")"
        });
      }

      function generateLinks(graphData) {
        // graphData = graphData.filter(x => !(x.Attacker_IP == '192.168.0.1' && x.id != 218))
        // graphData = graphData.filter(x => !(x.Attacker_IP == '192.168.0.16' && x.id != 230))

        var mainNode = 'Dejavu';
        decoyGroups = Array.from(new Set(graphData.map(x => x.Decoy_Group)));
        decoyNames = Array.from(new Set(graphData.map(x => x.Decoy_Name)));

        calculateAngles(decoyGroups, decoyNames);

        var topLevelLinks = generateTopLevelLinks(mainNode, decoyGroups);
        connectGroupsToDecoys(topLevelLinks, graphData);
        var allLinks = generateAllLinks(topLevelLinks, graphData);

        return allLinks;
      }

      function filterBySlider(graphDataGlobal) {
        var maxDateSeconds = d3.max(graphDataGlobal.map(x => new Date(x.LogInsertedTimeStamp).getTime()));
        var maxDate = new Date(maxDateSeconds);

        var start = startDateGlobal || new Date(maxDate.setDate(maxDate.getDate() - 14));
        var end = endDateGlobal || new Date(maxDateSeconds);

        start.setHours(0, 0, 0, 0);
        end.setHours(23, 59, 59, 999);

        return graphDataGlobal.filter(x => new Date(x.LogInsertedTimeStamp) > start && new Date(x.LogInsertedTimeStamp) < end);
      }

      function calculateAngles(groups, decoys) {
        let eachGroupAngle = 360 / groups.length;
        let eachDecoyAngle = 360 / decoys.length;
        let groupWithAngles = [];
        let decoysWithAngles = [];

        groups.forEach(function (groupName, i) {
          anglesGlobal.push({ name: groupName, angle: i * eachGroupAngle });
        });

        decoys.forEach(function (decoyName, i) {
          anglesGlobal.push({ name: decoyName, angle: i * eachDecoyAngle });
        });

      }

      function generateAllLinks(topLevelLinks, graphData) {
        graphData.forEach(function (d) {
          topLevelLinks.push({ source: d.Attacker_IP, target: d.Decoy_Name, value: 1.5, timestamp: d.LogInsertedTimeStamp, source_type: 'attacker', target_type: 'decoy_name' });
        });

        return topLevelLinks;
      }

      function connectGroupsToDecoys(topLevelLinks, graphData) {
        let nameGroupPairs = graphData.map(function (x) { return { name: x.Decoy_Name, group: x.Decoy_Group } });
        let uniquePairs = [];

        nameGroupPairs.forEach(function (x) {
          if (!uniquePairs.find(d => d.name == x.name && d.group == x.group)) uniquePairs.push(x);
        });

        uniquePairs.forEach(function (d) {
          topLevelLinks.push({ source: d.group, target: d.name, value: 3, source_type: 'decoy_group', target_type: 'decoy_name' });
        })
      }

      function generateTopLevelLinks(mainNode, decoyGroups) {
        var records = [];
        decoyGroups.forEach(decoyGroup => records.push({ source: mainNode, target: decoyGroup, value: 10, source_type: 'dejavu', target_type: 'decoy_group' }));
        return records;
      }

      // action to take on mouse click
      function click() {
        d3.select(this).select("text").transition()
          .duration(750)
          .attr("x", 22)
          .style("stroke", "lightsteelblue")
          .style("stroke-width", ".5px")
          .style("font", "20px sans-serif");
        d3.select(this).select("circle").transition()
          .duration(750)
          .attr("r", 16);
      }

      // action to take on mouse double click
      function dblclick() {
        d3.select(this).select("circle").transition()
          .duration(750)
          .attr("r", 10);
        d3.select(this).select("text").transition()
          .duration(750)
          .attr("x", 12)
          .style("stroke", "none")
          .style("fill", "black")
          .style("stroke", "none")
          .style("font", "10px sans-serif");
      }

    }

  </script>

<script>
    $(document).ready(function () {
      $('.sidebar-menu').tree()
    })
  </script>
<?php include 'template/main-footer.php';?>
</body>
</html>
<?php
}
else 
{
  header('location:loginView.php');
}
?>
