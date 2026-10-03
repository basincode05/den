SET SESSION sql_require_primary_key = 0;
-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 03, 2026 at 11:49 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dentq_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `Admin_ID` int(11) NOT NULL,
  `Username` varchar(50) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`Admin_ID`, `Username`, `Password`, `Name`) VALUES
(1, 'admin', '123456', 'ผู้ดูแลระบบ');

-- --------------------------------------------------------

--
-- Table structure for table `appointment`
--

CREATE TABLE `appointment` (
  `Appointment_ID` int(11) NOT NULL,
  `Patient_ID` int(11) DEFAULT NULL,
  `Dentist_ID` int(11) DEFAULT NULL,
  `Service_ID` int(11) DEFAULT NULL,
  `Appt_Date` date DEFAULT NULL,
  `Time_Slot` time DEFAULT NULL,
  `Status` varchar(20) DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointment`
--

INSERT INTO `appointment` (`Appointment_ID`, `Patient_ID`, `Dentist_ID`, `Service_ID`, `Appt_Date`, `Time_Slot`, `Status`) VALUES
(1, 1, 2, 1, '2026-09-28', '09:00:00', 'Done'),
(2, 1, 2, 1, '2026-09-30', '09:30:00', 'Pending'),
(3, 1, 2, 1, '2026-10-08', '09:00:00', 'Pending'),
(4, 2, 1, 1, '2026-10-14', '10:00:00', 'Pending'),
(5, 1, 2, 1, '2026-10-16', '09:00:00', 'Pending'),
(6, 1, 2, 1, '2026-10-02', '13:30:00', 'Pending'),
(7, 2, 1, 1, '2026-10-08', '10:30:00', 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `dentist`
--

CREATE TABLE `dentist` (
  `Dentist_ID` int(11) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Specialty` varchar(100) DEFAULT NULL,
  `Phone` varchar(20) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `Password` varchar(255) DEFAULT NULL,
  `Status` varchar(20) DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dentist`
--

INSERT INTO `dentist` (`Dentist_ID`, `Name`, `Specialty`, `Phone`, `Email`, `Password`, `Status`) VALUES
(1, 'ทพ. สมชาย ใจดี', 'ทั่วไป', '0898765432', 'dentist@test.com', '123456', 'Active'),
(2, 'ทพ.บาซิล ', 'จัดฟัน', '093625631', 'basinluklem.s@gmail.com', 'basin270648', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `patient`
--

CREATE TABLE `patient` (
  `Patient_ID` int(11) NOT NULL,
  `Name_Surname` varchar(100) NOT NULL,
  `Phone` varchar(20) DEFAULT NULL,
  `Email` varchar(100) NOT NULL,
  `Password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patient`
--

INSERT INTO `patient` (`Patient_ID`, `Name_Surname`, `Phone`, `Email`, `Password`) VALUES
(1, 'สมหญิง รักฟันสวย', '0812345678', 'patient@test.com', '123456'),
(2, 'เด็นดอ', '5552211555', 'muttakeam@test.com', '258852');

-- --------------------------------------------------------

--
-- Table structure for table `problem`
--

CREATE TABLE `problem` (
  `Problem_ID` int(11) NOT NULL,
  `Problem_Name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `problem`
--

INSERT INTO `problem` (`Problem_ID`, `Problem_Name`) VALUES
(1, 'ฟันผุ'),
(2, 'หินปูน'),
(3, 'ฟันคุด'),
(4, 'เหงือกอักเสบ'),
(5, 'ฟันแตก'),
(6, 'ฟันโยก');

-- --------------------------------------------------------

--
-- Table structure for table `service`
--

CREATE TABLE `service` (
  `Service_ID` int(11) NOT NULL,
  `Service_Name` varchar(100) NOT NULL,
  `Description` text DEFAULT NULL,
  `Duration` int(11) NOT NULL,
  `Price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service`
--

INSERT INTO `service` (`Service_ID`, `Service_Name`, `Description`, `Duration`, `Price`) VALUES
(1, 'อุดฟัน', '', 30, 500.00);

-- --------------------------------------------------------

--
-- Table structure for table `tooth`
--

CREATE TABLE `tooth` (
  `Tooth_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tooth`
--

INSERT INTO `tooth` (`Tooth_ID`) VALUES
(11),
(12),
(13),
(14),
(15),
(16),
(17),
(18),
(21),
(22),
(23),
(24),
(25),
(26),
(27),
(28),
(31),
(32),
(33),
(34),
(35),
(36),
(37),
(38),
(41),
(42),
(43),
(44),
(45),
(46),
(47),
(48);

-- --------------------------------------------------------

--
-- Table structure for table `treatment_detail`
--

CREATE TABLE `treatment_detail` (
  `Detail_ID` int(11) NOT NULL,
  `Record_ID` int(11) DEFAULT NULL,
  `Tooth_ID` int(11) DEFAULT NULL,
  `Problem_ID` int(11) DEFAULT NULL,
  `Treatment_Method_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `treatment_detail`
--

INSERT INTO `treatment_detail` (`Detail_ID`, `Record_ID`, `Tooth_ID`, `Problem_ID`, `Treatment_Method_ID`) VALUES
(1, 1, 14, 1, 3);

-- --------------------------------------------------------

--
-- Table structure for table `treatment_method`
--

CREATE TABLE `treatment_method` (
  `Treatment_Method_ID` int(11) NOT NULL,
  `Treatment_Name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `treatment_method`
--

INSERT INTO `treatment_method` (`Treatment_Method_ID`, `Treatment_Name`) VALUES
(1, 'อุดฟัน'),
(2, 'ขูดหินปูน'),
(3, 'ถอนฟัน'),
(4, 'รักษารากฟัน'),
(5, 'เคลือบฟลูออไรด์');

-- --------------------------------------------------------

--
-- Table structure for table `treatment_record`
--

CREATE TABLE `treatment_record` (
  `Record_ID` int(11) NOT NULL,
  `Appointment_ID` int(11) DEFAULT NULL,
  `Note` text DEFAULT NULL,
  `Next_Appt_Date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `treatment_record`
--

INSERT INTO `treatment_record` (`Record_ID`, `Appointment_ID`, `Note`, `Next_Appt_Date`) VALUES
(1, 1, '', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`Admin_ID`);

--
-- Indexes for table `appointment`
--
ALTER TABLE `appointment`
  ADD PRIMARY KEY (`Appointment_ID`),
  ADD KEY `Patient_ID` (`Patient_ID`),
  ADD KEY `Dentist_ID` (`Dentist_ID`),
  ADD KEY `Service_ID` (`Service_ID`);

--
-- Indexes for table `dentist`
--
ALTER TABLE `dentist`
  ADD PRIMARY KEY (`Dentist_ID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Indexes for table `patient`
--
ALTER TABLE `patient`
  ADD PRIMARY KEY (`Patient_ID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Indexes for table `problem`
--
ALTER TABLE `problem`
  ADD PRIMARY KEY (`Problem_ID`);

--
-- Indexes for table `service`
--
ALTER TABLE `service`
  ADD PRIMARY KEY (`Service_ID`);

--
-- Indexes for table `tooth`
--
ALTER TABLE `tooth`
  ADD PRIMARY KEY (`Tooth_ID`);

--
-- Indexes for table `treatment_detail`
--
ALTER TABLE `treatment_detail`
  ADD PRIMARY KEY (`Detail_ID`),
  ADD KEY `Record_ID` (`Record_ID`),
  ADD KEY `Tooth_ID` (`Tooth_ID`),
  ADD KEY `Problem_ID` (`Problem_ID`),
  ADD KEY `Treatment_Method_ID` (`Treatment_Method_ID`);

--
-- Indexes for table `treatment_method`
--
ALTER TABLE `treatment_method`
  ADD PRIMARY KEY (`Treatment_Method_ID`);

--
-- Indexes for table `treatment_record`
--
ALTER TABLE `treatment_record`
  ADD PRIMARY KEY (`Record_ID`),
  ADD KEY `Appointment_ID` (`Appointment_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `Admin_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `appointment`
--
ALTER TABLE `appointment`
  MODIFY `Appointment_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `dentist`
--
ALTER TABLE `dentist`
  MODIFY `Dentist_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `patient`
--
ALTER TABLE `patient`
  MODIFY `Patient_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `problem`
--
ALTER TABLE `problem`
  MODIFY `Problem_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `service`
--
ALTER TABLE `service`
  MODIFY `Service_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `treatment_detail`
--
ALTER TABLE `treatment_detail`
  MODIFY `Detail_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `treatment_method`
--
ALTER TABLE `treatment_method`
  MODIFY `Treatment_Method_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `treatment_record`
--
ALTER TABLE `treatment_record`
  MODIFY `Record_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointment`
--
ALTER TABLE `appointment`
  ADD CONSTRAINT `appointment_ibfk_1` FOREIGN KEY (`Patient_ID`) REFERENCES `patient` (`Patient_ID`),
  ADD CONSTRAINT `appointment_ibfk_2` FOREIGN KEY (`Dentist_ID`) REFERENCES `dentist` (`Dentist_ID`),
  ADD CONSTRAINT `appointment_ibfk_3` FOREIGN KEY (`Service_ID`) REFERENCES `service` (`Service_ID`);

--
-- Constraints for table `treatment_detail`
--
ALTER TABLE `treatment_detail`
  ADD CONSTRAINT `treatment_detail_ibfk_1` FOREIGN KEY (`Record_ID`) REFERENCES `treatment_record` (`Record_ID`),
  ADD CONSTRAINT `treatment_detail_ibfk_2` FOREIGN KEY (`Tooth_ID`) REFERENCES `tooth` (`Tooth_ID`),
  ADD CONSTRAINT `treatment_detail_ibfk_3` FOREIGN KEY (`Problem_ID`) REFERENCES `problem` (`Problem_ID`),
  ADD CONSTRAINT `treatment_detail_ibfk_4` FOREIGN KEY (`Treatment_Method_ID`) REFERENCES `treatment_method` (`Treatment_Method_ID`);

--
-- Constraints for table `treatment_record`
--
ALTER TABLE `treatment_record`
  ADD CONSTRAINT `treatment_record_ibfk_1` FOREIGN KEY (`Appointment_ID`) REFERENCES `appointment` (`Appointment_ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
