-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: mysql5027.site4now.net
-- Generation Time: Oct 14, 2025 at 09:31 AM
-- Server version: 5.6.26
-- PHP Version: 8.3.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_9b86be_arpusof`
--

-- --------------------------------------------------------

--
-- Table structure for table `delivery_notes`
--

CREATE TABLE `delivery_notes` (
  `id` int(11) NOT NULL,
  `print_date` datetime DEFAULT NULL,
  `purpose_of_delivery` text,
  `delivery_address` text,
  `site_Code` varchar(255) DEFAULT NULL,
  `contract_info` varchar(255) DEFAULT NULL,
  `Customer` varchar(255) DEFAULT NULL,
  `Customer_PO` varchar(255) DEFAULT NULL,
  `Customer_Tel` varchar(255) DEFAULT NULL,
  `Project_Name` varchar(255) DEFAULT NULL,
  `Project_Code` varchar(255) DEFAULT NULL,
  `Product_Category` varchar(255) DEFAULT NULL,
  `Product_Manager` varchar(255) DEFAULT NULL,
  `Special_Unloading_Req` varchar(255) DEFAULT NULL,
  `Installation_Environment` varchar(255) DEFAULT NULL,
  `Description_MR` varchar(255) DEFAULT NULL,
  `From_Warehouse` varchar(255) DEFAULT NULL,
  `Warehouse_Keeper` varchar(255) DEFAULT NULL,
  `Warehouse_Keeper_tel` varchar(50) DEFAULT NULL,
  `Description_DN` varchar(255) DEFAULT NULL,
  `Including_dangerous_goods` varchar(255) DEFAULT NULL,
  `pickup_address` text,
  `Site_Address` varchar(255) DEFAULT NULL,
  `dn_no` varchar(50) DEFAULT NULL,
  `mr_no` varchar(50) DEFAULT NULL,
  `receiver_name` varchar(255) DEFAULT NULL,
  `receiver_tel` varchar(50) DEFAULT NULL,
  `Receiver_Company_Name` varchar(255) DEFAULT NULL,
  `request_arrived_date` datetime DEFAULT NULL,
  `request_shipment_date` datetime DEFAULT NULL,
  `logistics_specialist` varchar(255) DEFAULT NULL,
  `logistics_specialist_Tel` varchar(255) DEFAULT NULL,
  `received_location` varchar(255) DEFAULT NULL,
  `received_Auto_location` varchar(255) DEFAULT NULL,
  `Collected_location` varchar(255) DEFAULT NULL,
  `Collected_Auto_location` varchar(255) DEFAULT NULL,
  `DN_Status` enum('Created','Received','Delivered','Collected','Closed') DEFAULT 'Created',
  `Company_code` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Project_code_User` varchar(50) DEFAULT NULL,
   
  `Batch_no_user` varchar(50) DEFAULT NULL,
  `Receive_Rep` varchar(100) DEFAULT NULL,
  `Delivery_Rep` varchar(100) DEFAULT NULL,
  `Collect_Rep` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `delivery_notes`
--


--
-- Table structure for table `dn_items`
--

CREATE TABLE `dn_items` (
  `id` int(11) NOT NULL,
  `dn_no` varchar(50) NOT NULL,
  `site_code` varchar(255) DEFAULT NULL,
  `item_code` varchar(50) NOT NULL,
  `qty` decimal(12,2) NOT NULL,
  `item_description` text,
  `Item_received_Image` varchar(255) DEFAULT NULL,
  `Item_Delivered_Image` varchar(255) DEFAULT NULL,
  `Item_Collected_Image` varchar(255) DEFAULT NULL,
  `Item_User_Created` varchar(255) DEFAULT NULL,
  `Item_User_Update` varchar(255) DEFAULT NULL,
  `Company_code` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `item_status` enum('Created','Received','Delivered','Collected') DEFAULT 'Created'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `dn_items`
--



-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `employee_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `hire_date` date NOT NULL,
  `job_title` varchar(100) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `salary` decimal(10,2) DEFAULT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `App_user` varchar(50) NOT NULL,
  `app_Password` varchar(50) NOT NULL,
  `App_User_Status` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `employees`
--

-- --------------------------------------------------------

--
-- Table structure for table `items_info`
--

CREATE TABLE `items_info` (
  `id` int(11) NOT NULL,
  `item_code` varchar(50) DEFAULT NULL,
  `item_description` text,
  `item_Status` enum('Active','Not Active') DEFAULT 'Active',
  `Company_code` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `items_info`
--


-- --------------------------------------------------------

--
-- Table structure for table `project_info`
--

CREATE TABLE `project_info` (
  `id` int(11) NOT NULL,
  `Project_code_User` varchar(50) NOT NULL,
  `Batch_no_user` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `project_status` enum('active','not active') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `project_info`
--



--
-- Table structure for table `site_details`
--

CREATE TABLE `site_details` (
  `id` int(11) NOT NULL,
  `site_Code` varchar(20) DEFAULT NULL,
  `Site_Name` varchar(100) NOT NULL,
  `Tel_Operator` varchar(50) NOT NULL,
  `Address` varchar(255) DEFAULT NULL,
  `GPS_Latitude` decimal(10,6) DEFAULT NULL,
  `GPS_Longitude` decimal(10,6) DEFAULT NULL,
  `Site_Type` enum('Macro','Rooftop','Indoor','Small Cell') NOT NULL,
  `Access_Instructions` text,
  `Company_code` varchar(50) DEFAULT NULL,
  `Created_At` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Updated_At` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `site_details`
--


-- Table structure for table `trucks_info`
--

CREATE TABLE `trucks_info` (
  `truck_id` int(11) NOT NULL,
  `truck_no` varchar(50) NOT NULL,
  `driver_name` varchar(255) NOT NULL,
  `driver_phone` varchar(50) DEFAULT NULL,
  `capacity` decimal(10,2) DEFAULT NULL,
  `status` enum('Available','In Transit','Maintenance','Unavailable') DEFAULT 'Available',
  `Company_code` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) 

CREATE TABLE `truck_assignments` (
  `id` int(11) NOT NULL,
  `assignment_id` int(11) NOT NULL,
  `site_Code` varchar(20) NOT NULL,
  `dn_no` varchar(50) NOT NULL,
  `truck_id` int(11) NOT NULL,
  `assigned_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `status` enum('Assigned','In Transit','Delivered','Cancelled') DEFAULT 'Assigned',
  `Company_code` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `truck_assignments`
--


-- Table structure for table `truck_assignment_accounts`
--

CREATE TABLE `truck_assignment_accounts` (
  `id` int(11) NOT NULL,
  `assignment_id` int(11) NOT NULL,
  `cost` decimal(12,2) NOT NULL,
  `advance_payment` decimal(12,2) DEFAULT '0.00',
  `Gove_Fees` decimal(12,2) DEFAULT '0.00',
  `balance` decimal(12,2) DEFAULT NULL,
  `payment_status` enum('Unpaid','Partial','Paid') DEFAULT 'Unpaid',
  `payment_date` datetime DEFAULT NULL,
  `remarks` text,
  `Company_code` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `truck_assignment_accounts`

ALTER TABLE `delivery_notes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dn_items`
--
ALTER TABLE `dn_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`employee_id`),
  ADD UNIQUE KEY `email` (`email`);
ALTER TABLE `employees` ADD FULLTEXT KEY `first_name` (`first_name`);

--
-- Indexes for table `items_info`
--
ALTER TABLE `items_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `project_info`
--
ALTER TABLE `project_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `site_details`
--
ALTER TABLE `site_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `trucks_info`
--
ALTER TABLE `trucks_info`
  ADD PRIMARY KEY (`truck_id`);

--
-- Indexes for table `truck_assignments`
--
ALTER TABLE `truck_assignments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `truck_assignment_accounts`
--
ALTER TABLE `truck_assignment_accounts`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `delivery_notes`
--
ALTER TABLE `delivery_notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- AUTO_INCREMENT for table `dn_items`
--
ALTER TABLE `dn_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=622;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `employee_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `items_info`
--
ALTER TABLE `items_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `project_info`
--
ALTER TABLE `project_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `site_details`
--
ALTER TABLE `site_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `trucks_info`
--
ALTER TABLE `trucks_info`
  MODIFY `truck_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `truck_assignments`
--
ALTER TABLE `truck_assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `truck_assignment_accounts`
--
ALTER TABLE `truck_assignment_accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
