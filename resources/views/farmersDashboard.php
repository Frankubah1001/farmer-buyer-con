<?php
include 'session.php';
include 'DBcon.php';
?>
<?php include 'header.php'; ?>

<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">
    <!-- Main Content -->
    <div id="content">
        <?php include 'topbar.php'; ?>
        
        <!-- Begin Page Content -->
        <div class="container-fluid">
            <!-- Your main content here (cards, charts, tables) -->
                 <!-- Content Row -->
<div class="row">
<!-- Earnings (Monthly) Card Example -->
<div class="col-xl-3 col-md-6 mb-4">
    <div class="card border-left-primary shadow h-100 py-2">
        <div class="card-body">
            <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                        Total Earnings (Monthly)</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800"> ₦40,000</div>
                </div>
                <div class="col-auto">
                    <i class="fas fa-calendar fa-2x text-gray-300"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Earnings (Monthly) Card Example -->
<div class="col-xl-3 col-md-6 mb-4">
    <div class="card border-left-success shadow h-100 py-2">
        <div class="card-body">
            <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                        Total Earnings (Annual)</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800"> ₦215,000</div>
                </div>
                <div class="col-auto">
                    <i class="fas fa-shapes fa-2x text-gray-300"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Earnings (Monthly) Card Example -->
<div class="col-xl-3 col-md-6 mb-4">
    <div class="card border-left-info shadow h-100 py-2">
        <div class="card-body">
            <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Frequently Sold Farm Produce
                    </div>
                    <div class="row no-gutters align-items-center">
                        <div class="col-auto">
                            <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">Cassava</div>
                        </div>
                       
                    </div>
                </div>
                <div class="col-auto">
                    <i class="fas fa-pepper-hot fa-2x text-gray-300"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pending Requests Card Example -->
<div class="col-xl-3 col-md-6 mb-4">
    <div class="card border-left-dark shadow h-100 py-2">
        <div class="card-body">
            <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                        Total Orders</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">18</div>
                </div>
                <div class="col-auto">
                    <i class="fas fa-atom fa-2x text-gray-300"></i>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<!-- Second Row -->
<div class="row">

<!-- Earnings (Monthly) Card Example -->
<div class="col-xl-3 col-md-6 mb-4">
    <div class="card border-left-secondary shadow h-100 py-2">
        <div class="card-body">
            <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                        Total Number Of Farm Products </div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">3</div>
                </div>
                <div class="col-auto">
                    <i class="fas fa-seedling fa-2x text-gray-300"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Earnings (Monthly) Card Example -->
<div class="col-xl-3 col-md-6 mb-4">
    <div class="card border-left-warning shadow h-100 py-2">
        <div class="card-body">
            <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                        Number Of Orders (Monthly)</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">10</div>
                </div>
                <div class="col-auto">
                    <i class="fas fa-poop fa-2x text-gray-300"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-xl-3 col-md-6 mb-4">
    <div class="card border-left-danger shadow h-100 py-2">
        <div class="card-body">
            <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                        Total Orders (Yearly)</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">15</div>
                </div>
                <div class="col-auto">
                    <i class="fas fa-atom fa-2x text-gray-300"></i>
                </div>
            </div>
        </div>
    </div>
</div>
</div>


<!-- Content Row -->

<div class="row">
<div class="card-body">
    <div class="card shadow">
        <!-- Card Header - Dropdown -->
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Earnings Overview</h6>
        </div>
        <div class="card-body">
            <div class="chart-area">
                <div class="chartjs-size-monitor">
                    <div class="chartjs-size-monitor-expand">
                        <div class="">
                        </div>
                    </div>
                    <div class="chartjs-size-monitor-shrink"><div class="">
                    </div>
                </div>
            </div>
                <canvas id="myAreaChart" width="862" height="400" style="display: block; height: 320px; width: 690px;" class="chartjs-render-monitor">
                </canvas>
            </div>
        </div>
    </div>
</div>



<div class="card-body">
<div class="card shadow mb-4">
<div class="card-header py-3">
    <h6 class="m-0 font-weight-bold text-primary">Most Recent Transactions</h6>
</div>
<div class="card-body">
    <div class="table-responsive">
        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th>Name Of Buyer</th>
                    <th>Location</th>
                    <th>Produce Purchased</th>
                    <th>Quantity</th>
                    <th>Amount</th>
                    <th>Date Of Transaction</th>
                </tr>
            </thead>
            
            <tbody>
                
                <tr>
                    <td>Haley Kennedy</td>
                    <td>Lagos</td>
                    <td>Maize</td>
                    <td>20 Tons</td>
                    <td>1,000,000</td>
                    <td>2010/03/17</td>
                </tr>
                <tr>
                    <td>Tomiwa Amole</td>
                    <td>Ibadan</td>
                    <td>Cassava</td>
                    <td>10 Tons</td>
                    <td>2,000,000</td>
                    <td>2010/03/17</td>
                </tr>
               
            </tbody>
        </table>
    </div>
</div>
</div>
</div>
  

</div>
</div>
        </div>
    

    <?php include 'footer.php'; ?>
</div>

<?php include 'script.php'; ?>