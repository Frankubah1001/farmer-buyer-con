<?php
// head.php - Common head section with styles and external links
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgriAdmin - Agricultural Platform Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary-green: #4CAF50;
            --dark-green: #388E3C;
            --light-green: #C8E6C9;
            --primary-brown: #795548;
            --light-brown: #D7CCC8;
            --accent-blue: #2196F3;
            --text-dark: #212121;
            --text-light: #757575;
            --background: #F5F5F5;
            --header-height: 70px;
            --footer-height: 60px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--background);
            color: var(--text-dark);
            overflow-x: hidden;
            display: grid;
            grid-template-areas: 
                "sidebar header"
                "sidebar main"
                "sidebar footer";
            grid-template-columns: 250px 1fr;
            grid-template-rows: var(--header-height) 1fr var(--footer-height);
            min-height: 100vh;
        }

        /* Header Styles */
        .header {
            grid-area: header;
            background-color: white;
            padding: 0 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 100;
        }

        .toggle-btn {
            background: none;
            border: none;
            font-size: 1.2rem;
            color: var(--text-dark);
            cursor: pointer;
        }

        .user-info {
            display: flex;
            align-items: center;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--light-brown);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            color: var(--primary-brown);
            font-weight: bold;
        }

        /* Sidebar Styles */
        .sidebar {
            grid-area: sidebar;
            background: linear-gradient(to bottom, var(--primary-green), var(--dark-green));
            color: white;
            transition: all 0.3s;
            box-shadow: 3px 0 10px rgba(0, 0, 0, 0.1);
            overflow-y: auto;
        }

        .sidebar.collapsed {
            width: 70px;
        }

        .sidebar.collapsed .sidebar-text {
            display: none;
        }

        .sidebar-header {
            padding: 20px;
            background-color: rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: var(--header-height);
        }

        .sidebar-header h3 {
            margin: 0;
            font-size: 1.5rem;
        }

        .sidebar-menu {
            padding: 0;
            list-style: none;
            margin-top: 20px;
        }

        .sidebar-menu li {
            padding: 0;
        }

        .sidebar-menu a {
            color: white;
            text-decoration: none;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            transition: all 0.3s;
            border-left: 4px solid transparent;
        }

        .sidebar-menu a:hover, .sidebar-menu a.active {
            background-color: rgba(255, 255, 255, 0.1);
            border-left: 4px solid var(--accent-blue);
        }

        .sidebar-menu i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        .sidebar.collapsed .sidebar-menu i {
            margin-right: 0;
        }

        /* Main Content Styles */
        .main-content {
            grid-area: main;
            padding: 20px;
            overflow-y: auto;
        }

        /* Footer Styles */
        .footer {
            grid-area: footer;
            background-color: white;
            padding: 0 20px;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: var(--text-light);
            font-size: 0.9rem;
        }

        .footer-links a {
            color: var(--text-light);
            text-decoration: none;
            margin-left: 15px;
            transition: color 0.3s;
        }

        .footer-links a:hover {
            color: var(--primary-green);
        }

        /* Cards Styles */
        .summary-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s;
            margin-bottom: 20px;
            border-top: 4px solid var(--primary-green);
        }

        .summary-card:hover {
            transform: translateY(-5px);
        }

        .summary-card i {
            font-size: 2rem;
            color: var(--primary-green);
            margin-bottom: 10px;
        }

        .summary-card h3 {
            font-size: 1.8rem;
            margin: 10px 0;
            color: var(--text-dark);
        }

        .summary-card p {
            color: var(--text-light);
            margin: 0;
        }

        /* Chart Containers */
        .chart-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        /* Table Styles */
        .data-table-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .table-actions {
            display: flex;
            gap: 10px;
        }

        .btn-agri {
            background-color: var(--primary-green);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            transition: all 0.3s;
        }

        .btn-agri:hover {
            background-color: var(--dark-green);
            color: white;
        }

        .btn-agri-blue {
            background-color: var(--accent-blue);
            color: white;
        }

        .btn-agri-blue:hover {
            background-color: #0b7dda;
        }

        .table th {
            background-color: var(--light-green);
            color: var(--text-dark);
        }

        /* Modal Styles */
        .modal-header {
            background-color: var(--primary-green);
            color: white;
        }

        .modal-footer {
            background-color: var(--background);
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            body {
                grid-template-areas: 
                    "header header"
                    "main main"
                    "footer footer";
                grid-template-columns: 1fr;
                grid-template-rows: var(--header-height) 1fr var(--footer-height);
            }
            
            .sidebar {
                position: fixed;
                top: 0;
                left: -250px;
                height: 100vh;
                z-index: 1000;
                width: 250px;
                transition: left 0.3s;
            }
            
            .sidebar.active {
                left: 0;
            }
            
            .sidebar-header h3 {
                display: block;
            }
            
            .main-content {
                margin-left: 0;
            }
        }

        /* Status badges */
        .badge-approved {
            background-color: var(--primary-green);
            color: white;
        }

        .badge-pending {
            background-color: #FFC107;
            color: black;
        }

        .badge-disabled {
            background-color: #F44336;
            color: white;
        }
        .badge-blue {
            background-color: #0052cc; /* A custom blue shade (darker than Bootstrap's primary) */
            color: #fff; /* White text for contrast */
            padding: 0.35em 0.65em;
            font-size: 0.75em;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.25rem;
        }

        /* Action buttons in tables */
        .action-buttons {
            display: flex;
            gap: 5px;
        }

        .action-btn {
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.8rem;
        }

        .btn-edit {
            background-color: #FFC107;
            color: black;
        }

        .btn-approve {
            background-color: var(--primary-green);
            color: white;
        }

        .btn-disable {
            background-color: #F44336;
            color: white;
        }

        .btn-export {
            background-color: var(--accent-blue);
            color: white;
        }
    </style>
</head>