<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Management - Pathology Lab</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/modern-sidebar.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar">
                <div class="sidebar-header">
                    <h4><i class="fas fa-microscope"></i> Lab System</h4>
                </div>
                
                <div class="sidebar-section">
                    <div class="section-title">MAIN</div>
                    <a href="../admin/simple_dashboard.php" class="sidebar-item">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </div>
                
                <div class="sidebar-section">
                    <div class="section-title">OPERATIONS</div>
                    <a href="../invoices/list.php" class="sidebar-item">
                        <i class="fas fa-file-invoice"></i>
                        <span>Invoices</span>
                    </a>
                    <a href="list.php" class="sidebar-item active">
                        <i class="fas fa-vial"></i>
                        <span>Tests</span>
                    </a>
                    <a href="../reports/list.php" class="sidebar-item">
                        <i class="fas fa-chart-bar"></i>
                        <span>Reports</span>
                    </a>
                </div>
                
                <div class="sidebar-section">
                    <div class="section-title">MANAGEMENT</div>
                    <a href="../doctors/list.php" class="sidebar-item">
                        <i class="fas fa-user-md"></i>
                        <span>Doctors</span>
                    </a>
                    <a href="../users/list.php" class="sidebar-item">
                        <i class="fas fa-users"></i>
                        <span>Users</span>
                    </a>
                    <a href="../consumables/list.php" class="sidebar-item">
                        <i class="fas fa-boxes"></i>
                        <span>Inventory</span>
                    </a>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="content-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2><i class="fas fa-vial text-primary"></i> Test Management</h2>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTestModal">
                            <i class="fas fa-plus"></i> New Test
                        </button>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6>Total Tests</h6>
                                        <h3 id="totalTests">0</h3>
                                    </div>
                                    <i class="fas fa-vial fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6>Hematology</h6>
                                        <h3 id="hematologyTests">0</h3>
                                    </div>
                                    <i class="fas fa-tint fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6>Biochemistry</h6>
                                        <h3 id="biochemistryTests">0</h3>
                                    </div>
                                    <i class="fas fa-flask fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6>Avg Price</h6>
                                        <h3 id="avgPrice">৳0</h3>
                                    </div>
                                    <i class="fas fa-coins fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">Category</label>
                                <select class="form-select" id="categoryFilter">
                                    <option value="">All Categories</option>
                                    <option value="Hematology">Hematology</option>
                                    <option value="Biochemistry">Biochemistry</option>
                                    <option value="Microbiology">Microbiology</option>
                                    <option value="Immunology">Immunology</option>
                                    <option value="Hormones">Hormones</option>
                                    <option value="Urine">Urine Analysis</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Sample Type</label>
                                <select class="form-select" id="sampleFilter">
                                    <option value="">All Sample Types</option>
                                    <option value="Blood">Blood</option>
                                    <option value="Urine">Urine</option>
                                    <option value="Stool">Stool</option>
                                    <option value="Serum">Serum</option>
                                    <option value="Plasma">Plasma</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Price Range</label>
                                <select class="form-select" id="priceFilter">
                                    <option value="">All Prices</option>
                                    <option value="0-500">৳0 - ৳500</option>
                                    <option value="500-1000">৳500 - ৳1000</option>
                                    <option value="1000-2000">৳1000 - ৳2000</option>
                                    <option value="2000+">৳2000+</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Search</label>
                                <input type="text" class="form-control" id="searchInput" placeholder="Test name or code">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <button class="btn btn-primary" onclick="filterTests()">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                                <button class="btn btn-secondary" onclick="clearFilters()">
                                    <i class="fas fa-times"></i> Clear
                                </button>
                                <button class="btn btn-success" onclick="exportTests()">
                                    <i class="fas fa-download"></i> Export
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tests Table -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="testsTable">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Test Name</th>
                                        <th>Category</th>
                                        <th>Sample Type</th>
                                        <th>Price</th>
                                        <th>Parameters</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="testsTableBody">
                                    <!-- Dynamic content -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Test Modal -->
    <div class="modal fade" id="addTestModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">New Test</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addTestForm">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label">Test Code *</label>
                                <input type="text" class="form-control" name="code" placeholder="e.g., CBC001" required>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Test Name *</label>
                                <input type="text" class="form-control" name="name" placeholder="e.g., Complete Blood Count" required>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <label class="form-label">Category *</label>
                                <select class="form-select" name="category" required>
                                    <option value="">Select Category</option>
                                    <option value="Hematology">Hematology</option>
                                    <option value="Biochemistry">Biochemistry</option>
                                    <option value="Microbiology">Microbiology</option>
                                    <option value="Immunology">Immunology</option>
                                    <option value="Hormones">Hormones</option>
                                    <option value="Urine">Urine Analysis</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Sample Type *</label>
                                <select class="form-select" name="sample_type" required>
                                    <option value="">Select Sample</option>
                                    <option value="Blood">Blood</option>
                                    <option value="Urine">Urine</option>
                                    <option value="Stool">Stool</option>
                                    <option value="Serum">Serum</option>
                                    <option value="Plasma">Plasma</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Price (৳) *</label>
                                <input type="number" class="form-control" name="price" step="0.01" required>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label class="form-label">Method</label>
                                <input type="text" class="form-control" name="method" placeholder="e.g., Automated Analyzer">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">TAT (Hours)</label>
                                <input type="number" class="form-control" name="tat_hours" placeholder="Turnaround time">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" name="description" rows="2" placeholder="Test description and clinical significance"></textarea>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        <h6>Test Parameters</h6>
                        <div id="parametersContainer">
                            <!-- Dynamic parameters -->
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addParameterRow()">
                            <i class="fas fa-plus"></i> Add Parameter
                        </button>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Test</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/test-management.js"></script>
</body>
</html>
