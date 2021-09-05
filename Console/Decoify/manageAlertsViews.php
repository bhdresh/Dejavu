<?php

if(!isset($_SESSION)) 
{
	
    session_start(); 
}
if(isset($_SESSION['user_name']) && $_SESSION['role'] == 'admin') {
?>
<!-- Header.php. Contains header content -->
<?php include 'template/header.php';?>
<body class="hold-transition skin-black-light sidebar-mini">
<?php include 'template/main-header.php';?>
 <!-- Left side column. contains the logo and sidebar -->
<?php include 'template/main-sidebar.php';?>
<script>
var new_filter =`<div class="margin" id="advanceSearch">
    <select name="criteria" id="criteria">
      <option value="decoyName">Decoy Name</option>
      <option value="decoyGroup">Network Location</option>
      <option value="decoyIP">Decoy IP</option>
      <option value="attackerIP">Attacker IP</option>
      <option value="eventType">Event Type</option>
<option value="serviceType">Service Type</option>

    </select>

    <select name="condition" id="condition">
      <option value="eq">Is equal to</option>
      <option value="not_eq">Not equal to</option>
      <option value="not_eq" id="any_select" class="any_select">Any</option>
    </select>
    <input type="text" id="search_data" style="width:200px; height: 30px" name="search_data" placeholder="Enter Here" class="search_data">
    <i class="fa fa-fw fa-close" id="del-filter" onClick='Remove(this)'></i>
</div>`;//Get inner html
$(document).ready(function(){
    $(".add-filter").click(function() {
        $(this).closest(".modal-body").append(new_filter);
        return false;
        });
masterAlert = [];

alertCriteria = [];

});

$(document).on('change', '#condition', function(){
        if($(this).find('option:selected').text()=='Any'){
          $(this).closest('div').find(".search_data").val("XXXnoval1234XXX");
          $(this).closest('div').find(".search_data").hide();
           
        }
        else{
          $(this).closest('div').find(".search_data").show();
          $(this).closest('div').find(".search_data").val('');
        }
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
        
        alertCriteria.push(item);
    });

    var filter = $('#filter').val();

    masterAlert.push({
        filter: filter,
        alertCriteria: alertCriteria
    });

    var jsonSearchString = JSON.stringify(masterAlert);

    $('#jsonSearchString').val(jsonSearchString);

    $("#formSearch").submit();

}

function modifyAlert(alertID)
{
  var alertID = alertID;

   $('div#editAlert' + alertID).each(function() {
        
      
        var criteria = $('#criteria' + alertID, this).val();
        var condition = $('#condition' + alertID, this).val();
        var search_data = $('#search_data' + alertID, this).val();

        item = {};

        item ["criteria"] = criteria;
        item ["condition"] = condition;
        item ["search_data"] = search_data;
        
        alertCriteria.push(item);
    });

    var filter = $('#filter' + alertID).val();

    var action = 'modify';

    masterAlert.push({
        filter: filter,
        alertCriteria: alertCriteria
    });

    var jsonSearchString = JSON.stringify(masterAlert);

    $('#jsonSearchString' + alertID).val(jsonSearchString);

    $("#formSearch" + alertID).submit();
}

function disbaleAlert(alertID)
{
  var alertID = alertID;

  var action = 'disable';

  $("#disableAlert" + alertID).submit();

}
</script> 

<script>
  $(function () {
    $('#example1').DataTable()
    $('#example2').DataTable({
      'paging'      : true,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : true,
      'autoWidth'   : true
    })
  })
</script>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Manage Notifications
        <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#modal-default">
        New Alert
        </button>
      </h1>
    </section>

    <!-- Main content -->
<section class="content">
      <div class="row">

        <div class="col-xs-12">
           <div class="modal fade in" id="modal-default" style="display: none;">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">Ã—</span></button>
                    <h4 class="modal-title">Create New Alert</h4>
                  </div>
                  <div class="modal-body">

                    <form id="formSearch" name="formSearch" action="manageAlerts.php" method="POST">

                      <input type="hidden" name="jsonSearchString" id="jsonSearchString" value="" />
                      <input type="hidden" name="action"" value="add" />

                      <div class="form-group">
                        <label for="exampleInputEmail1">Email address</label>
                        <input type="email" class="form-control" name="email_to" id="email_to" placeholder="Enter Email to Notify">
                      </div>

                      <div class="form-group">
                        <label for="exampleInputEmail1">Alert Name</label>
                        <input type="email" class="form-control" name="alert_name" id="alert_name" placeholder="Enter Alert Name">
                      </div>

                      <div class="form-group">
                        <label for="exampleInputEmail1">Description</label>
                        <input type="email" class="form-control" name="alert_desc" id="alert_desc" placeholder="Enter Decoy Description(Optional)">
                      </div>
  
                    </form>
                      <div class="margin">
                        <p>Match 
                          <select name="filter" id="filter">
                          <option value="all">All</option>
                          <option value="any">Any</option>
                          </select>
                        of the following
                      </div>
                      <div class="margin" id="advanceSearch">
                          <select name="criteria" id="criteria">
                            <option value="decoyName">Decoy Name</option>
                            <option value="decoyGroup">Network Location</option>
                            <option value="decoyIP">Decoy IP</option>
                            <option value="attackerIP">Attacker IP</option>
                            <option value="eventType">Event Type</option>
                            <option value="serviceType">Service Type</option>
                          </select>
                          <select name="condition" id="condition">
                            <option value="eq">Is equal to</option>
                            <option value="not_eq">Not equal to</option>
                            <option value="not_eq" id="any_select" class="any_select">Any</option>
                          </select>
                          
                          <input type="text"  style="width:200px; height: 30px" name="search_data" placeholder="Enter Here" id="search_data" class="search_data">
                          <i class="fa fa-fw fa-plus add-filter"></i>
                      </div>                     
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="advSearchSubmit" onclick="searchSubmit()">Add Alert</button>
                  </div>
                </div>
                <!-- /.modal-content -->
              </div>
              <!-- /.modal-dialog -->
           </div>
          
          <div class="col-xs-12">
          <div class="box">
            <div class="box-header">

            </div>
            <!-- /.box-header -->
                  <div class="box-body">
                    <table id="example1" class="table table-bordered table-striped">
                      <thead>
                      <tr>
                        <th>Alert Name</th>
                        <th>Description</th>
                        <th>Email Recipient</th>
                        <th>Modified Date</th>
                        <th>Modify</th>
                        <th>Delete</th>
                      </tr>
                      </thead>
                      <tbody>
                      <?php foreach ($event as $key => $value): ?>
                      <tr role="row" class="odd">
                        <td class=""><?= dataFilter($event[$key]['Alert_Name'])?></td>
                        <td class=""><?= dataFilter($event[$key]['Alert_Desc'])?></td>
                        <td class=""><?= dataFilter($event[$key]['Email_To'])?></td>
                        <td class=""><?= dataFilter($event[$key]['Updated_Date'])?></td>
                        <td class="" style="text-align: center;">
                        <?php $alert_info = $event[$key]['Alert_Info'];
                           $alert = json_decode($alert_info, true);
                            //print_r(array_values($alert));
                            //echo $alert[0]['filter'];
                           $alert_id = $event[$key]['id'];
                        ?>
                          <button type="button" data-toggle="modal" data-target="#modal-default<?= $alert_id?>" >   <span class="glyphicon glyphicon-edit"></span>
                          </button>
                        </td>
                        <div class="modal fade in" id="modal-default<?= $alert_id?>" style="display: none;">
                              <div class="modal-dialog">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                      <span aria-hidden="true">Ã—</span></button>
                                    <h4 class="modal-title">Modify Alert</h4>
                                  </div>
                                  <div class="modal-body">

                                    <form id="formSearch<?= $alert_id?>" name="formSearch" action="manageAlerts.php" method="POST">

                                      <input type="hidden" name="jsonSearchString" id="jsonSearchString<?= $alert_id?>" value="" />

                                      <input type="hidden" name="alert_id"" value="<?= $alert_id?>" />
                                      <input type="hidden" name="action"" value="modify" />

                                      <div class="form-group">
                                        <label for="exampleInputEmail1">Email address</label>
                                        <input type="email" class="form-control" name="email_to" id="email_to" value="<?= dataFilter($event[$key]['Email_To'])?>">
                                      </div>

                                      <div class="form-group">
                                        <label for="exampleInputEmail1">Alert Name</label>
                                        <input type="email" class="form-control" name="alert_name" id="alert_name" value="<?= dataFilter($event[$key]['Alert_Name'])?>">
                                      </div>

                                      <div class="form-group">
                                        <label for="exampleInputEmail1">Description</label>
                                        <input type="email" class="form-control" name="alert_desc" id="alert_desc" value="<?= dataFilter($event[$key]['Alert_Desc'])?>">
                                      </div>
                  
                                    </form>
                                      <div class="margin">
                                        <p>Match 
                                          <select name="filter" id="filter<?= $alert_id?>">
                                          <option value="all" <?php if ($alert[0]['filter'] == 'all') { echo ' selected="selected"'; } ?> >All
                                          </option>
                                          <option value="any" <?php if ($alert[0]['filter'] == 'any') { echo ' selected="selected"'; } ?> >Any</option>
                                          </select>
                                        of the following
                                      </div>
                                    <?php foreach ($alert[0]['alertCriteria'] as $alertCriteria): ?>
                                      <div class="margin" id="editAlert<?= $alert_id?>">
                                          <select name="criteria" id="criteria<?= $alert_id?>">
                                            <option value="decoyName" <?php if ($alertCriteria['criteria'] == 'decoyName') { echo ' selected="selected"'; } ?>>Decoy Name
                                            </option>
                                            <option value="decoyGroup" <?php if ($alertCriteria['criteria'] == 'decoyGroup') { echo ' selected="selected"'; } ?> >Network Location
                                            </option>
                                            <option value="decoyIP" <?php if ($alertCriteria['criteria'] == 'decoyIP') { echo ' selected="selected"'; } ?> >Decoy IP
                                            </option>
                                            <option value="attackerIP" <?php if ($alertCriteria['criteria'] == 'attackerIP') { echo ' selected="selected"'; } ?> >Attacker IP
                                            </option>
                                            <option value="eventType" <?php if ($alertCriteria['criteria'] == 'eventType') { echo ' selected="selected"'; } ?> >Event Type
                                            </option>
<option value="serviceType" <?php if ($alertCriteria['criteria'] == 'serviceType') { echo ' selected="selected"'; } ?> >Service Type
                                            </option>
                                          </select>

                                          <select name="condition" id="condition<?= $alert_id?>">
                                            <option value="eq" <?php if ($alertCriteria['condition'] == 'eq') { echo ' selected="selected"'; } ?> >Is equal to</option>
                                            <option value="not_eq" <?php if ($alertCriteria['condition'] == 'not_eq' and $alertCriteria['search_data']!='XXXnoval1234XXX') { echo ' selected="selected"'; } ?> >Not equal to</option>
                                            <option value="not_eq" <?php if ($alertCriteria['condition'] == 'not_eq' and $alertCriteria['search_data']=='XXXnoval1234XXX') { echo ' selected="selected"'; } ?> >Any</option>
                                          </select>
                                          <input type="text"  style="width:200px; height: 30px" name="search_data" value="<?= $alertCriteria['search_data']?>" id="search_data<?= $alert_id?>" <?php if ($alertCriteria['condition'] == 'not_eq' and $alertCriteria['search_data']=='XXXnoval1234XXX') { echo 'hidden'; } ?>>
                                          <i class="fa fa-fw fa-plus add-filter"></i>
                                      </div>
                                    <?php endforeach;?>                    
                                  </div>
                                  <div class="modal-footer">
                                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary" id="advSearchSubmit" onclick="modifyAlert(<?= $alert_id?>)">Modify Alert</button>
                                  </div>
                                </div>
                                <!-- /.modal-content -->
                              </div>
                              <!-- /.modal-dialog -->
                        </div>

                        <form id="disableAlert<?= $alert_id?>" action="manageAlerts.php" method="POST">

                                      <input type="hidden" name="alert_id"" value="<?= $alert_id?>" />
                                      <input type="hidden" name="action"" value="disable" />
                        </form>
                        <td class="" style="text-align: center;"><span class="glyphicon glyphicon-remove" onclick="disbaleAlert(<?= $alert_id?>)"></span></td>
                      </tr>
                      <?php endforeach;?>
                      </tbody>
                    </table>
                  </div>
              </div>
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
<?php include 'template/main-footer.php';?>

</body>
<?php
}
else 
{
  header('location:loginView.php');
}
?>

