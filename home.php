<?php include ('db_connect.php') ?>
<?php
$twhere = "";
if ($_SESSION['login_type'] != 1)
  $twhere = "  ";
?>
<!-- Info boxes -->
<div class="container mt-4">
  <div class="row">
    <div class="col-12">
      <div class="alert alert-success">
        Chào mừng <?php echo $_SESSION['login_name'] ?>!
      </div>
    </div>
  </div>
  <hr>
  <?php

  $where = "";
  if ($_SESSION['login_type'] == 2) {
    $where = " where manager_id = '{$_SESSION['login_id']}' ";
  } elseif ($_SESSION['login_type'] == 3) {
    $where = " where concat('[',REPLACE(user_ids,',','],['),']') LIKE '%[{$_SESSION['login_id']}]%' ";
  }
  $where2 = "";
  if ($_SESSION['login_type'] == 2) {
    $where2 = " where p.manager_id = '{$_SESSION['login_id']}' ";
  } elseif ($_SESSION['login_type'] == 3) {
    $where2 = " where concat('[',REPLACE(p.user_ids,',','],['),']') LIKE '%[{$_SESSION['login_id']}]%' ";
  }
  ?>

  <div class="row">
    <div class="col-md-8">
      <div class="card border-success">
        <div class="card-header bg-success text-white">
          <b>Tiến độ dự án </b>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-striped table-hover">
              <colgroup>
                <col width="5%">
                <col width="30%">
                <col width="35%">
                <col width="15%">
                <col width="15%">
              </colgroup>
              <thead class="thead-dark">
                <tr>
                  <th>#</th>
                  <th>Dự án của bạn</th>
                  <th>Tiến độ</th>
                  <th>trạng thái</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <?php
                $i = 1;
                $stat = array("Pending", "Started", "On-Progress", "On-Hold", "Over Due", "Done");
                $qry = $conn->query("SELECT * FROM project_list $where order by name asc");
                while ($row = $qry->fetch_assoc()):
                  $prog = 0;
                  $tprog = $conn->query("SELECT * FROM task_list where project_id = {$row['id']}")->num_rows;
                  $cprog = $conn->query("SELECT * FROM task_list where project_id = {$row['id']} and status = 3")->num_rows;
                  $prog = $tprog > 0 ? ($cprog / $tprog) * 100 : 0;
                  $prog = $prog > 0 ? number_format($prog, 2) : $prog;
                  $prod = $conn->query("SELECT * FROM user_productivity where project_id = {$row['id']}")->num_rows;
                  if ($row['status'] == 0 && strtotime(date('Y-m-d')) >= strtotime($row['start_date'])):
                    if ($prod > 0 || $cprog > 0)
                      $row['status'] = 2;
                    else
                      $row['status'] = 1;
                  elseif ($row['status'] == 0 && strtotime(date('Y-m-d')) > strtotime($row['end_date'])):
                    $row['status'] = 4;
                  endif;
                  ?>
                  <tr>
                    <td><?php echo $i++ ?></td>
                    <td>
                      <a><?php echo ucwords($row['name']) ?></a>
                      <br>
                      <small>ngày: <?php echo date("Y-m-d", strtotime($row['end_date'])) ?></small>
                    </td>
                    <td>
                      <div class="progress">
                        <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $prog ?>%;"
                          aria-valuenow="<?php echo $prog ?>" aria-valuemin="0" aria-valuemax="100"></div>
                      </div>
                      <small><?php echo $prog ?>% Hoàn thiện</small>
                    </td>
                    <td>
                      <?php
                      $status_class = array("badge-secondary", "badge-primary", "badge-info", "badge-warning", "badge-danger", "badge-success");
                      echo "<span class='badge {$status_class[$row['status']]}'>{$stat[$row['status']]}</span>";
                      ?>
                    </td>
                    <td>
                      <a class="btn btn-primary btn-sm" href="./index.php?page=view_project&id=<?php echo $row['id'] ?>">
                        <i class="fas fa-folder"></i> Xem
                      </a>
                    </td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row mt-4">
    <div class="col-md-6">
      <div class="card text-white bg-light mb-3">
        <div class="card-header">Tất cả dự án </div>
        <div class="card-body">
          <h5 class="card-title"><?php echo $conn->query("SELECT * FROM project_list $where")->num_rows; ?></h5>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="ccard text-white bg-light mb-3">
        <div class="card-header">Tất cả nhiệm vụ </div>
        <div class="card-body">
          <h5 class="card-title">
            <?php echo $conn->query("SELECT t.*,p.name as pname,p.start_date,p.status as pstatus, p.end_date,p.id as pid FROM task_list t inner join project_list p on p.id = t.project_id $where2")->num_rows; ?>
          </h5>
        </div>
      </div>
    </div>
  </div>
</div>