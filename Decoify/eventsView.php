<?php
if(!isset($_SESSION)) 
{ 
    session_start(); 
}

if(isset($_SESSION['user_name']) && $_SESSION['role'] == 'admin') {
?>
<!-- Header.php. Contains header content -->
<?php include 'template/header.php';?>
<body class="hold-transition skin-blue sidebar-mini">
<?php include 'template/main-header.php';?>
 <!-- Left side column. contains the logo and sidebar -->
<?php include 'template/main-sidebar.php';?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        List Events
        <!--
        <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#modal-default">
                Search Filter
        </button>
        -->
      <!--       
      <button type="button" class="btn btn-default" id="daterange-btn">
        <span>
        <?php 
          if(isset($_POST["startDate"]) && $_POST["startDate"] != '')
          {
            echo $_POST["startDate"] . ' - ' . $_POST["endDate"];
          }
          else{
            echo '<i class=\"fa fa-calendar\"></i> Filter by Date';
          }

        ?>

        </span>
        <i class="fa fa-caret-down"></i>
      </button>
    -->

      <button type="button" class="btn btn-success btn-sm" id="graphsubmit-btn" onclick="graphsubmit()">
       View as Graph         
      </button>

      <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modal-danger">
                Remove All
      </button>

      <script>
      function removeAlerts() {
          document.getElementById("removeAlerts").submit();
      }
      </script>

      <div class="modal modal-danger fade" id="modal-danger">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
              <p>Remove all seen alerts</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Close</button>
              <button type="button" class="btn btn-outline" onclick="removeAlerts()" />Save changes</button>
              <form id="removeAlerts" action="events.php" method="post">
                  <input type="hidden" name="delete" value="delete_all"/>
              </form>
            </div>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>
        <!-- /.modal -->
      

                
      </h1>
     
    
       
    </section>

    <!-- Main content -->
<section class="content">
      <div class="row">

        <div class="col-xs-12">
      
        </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
           <div class="modal fade" id="modal-default">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">×</span></button>

                    <h4 class="modal-title">Search Filter</h4>

                  </div>
                  <div class="modal-body" id="modal-body">
                      <div class="margin">
                        <p>Match 
                          <select name="filter" id="filter">
                          <?php if (!isset($vals[0]['filter'])) $vals[0]['filter'] = '';?>
                          <option value="all" <?php if ($vals[0]['filter'] == 'all') { echo ' selected="selected"'; } ?> >All
                          </option>
                          <option value="any" <?php if ($vals[0]['filter'] == 'any') { echo ' selected="selected"'; } ?> >Any
                          </option>
                          </select>
                        of the following
                        </p>
                      </div>
                      <?php if(empty($vals[0]['searchQuery']))
                      {
                      ?>
                      <div class="margin" id="advanceSearch">
                          <select name="criteria" id="criteria">
                            <option value="decoyName">Decoy Name</option>
                            <option value="decoyGroup">Network Location</option>
                            <option value="decoyIP">Decoy IP</option>
                            <option value="attackerIP">Attacker IP</option>
                            <option value="eventType">Event Type</option>
                          </select>

                          <select name="condition" id="condition">
                            <option value="eq">Is equal to</option>
                            <option value="not_eq">Not equal to</option>
                          </select>
                          <input type="text"  style="width:200px; height: 30px" name="search_data" placeholder="Enter Here" id="search_data" required="" />
                          <i class="fa fa-fw fa-plus" id="add-filter"></i>
                      </div>
                      <?php 
                      }
                      ?>
                      <?php if(!empty($vals[0]['searchQuery']))
                      {
                        //print_r($vals[0]['searchQuery']);
                        $i = 0;
                        foreach ($vals[0]['searchQuery'] as $vals):
                      ?>
                      <div class="margin" id="advanceSearch">
                          <select name="criteria" id="criteria">
                            <option value="decoyName" <?php if ($vals['criteria'] == 'decoyName') { echo ' selected="selected"'; } ?>>Decoy Name
                            </option>
                            <option value="decoyGroup" <?php if ($vals['criteria'] == 'decoyGroup') { echo ' selected="selected"'; } ?> >Network Location
                            </option>
                            <option value="decoyIP" <?php if ($vals['criteria'] == 'decoyIP') { echo ' selected="selected"'; } ?> >Decoy IP
                            </option>
                            <option value="attackerIP" <?php if ($vals['criteria'] == 'attackerIP') { echo ' selected="selected"'; } ?> >Attacker IP
                            </option>
                            <option value="eventType" <?php if ($vals['criteria'] == 'eventType') { echo ' selected="selected"'; } ?> >Event Type
                            </option>
                          </select>

                          <select name="condition" id="condition">
                            <option value="eq" <?php if ($vals['condition'] == 'eq') { echo ' selected="selected"'; } ?> >Is equal to</option>
                            <option value="not_eq" <?php if ($vals['condition'] == 'not_eq') { echo ' selected="selected"'; } ?> >Not equal to</option>
                          </select>
                          <input type="text"  style="width:200px; height: 30px" name="search_data" value="<?= dataFilter($vals['search_data'])?>" id="search_data" required="" />
                          <?php if($i == 0){ ?>
                            <i class="fa fa-fw fa-plus" id="add-filter"></i>
                          <?php }
                          else { ?>
                            <i class="fa fa-fw fa-close" id="del-filter" onClick='Remove(this)'></i>
                          <?php }?>
                      </div>
                      <?php
                        $i++;
                        endforeach;
                      }
                      ?>
                      <form id="formSearch" name="formSearch" action="search.php" method="POST">
                          <input type="hidden" name="jsonSearchString" id="jsonSearchString" value="" />
                          <input type="hidden" name="deleteFilter" id="deleteFilter" value="" />
                           <input type="hidden" name="startDate" id="startDate" value="" />
                            <input type="hidden" name="endDate" id="endDate" value="" />
                      </form>

                      <form id="graphview" name="graphview" action="loggraph.php" method="POST">
                             <input type="hidden" name="jsonSearchString" id="jsonSearchStringgraph" value="" />
                           <input type="hidden" name="startDate" id="startDate" value="<?= $startDate?>" />
                            <input type="hidden" name="endDate" id="endDate" value="<?= $endDate?>" />
                      </form>
                    
                    <script>
                      var new_filter =`<div class="margin" id="advanceSearch">
                          <select name="criteria" id="criteria">
                            <option value="decoyName">Decoy Name</option>
                            <option value="decoyGroup">Network Location</option>
                            <option value="decoyIP">Decoy IP</option>
                            <option value="attackerIP">Attacker IP</option>
                            <option value="eventType">Event Type</option>
                          </select>

                          <select name="condition" id="condition">
                            <option value="eq">Is equal to</option>
                            <option value="not_eq">Not equal to</option>
                          </select>
                          <input type="text" id="search_data" style="width:200px; height: 30px" name="search_data" placeholder="Enter Here" required="" />
                          <i class="fa fa-fw fa-close" id="del-filter" onClick='Remove(this)'></i>
                      </div>`;//Get inner html
                      $(document).ready(function(){
                          $('#add-filter').click(function() {
                              $('#modal-body').append(new_filter);
                              return false;
                              });

                          jsonObj = [];

                          masterSearch = [];

                          });

                      function Remove(obj){
                          $(obj).closest('div').remove();
                      }

                      function searchSubmit() {

                          $('div#advanceSearch').each(function() {
                              
                            
                              var criteria = $('#criteria', this).val();
                              var condition = $('#condition', this).val();
                              var search_data = $('#search_data', this).val();

                              item = {};

                              item ["criteria"] = criteria;
                              item ["condition"] = condition;
                              item ["search_data"] = search_data;
                              
                              jsonObj.push(item);
                          });

                           //Get the filter value

                          var filter = $('#filter').val();
                          

                          masterSearch.push({
                              filter: filter,
                              searchQuery: jsonObj
                          });
                          
                          var jsonSearchString = JSON.stringify(masterSearch);

                          $('#jsonSearchString').val(jsonSearchString);

                          $("#formSearch").submit();

                      }

                      function graphsubmit()
                      {
                        $('div#advanceSearch').each(function() {
                              
                            
                              var criteria = $('#criteria', this).val();
                              var condition = $('#condition', this).val();
                              var search_data = $('#search_data', this).val();

                              item = {};

                              item ["criteria"] = criteria;
                              item ["condition"] = condition;
                              item ["search_data"] = search_data;
                              
                              jsonObj.push(item);
                          });

                           //Get the filter value

                          var filter = $('#filter').val();
                          

                          masterSearch.push({
                              filter: filter,
                              searchQuery: jsonObj
                          });
                          
                          var jsonSearchString = JSON.stringify(masterSearch);

                          $('#jsonSearchStringgraph').val(jsonSearchString);

                          $("#graphview").submit();
                      }

                      function deleteFilter()
                      {
                         var deleteFilter = 'Yes';

                          $('#deleteFilter').val(deleteFilter);

                          $("#formSearch").submit();

                      }
                      function disbaleAlert(alertID)
                      {
                        var alertID = alertID;

                        var action = 'disable';

                        $("#disableAlert" + alertID).submit();

                      }
                      </script>    
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" onclick="deleteFilter()">Remove Filter</button>

                    <button type="button" class="btn btn-primary" id="advSearchSubmit" onclick="searchSubmit()">Save changes</button>
                  </div>
                </div>
                <!-- /.modal-content -->
              </div>
              <!-- /.modal-dialog -->
          </div>

        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
            <!--
              <h3 class="box-title">
              
                  <button type="button" class="btn btn-default" id="daterange-btn">
                      <span>
                        <i class="fa fa-calendar"></i> Date Range
                      </span>
                      <i class="fa fa-caret-down"></i>
                  </button>
              </h3>
            -->
            </div>
            <!-- /.box-header -->
                  <div class="box-body">

                    <table id="example1" class="table table-bordered table-striped">

                      <thead>
                      <tr>
                        <th>Decoy Name</th>
                        <th>Network Location</th>
                        <th>Decoy IP</th>
                        <th>Attacker IP</th>
                        <th class="sorting_desc" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Timestamp: activate to sort column ascending" aria-sort="descending">Last Attack Time</th>
                        <th>Raw Logs</th>
                        <th>Remove Seen</th>
                      </tr>
                      </thead>
                      <tbody>
                      <?php if(!empty($event)){
                      foreach ($event as $key => $value): 
                      ?>
                        <tr role="row" class="odd">
                          <td class=""><?= dataFilter($event[$key]['Decoy_Name'])?></td>
                          <td class=""><?= dataFilter($event[$key]['Decoy_Group'])?></td>
                          <td class=""><?= dataFilter($event[$key]['Decoy_IP'])?></td>
                          <td class=""><?= dataFilter($event[$key]['Attacker_IP'])?></td>
                          <td class="" ><?= dataFilter($event[$key]['LogInsertedTimeStamp'])?></td>
                          <td class=""><a href="search.php?attackerIP=<?= dataFilter($event[$key]['Attacker_IP'])?>&decoyIP=<?= dataFilter($event[$key]['Decoy_IP'])?>">View Logs</a></td>
                            <form id="disableAlert<?= dataFilter($event[$key]['id'])?>" action="events.php" method="POST">

                            <input type="hidden" name="alert_id"" value="<?= dataFilter($event[$key]['id'])?>" />
                            <input type="hidden" name="action" value="disable" />
                            </form>

                          <td class="" style="text-align: center;"><span class="glyphicon glyphicon-remove" onclick="disbaleAlert(<?= dataFilter($event[$key]['id'])?>)"></span></td>
                        </tr>
                      <?php endforeach;
                      }
                      ?>
                      </tbody>
                    </table>
                  </div>
              </div>
            </div>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
          <!-- /.box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
<!-- DataTables -->
<script src="UIElements/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="UIElements/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
 <!-- DataTables -->
<link rel="stylesheet" href="UIElements/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
<script>
  $(function () {
    $('#example1').DataTable({
      "order": [[ 5, "desc" ]]
    })
    $('#example2').DataTable({
      'paging'      : true,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : true,
      'autoWidth'   : false
    })
  })
</script>
<script>
  $(function () {
    //Initialize Select2 Elements
    $('.select2').select2()

    //Datemask dd/mm/yyyy
    $('#datemask').inputmask('dd/mm/yyyy', { 'placeholder': 'dd/mm/yyyy' })
    //Datemask2 mm/dd/yyyy
    $('#datemask2').inputmask('mm/dd/yyyy', { 'placeholder': 'mm/dd/yyyy' })
    //Money Euro
    $('[data-mask]').inputmask()

    //Date range picker
    $('#reservation').daterangepicker()
    //Date range picker with time picker
    $('#reservationtime').daterangepicker({ timePicker: true, timePickerIncrement: 30, format: 'MM/DD/YYYY h:mm A' })
    //Date range as a button
    $('#daterange-btn').daterangepicker(
      {
        ranges   : {
          'Today'       : [moment(), moment()],
          'Yesterday'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
          'Last 7 Days' : [moment().subtract(6, 'days'), moment()],
          'Last 30 Days': [moment().subtract(29, 'days'), moment()],
          'This Month'  : [moment().startOf('month'), moment().endOf('month')],
          'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        startDate: moment().subtract(29, 'days'),
        endDate  : moment()
      },
      function (start, end) {
        $('#daterange-btn span').html(start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
        $('#startDate').val(start.format('YYYY-MM-DD'));
        $('#endDate').val(end.format('YYYY-MM-DD'));
        searchSubmit();
      }
    )

    //Date picker
    $('#datepicker').datepicker({
      autoclose: true
    })

    //iCheck for checkbox and radio inputs
    $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
      checkboxClass: 'icheckbox_minimal-blue',
      radioClass   : 'iradio_minimal-blue'
    })
    //Red color scheme for iCheck
    $('input[type="checkbox"].minimal-red, input[type="radio"].minimal-red').iCheck({
      checkboxClass: 'icheckbox_minimal-red',
      radioClass   : 'iradio_minimal-red'
    })
    //Flat red color scheme for iCheck
    $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
      checkboxClass: 'icheckbox_flat-green',
      radioClass   : 'iradio_flat-green'
    })

    //Colorpicker
    $('.my-colorpicker1').colorpicker()
    //color picker with addon
    $('.my-colorpicker2').colorpicker()

    //Timepicker
    $('.timepicker').timepicker({
      showInputs: false
    })
  })
</script>
  <style>
    .example-modal .modal {
      position: relative;
      top: auto;
      bottom: auto;
      right: auto;
      left: auto;
      display: block;
      z-index: 1;
    }

    .example-modal .modal {
      background: transparent !important;
    }
  </style>

<?php include 'template/main-footer.php';?>

</body>
<?php
}
else 
{
  header('location:loginView.php');
}
?>
