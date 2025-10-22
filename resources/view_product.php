<?php
include 'DBcon.php';
require_once 'auth_check.php';
?>
<?php include 'header.php'; ?>

<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        <?php include 'topbar.php'; ?>

        <div class="container-fluid">
            <style>
                /* Custom styles for Sold/Available buttons */
                .btn-available {
                    background-color: #28a745 !important;
                    color: white !important;
                    border-color: #28a745 !important;
                }
                .btn-available:hover {
                    background-color: #218838 !important;
                    border-color: #1e7e34 !important;
                }
                .btn-sold {
                    background-color: #6c757d !important;
                    color: white !important;
                    border-color: #6c757d !important;
                    cursor: not-allowed !important;
                }
                .btn-sold:hover {
                    background-color: #545b62 !important;
                    border-color: #4e555b !important;
                }
                .btn-delete {
                    background-color: #dc3545 !important;
                    color: white !important;
                    border-color: #dc3545 !important;
                }
                .btn-delete:hover {
                    background-color: #c82333 !important;
                    border-color: #bd2130 !important;
                }
                .btn-view {
                    background-color: #007bff !important;
                    color: white !important;
                    border-color: #007bff !important;
                }
                .btn-view:hover {
                    background-color: #0056b3 !important;
                    border-color: #004085 !important;
                }
            </style>

            <h1 class="h3 mb-2 text-gray-800">Your Produce Listings</h1>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Filter & View Your Produce</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-5">
                            <label for="filter_produce">Produce:</label>
                            <select class="form-control" id="filter_produce">
                                <option value="">All</option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label for="filter_condition">Condition:</label>
                            <select class="form-control" id="filter_condition">
                                <option value="">All</option>
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered" id="produceTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Produce</th>
                                    <th>Farmer Quantity</th>
                                    <th>Quantity Ordered</th>
                                    <th>Remaining</th>
                                    <th>Price</th>
                                    <th>Condition</th>
                                    <th>Available Date</th>
                                    <th>Address</th>
                                    <th>Image</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center" id="pagination" style="display: none;">
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</div>

<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Produce Image</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <img src="" id="modalImage" class="img-fluid" alt="Produce Image">
            </div>
        </div>
    </div>
</div>

<?php include 'script.php'; ?>

<script src="user_produce.js"></script>

<script>
    // JavaScript to fetch unique produce and conditions for filter dropdowns
    document.addEventListener('DOMContentLoaded', function() {
        loadProduceData(1); // Load data on page load, and this function handles pagination
        loadUniqueFilters();

        document.getElementById('filter_produce').addEventListener('change', function() {
            loadProduceData(1); // Go to first page on filter change
        });

        document.getElementById('filter_availability').addEventListener('change', function() {
            loadProduceData(1);
        });

        document.getElementById('filter_condition').addEventListener('change', function() {
            loadProduceData(1);
        });
    });
</script>