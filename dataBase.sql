-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3666:3666
-- Generation Time: Sep 05, 2024 at 07:36 PM
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
-- Database: `ahl_alqhanon`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(127) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fname` varchar(127) NOT NULL,
  `lname` varchar(127) NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `stop` int(11) NOT NULL DEFAULT 0,
  `stop_date` text DEFAULT NULL,
  `email` text DEFAULT NULL,
  `lang` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `username`, `password`, `fname`, `lname`, `role_id`, `stop`, `stop_date`, `email`, `lang`) VALUES
(1, 'elias', '$2y$10$.eFlPzKdOk4jlZjxnNHIJ.a/kA1xURrf/yAg9jltbvcwTw0GDgcFW', 'Youssef', 'Shahboun', 11, 0, NULL, NULL, 'en'),
(9, 'anas', '$2y$10$6V3g4Ffk75XglkD/y1SLHujGdSvPrAf5dBJXMDgTwOxz5GGrVPTHy', 'Anas', 'anas', 36, 0, '', NULL, 'ar'),
(10, 'yyy', '$2y$10$rbTT9/VZkYnl77fskmgWwee15WuygEDZrW7ltSnEwjGKqZydWLnGK', 'yyy', 'yyy', 40, 0, '', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `adversaries`
--

CREATE TABLE `adversaries` (
  `id` int(11) NOT NULL,
  `fname` text DEFAULT NULL,
  `lname` text DEFAULT NULL,
  `address` text DEFAULT NULL,
  `email_address` text DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `city` text DEFAULT NULL,
  `phone` text DEFAULT NULL,
  `gender` text DEFAULT NULL,
  `office_id` int(11) DEFAULT NULL,
  `lawyer_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `adversaries`
--

INSERT INTO `adversaries` (`id`, `fname`, `lname`, `address`, `email_address`, `date_of_birth`, `city`, `phone`, `gender`, `office_id`, `lawyer_id`) VALUES
(16, 'Youssef', 'Shahboun', 'Roxy square', 'youssef@shahboun.com', '2000-11-11', 'Cairo', '01064029402', 'Male', 20, 31),
(20, 'anas', 'anas', 'Damascus', 'anas0alnajjar@gmail.com', '2024-07-28', 'syria', '0117750318', 'Male', 20, 31),
(21, 'Youssef', 'Mohamed', '80 شارع الخليفة المأمون مصر الجديدة روكسي', 'youssefshahboun1900@gmail.com', '0000-00-00', 'Cairo', '', 'Male', 20, NULL),
(23, 'Youssef', 'Shahboun', 'Roxy square', 'youssef@shahboun.com', '0000-00-00', 'alex', '01064029402', 'Male', 20, 47),
(24, 'Ahmed', 'rabeaa', 'Qalubia- Banha- warwarh', 'mada0743@gmail.com', '0000-00-00', 'Qalyubia', '0133101759', 'Male', 20, 115);

-- --------------------------------------------------------

--
-- Table structure for table `ask_join`
--

CREATE TABLE `ask_join` (
  `user_id` int(11) NOT NULL,
  `first_name` text NOT NULL,
  `last_name` text NOT NULL,
  `email` text NOT NULL,
  `phone` text NOT NULL,
  `username` text NOT NULL,
  `password` text NOT NULL,
  `address` text NOT NULL,
  `gender` text NOT NULL,
  `date_of_birth` text NOT NULL,
  `city` text NOT NULL,
  `as_a` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cases`
--

CREATE TABLE `cases` (
  `case_id` int(11) NOT NULL,
  `case_title` text NOT NULL,
  `case_type` text NOT NULL,
  `case_number` text NOT NULL,
  `case_description` text NOT NULL,
  `client_id` text NOT NULL,
  `client_email` text NOT NULL,
  `lawyer_id` text NOT NULL,
  `agency` varchar(250) NOT NULL,
  `last_modified` date DEFAULT current_timestamp(),
  `created_date` date DEFAULT NULL,
  `plaintiff` varchar(255) DEFAULT NULL,
  `defendant` varchar(255) DEFAULT NULL,
  `court_name` varchar(255) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `id_picture` text DEFAULT NULL,
  `judge_name` varchar(30) DEFAULT NULL,
  `helper_name` varchar(30) DEFAULT NULL,
  `office_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `cases`
--

INSERT INTO `cases` (`case_id`, `case_title`, `case_type`, `case_number`, `case_description`, `client_id`, `client_email`, `lawyer_id`, `agency`, `last_modified`, `created_date`, `plaintiff`, `defendant`, `court_name`, `department`, `notes`, `id_picture`, `judge_name`, `helper_name`, `office_id`) VALUES
(83, 'احوال مدنيه', '18', '102030', 'احوال مدنية ', '271', '', '29', '0', '2024-07-22', '2024-07-15', '271', '', '13', '8', '', 'id_picture669558ac5924e8.61449771_ed4417a2bda6cc21.jpeg', 'محمد سيد', '', 20),
(84, 'احوال مدنيه', '18', '552030', 'احوال مدنيه محكمة الاسره', '272', '', '36', '', '2024-07-17', '2024-07-17', '272', '', '13', '8', NULL, 'id_picture66979a208d1252.61577881_bfa46bf45151e22a.jpg', 'محمد سيد', NULL, 20),
(85, 'احوال مدنيه', '19', '554430', 'احوال مدنيه 2 ', '273', '', '36', '0', '2024-07-22', '2024-07-17', '273', '', '14', '9', '', 'id_picture66979b526436e6.90307772_d1f057043e2db9ba.jpg', 'محمد سيد', '', 20),
(87, 'Test', '18', '', '', '274', '', '41', '0', '2024-07-23', '2024-07-23', '', '', '', '', '', '', '', '', 20),
(88, 'تجارة عبيد', '18', '12345', '', '274', '', '40', '0', '2024-07-28', '2024-07-27', '', '', '', '', '', '', '', '', 20),
(89, 'Test', '19', '', '', '290', '', '40', '0', '2024-07-28', '2024-07-28', '', '', '', '', '', '', '', '', 20),
(90, 'قضية 23سيؤي', '18', '5555555', '', '292', '', '47', '0', '2024-08-03', '2024-08-03', '292', '', '14', '8', '', '', '', '', 20),
(91, 'احوال مدنيه', '', '253545', 'احوال مدنيه', '293', '', '50', '0', '2024-08-03', '2024-08-03', '293', '', '', '', '', 'id_picture66ae5f37181523.86287419_c2d002b77b515660.png', 'محمد سيد', '', 27),
(92, 'دعوي ريع', '24', '5551', '', '294', '', '56', 'on', '2024-08-09', '2024-08-09', '294', '', '16', '8', '', '', '', '', 20),
(93, 'نزاع علي الملكية', '18', '234/2024', '', '295', '', '65', '0', '2024-08-11', '2024-08-11', '295', '', '16', '8', '', '', 'غير معروف', '', 20),
(95, 'شرعي', '27', '٥٦٦٦', 'تلل بلا بات', '300', '', '104', '', '2024-08-23', '2024-08-23', '300', '', '17', '8', NULL, '', '', NULL, 20),
(96, 'cvgnb', '27', '1666', '', '302', '', '115', '', '2024-08-24', '2024-08-24', '302', '', '', '', NULL, '', '', NULL, 20),
(97, 'cvgnb', '27', '1666', '', '302', '', '115', '', '2024-08-24', '2024-08-24', '302', '', '', '', NULL, '', '', NULL, 20),
(98, 'cvgnb', '35', '1666', '', '302', '', '115', '', '2024-08-24', '2024-08-24', '', '', NULL, '', NULL, '', '', NULL, 20),
(99, 'صورية عقد البيع (ارض الجراج )', '24', '967 ب2022', '', '306', '', '137', 'on', '2024-08-27', '2024-08-27', '306', '', '21', '8', '', 'id_picture66cdbc751a9044.41683341_45db8f9f24258a8e.jpg', 'محمد فوزى ', '', 20),
(100, 'محكمة القضاء الإداري الدائرة السادسة تعليم (الرحاب)', '18', '84174 لسنة 78 قضائية', '', '307', '', '140', 'on', '2024-08-27', '2024-08-27', '307', '', '13', '9', NULL, '', '', NULL, 20),
(101, 'قضية 3', '21', 'jhhh', '', '295', '', '29', '', '2024-08-27', '2024-08-27', '295', '', '15', '12', NULL, '', '', NULL, 20),
(102, 'Rape of a girl', '37', '', '', '308', '', '142', '', '2024-09-05', '2024-09-05', '', '', '', '', NULL, '', '', NULL, 36);

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `client_id` int(11) NOT NULL,
  `first_name` text NOT NULL,
  `last_name` text NOT NULL,
  `email` text NOT NULL,
  `phone` text NOT NULL,
  `username` text NOT NULL,
  `password` text NOT NULL,
  `address` text NOT NULL,
  `gender` text NOT NULL,
  `date_of_birth` text NOT NULL,
  `city` text NOT NULL,
  `father_name` varchar(30) DEFAULT NULL,
  `grandfather_name` varchar(30) DEFAULT NULL,
  `national_num` varchar(30) DEFAULT NULL,
  `alhi` varchar(30) DEFAULT NULL,
  `street_name` varchar(30) DEFAULT NULL,
  `num_build` varchar(30) DEFAULT NULL,
  `num_unit` varchar(30) DEFAULT NULL,
  `zip_code` varchar(30) DEFAULT NULL,
  `subnumber` varchar(30) DEFAULT NULL,
  `receive_emails` int(11) NOT NULL DEFAULT 1,
  `receive_whatsupp` int(11) NOT NULL DEFAULT 1,
  `role_id` int(11) DEFAULT NULL,
  `client_passport` text DEFAULT NULL,
  `stop` int(11) NOT NULL DEFAULT 0,
  `office_id` int(11) DEFAULT NULL,
  `stop_date` text DEFAULT NULL,
  `lawyer_id` int(11) DEFAULT NULL,
  `lang` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`client_id`, `first_name`, `last_name`, `email`, `phone`, `username`, `password`, `address`, `gender`, `date_of_birth`, `city`, `father_name`, `grandfather_name`, `national_num`, `alhi`, `street_name`, `num_build`, `num_unit`, `zip_code`, `subnumber`, `receive_emails`, `receive_whatsupp`, `role_id`, `client_passport`, `stop`, `office_id`, `stop_date`, `lawyer_id`, `lang`) VALUES
(262, 'Mohammad', 'sdd', 'anas0alnajjar@gmail.com', '0117750318', 'eliashg', '$2y$10$XVwNIvx2YBBIxlfFGYMOx.CvZZcfTWQvhKqt1YdQO1ksYVkGiQDVu', 'Damascus', 'Male', '2024-07-23', 'syria', '', '', '', '', '', '', '', '', '', 1, 1, 33, '', 0, 20, NULL, 27, ''),
(271, 'said', 'ali', 'said@gmail.com', '0100 503 0067', 'said ali', '$2y$10$7WRl1tuvxDNa/LxHBhWhGOmirocwms8cXs8/JfnvaxUxwDncZ7pw6', '25 الهرم جيزه ', 'Male', '1980-02-15', 'القاهرة', 'hassan', 'saied', '01245489884133', 'مدنة نصر', 'النحاس', '15', '10', '0202', '3040', 1, 1, 34, '14578962121584', 0, 20, NULL, 29, ''),
(272, 'saber', 'ali', 'saber@gmail.com', '+20 106 482 5572', 'sabir ali', '$2y$10$S6LV2t9ovr8xA.MAQJ3FxejaylVzM98sFw.TibdQJjiRaNVWZZdZC', '50 الهرم جيزة', 'Male', '', 'الجيزة', 'hassan', 'saied', '01245489884134', 'الهرم', 'العروبه', '18', '12', '0202', '3040', 1, 1, 34, '14578962121554', 0, 20, NULL, 36, ''),
(273, 'mohsen', 'ali', 'mohsen@gmail.com', '+20 106 882 5572', 'mohsen ali', '$2y$10$RUtLj6DY60SgoitJ0nuYueTGvPmxOMTWSQ5ZyfnTbWT/V1Zx6mJf6', '60 الهرم جيزة', 'Male', '1992-06-17', 'الجيزة', 'hassan', 'saied', '00245489884133', 'الهرم', 'العروبه', '20', '13', '0202', '3040', 1, 1, 34, '002457859884133', 0, 20, NULL, 36, ''),
(274, 'anas', 'anas', 'anas0alnajjar@gmail.com', '0117750318', 'honi', '$2y$10$O3kgzVEhGihLQ1opJmt4EOadQ89KluzWMXjDFX2dimgJ2cg3HPFNO', 'Damascus', 'Male', '', 'syria', '', '', '', '', '', '', '', '', '', 1, 1, 34, '', 0, 20, NULL, 40, ''),
(278, 'anas', 'anas', 'anas0alnajjar@gmail.com', '0117750318', 'dsds', '$2y$10$/tLJpvc6kVKnuFSt.E0cjewcNI1nUEZN.aYRKlyb2LV21BmwjwebK', 'Damascus', 'Male', '', 'syria', '', '', '', '', '', '', '', '', '', 1, 1, 34, '', 0, 20, NULL, 0, ''),
(279, 'anas', 'anas', 'anas0alnajjar@gmail.com', ' 31 117 750 318', 'anass', '$2y$10$Oq9/YKRN82WqiBGo76YWLumXTACrmcrA2Id6bo6zijzLjPzeHOm7K', 'Damascus', 'Male', '', 'syria', '', '', '', '', '', '', '', '', '', 1, 1, 34, '', 0, 20, NULL, 0, ''),
(280, 'd', 'd', 'anas0alnajjar@gmail.com', ' 31 117 750 318', 'honi0123', '$2y$10$iaKXoSVAysGahnwCMPMfherz5K7V9QDqJvxfg8mkZBgBHghcNpjoW', '', 'Male', '', '', '', '', '', '', '', '', '', '', '', 1, 1, 34, '', 0, 20, NULL, 40, ''),
(283, 'user', 'usre', 'a@g.co', '999999999999999999', 'sddwqw', '$2y$10$DY2RhiqLDR5tMJuy6NI2o.HI0ZccI2ceBaovGlZz08VI67ta2IBd6', '', 'Male', '', '', '', '', '', '', '', '', '', '', '', 1, 1, 34, '', 0, 20, NULL, 40, ''),
(285, 'anas', 'anas', 'anas0alnajjar@gmail.com', '+1 201 555 1234', 'hيoni', '$2y$10$iv3WLJN40SOh01RzNE4Hx.9L5V1Jbmhb3kg7N3Ia3EVOTCu3sQDxy', 'Damascus', 'Male', '', 'syria', '', '', '', '', '', '', '', '', NULL, 1, 1, 34, '', 0, 20, NULL, 40, ''),
(289, 'Jon', 'Doe', 'test@example.us', '(601) 952-1325', 'جون', '$2y$10$cmP/kV/A1cgX2qB8SNu.nexB/XCX2mCCmVDWD73gWEnz6l.TUiLbG', '1600 Amphitheatre Parkway', 'Male', '', 'Mountain View', '', '', '', '', '', '', '', '', '', 1, 1, 34, '', 0, 20, NULL, 40, ''),
(290, 'A', 'B', 'anas0alnajjar@gmail.com', '+1 201 555 1234', 'X', '$2y$10$MgiqlI5sefFtNNnHAvnwSuI51wsI6jSTOKBb5Nlv.DSbXwnJZwAAO', '', 'Male', '', '', '', '', '', '', '', '', '', '', '', 1, 1, 34, '', 0, 20, NULL, 40, ''),
(291, 'Youssef', 'Shahboun', 'youssef@shahboun.com', '01064029402', 'qqq', '$2y$10$WBHgqsJ1yzvLzAr7JXyKi.y0oL7JiL4h/SRd3y8JCsiZV5I9x5N8u', 'Roxy square', 'Male', '1999-08-24', 'Cairo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 34, NULL, 1, 20, '2024-08-17', NULL, ''),
(292, 'Youssef', 'Shahboun', 'youssef@shahboun.com', '+20 106 402 9402', 'صصص', '$2y$10$qJby4Dulm1OTrMi7SwHSH.qaLMNAafPQVZZ7hlVQirYR6nWOLByBm', 'Roxy square', 'Male', '', 'Cairo', '', '', '', '', '', '', '', '', '', 1, 1, 34, '', 0, 20, NULL, 47, ''),
(293, 'ali ', 'nasser', 'alinasser@gmail.com', '+20 109 145 6623', 'alinasser', '$2y$10$OUPLMbpCTrJq975202WLlePusLBbye3d12z38o04z97dQ.D6w77tC', '32 الهرم جيزة', 'Male', '1989-03-03', 'الجيزه', 'hassan', 'saied', '01245400884133', 'الهرم', 'العروب', '18', '13', '0202', '3040', 1, 1, 34, '145789610121584', 0, 27, NULL, 0, ''),
(294, 'موكل تجريبي', 'تجربة', 'sherifaga@mail.com', '+20 127 183 9655', 'ههع', '$2y$10$m.HwPRL6VdPQZDUuj1g89ueJyhPmQM5LskHTpg5NcNvBmBGB7yzZ6', 'منيا', 'Male', '', '', '', '', '', '', '', '', '', '', '', 1, 0, 34, '', 0, 20, NULL, 56, ''),
(295, 'Youssef', 'Mohamed', 'youssef@shahboun.com', '+20 106 402 9402', 'موكل', '$2y$10$YgM4UiNU5zd7Q5CFsD5IEuRREpKAEXX8uTOMc3pvnQqyCGd/Yt9sK', '80 El-Khalifa El-Maamoun, Mansheya El-Bakry, Heliopolis, Cairo Governorate', 'Male', '1988-08-28', 'Cairo', '', '', '', '', '', '', '', '', '', 1, 1, 34, '', 0, 20, NULL, 65, ''),
(296, 'محسن', 'خليل', 'لايرحد', '+20 114 606 6538', '', '', '', 'Male', '2024-08-13', 'القاهرة', '', '', '١٢٣٤٥٦٨٨٨', '', '', '', '', '', '', 1, 1, NULL, '', 0, 20, NULL, 73, ''),
(298, 'عماد', 'رجب احمد سليم', 'vb7_kkk@yahoo.com', '+20 118 611 1889', 'عماد', '$2y$10$JZ3UKMdloATVfnpFkCmQYurqVTuBAjI3LvXMxSUvU.koDx38WoBzC', '', 'Male', '', '', '', '', '', '', '', '', '', '', '', 1, 1, 34, '', 0, 20, NULL, 87, ''),
(299, 'شهد ', 'الخضيري', 'majedgost@yahoo.com', '+20 102 222 6014', 'Shahd', '$2y$10$jqFMoaExmrGbiDXf4QdMgOGIgNNzEF.AjxV9XB3lXxpqjfBdlCRZS', 'الشرق', 'Male', '', 'بورسعيد', '', '', '٢٨٩١٠٢٠٣٤٠٣٠٠٠٥٤', '', '', '', '', '', '', 0, 1, 34, '', 0, 20, NULL, 88, ''),
(300, 'ابو سريع', 'نمس', 'Wwwee@gmail.com', '+20 101 010 1010', 'سريع', '$2y$10$6oyjLIB04fcCNP2epWvqW.Oo77xj2CXauoTNgRCSNIztSTDj9S7uC', 'بيتهم', 'Male', '', '', '', '', '', '', '', '', '', '', '', 1, 1, 34, '', 0, 20, NULL, 104, ''),
(301, 'Soso', 'Soso', 'Rtyfg@gmail.com', '+20 111 010 1010', 'Soso', '$2y$10$n.vBKDMgV2sV1yWkrTj4iunGHvo5BZQf/xWrd15Xg24ACBRmpbGLa', '', 'Male', '', '', '', '', '', '', '', '', '', '', '', 1, 1, 34, '', 0, 20, NULL, 104, ''),
(302, 'Ahmed', 'Rabea', 'mada0743@gmail.com', '+20 106 523 7079', 'احمدسامي', '$2y$10$v3nGccwgCiwl8Urnu5ksvOJ/RrKyYhyRAUzs3E8tnf4JjWJGNwbQK', 'Qalubia- Banha- warwarh', 'Male', '', 'Qalyubia', '', '', '300005201400976', '', '', '', '', '', '', 1, 1, 34, '', 0, 20, NULL, 115, ''),
(303, 'Youssef', 'Shahboun', 'youssef@shahboun.com', '+20 106 402 9402', 'ححح', '$2y$10$ROw6tPiY8fosSCW2yw4I8e2Z.WiF3X39sukESapQ45oS5dwQq.a0.', '', 'Male', '', 'Cairo', '', '', '', '', '', '', '', '', '', 1, 1, 34, '', 0, 20, '', 0, ''),
(304, 'طارق', 'بهجت', 'fgfgg@ggggg.com', '+20 105 548 8775', 'Tarek', '$2y$10$1Skyj4CpNpRmHa7X25/jGuYHe9zGSLc.uY4XI6hquywtBMpGN9RDm', '', 'Male', '', '', '', '', '', '', '', '', '', '', '', 1, 1, 34, '', 0, 20, NULL, 135, ''),
(305, 'شيماء', 'على', 'aaa@ssss.com', '+20 105 541 1112', 'شيماء', '$2y$10$v/Uc4TI8RorS4hRIs7f7g.V6fjuaj0Y2iWqt9aKp.4yrWLjE6WaeW', '', 'Male', '', '', '', '', '', '', '', '', '', '', '', 1, 1, 34, '', 0, 20, NULL, 135, ''),
(306, 'مروة', 'وهيب', 'shroukebrahem55@gmail.com', '+20 122 007 9444', 'مروة وهيب', '$2y$10$.xmZ0WQMAG8nvQ6hABNkVuXBw57xiE97vY70hBG2vvXFWOu8WXvsG', 'لوران', 'Female', '1982-03-08', 'الاسكندرية', '', '', '28203311802881', '', '', '', '', '', '', 1, 1, 34, '', 0, 20, NULL, 137, ''),
(307, 'محمود محمد محمد صابر', 'عبدالحميد الكفراوي', 'abdo5766@gmail.com', '0111 143 2670', 'abc', '$2y$10$q7537juNoBCZScrDzhKPd.ef0eVgLneyadjvAob8XYdcXyfbVTeRe', 'شارع زغلول مشعل الهرم', 'Male', '2003-06-11', 'الجيزة', '', '', '', '', '', '', '', '', '', 1, 1, 34, '', 0, 20, '', 140, 'fr'),
(308, 'Anas', 'anas', 'anas0alnajjar@gmail.com', '+31 117 750 318', '', '', 'Damascus', 'Male', '2024-09-13', 'syria', '', '', '', '', '', '', '', '', '', 1, 1, NULL, '', 0, 36, NULL, NULL, '');

-- --------------------------------------------------------

--
-- Table structure for table `costs_type`
--

CREATE TABLE `costs_type` (
  `id` int(11) NOT NULL,
  `type` text NOT NULL,
  `office_id` int(11) DEFAULT NULL,
  `public` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `costs_type`
--

INSERT INTO `costs_type` (`id`, `type`, `office_id`, `public`) VALUES
(18, 'نوع المصروف 1', 20, NULL),
(19, 'نوع المصروف 2', 20, NULL),
(20, 'إيجار', 22, NULL),
(21, 'كهرباء', 22, NULL),
(22, 'مياة', 22, NULL),
(23, 'مرتبات', 22, NULL),
(24, 'ايجار', 20, NULL),
(25, 'مرتبات', 20, NULL),
(26, 'كهرباء', 20, 0);

-- --------------------------------------------------------

--
-- Table structure for table `courts`
--

CREATE TABLE `courts` (
  `id` int(11) NOT NULL,
  `court_name` text NOT NULL,
  `office_id` int(11) DEFAULT NULL,
  `public` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courts`
--

INSERT INTO `courts` (`id`, `court_name`, `office_id`, `public`) VALUES
(13, 'محمكة 1', 20, 0),
(14, 'محمكة 2', 20, 0),
(15, 'محكمة مصر الجديدة', 20, 1),
(16, 'محكمة القاهرة الجديدة', 20, 1),
(17, 'عام', 24, 0),
(21, 'مجمع محاكم السيد كريم', 20, 0);

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `type` text NOT NULL,
  `office_id` int(11) DEFAULT NULL,
  `public` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `type`, `office_id`, `public`) VALUES
(8, 'دائرة 1', 20, NULL),
(9, 'دائرة 2', 20, NULL),
(10, 'دائرة مصر الجديدة', 22, NULL),
(11, 'دائرة الزيتون', 22, NULL),
(12, 'دائرة المطرية', 22, NULL),
(13, 'دائرة مدينة نصر', 22, 0);

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `document_id` int(11) NOT NULL,
  `title` varchar(250) NOT NULL,
  `content` text NOT NULL,
  `client_id` int(11) NOT NULL,
  `lawyer_id` int(11) NOT NULL,
  `attachments` text DEFAULT NULL,
  `case_id` int(11) DEFAULT NULL,
  `office_id` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`document_id`, `title`, `content`, `client_id`, `lawyer_id`, `attachments`, `case_id`, `office_id`, `notes`) VALUES
(115, 'عقد زواج موثق', '<p>عقد زواج موثق</p>', 271, 29, '66955e3b612bb_271_2024-07-15.pdf', NULL, 20, ''),
(116, 'وثيقة طلاق', '<p>وثيقة طلاق</p>', 271, 29, '66955fd506659_271_2024-07-15.pdf', NULL, 20, ''),
(117, 'وثيقة طلاق 2', '<p>وثيقة طلاق 2</p>', 271, 29, '6695602f0907d_271_2024-07-15.pdf', NULL, 20, ''),
(120, 'نموذج عقد ايجار', '<h2><strong>نموذج عقد ايجار&nbsp;</strong></h2><p><strong>رسيرسيرسيرسيرسي &nbsp; &nbsp; يسرسيريسرسير &nbsp; &nbsp; &nbsp; &nbsp;يسريسرسيرسي &nbsp; &nbsp; &nbsp; سيريسرسي &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;يسيسرسيرسي&nbsp;</strong></p>', 271, 29, '669615fbebd1c_271_2024-07-16.pdf', NULL, 20, ''),
(121, 'عقدايجار', '<p>عقدايجار</p>', 272, 36, '66979babeadfb_272_2024-07-17.pdf', NULL, 20, 'لا يوجد'),
(122, 'test', '<p>إعـ.ـلان ((مـ.ـحل الصبا))</p><p>رجعـ.ـنالكن مـ.ـن جـ.ـديد بأحلى المنـ.ـتجات اللي تعـ.ـودتوا عليها.</p><p>نرحـ.ـب بكـ.ـم في المـ.ـحل الجـ.ـديد المـ.ـوجود خـ.ـلف الصـ.ـراف الآلـ.ـي _ مدخـ.ـل عـ.ـيادة الدكتور عمر قريع.</p><p>ونتشرف بزيارتكم لـ.ـتعرفوا كل العـ.ـروض الممـ.ـيزة والأنـ.ـواع الجـ.ـديدة مـ.ـن المنتـ.ـجات.</p>', 274, 40, '', NULL, 20, ''),
(124, 'عقد زواج موثق 65', '<p>لاببيسسسششالب</p>', 271, 29, '669e7a95558f8_271_2024-07-22.pdf', NULL, 20, ''),
(126, 'صباح الخير', '<p>ملف اهل القانون</p>', 292, 47, '66ae29ec65050_292_2024-08-03.pdf', NULL, 20, ''),
(127, 'مذكرة ٦-٦-٢٠٢٤', '<p>مذكرة تم تقديمها بجلسة ٧-٤ وتم تأجيل الجلسة للرد والتعقيب من محامي الخصم وسداد الرسم</p>', 301, 104, '66c80f45388d1_301_2024-08-23.pdf', NULL, 20, ''),
(128, 'dgfsdffgf', '<p>.</p>', 302, 115, 'reportFile_family_66c9c2fa49bb13.88800758_1a86b302d980d550.pdf', 97, 20, ''),
(129, 'xxx', '<p>اضافة كل الحقول</p>', 302, 115, '', NULL, 20, ''),
(131, 'test', '<p>ghg</p>', 307, 140, '', 100, 20, 'ghghgh'),
(132, 'هاي', '<p>الله حيو الزلم</p>', 295, 29, '', 101, 20, 'مرحباً هابهبي'),
(133, 'test files33', '<p>fddfdf</p>', 302, 29, '', NULL, 27, 'fddfdf'),
(136, 'test files33', '<p>hgijdkgk</p>', 308, 142, '', 102, 36, 'lgkjjkdgkd213'),
(137, 'test files55', '<p>05465</p>', 308, 142, '', 102, 36, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `event_id` int(11) NOT NULL,
  `event_name` text DEFAULT NULL,
  `event_start_date` text DEFAULT NULL,
  `event_end_date` text DEFAULT NULL,
  `lawyer_id` int(11) DEFAULT NULL,
  `client_id` int(11) DEFAULT NULL,
  `helper_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`event_id`, `event_name`, `event_start_date`, `event_end_date`, `lawyer_id`, `client_id`, `helper_id`) VALUES
(77, 'تجربة', '2024-07-12T00:00', '2024-07-13T00:00', 40, 0, NULL),
(80, 'عمل توكيل لمحمد علي ', '2024-07-11T00:00', '2024-07-12T00:00', 30, 274, NULL),
(83, 'تأكيد على المؤكد!', '2024-07-21T00:00', '2024-07-22T00:00', 40, 274, NULL),
(84, 'حدث قديم', '2024-07-07T19:01', '2024-07-10T16:02', 28, 262, NULL),
(87, '010465045', '2024-07-26T20:43', '2024-07-27T20:43', 40, 0, NULL),
(88, 'vvv', '2024-07-02T02:38', '2024-07-03T02:38', 28, 0, NULL),
(89, 'تقييد', '2024-07-27T00:00', '2024-07-28T00:00', 40, 0, NULL),
(91, 'بيييييييييييييي', '2024-07-30T22:55', '2024-08-07T00:55', 40, 280, NULL),
(92, 'ؤؤؤ', '2024-08-17T00:00', '2024-08-18T00:00', 47, 292, NULL),
(93, 'اىتااا', '2024-08-14T00:00', '2024-08-15T00:00', 29, 0, NULL),
(94, 'قضية نفقة', '2024-08-25T00:00', '2024-08-26T02:00', 115, 302, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` int(11) NOT NULL,
  `case_id` int(11) DEFAULT NULL,
  `pay_date` date DEFAULT NULL,
  `amount` text DEFAULT NULL,
  `notes_expenses` text DEFAULT NULL,
  `pay_date_hijri` varchar(30) DEFAULT NULL,
  `session_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`id`, `case_id`, `pay_date`, `amount`, `notes_expenses`, `pay_date_hijri`, `session_id`) VALUES
(38, 83, '2024-07-31', '5000', '', '1446-01-25', 215),
(40, 88, '2024-07-25', '100', '', '1446-01-19', 0),
(41, 91, '2024-11-21', '5000', '', '1446-05-19', 228),
(42, 93, '2024-08-12', '100', '', '1446-02-08', 234),
(43, 99, '2023-03-19', '150', 'مواصلات و توثيق حوافظ', '1444-08-27', 237);

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE `files` (
  `id` int(11) NOT NULL,
  `case_id` int(11) NOT NULL,
  `file_name` text NOT NULL,
  `file_path` text NOT NULL,
  `created_date` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `files`
--

INSERT INTO `files` (`id`, `case_id`, `file_name`, `file_path`, `created_date`) VALUES
(106, 83, 'Logo web.png', 'Logo web_669e762af3b67.png', '2024-07-22'),
(107, 83, 'meta_image.png', 'meta_image_669e763cd2bb2.png', '2024-07-22'),
(109, 89, 'السجل التجاري.pdf', 'السجل التجاري_66a6ca9fe9e87.pdf', '2024-07-28'),
(110, 88, 'السجل التجاري.pdf', 'السجل التجاري_66a6cc3fbb77c.pdf', '2024-07-28'),
(111, 90, '345352452345.png', '345352452345_66ae28ecd002f.png', '2024-08-03');

-- --------------------------------------------------------

--
-- Table structure for table `headers`
--

CREATE TABLE `headers` (
  `id` int(11) NOT NULL,
  `office_id` int(11) DEFAULT NULL,
  `header` text NOT NULL,
  `profile_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `headers`
--

INSERT INTO `headers` (`id`, `office_id`, `header`, `profile_id`) VALUES
(29, NULL, '1720871820_Logo web.png', 6),
(30, 20, '1721212310_cover.jpg', 7),
(31, NULL, '1722700591_345352452345.png', 6),
(32, NULL, '1722700591_Logo web.png', 6),
(33, NULL, '1722700591_Logo web2.png', 6),
(37, NULL, '1722704944_Logo web.png', 6),
(38, NULL, '1722704944_Logo web2.png', 6),
(42, NULL, '1722723487_ar.png', 8),
(43, NULL, '1722723487_en.png', 8);

-- --------------------------------------------------------

--
-- Table structure for table `helpers`
--

CREATE TABLE `helpers` (
  `id` int(11) NOT NULL,
  `candelete` int(11) DEFAULT 1,
  `helper_name` varchar(30) NOT NULL,
  `username` varchar(30) NOT NULL,
  `pass` text NOT NULL,
  `lawyer_id` int(11) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL,
  `national_helper` text DEFAULT NULL,
  `passport_helper` text DEFAULT NULL,
  `office_id` int(11) DEFAULT NULL,
  `stop` int(11) DEFAULT NULL,
  `stop_date` text DEFAULT NULL,
  `email` text DEFAULT NULL,
  `lang` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `helpers`
--

INSERT INTO `helpers` (`id`, `candelete`, `helper_name`, `username`, `pass`, `lawyer_id`, `phone`, `role_id`, `national_helper`, `passport_helper`, `office_id`, `stop`, `stop_date`, `email`, `lang`) VALUES
(30, 1, 'edrf', 'sss', '$2y$10$mijUCfTrQtYX0zBEUwR2vea4VbVq8F8ijKUjgL7XfLZJELOcoDpOa', 0, '0107 399 1099', 39, '', '', 20, 0, '', NULL, 'ar');

-- --------------------------------------------------------

--
-- Table structure for table `lawyer`
--

CREATE TABLE `lawyer` (
  `lawyer_id` int(11) NOT NULL,
  `lawyer_name` text NOT NULL,
  `date_of_birth` varchar(200) NOT NULL,
  `lawyer_email` varchar(250) NOT NULL,
  `lawyer_phone` text NOT NULL,
  `username` text NOT NULL,
  `lawyer_password` text NOT NULL,
  `lawyer_address` text NOT NULL,
  `lawyer_gender` text NOT NULL,
  `lawyer_city` text NOT NULL,
  `lawyer_logo` text DEFAULT NULL,
  `preferred_date` varchar(30) DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL,
  `lawyer_passport` text DEFAULT NULL,
  `lawyer_national` text DEFAULT NULL,
  `stop` int(11) NOT NULL DEFAULT 0,
  `office_id` int(11) DEFAULT NULL,
  `stop_date` text DEFAULT NULL,
  `lang` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `lawyer`
--

INSERT INTO `lawyer` (`lawyer_id`, `lawyer_name`, `date_of_birth`, `lawyer_email`, `lawyer_phone`, `username`, `lawyer_password`, `lawyer_address`, `lawyer_gender`, `lawyer_city`, `lawyer_logo`, `preferred_date`, `role_id`, `lawyer_passport`, `lawyer_national`, `stop`, `office_id`, `stop_date`, `lang`) VALUES
(29, 'Ahmed  azab', '1986-04-19', 'ahmedazb80@gmail.com', '01061926672', 'ahmed azab', '$2y$10$oqOK7ubiU5WUBaiu1MUJEOikRnl8gJSDzdz/sz9d5j/f.HrYKt.eG', '18 الهرم جيزة', 'Male', 'القاهرة', 'lawyer_logo669e80173f5282.78557361_10e17aed3fdd5c0d.png', NULL, 33, NULL, NULL, 0, 20, NULL, ''),
(30, 'ابراهيم رفاعي المحامي', '1979-01-01', 'EMAIL@EMAIL.COM', '0100 466 6970', 'ابراهيم', '$2y$10$Bbi3phbIyP.pjudzNyhWpuHtrXzJ7H1seby4TklimRvZ2rEWleWBK', 'المطرية', 'Male', 'القاهرة', NULL, '0', 33, '', '', 0, 20, '2024-07-31', ''),
(34, 'Mohammad anas', '2024-07-24', 'anas0alnajjar@gmail.com', '0117750318', 'as', '$2y$10$Ka.Im5.ML8Q1avPeprZUKuKCeFuYjBpFqOvo7odUslMSZRjQ9hwfy', 'Damascus', 'Male', 'syria', NULL, '0', 33, '', '', 1, 22, '2024-07-29', ''),
(36, 'ahmed elazab', '1984-03-17', 'elazab@gmail.com', '01061626672', 'ahmed elazab', '$2y$10$g.B8gEYl6O4ZJpP3296NcuZ8j5JX.DKK3tLv4YiD.sNoHQA0oDizW', '30 الهرم جيزة', 'Male', 'الجيزه', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-07-31', ''),
(37, 'حنان  عبدالادرق', '1975-07-30', 'tota79749@gmail.com', '01147215216', '   Hanan', '$2y$10$koiO4Trx.gjSBadoqQXL6.5Cnj/OhOfDFCMBZl4WlcF.CFzkFWZHq', '٨٠ ميدان روكسي', 'Female', 'Cairo', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-08-02', ''),
(38, 'حنان عبد القادر', '1975-07-30', 'tota79749@gmail.com', '01147215216', 'Hanan Abdelkhader', '$2y$10$aajXFqpFPRCW4C3vbE1Az.p6UNUC0yh13d5zEqYbLPPG9FjjwoYVC', '80 ش ميدان روكسي مصر الجديدة', 'Female', 'القاهرة', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-08-02', ''),
(39, 'Hany Lawendy', '1972-01-10', 'hanylawyer2@yahoo.com', '01000533300', 'hanylawendy', '$2y$10$Fb0ZUCA9ehwWNqHdTcCJduYo3Hi1McUgCP9U7LTs7o4CZ1Id5jLjO', '13ebrahim elkhalel qebaa ', 'Male', 'cairo', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-08-02', ''),
(40, 'anas', '', 'anas0alnajjar@gmail.com', '0117 750 318', 'anos', '$2y$10$Ka.Im5.ML8Q1avPeprZUKuKCeFuYjBpFqOvo7odUslMSZRjQ9hwfy', 'Damascus', 'Male', 'syria', 'lawyer_logo669bfd519a1426.68485487_4239f6b535079400.png', '0', 33, '', '', 0, 20, '', ''),
(42, 'محمد  الغاوي', '1996-10-22', 'mohamedelghawy8@gmail.com', '01006946275', ' Mohamed elghawy', '$2y$10$3/rgcVG9/MT3fiioCYYOIePkBzZMUGoUB6njGHxmgiVJ/RjWV8heK', 'شبين الكوم المنوفيه', 'Male', '02', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-08-06', ''),
(43, 'Ali Morsy', '1977-04-21', 'alimorsyalimorsy@yahoo.com', '01283030133', 'alimorsyalimorsy@yahoo.com', '$2y$10$ubYzgd9AVIu6h6DSY8m3LugQzmFXKcuhOORMN6wi6zxvVlGQLYX.i', '٨٠ شارع الخليفة المامون روكسي', 'Male', 'Cairo', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-08-06', ''),
(44, 'ناديه  الجمل', '1994-01-19', 'gamil.com@nadiaelgaml', '0112 767 6520', 'Nadia Elgaml ', '$2y$10$S633J5EI8gcAZGXtbpo8Oul4QMo5TafLuX/qrhG11aFGVFqVXEL3S', 'المطرية ', 'Female', 'القاهرة ', 'lawyer_logo66a22404cd1a42.88374357_9c93c517a571b41d.png', '1', 33, '', '', 1, 20, '2024-08-07', ''),
(45, 'ناديه  الجمل', '1994-01-19', 'gamil.com@nadiaelgaml', '01127676520', 'Nadia .Elgaml ', '$2y$10$PgB.ZQy7P9e2SJfF5QxSmuR6V2Uo.xxfZp20SncTQPtFnGVjMwGui', 'المطرية ', 'Female', 'القاهره', 'lawyer_logo66a223e0048525.95860100_4dadc69eee61d2cd.png', '1', 33, '', '', 1, 20, '2024-08-07', ''),
(46, 'Shenouda Eskander', '1977-07-10', 'theavo2000@yahoo.com', '+20 111 005 6260', 'المستشار شنوده اسكندر', '$2y$10$xNPJ30cNnefhUZu/s11R8upn/qN3yNgcsletngIlG6VDLmJmj1uq2', 'النزهه الجديده القاهره', 'Male', 'القاهره ', 'lawyer_logo66a21ddb7eb658.39575340_43966222ec625abf.png', '1', 33, '', '', 1, 22, '2024-08-08', ''),
(48, 'ahmed azab 2', '1995-02-03', 'imediapro696@gmail.com', '01064825572', 'ahmed azab 2', '$2y$10$bVJ6Wu1vg0sHB8JBgecizOGBw/w35AQUN/3EMqnQM5477um0F8kPO', '50 الهرم جيزة', 'Male', 'الجيزة', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-08-17', ''),
(50, 'mahoud ali', '', 'mahmoudali@gmail.com', '+20 105 864 7789', 'mahmoudali', '$2y$10$fOkkU8/XrOdv6stWZu66s.hkF/jxVMDsoSuJt6QBe9w2Vqm3Mqojq', '13 الهرم جيزة', 'Male', 'الجيزة', NULL, NULL, 33, '23564789200', '012345678221', 0, 27, NULL, ''),
(51, 'محمد  عبدالجواد', '1978-04-20', 'saad_mahamy@yahoo.com', '01003053651', 'mohamed2024', '$2y$10$EaLo.ZA4wJIbTiLy1hbZs.leKyoG7goRb6Rvn6Zv9ZbMxZaUwNJ/O', '60 شارع السودان الدقى الجيزة ', 'Male', 'الدقى ', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-08-20', ''),
(53, 'حسين خطاب', '1986-09-01', 'mr.hussien.86@gmail.com', '01068883435', 'gfx.hussien', '$2y$10$MnyJq2Cz6LA.IvvterMdL.8IQkwKV7juDBS8JKDXNCKSrdt78wrCe', 'المنصورة - شربين ', 'Male', 'شربين', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-08-20', ''),
(54, 'محمود صلاح  حسيب', '1988-07-28', 'Mahmoudsalahhasib@gmail.com', '01093015307', 'Mahmoudsalahhasib', '$2y$10$JutshIA5Jw8UpI5BXF0u/.gwfGS0hkLpD4J2xWv04XpY22uLZ6dyu', 'بني رافع منفلوط أسيوط ', 'Male', 'منفلوط ', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-08-21', ''),
(55, 'عبدالله  فوزي', '1999-08-19', 'abdallahessam108@gmail.com', '01068705441', 'abdallahessam', '$2y$10$B83VRpTPZtI2WNGY6yviG.O6ZRvjSVprggGzqpcao2b9ZwXuuZ1iG', 'الشرقية ', 'Male', 'ابوحماد ', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-08-21', ''),
(56, 'sherif ibrahim', '1978-09-11', 'sherifaga@mail.com', '01224473146', 'sherifaga', '$2y$10$2fnmNs/jpZtB2fM9wrZr1.fV6aWKgXuQidJ8NMRJXfbYtTaEuFCMu', '73 adly yaken st, minia', 'Male', 'minia', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-08-22', ''),
(57, 'سمير محمد', '1995-08-21', 'samir.mohamed.samir21@gmail.com', '+201065117506', 'Samir mohamed', '$2y$10$TXfmSaNwcXweYr5vHblBleZoxNJJ72MJz3dSORp0MmwdVl3TeqCF.', '10ميدان جامع عمرو ', 'Male', 'القاهره', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-08-22', ''),
(58, 'علي  الخولي', '1997-06-20', 'aya813977@gmail.com', '01001001000', 'Ali Elkhouly', '$2y$10$3mgJoaGaBzIf8imOSqyu/e4Qy.Zxxe6NmnHZSLndtQj1y4N8TBvja', 'اسوان', 'Male', 'اسوان', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-08-22', ''),
(59, 'Mohamed Tolpa', '1986-08-24', 'mohamed_tolpa@yahoo.com', '01060600380', 'mohamed_tolpa@yahoo.com', '$2y$10$oJNrAkGuOD6g6.2y40QDUuXSdSiJ4fEUju4/kQMbf8W.1unVieqqi', 'Alexandria', 'Male', 'Alexandria', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-08-22', ''),
(60, 'محمد  جمال حامد', '1987-02-09', 'mohamed.gm318@gmail.com', '01277261162', 'Mohamed gamal', '$2y$10$9rJJvqG/uaZupCOdKS4rIuhVcP7ABiuqYznte/5eVv2fZIUT2d5ga', 'الاسكندرية', 'Male', 'الاسكندرية', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-08-22', ''),
(63, 'نور علي', '2024-08-10', 'nourali189235@gmail.com', '01021091573', 'Nour ali', '$2y$10$73hd0TslV4vJJ.ArK8rF3O.8/nCxAx1t/w4UVhtwTTCbL7NgiHLj.', 'المنصوره', 'Male', 'المنصوره ', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-08-23', ''),
(64, 'محمد أحمد محيسن', '1999-09-13', 'Mohamedmhesn8642@gmail.com', '01014118642', 'Ahmed Mhesn', '$2y$10$i2PdbNmSNfGGTA4pEJ1ViORGacUWcnJojo8j1aIJH1sJ2OrZ77Rk2', 'كفر الشيخ', 'Male', 'كفر الشيخ', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-08-23', ''),
(65, 'Youssef Mohamed', '1999-08-28', 'youssef1@shahboun.com', '01064029402', 'يوسف شهبون', '$2y$10$1yyMLOXMD8Tv5lzY/jV9TOtju2qzunToGJA.jvtnKNCqoNNm5TxX2', '80 El-Khalifa El-Maamoun, Mansheya El-Bakry, Heliopolis, Cairo Governorate', 'Male', 'Cairo', NULL, '0', 33, '', '', 1, 20, '2024-08-24', ''),
(66, 'Ahmed  Ezzat ', '1986-02-20', 'possjaguar@hotmail.com', '01000439252', 'Ahmedezzat000', '$2y$10$2lu9WsoFbTMZyheVFfxbIOZ0b4hBlvodcpCHZ3aRg9SL7dFF0wDUu', 'Cairo', 'Male', 'Cairo', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-08-24', ''),
(67, 'محمد  النجار ', '1980-09-21', 'myelnagar@gmail.com', '01117084000', 'Mye', '$2y$10$PVpiADmiQ9CWNpkSj1gxDuT1p8J6JGGuP.e/UZngYm8EYjFAksMUy', 'شارع جامعة الدول العربية المهندسين ', 'Male', 'الجيزة ', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-08-24', ''),
(68, 'محمد  النجار ', '1980-09-21', 'nagar1105@yahoo.com', '01117084000', 'Mye1105', '$2y$10$m94Uhs.xoVeaKPvP9Ta8aeX3ZAkbNuwxOMCZujKirewoXuDNnEtri', 'المهندسين ', 'Male', 'الجيزة', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-08-24', ''),
(69, 'Mohamed  Elboshy', '1991-10-01', 'mo.elboshy91@gmail.com', '01013960824', 'Mohelboshy', '$2y$10$Epjm/CCplB30sUG4vMqX.eq3pUD5sLcZMzb/GsL7eqJmYXhHDfB/m', 'عمارة ٣٤ - المنطقة التاسعة -مدينة الشروق', 'Male', 'الشروق', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-08-25', ''),
(70, 'محمد فاروق زكي', '1984-09-30', 'mohamed55555521@yahoo.com', '01117244885', 'محمد فاروق', '$2y$10$jbyhy/bmNOx2WpyWUIE.Me7cc514UX5Iv4FqelSFMkwIxjpcaSBT6', 'المنيا', 'Male', 'ابوقرقاص', NULL, '0', 33, '', '', 1, 20, '2024-08-25', ''),
(71, 'محمود السعودي', '1994-12-09', 'mahmoudelsoudy18@gmail.com', '01112444233', 'Saudy Law Firm', '$2y$10$us5tw0niRtGXE822ISzh/enzpanraA30xJBI9h80wSwuIB7PPqPny', 'التجاريين - المقطم - القاهرة ', 'Male', 'القاهرة ', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-08-25', ''),
(72, 'اميرة جمال', '1984-08-13', 'amiragamal.dev@gmail.com', '01223437206 ', 'amiragamal.dev', '$2y$10$nqVdkRc9X7qqCM4zVAtxoOa5mymiAb7oAhfjHJNCrLiRFjYiwrzSG', 'منوفية', 'Male', 'المنوفية', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-08-26', ''),
(73, 'البير  انسي', '1982-07-13', 'alber.onsy@outlook.com', '01146066538', 'Alber', '$2y$10$ofMi5dUWHl5tNCYCuwS6GexMDNqLeNbl9zAC0j94e75rVhWcu4OWK', 'القاهرة', 'Male', 'القاهرة', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-08-26', ''),
(74, 'Amira Gamal Bata', '1984-06-13', 'amiragamal2.dev@gmail.com', '01223437206', 'amiragamal2.dev', '$2y$10$.qR0O0hhfEXlbVYVTZ/MSehmyVZKmngym4cEUre1WKrfLc1XFp1Ve', 'Egypt Cairo 44 Hafiz El Khayat st.,', 'Male', 'menofia', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-08-26', ''),
(75, 'ميرنا عبد العزيز', '2001-06-06', 'mirnaabdelaziz246@icloud.com', '01033060099', 'Mirna', '$2y$10$Eq/bmOK229atSRCMxBkQre/zX8Ojy81INfvCfxtenMTRYOiL85zQS', '6 شارع احمد عبد السيد المنشيه الجديده حدائق القبه القاهره', 'Female', 'القاهره', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-08-28', ''),
(76, 'رندا  حسن', '2024-08-15', 'randacms@gmail.com', '01063655402', 'Randa', '$2y$10$fdzC1fmHTV0WE43FaIp8SePP7qGkgVxwMHiCl4kSLhM4K6dyQUI1K', 'شارع جمال عبد الناصر .الحرفيين', 'Female', 'القاهره', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-08-28', ''),
(77, 'Omnia Elsayed', '2002-09-02', 'omniaalsayed789@gmail.com', '01016946924', 'Omnia12', '$2y$10$YCA/CKHWvIgAma2mzv9IjefjQssGpmk0BQP/PNVWNZqnGJrPQGg8G', 'الخانكة بجوار المطافي ', 'Female', 'الخانكة ', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-08-28', ''),
(78, 'اميرة عرابى ', '2004-12-05', 'amiraoraby512@gmail.com', '01275154222', 'Amira Oraby', '$2y$10$soSTo3eaafGYo3Sa0lR.d..t2Z/RIaGFYFzXJw4.NSDtQMeMyguYG', 'ميامى ', 'Female', 'اسكندريه ', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-08-28', ''),
(79, 'محمد عيسى  العشماوي', '1991-03-01', 'mm33isa@gmail.com', '0110099090', 'mm33isa', '$2y$10$ggVgSi.L7eyQkMcv879F9ed6JGcQ.V8frxRSBRAT8IWZN.4AugNC6', '25 عثمان محرم الطالبية هرم الجيزة ', 'Male', 'giza', NULL, '1', 33, '', '', 1, 20, '2024-08-28', ''),
(80, 'Mony Elsayed', '2002-09-02', 'omniaelsayed268@gmail.com', '01062760118', 'Mony', '$2y$10$MdUPoJzdCoLQtH9xNA2GnOBH3bPLGLF2jmqYt9NSqEQ/eBcoMUhUS', 'الخانكة بجوار المطافي ', 'Female', 'الخانكة', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-08-28', ''),
(81, 'هاجر  حسين', '2003-08-22', 'hagarloka250@gmail.com', '01206718524', 'Hajar', '$2y$10$jbNYY8CdIims97zAn77Zx.UQ3iqfKC1wIuK8KqVtwmEKh.D71jrE2', 'منطقة نادي المقاولين العرب - نادي السكة الحديد ( محيط مدينة نصر)', 'Female', 'Cairo', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-08-28', ''),
(83, 'ابراهيم  بكرى', '1973-01-19', 'lbraheeem2011@yahoo.com', '01005788470', 'Ibrahim BAKRY ', '$2y$10$RUS.MdkNn5SeRQpoK1xLdONkZpq6B7uzLpRamtaB6iYfcsj2VgkBq', 'الشيخ زايد ', 'Male', 'الجيزة', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-08-29', ''),
(84, 'كريم محمد', '1989-12-10', 'kareem.mohamed.mohy@gmail.com', '01000818905', 'Kareemlaw', '$2y$10$Qe.hFWB4pRwLqlX.1KCyFOeEWJkROLP7J.ZsG.E/saovhXr3wEL9m', '٣٥ عمارات المصرية ابني بيتك الثانية حدائق اكتوبر', 'Male', 'october garden', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-08-29', ''),
(85, 'Youssef', '', 'youssef@shahboun.com', '+20 106 402 9402', 'aaa', '$2y$10$XuCSIa2WZvV/FVc3sqxiFuuht01zCns/2nmaXpou2yHySbyXwSmSO', 'Roxy square', 'Male', 'Cairo', NULL, NULL, 33, '', '', 0, 31, NULL, ''),
(86, 'إبراهيم  رفاعي', '1979-02-06', 'hemahemond@gmail.com', '01224222433', 'Ebrahim', '$2y$10$3M6oPtegs7cfxPoqxELBK.5cX1syQlFdGNfvLwiwsx.ZCn3P7wlxm', 'المطرية', 'Male', 'القاهرة', NULL, '1', 33, '', '', 1, 32, '2024-08-31', ''),
(87, 'كرم ادهم', '1985-08-12', 'vb7_kkk@yahoo.com', '٠١٠٨٢١٧٢١٩', 'Karamsaad', '$2y$10$.d/0CxY07ekHyy9wv.ipTut/2KwDmhIW3zSyBz/sED/acNdyp02pi', 'قنا', 'Male', 'Nagehamady', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-09-02', ''),
(88, 'ماجد جاد', '1987-09-29', 'majedgost@hotmail.com', '01005092625', 'majedgost', '$2y$10$c9urhPMhP3/ANnvV1z1DaOCjN8E4sqz0TjvRyooCfgjM8Hu81xt.G', 'بورسعيد', 'Male', 'بورسعيد', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-09-02', ''),
(89, 'محمد عمارة', '1980-08-19', 'msalah010@hotmail.com', '01005866424', 'Mohamed_salah', '$2y$10$UgBlKDciFBR2fWxbuWPGbO3pLpQxNPynwbsUr.swjaUq.Tv//K.wO', 'حدائق الأهرام ', 'Male', 'الجيزة ', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-09-02', ''),
(90, 'احمد مصطفي  وهبه ', '1993-12-05', 'ahmedmostafa5121993@gmail.com', '٠١٠٢٥٥٥٦٠٥٥', 'احمد وهبه', '$2y$10$14q3tv5EB6yt1HmabT67UO3jWTLzuljmrCPIg4WobNfK3k4A9ZrKe', '٢٧ محمد خلف بالدقي ', 'Male', 'الجيزة', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-09-02', ''),
(91, 'احمد  الالفى', '1988-06-04', 'ahmedabasselalfey55@gmail.com', '01111007895', 'احمد الالفى', '$2y$10$yajM9jEwk3zTiC8kHPg12eO5l4.xREeFnT8PJS4ekfpicHrcwJAMG', 'امبابه ', 'Male', 'الجيزة', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-09-03', ''),
(92, 'طارق يحيى  ذكي', '1989-10-10', 'tarqyhyydhky@gmail.com', '01114225214 ', 'طارق يحيى ذكي', '$2y$10$9NxqBNf.L4ls3dBxYotzDef0y/ntNl6Aje0dPH8y/KXWwt2L.yt6O', 'المتانيا العياط الجيزه ', 'Male', 'العياط', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-09-03', ''),
(93, 'حمدي  فرجاني ', '1971-03-23', 'hamdy.frgany355@gmail.com', '01006715058', 'Hamdy fergany', '$2y$10$ih9.Oz7njyoffMdXq0yUc.KNGP5SS2BvPDBGqUIcLzbX9CoWEtwZC', 'مدينة السادات ', 'Male', 'مدينة السادات123456789Hh', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-09-03', ''),
(94, 'محمد سيد القرمانى', '1983-10-04', 'mo.oramany@gmail.com', '01286050508', 'ElOramany', '$2y$10$/qWo0knKATpqPxb.oZVwLuJqa/JFksXkYFYaBmDKqiNN7hyZUiDm2', '70 ش 9 - المعادى', 'Male', 'القاهرة', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-09-03', ''),
(95, 'محمد  سيد السيد علي ', '1982-07-06', 'mohamed01200594148@gmail.com', '01200594148', 'محمد عربي', '$2y$10$RZ97PDG36cwg6h4QGR92oO0O6OOIje/iR47tdlb3sDm.pR0ywp15O', '١شارع السيد ابراهيم _الساحل ', 'Male', 'الساحل القاهره', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-09-03', ''),
(96, 'احمد شادي', '2024-08-21', 'ahmedshadyahmed160@gamil.com', '01007558079', 'احممد', '$2y$10$nTZmpL6t4CQuygsFL/x1quaXl/Ai.wezdExaiIAatpk6QN1ZWC.Ey', 'شبين القناطر', 'Male', 'شبين القناطر', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-09-03', ''),
(97, 'عمرو السرسي', '1996-01-31', 'amralsersy31@gmail.com', '01014842466', 'Amr', '$2y$10$4ezLO35GTt2xY6UgRKjVJelugkHg9FBt57Xkac0Gz4fx2O7vjvVAe', 'المنوفية شبين الكوم', 'Male', 'شبين الكوم', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-09-03', ''),
(98, 'Randa Osama', '1990-10-05', 'rudyosama72@gmail.com', '01127492570', 'Randaosama', '$2y$10$Ksckmb/vYd0Trt.Xi0gb8OrHhVcH/7FjWBBap9sQ.ayaMddF8pzdy', '85 ش العشرين فيصل ', 'Female', 'Giza', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-09-04', ''),
(99, 'امير محمد', '1988-07-15', 'amir7876i887@gmail.com', '01018392423', 'Amir90', '$2y$10$thEo3vNAvssDv6mc3IfgbOlEWpOlASIyZ2C7KrDUBp0fkK0rLrTjq', '21abedasmed street', 'Male', 'Cairo', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-09-04', ''),
(100, 'أحمد  الشاعر ', '1996-12-25', 'ahmedsaber974@gmail.com', '01224022189', 'Ahmed_el', '$2y$10$EacXkAdD/VrFjfs8p1yDvOZdhCS48Ro/35.SzhFUXgKznuMIzV8Uu', 'سلامون القماش - المنصورة - الدقهلية', 'Male', 'المنصورة ', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-09-04', ''),
(101, 'طارق ابراهيم', '1988-04-19', 't.ibrahem1988@gmail.com', '+20 1015057707', 'طارق', '$2y$10$phGK5SKqS8xQwxCw9sfo1OExYHd3.7iul7KTWH1FbA4nXhsCSNpL.', 'مدينة نصر ', 'Male', 'مدينة نصر ', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-09-04', ''),
(102, 'وليد البنا', '1981-09-14', 'elbana.office@gmail.com', '+201221529352', 'وليد البنا ', '$2y$10$Xgp0ySsoK7IwcZ6ObTMiceLUBzJ2lRClAmFP.0xwUSQujyZI4/smi', 'برج الشرطه شارع كورنيش النيل بجوار مجمع محاكم شبرا الخيمه الدور الثاني ', 'Male', 'Shubra El Kheima, Qalyubia', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-09-04', ''),
(103, 'ايناس ابراهيم', '1968-11-26', 'Hossamelgamal@hotmail.com', '01550165644', 'Inasibrahim', '$2y$10$Uim66IWuaJfnZ9cL/hq/9Oh8r6nWbDmAzvhwPcfHwPTbcVRm7G4ce', '١٢ شارع الفريق محمود شكرى', 'Female', 'القاهرة ', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-09-04', ''),
(104, 'وائل عبد الحميد', '1975-01-01', 'wael.a.elhamid@gmail.com', '٠١٠٤٠٨٨٠٨٧٣', 'wael', '$2y$10$0CswgdUx1lGOi/k8hPbtF.3O7o4lPMWSpottpuZ4BtxJklpZ0mx2S', 'المنصورة - الدقهلية', 'Male', 'المنصورة', NULL, '0', 33, '', '', 1, 20, '2024-09-04', ''),
(105, 'Fouad Hagag', '1989-12-15', 'fouadhagag318@yahoo.com', '01004749686', 'Fouad', '$2y$10$nlcZ1kc1IIAuw8fmecgFGOc//B../fJVsoJo5NIuQY35lKQsy.Xiy', 'Dar masr', 'Male', 'Cairo', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-09-04', ''),
(106, 'حمدي  عادل ', '1992-12-02', 'hamdy.adel92@gmail.com', '01050888978', 'حمدي عادل ', '$2y$10$KQ8.YLYFPP48IPDcIJ3kvu6mx2sMLkPnX4hscqLDjINXe5VikRX9S', 'المعادي ', 'Male', 'Cairo ', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-09-05', ''),
(107, 'باهر الحضري', '1980-06-01', 'info@baherjames.net', '01102888811', 'Baher77', '$2y$10$kob5C1/7lB0a0fPblRET5.f9d6qXTifSJ8We/7.Ja6cqQWqqRYpAu', '160 ابراج النصر المقطم', 'Male', 'القاهرة', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-09-05', ''),
(108, 'نصحى  تادرس ', '1973-09-23', 'nossehy@gmail.com', '01225013454', 'نصحى عياد ', '$2y$10$QEaKwS3TLPePpJzid6J4xehbNDXAOB4u19yzsf95sPmAOIVJCIABe', 'مغاغة /  المنيا', 'Male', 'مغاغة', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-09-05', ''),
(109, 'Mohamed Mohsen Saad', '1990-08-14', 'mohammed_moohsen@yahoo.com', '01004211500', 'Mohamed_mohsen', '$2y$10$UvsBVzQu9dMq42Jjg8qM.Op73d/ZQc9S2ZxcgU/adPjIor8Ee61pS', 'Damittaa', 'Male', 'Damittaa', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-09-05', ''),
(110, 'أحمد عادل ', '2024-08-23', 'ahad22141@gmail.com', '01003134539', 'أحمد عادل ', '$2y$10$BcpLbja58DNdvv4bNWwWNuTsPJkA9sFtMCVGQFMnF7gNoZwpk/w6q', 'اشمون ', 'Male', 'Ashmoun', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-09-05', ''),
(111, 'anas anas', '2024-08-24', 'cv0alnajjar@gmail.com', '0117750318', 'ؤررؤ', '$2y$10$WALbccbKFX81S/M45gu48ObajYwYzfjp6cXBYSMa9QxuUh3x5INLC', 'Damascus', 'Male', 'syria', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-09-05', ''),
(112, 'd d', '2024-08-23', 'jar@gmail.com', '0117750318', 'ddd', '$2y$10$sijD8ETIA34D1LHRqOBodOj0bXhmZNNzctnveEmeOCpixc0HL9x0C', 'Damascus', 'Male', 'syria', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-09-05', ''),
(113, 'd anas', '2024-08-23', 'fjjar@gmail.com', '0117750318', 'honig', '$2y$10$puUs3wcW2Hv/KZ3OpN.LQ.d7sphhDJwq/HQGwdU1btf/Oq2tytIyy', 'Damascus', 'Male', 'syria', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-09-05', ''),
(114, 'حسين عادل', '1990-08-22', 'lwr.husseinadel@gmail.com', '٠١٢٢٢٢٦٦٦٤٥', 'حسين عادل', '$2y$10$xJ6jM9f0bA/0LLazEhkmAO1YpkcMXEUU0.LyYJGTPpdDm1bvPQP3S', '٣٢ شارع سعد زغلول', 'Male', 'الاسكندية', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-09-05', ''),
(115, 'Ahmed rabeaa', '1980-05-20', 'mada0743@gmail.com', '01065237079', 'AhmedOmara', '$2y$10$wlBT9tCC/TIb6Il.Bx3ihuEA2Sw76gnpRdk6XL3sheVZPAB9me83i', 'Qalubia- Banha- warwarh', 'Male', 'Qalyubia', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-09-05', ''),
(116, 'محمد جمعه ', '1992-09-28', 'medogomaa6@gmail.com', '+201062589556', 'MohamedGomaa', '$2y$10$icQDfz6PoSOmW3DmRDLVE.apWuvLiYcqfK6Jt.dR1nOidZNt9qSjy', 'كفر الشيخ ', 'Male', 'كفر الشيخ', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-09-05', ''),
(117, 'وائل الحصرى', '2024-08-23', 'hosreywael@yahoo.com', '01278000913', 'وائل', '$2y$10$HkmvsNucbVpmV2e2kuld6eqnSmLVZv.Wjq9ukBDAT1v3fxxFAwwmG', '30س ابراج العالميه مصر القديمه', 'Male', 'القاهره', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-09-05', ''),
(118, 'نادية  مختار', '2006-04-23', 'nadiamokhtar750@gmail.com', '01151972305', 'nadiamokhtar', '$2y$10$PYhbonb7zi4gtEpa43MOVuHNgpHQ.vMUBlAiLCfCXwUzo.yNpYv..', 'كفر الشيخ ', 'Female', 'فوه', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-09-05', ''),
(119, 'mohamed atia', '1980-08-13', 'ac_mohamedatia@yahoo.com', '01006553332', 'mohamed', '$2y$10$nwWt7Yrh9bzN6mKQhsUwdOva6otbAEJidlwYNaLJ2M7o2gkhkAmiu', 'lmm', 'Male', 'cairo', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-09-06', ''),
(120, 'محمد أبو الفتوح', '1990-12-20', 'mohamed_elsayed909011@yahoo.com', '01159899983', 'M.Abuelfottouh', '$2y$10$pXCBLR21N0KYz5ohRZumX.bD9VbgXoL2gx4FUeHr5bbAxG3fWQ7HO', 'الشيخ زايد', 'Male', 'Cairo', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-09-06', ''),
(121, 'علاء الدين عوض', '1993-01-01', 'awadalaa910@gimal.com', '٠١٠٠٩٢٧١٧٠٨', 'علاء عوض', '$2y$10$9oIGVfckfixXRTZyGmZSJu6aSDRQvB7Lo/ST4v.XZ85Sgr7dqQl4e', 'فاقوس - محافظة الشرقيه', 'Male', 'فاقوس شرقية', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-09-06', ''),
(122, 'Morad Mohamed ', '1986-01-02', 'amlbdalhmyd868@gmail.com', '01222962522', 'MWaelM', '$2y$10$OuCc9kdKf9C/hncR5tK7oOf9.jTCq07f.jYGv1bgazIUZnbpOESJ6', 'شبرا - القليوبية ', 'Male', 'قليوب ', NULL, NULL, 33, '', '', 1, 20, '2024-09-06', ''),
(123, 'هيبه عوض', '1979-10-07', 'hgedapocy79@gmail.com', '01007434305', 'هيبه جويده', '$2y$10$5quFyGlwJ5sgGB8598WrYONWGmyodoskhTdhkyFR9vdUuoYMdt6v6', 'طيبه 1 اول العامريه الاسكندريه', 'Male', 'العامريه', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-09-06', ''),
(124, 'محمد شلقامى ', '1975-04-01', 'shalkamylawegypt@gmail.com', '01270007411', 'shalkamy_law', '$2y$10$EU939JFcB5oyloMhDQFxbeEEnLC5LTlUAR6G.BGERuYaaYqokDS02', 'الشيخ زايد. الجيزة ', 'Male', 'الشيخ زايد ', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-09-06', ''),
(125, 'محمد رمضان حسين رمضان  رمضان حسين ', '1992-02-14', 'mashrefzaza@yahoo.com', '٠١٢٢٠٧٥١١١٧', 'محمد رمضان ', '$2y$10$HdQVS1JBM8Dh7p2eeCc29eD.9Eof1ZotgIVsbjBreQNZUeKnbhk9m', 'بلبيس ', 'Male', 'بلبيس ', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-09-06', ''),
(126, 'محمد  خلف ', '1988-12-12', 'mo.khalaf1988@gmail.com', '01003454098', 'محمد خلف ', '$2y$10$dVgLNJ2h1OaQoQtwbPz6A.X.DjPpWCsVRMKgr70rmZy8yLR991Tmm', 'Sohag hiy Rashid', 'Male', 'sohag', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-09-06', ''),
(127, 'محمد سيد ناجي', '1994-08-15', 'syd196240@gmail.com', '01063404690', 'محمد سيد ناجي', '$2y$10$j.7Y5Gi6M3IwJPQIWTzro.ReZE3te.dFKUlgyAoBueW2UeDr/ykV2', 'مدينه السلام القاهره', 'Male', 'القاهره', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-09-07', ''),
(128, 'سامى  الصياد ', '1985-02-01', 'Samyelsayad4000@gmail.com', '٠١٠٠٠٢٥٦٠٦٦ ', 'Samy ', '$2y$10$mdIPJZ5oH2HEoB4sZaYmyOYDp5UeCo2LAtK/Rc25w5zPmVHoDO0qy', 'المحله الكبرى ', 'Male', 'المحله الكبرى ', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-09-07', ''),
(129, 'محمود عيد', '1997-08-20', 'mahmoudelhkeem66@gmail.com', '01018288599', 'hkeem66', '$2y$10$.3P/dy.9jcBwWAi8kUr4A.uFZeRCbmS/y7eFFM8Z6.Qysn10NgQWG', 'الحادقة ، بندر الفيوم ، محافظة الفيوم', 'Male', 'الفيوم', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-09-07', ''),
(130, 'فوزى محسوب', '2024-08-25', 'fawzymahsoub@gmail.com', '٠١٠٠٩٩٧٧٣٦١', 'فوزى محسوب', '$2y$10$ruqw5UgSALc7VPC7SonsG.2V5LAjNYrmyBmm0tFJ4En0P0B/pTOOS', 'الزقازيق', 'Male', 'القاهرة', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-09-07', ''),
(131, 'محمد مصطفى سيد  عمر', '1996-08-18', 'mohamedmastafa868@gmail.com', '01126974291', 'Mohamed bek MosTafa ', '$2y$10$5j3zDL0VgtgzVBJ/jOJZk.6HFkXgKFRhbTyf6o/TOCz.wgsBTNnu6', 'الوادى الجديد/باريس', 'Male', 'باريس', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-09-07', ''),
(132, 'آلمِسّتشِآࢪ/ آلمِسّتشِآࢪ/أحمِدآلفقي', '1981-08-07', 'Ahmed.Elfeky@Lawyer.com', '01114858797', 'Elfeky.lawyer', '$2y$10$EEKGSNMUZHR7D2gAjCjhUOOWnw9un8fBAWhq.uy55.WYanY6YMnK2', 'شارع الايمان الخيرى من شارع فريد ندا', 'Male', 'بنها', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-09-07', ''),
(133, 'بدر شوقات بدر  زيد', '1969-01-01', 'badrzeed71@gmail.com', '01022332661', 'بدر شوقات', '$2y$10$WIqhlzTRKhaiOXgFzKcHXO4vi3uzwv.IRLbBflExMw4.nSIGciTvm', '١ شارع محى الدين ابو العز الدقى الجيزة', 'Male', 'الدقى', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-09-08', ''),
(134, 'ايمان سمير', '2000-06-02', 'mena2685.love@gmail.com', '01144290329', 'Emy', '$2y$10$Hk.5RNVb.URtye0v6Wi3WeyU0Ye7nITAIWFgPPrB9pUUGcbfNB6YK', 'المعادي ', 'Female', 'القاهره', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-09-09', ''),
(135, 'محمود رمزى', '2000-09-15', 'MRamzy@yahoo.com', '01017711552', 'MRamzy', '$2y$10$pCAp38/bpW.krDMFteROFu6/ApCux4C.Z0rWIN4bw.qWUGvKaN1ra', 'قنا', 'Male', 'الوقف', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-09-09', ''),
(136, 'مصطفى  الطويل ', '1999-08-27', 'shroukebrahem55@gmail.com', '٠١١٥٣٥٥٣٦٢٢', 'مصطفى الطويل', '$2y$10$/vzXxu0qwRFdzKrodlDgo.SvSXZiEa35UM/b4G46pq.R36SIPvMQC', '٢٨٨ طريق الحريه ', 'Male', 'الاسكندريه', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-09-09', ''),
(137, 'مصطفى  حافظ', '1990-03-06', 'sarasaied707@gmail.com', '01220079444', 'مصطفى حافظ', '$2y$10$pr/0WCjit1wsM0S8JQAy7.ka0/RTVsEUjsTOKGBr6X70MJaWUsUY2', 'لوران', 'Male', 'الاسكندرية', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-09-09', ''),
(138, 'سيد عامر', '2003-06-12', '01000499158sayed@gmail.com', '01149929322', 'Sayed elbatal', '$2y$10$neBt3mzOFp..N8sJPIk9..7PDxJ6UlTax.fERtsbHFNW6.baO8w96', 'كفر الرفاعي', 'Male', 'العياط', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-09-09', ''),
(139, 'روان  هشام محمود', '2024-08-30', 'rewanhesham000@gmail.com', '01062220563', 'Rewan30', '$2y$10$PPgY1SNTK8COkRYe5soO4OqAXK5XXFOvs.LqffmQpkk3iPFyTMtQK', 'طوسون... الاسكندريه ', 'Female', 'الاسكندريه ', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-09-09', ''),
(140, 'abdelrahman Elkafrawy‏', '1995-08-13', 'abdelrahman.elkafrawy1995@gmail.com', '01111418994', 'abdelrahman.elkafrawy1995@gmail.com', '$2y$10$HAhnHZVBePEPDU0IdqV/DOih/jX2RzXoNqOyohnlcReIzdvHSWVpu', '28 شارع زغلول مشعل الهرم الجيزه ', 'Male', 'الهرم', NULL, NULL, 33, '', '', 1, 20, '2024-09-09', ''),
(141, 'Hassan El Motagally', '2001-10-29', 'Hassanmotagally29@gmail.com', '01100711338', 'Hassan Motagally', '$2y$10$f0T88Oypd8w2N2dilf/WbOA0C4gceM396BU43KVA0q4xYHNdSvs0G', 'فيلا 17 الحي 3 مج 2 مدينه الروق', 'Male', 'الشروق', NULL, NULL, 33, NULL, NULL, 1, 20, '2024-09-15', ''),
(142, 'hosni', '2000-01-01', 'syria@gmail.com', '+48 512 345 678', 'syria', '$2y$10$i3YSD8B9vgNsPjJlqz7RmOmkxYqAH/bqfF7OBM8wfPrrbwYGpjHKi', 'syria', 'Male', 'syria', NULL, NULL, 33, '02A544444', '03145567777', 0, 36, NULL, 'en');

-- --------------------------------------------------------

--
-- Table structure for table `managers_office`
--

CREATE TABLE `managers_office` (
  `id` int(11) NOT NULL,
  `manager_name` text NOT NULL,
  `date_of_birth` varchar(200) NOT NULL,
  `manager_email` varchar(250) NOT NULL,
  `manager_phone` text NOT NULL,
  `username` text NOT NULL,
  `manager_password` text NOT NULL,
  `manager_address` text NOT NULL,
  `manager_gender` text NOT NULL,
  `manager_city` text NOT NULL,
  `manager_national` text NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `manager_passport` text DEFAULT NULL,
  `office_id` int(11) NOT NULL,
  `stop` int(11) DEFAULT NULL,
  `stop_date` text DEFAULT NULL,
  `lang` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `managers_office`
--

INSERT INTO `managers_office` (`id`, `manager_name`, `date_of_birth`, `manager_email`, `manager_phone`, `username`, `manager_password`, `manager_address`, `manager_gender`, `manager_city`, `manager_national`, `role_id`, `manager_passport`, `office_id`, `stop`, `stop_date`, `lang`) VALUES
(9, 'shadi', '', 'anas0alnajjar@gmail.com', ' 31 117 750 318', 'hosni', '$2y$10$ZfOjVIEf.ihc9BGwywLaH.HL2tgI7hsrRy/d77uKD0bdbMl1k3Lce', '', 'Male', 'syria', '', 36, '', 20, NULL, NULL, ''),
(12, 'مكتب1', '', 'youssefshahboun1900@gmail.com', '01064029402', 'مكتب1', '$2y$10$qMOIk7llTGpjW8hFznmk7.KS1lQtJQ4qx.rap00hE3j66BOFze.MW', 'عنوان1', 'Male', '', '', 38, NULL, 25, 1, '2024-08-17', ''),
(13, 'ahmed azab3', '', 'imediapro696@gmail.com', '01064825572', 'ahmed azab 3', '$2y$10$hx2djfGQvS5lwsgFP/yXWOKMVv3fWcECOJfVmmS/kFB2i5GHnN73G', '90 الجيزه هرم ', 'Male', '', '', 38, NULL, 27, 1, '2024-08-17', ''),
(14, 'Amira', '', 'amira@say-assist.com', '01020004430', 'Amira', '$2y$10$h8A.OEhhxOyFqiHl6ZJNNOJgv2fP4pto0oYgl2qcAqoLzlepYEPeC', 'Cairo', 'Male', '', '', 38, NULL, 28, 1, '2024-08-24', ''),
(15, 'fdfdsf', '', 'ahmedgemy.egypt@gmail.com', '01223437206', 'amiragamal3.dev', '$2y$10$0SrzHsRMn6FCRCvkObvOsuTj307FMmrLtNhtWwTjFsVU3ZiIh/53G', 'sdfdsf', 'Male', '', '', 38, NULL, 29, 1, '2024-08-26', ''),
(16, 'Youssef', '', 'youssef@shahboun.com', '01064029402', 'ppp', '$2y$10$4HQ8z5BTCrhNx9FQEIjZNutTZ0nAgsoxDgAK.BWMvlt/yf9O0A7oi', 'روكسي', 'Male', '', '', 38, NULL, 31, 1, '2024-08-31', ''),
(17, 'Ahmed Ezzat', '', 'ahmedezzatoffice@gmail.com', '1000439252', 'Ahmedezzat', '$2y$10$qkucTnKxdC.Uz591zSP88.QF1d82A56L4e9cRHNdrlVZ4gY3i3bqG', 'Cairo', 'Male', '', '', 38, NULL, 33, 1, '2024-09-02', ''),
(18, 'وائل محمد فهمى خليل', '', 'w.avocato@gmail.com', '01223687776', 'w.avocato', '$2y$10$q9UNRowesCAvL4tMQnbmA.5xk9UQrjGccZijDgwdVH9gI9ucsNxEC', 'بلبيس الشرقيه', 'Male', '', '', 38, NULL, 34, 1, '2024-09-06', ''),
(19, 'روان هشام محمود', '2024-09-04', 'rewanhesham000@gmail.com', '01062220563', 'roro', '$2y$10$9BADzY7GUMD5Vr1obSnBo.8vfhunQc5KREQ/Lu9k7tpRG.3ASiZPe', 'طوسون .. الاسكندريه ', 'Female', 'Daraa', '', 38, '', 36, 1, '2024-09-09', 'en'),
(20, 'Ahmed ', '', 'ahmedfouda90a@gmail.com', '01145150953', 'Ahmed ', '$2y$10$VFhmiDbiAVVgzvgQid.byeuyOuuNMRzCYeGgXpO0CANJ.XmB0qwtq', 'Zagazig ', 'Male', '', '', 38, NULL, 37, 1, '2024-09-11', ''),
(21, 'Ahlalqanon office', '', 'engmohamedzain563@gmail.com', '01221308040', 'Ahlalqanon office', '$2y$10$eI2VVabPUqqHpj7o7deMK.Y2fQo6Fk36aLCrEaj9hoD0xfAHcfIM2', '95 الجيزه هرم ', 'Male', '', '', 38, NULL, 38, 1, '2024-09-13', '');

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

CREATE TABLE `message` (
  `message_id` int(11) NOT NULL,
  `sender_full_name` varchar(100) NOT NULL,
  `sender_email` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `date_time` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `message`
--

INSERT INTO `message` (`message_id`, `sender_full_name`, `sender_email`, `message`, `date_time`) VALUES
(11, 'Nour Ali', 'nourali189235@gmail.com', 'الاكونت مش بيفتح معايا', '2024-08-10 05:49:07'),
(12, 'محمد أحمد محيسن', 'mohamedmhesn8642@gmail.com', 'خطأ في تسجيل الدخول', '2024-08-11 00:45:46'),
(13, 'Omnia Elsayed', 'omniaalsayed789@gmail.com', 'الحساب غير مفعل ', '2024-08-15 09:21:04'),
(14, 'إبراهيم رفاعي المحامي ', 'hemahemond@gmail.com', 'نسيت الباسورد ', '2024-08-23 14:07:55'),
(15, 'بدر شوقات بدر زيد ', 'badzeed71@gmail.com', 'ارجوا تفعيل النسخة التجريبية', '2024-08-26 08:37:47'),
(16, 'مصطفى حافظ', 'shroukebrahem55@gmail.com', 'can\'t log in probably forgot my password', '2024-08-27 03:25:19');

-- --------------------------------------------------------

--
-- Table structure for table `offices`
--

CREATE TABLE `offices` (
  `office_id` int(11) NOT NULL,
  `office_name` text DEFAULT NULL,
  `stop` int(11) NOT NULL DEFAULT 0,
  `admin_id` int(11) DEFAULT NULL,
  `stop_date` text DEFAULT NULL,
  `footer_text` text DEFAULT NULL,
  `header_image` text DEFAULT NULL,
  `default_office` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `offices`
--

INSERT INTO `offices` (`office_id`, `office_name`, `stop`, `admin_id`, `stop_date`, `footer_text`, `header_image`, `default_office`) VALUES
(20, 'أهل القانون', 0, 1, '', 'أهل القانون لخدمات المحامين', 'header_66a23b9cb9377.png', 1),
(22, 'مكتب المستشار شنوده اسكندر', 1, 1, '2024-07-08', 'مكتب المستشار شنوده اسكندر', 'header_66a21d5d27348.png', 0),
(23, 'مكتب المستشار أحمد مصطفي', 1, 1, '2024-08-08', '', NULL, 0),
(24, 'shadi office', 1, 1, '2024-08-09', NULL, NULL, NULL),
(25, 'المستشار مكتب1', 1, 1, '2024-08-17', NULL, NULL, NULL),
(27, 'ahmed azab 3', 1, 1, '2024-08-17', NULL, NULL, NULL),
(28, 'Say Assist', 1, 1, '2024-08-24', NULL, NULL, NULL),
(29, 'اسم المكتب', 1, 1, '2024-08-26', NULL, NULL, NULL),
(31, 'شركة وقتك للتحول الرقمي', 1, 1, '2024-08-31', NULL, NULL, NULL),
(32, 'مكتب المستشار إبراهيم رفاعي', 1, 1, '2024-09-30', 'مكتب المستشار إبراهيم رفاعي', 'header_66c297ff62561.png', 0),
(33, 'Ahmed Ezzat', 1, 1, '2024-09-02', NULL, NULL, NULL),
(34, 'وائل فهمى للمحاماه', 1, 1, '2024-09-06', NULL, NULL, NULL),
(35, 'مكتب تجربيي اهل القانون', 0, 1, '', 'مكتب تجربيي اهل القانون', 'header_66ccd2e19ff2e.png', 0),
(36, 'اهل القانون', 1, 9, '2024-09-09', '', NULL, 0),
(37, 'Fouda', 1, 1, '2024-09-11', NULL, NULL, NULL),
(38, 'Ahlalqanon office', 1, 1, '2024-09-13', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `overhead_costs`
--

CREATE TABLE `overhead_costs` (
  `id` int(11) NOT NULL,
  `office_id` int(11) DEFAULT NULL,
  `pay_date` text DEFAULT NULL,
  `amount` int(11) DEFAULT NULL,
  `notes_expenses` text DEFAULT NULL,
  `pay_date_hijri` text DEFAULT NULL,
  `type_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `overhead_costs`
--

INSERT INTO `overhead_costs` (`id`, `office_id`, `pay_date`, `amount`, `notes_expenses`, `pay_date_hijri`, `type_id`) VALUES
(12, 20, '2024-07-22', 10000, '', '1446-01-16', 19),
(13, 20, '2024-07-22', 1999, '', '1446-01-16', 19);

-- --------------------------------------------------------

--
-- Table structure for table `page_permissions`
--

CREATE TABLE `page_permissions` (
  `id` int(11) NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `page_name` text DEFAULT NULL,
  `can_read` int(11) DEFAULT NULL,
  `can_write` int(11) DEFAULT NULL,
  `can_add` int(11) DEFAULT NULL,
  `can_delete` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `page_permissions`
--

INSERT INTO `page_permissions` (`id`, `role_id`, `page_name`, `can_read`, `can_write`, `can_add`, `can_delete`) VALUES
(5632, 34, 'control', 1, 0, 0, 0),
(5633, 34, 'cases', 1, 0, 0, 0),
(5634, 34, 'payments', 1, 0, 0, 0),
(5752, 37, 'home', 1, 0, 0, 0),
(5753, 37, 'control', 1, 0, 0, 0),
(5754, 37, 'cases', 1, 1, 1, 1),
(5755, 37, 'sessions', 1, 1, 1, 1),
(5756, 37, 'add_old_session', 0, 0, 1, 0),
(5757, 37, 'expenses', 1, 1, 1, 1),
(5758, 37, 'expenses_sessions', 1, 1, 1, 1),
(5759, 37, 'payments', 1, 1, 1, 1),
(5760, 37, 'attachments', 1, 0, 1, 1),
(5761, 37, 'lawyers', 1, 1, 1, 1),
(5762, 37, 'clients', 1, 1, 1, 1),
(5763, 37, 'assistants', 1, 1, 1, 1),
(5764, 37, 'managers', 1, 1, 1, 1),
(5765, 37, 'expense_types', 1, 1, 1, 1),
(5766, 37, 'offices', 1, 1, 1, 1),
(5767, 37, 'courts', 1, 1, 1, 1),
(5768, 37, 'adversaries', 1, 1, 1, 1),
(5769, 37, 'case_types', 1, 1, 1, 1),
(5770, 37, 'judicial_circuits', 1, 1, 1, 1),
(5771, 37, 'documents', 1, 1, 1, 1),
(5772, 37, 'notifications', 1, 1, 1, 1),
(5773, 37, 'message_customization', 1, 1, 1, 1),
(5774, 37, 'inbox', 1, 0, 0, 1),
(5775, 37, 'outbox', 1, 0, 0, 0),
(5776, 37, 'join_requests', 1, 0, 1, 1),
(5777, 37, 'roles', 1, 1, 1, 1),
(5778, 37, 'logo_contact', 1, 1, 0, 0),
(5779, 37, 'user_management', 1, 1, 1, 1),
(5780, 37, 'profiles', 1, 1, 1, 1),
(5781, 37, 'import', 1, 1, 1, 0),
(5889, 38, 'control', 1, 0, 0, 0),
(5890, 38, 'cases', 1, 1, 1, 1),
(5891, 38, 'sessions', 1, 1, 1, 1),
(5892, 38, 'calendar', 1, 1, 1, 1),
(5893, 38, 'events', 1, 1, 1, 1),
(5894, 38, 'add_old_session', 0, 0, 1, 0),
(5895, 38, 'expenses', 1, 1, 1, 1),
(5896, 38, 'expenses_sessions', 1, 1, 1, 1),
(5897, 38, 'payments', 1, 1, 1, 1),
(5898, 38, 'attachments', 1, 0, 1, 1),
(5899, 38, 'lawyers', 1, 1, 1, 1),
(5900, 38, 'clients', 1, 1, 1, 1),
(5901, 38, 'assistants', 1, 1, 1, 1),
(5902, 38, 'expense_types', 1, 1, 1, 1),
(5903, 38, 'courts', 1, 1, 1, 1),
(5904, 38, 'case_types', 1, 1, 1, 1),
(5905, 38, 'judicial_circuits', 1, 1, 1, 1),
(5906, 38, 'documents', 1, 1, 1, 1),
(5907, 38, 'notifications', 1, 1, 1, 1),
(5908, 38, 'inbox', 1, 0, 0, 1),
(5909, 38, 'outbox', 1, 0, 0, 0),
(5910, 38, 'user_management', 1, 1, 1, 1),
(5911, 38, 'adversaries', 1, 1, 1, 1),
(5912, 38, 'profiles', 1, 1, 1, 1),
(5913, 33, 'control', 1, 0, 0, 0),
(5914, 33, 'cases', 1, 1, 1, 1),
(5915, 33, 'sessions', 1, 1, 1, 1),
(5916, 33, 'calendar', 1, 1, 1, 1),
(5917, 33, 'events', 1, 1, 1, 1),
(5918, 33, 'add_old_session', 0, 0, 1, 0),
(5919, 33, 'expenses', 1, 1, 1, 1),
(5920, 33, 'expenses_sessions', 1, 1, 1, 1),
(5921, 33, 'payments', 1, 1, 1, 1),
(5922, 33, 'attachments', 1, 0, 1, 1),
(5923, 33, 'clients', 1, 1, 1, 1),
(5924, 33, 'expense_types', 1, 1, 1, 0),
(5925, 33, 'courts', 1, 1, 1, 0),
(5926, 33, 'case_types', 1, 1, 1, 0),
(5927, 33, 'judicial_circuits', 1, 1, 1, 0),
(5928, 33, 'documents', 1, 1, 1, 0),
(5929, 33, 'notifications', 1, 1, 1, 0),
(5930, 33, 'inbox', 1, 0, 0, 1),
(5931, 33, 'outbox', 1, 0, 0, 0),
(5932, 33, 'adversaries', 1, 1, 1, 1),
(5933, 39, 'home', 1, 0, 0, 0),
(5934, 39, 'control', 1, 0, 0, 0),
(5935, 39, 'cases', 1, 1, 1, 0),
(5936, 39, 'sessions', 1, 1, 1, 0),
(5937, 39, 'calendar', 1, 1, 1, 0),
(5938, 39, 'events', 1, 1, 1, 0),
(5939, 39, 'add_old_session', 0, 0, 1, 0),
(5940, 39, 'expenses_sessions', 1, 1, 1, 0),
(5941, 39, 'payments', 1, 1, 1, 0),
(5942, 39, 'attachments', 1, 0, 1, 0),
(5943, 39, 'lawyers', 1, 0, 0, 0),
(5944, 39, 'clients', 1, 0, 1, 0),
(5945, 39, 'courts', 1, 1, 1, 0),
(5946, 39, 'adversaries', 1, 1, 1, 0),
(5947, 39, 'case_types', 1, 1, 1, 0),
(5948, 39, 'judicial_circuits', 1, 1, 1, 0),
(5949, 39, 'documents', 1, 1, 1, 0),
(5950, 39, 'notifications', 1, 1, 1, 0),
(5951, 39, 'inbox', 1, 0, 0, 0),
(5952, 39, 'outbox', 1, 0, 0, 0),
(5953, 39, 'logo_contact', 1, 0, 0, 0),
(5954, 39, 'profiles', 1, 0, 0, 0),
(5986, 36, 'control', 1, 0, 0, 0),
(5987, 36, 'cases', 1, 1, 1, 1),
(5988, 36, 'sessions', 1, 1, 1, 1),
(5989, 36, 'calendar', 1, 1, 1, 1),
(5990, 36, 'events', 1, 1, 1, 1),
(5991, 36, 'add_old_session', 0, 0, 1, 0),
(5992, 36, 'expenses', 1, 1, 1, 1),
(5993, 36, 'expenses_sessions', 1, 1, 1, 1),
(5994, 36, 'payments', 1, 1, 1, 1),
(5995, 36, 'attachments', 1, 0, 1, 1),
(5996, 36, 'lawyers', 1, 1, 1, 1),
(5997, 36, 'clients', 1, 1, 1, 1),
(5998, 36, 'assistants', 1, 1, 1, 1),
(5999, 36, 'expense_types', 1, 1, 1, 1),
(6000, 36, 'offices', 1, 1, 1, 1),
(6001, 36, 'courts', 1, 1, 1, 1),
(6002, 36, 'case_types', 1, 1, 1, 1),
(6003, 36, 'judicial_circuits', 1, 1, 1, 1),
(6004, 36, 'documents', 1, 1, 1, 1),
(6005, 36, 'notifications', 1, 1, 1, 1),
(6006, 36, 'message_customization', 1, 1, 1, 1),
(6007, 36, 'inbox', 1, 0, 0, 1),
(6008, 36, 'outbox', 1, 0, 0, 0),
(6009, 36, 'join_requests', 1, 0, 1, 1),
(6010, 36, 'roles', 1, 1, 1, 1),
(6011, 36, 'logo_contact', 1, 1, 0, 0),
(6012, 36, 'user_management', 1, 1, 1, 1),
(6013, 36, 'import', 1, 1, 1, 0),
(6014, 36, 'adversaries', 1, 1, 1, 1),
(6015, 36, 'managers', 1, 1, 1, 1),
(6016, 36, 'profiles', 1, 1, 1, 1),
(6017, 40, 'home', 1, 0, 0, 0),
(6018, 40, 'control', 1, 0, 0, 0),
(6019, 40, 'cases', 1, 1, 1, 1),
(6020, 40, 'sessions', 1, 1, 1, 1),
(6021, 40, 'calendar', 1, 1, 1, 1),
(6022, 40, 'events', 1, 1, 1, 1),
(6023, 40, 'add_old_session', 0, 0, 1, 0),
(6024, 40, 'expenses', 1, 1, 1, 1),
(6025, 40, 'expenses_sessions', 1, 1, 1, 1),
(6026, 40, 'payments', 1, 1, 1, 1),
(6027, 40, 'attachments', 1, 0, 1, 1),
(6028, 40, 'lawyers', 1, 1, 1, 1),
(6029, 40, 'clients', 1, 1, 1, 1),
(6030, 40, 'assistants', 1, 1, 1, 1),
(6031, 40, 'managers', 1, 1, 1, 1),
(6032, 40, 'expense_types', 1, 1, 1, 1),
(6033, 40, 'offices', 1, 1, 1, 1),
(6034, 40, 'courts', 1, 1, 1, 1),
(6035, 40, 'adversaries', 1, 1, 1, 1),
(6036, 40, 'case_types', 1, 1, 1, 1),
(6037, 40, 'judicial_circuits', 1, 1, 1, 1),
(6038, 40, 'documents', 1, 1, 1, 1),
(6039, 40, 'notifications', 1, 1, 1, 1),
(6040, 40, 'message_customization', 1, 1, 1, 1),
(6041, 40, 'inbox', 1, 0, 0, 1),
(6042, 40, 'outbox', 1, 0, 0, 0),
(6043, 40, 'join_requests', 1, 0, 1, 1),
(6044, 40, 'roles', 1, 1, 1, 1),
(6045, 40, 'logo_contact', 1, 1, 0, 0),
(6046, 40, 'user_management', 1, 1, 1, 1),
(6047, 40, 'profiles', 1, 1, 1, 1),
(6048, 40, 'import', 1, 1, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` text NOT NULL,
  `token` text NOT NULL,
  `expires` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `case_id` int(11) NOT NULL,
  `amount_paid` varchar(30) DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `payment_method` varchar(30) DEFAULT NULL,
  `payment_date_hiri` varchar(30) NOT NULL,
  `payment_notes` text DEFAULT NULL,
  `received` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `case_id`, `amount_paid`, `payment_date`, `payment_method`, `payment_date_hiri`, `payment_notes`, `received`) VALUES
(36, 85, '1000', '2024-07-23', 'كاش', '1446-01-17', '', NULL),
(37, 83, '3000', '2024-07-23', 'كاش', '1446-01-17', '', NULL),
(39, 87, '1000000', '2024-07-25', 'كاش', '1446-01-19', '', 1),
(40, 88, '100', '2024-07-27', 'كاش', '1446-01-21', '', 1),
(41, 93, '10000', '2024-08-12', 'كاش', '1446-02-08', '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `powers`
--

CREATE TABLE `powers` (
  `power_id` int(11) NOT NULL,
  `role` text DEFAULT NULL,
  `office_id` int(11) DEFAULT NULL,
  `default_role` int(11) DEFAULT NULL,
  `lawyer_id` text DEFAULT NULL,
  `default_role_client` int(11) NOT NULL DEFAULT 0,
  `default_role_lawyer` int(11) NOT NULL DEFAULT 0,
  `default_role_manager` int(11) NOT NULL DEFAULT 0,
  `default_role_helper` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `powers`
--

INSERT INTO `powers` (`power_id`, `role`, `office_id`, `default_role`, `lawyer_id`, `default_role_client`, `default_role_lawyer`, `default_role_manager`, `default_role_helper`) VALUES
(33, 'محامي', 20, 0, '27', 0, 1, 0, 0),
(34, 'موكل', 20, 0, '29,28,27,31', 1, 0, 0, 0),
(36, 'Admin', 20, 0, '', 0, 0, 0, 0),
(37, 'محامي متقدم', 20, 0, '41', 0, 0, 0, 0),
(38, 'مدير مكتب', 20, 0, '', 0, 0, 1, 0),
(39, 'إداري', 20, 0, '', 0, 0, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `profiles`
--

CREATE TABLE `profiles` (
  `id` int(11) NOT NULL,
  `office_id` int(11) NOT NULL,
  `logo` text DEFAULT NULL,
  `fname` text DEFAULT NULL,
  `address` text DEFAULT NULL,
  `email_address` text DEFAULT NULL,
  `longitude` text DEFAULT NULL,
  `latitude` text DEFAULT NULL,
  `phone` text DEFAULT NULL,
  `whatsapp` text DEFAULT NULL,
  `facebook` text DEFAULT NULL,
  `twitter` text DEFAULT NULL,
  `desc1` text DEFAULT NULL,
  `qr` text DEFAULT NULL,
  `desc2` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `profiles`
--

INSERT INTO `profiles` (`id`, `office_id`, `logo`, `fname`, `address`, `email_address`, `longitude`, `latitude`, `phone`, `whatsapp`, `facebook`, `twitter`, `desc1`, `qr`, `desc2`) VALUES
(6, 20, '1721942813_logo66956025c69ec3.87130870_8aa08425f439a9e8.png', 'Youssef', '80 el-khalifa elmaamon - roxy -Cairo -Egypt', 'youssef@shahboun.com', '31.3135705', '30.0921985', '0106 402 9402', '01064029402', '#', '#', 'At DEME we believe in the value of data and becoming data driven. As a Data Engineer you will join the Data & Analytics Office (DAO) within the IT Services department. Together with the team you will be transforming DEME’s data into high quality (BIG) data sets and reports. You will enable our businesses to gain insight in their data so they can support their decisions with facts. You will be working with various kinds of data on multiple projects.\r\n', '1722702369_qr.jpg', '<p>At DEME we believe in the value of data and becoming data driven. As a Data Engineer you will join the Data &amp; Analytics Office (DAO) within the IT Services department. Together with the team you will be transforming DEME’s data into high quality (BIG) data sets and reports. You will enable our businesses to gain insight in their data so they can support their decisions with facts. You will be working with various kinds of data on multiple projects.<br>&nbsp;</p>'),
(7, 20, '1721212310_lawyer-attorney-logo-vector.jpg', 'saber', '50 الهرم جيزة', 'saber@gmail.com', '', '', '+20 106 482 5572', '01012181360', 'https://www.facebook.com/imedia.pro.16', 'https://www.facebook.com/imedia.pro.16', ' المحامي بالنقض والدستوريه العليا\r\n\r\n', '1721212310_download.png', '<h2>نبذه عن مكتبنا</h2><p>&nbsp;</p><p>دكتوراة فى القانون الدولى والعلاقات الدولية والدبلوماسية<br>عضو الجمعية المصرية للقانون الدولى<br>عضو اتحاد المحاميين العرب<br>عضو اتحاد المحاميين الأفرو آسيوى<br>عضو المنظمة الدولية لحقوق الإنسان</p><p>مستشار التحكيم الدولي</p><p>&nbsp;</p><p><br>&nbsp;</p>'),
(8, 25, '1722723443_ar.png', 'شركة وقتك للتحول الرقمي', '80 شارع الخليفة المأمون مصر الجديدة روكسي', 'youssefshahboun1900@gmail.com', '31.3137045', '30.0924067', '0106 399 1099', '01064029402', 'https://www.facebook.com/youssef.shahboun', 'https://x.com/shahboun', 'شركة \"وقتك للتحول الرقمي\" تمتلك تاريخاً طويلاً في تقديم الخدمات الرقمية منذ عام 2006 تحت اسم \"شركة الحبيب\". أعادت إطلاق نفسها في عام 2012 بنفس الاسم، واستمرت في تقديم خدمات عالية الجودة. في عام 2017، أعادت تسمية نفسها إلى \"وقتك للتحول الرقمي\"، لتعكس التزامها بتقديم حلول رقمية مبتكرة. تطمح الشركة الآن إلى الريادة في مجال الخدمات الرقمية، بفضل فريقها المتخصص وخبرتها الواسعة.', '1722723153_qr.jpg', '<p><strong>حلول رقمية متكاملة للمحامين المتميزين مرحباً بكم في عائلة أهل القانون! نقدم دعماً قوياً للمحامين الذين يطمحون للتميز من خلال سوفت وير متقدم يوفر لك الأدوات اللازمة لمتابعة المواعيد، وإدارة الوثائق، وتحليل البيانات. تحكم كامل بأعمالك القانونية بكفاءة ودقة عالية من خلال سوفت وير يناسب متطلبات مكاتب المحاماة المتنوعة. احصل على صفحة ويب احترافية مع أهل القانون بسعر مميز لمحامين النقابة، وانضم إلى نخبة المحامين واستفد من خدماتنا المتقدمة لتعزيز&nbsp;تواجدك&nbsp;الرقمي.</strong></p><p><br>&nbsp;</p><p>&nbsp;</p>');

-- --------------------------------------------------------

--
-- Table structure for table `reminder_due`
--

CREATE TABLE `reminder_due` (
  `id` int(11) NOT NULL,
  `message_date` date DEFAULT NULL,
  `client_id` int(11) NOT NULL,
  `case_id` int(11) NOT NULL,
  `lawyer_id` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `phone_used` varchar(30) DEFAULT NULL,
  `type_notifcation` varchar(11) DEFAULT NULL,
  `payment_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reminder_due`
--

INSERT INTO `reminder_due` (`id`, `message_date`, `client_id`, `case_id`, `lawyer_id`, `message`, `phone_used`, `type_notifcation`, `payment_id`) VALUES
(67, '2024-07-23', 271, 83, NULL, '\r\n        عزيزي/عزيزتي said ali ،\r\n        لديك مستحقات بقيمة 3,000 للقضية احوال مدنيه .\r\n        يرجى دفع المبلغ المستحق في أقرب وقت ممكن لضمان استمرار خدماتنا.\r\n        شكرًا.\r\n    ', '0100 503 0067', 'Whatsupp', 37),
(68, '2024-07-23', 273, 85, NULL, '\r\n        عزيزي/عزيزتي mohsen ali ،\r\n        لديك مستحقات بقيمة 1,000 للقضية احوال مدنيه .\r\n        يرجى دفع المبلغ المستحق في أقرب وقت ممكن لضمان استمرار خدماتنا.\r\n        شكرًا.\r\n    ', '+20 106 882 5572', 'Whatsupp', 36),
(69, '2024-07-23', 271, 83, NULL, '<div style=\'direction: rtl; text-align: right; font-family: Arial, sans-serif;\'>\n    <h2 style=\'color: #4CAF50;\'>تذكير بالمستحقات المالية</h2>\n    \r\n        <div style=\'direction: rtl; text-align: right; font-family: Arial, sans-serif;\'>\r\n            <p>عزيزي said ali،</p>\r\n            <p>لديك مستحقات بقيمة 3,000.00 للقضية احوال مدنيه.</p>\r\n            <p>يرجى دفع المبلغ المستحق في أقرب وقت ممكن لضمان استمرار خدماتنا.</p>\r\n            <p>شكرًا.</p>\r\n        </div>\n</div>\n', 'said@gmail.com', 'Email', 37),
(70, '2024-07-23', 273, 85, NULL, '<div style=\'direction: rtl; text-align: right; font-family: Arial, sans-serif;\'>\n    <h2 style=\'color: #4CAF50;\'>تذكير بالمستحقات المالية</h2>\n    \r\n        <div style=\'direction: rtl; text-align: right; font-family: Arial, sans-serif;\'>\r\n            <p>عزيزي mohsen ali،</p>\r\n            <p>لديك مستحقات بقيمة 1,000.00 للقضية احوال مدنيه.</p>\r\n            <p>يرجى دفع المبلغ المستحق في أقرب وقت ممكن لضمان استمرار خدماتنا.</p>\r\n            <p>شكرًا.</p>\r\n        </div>\n</div>\n', 'mohsen@gmail.com', 'Email', 36),
(71, '2024-07-23', 274, 87, NULL, '\r\n        عزيزي/عزيزتي anas anas ،\r\n        لديك مستحقات بقيمة 1,000,000 للقضية Test .\r\n        يرجى دفع المبلغ المستحق في أقرب وقت ممكن لضمان استمرار خدماتنا.\r\n        شكرًا.\r\n    ', '0117750318', 'Whatsupp', 39),
(72, '2024-07-23', 274, 87, NULL, '<div style=\'direction: rtl; text-align: right; font-family: Arial, sans-serif;\'>\n    <h2 style=\'color: #4CAF50;\'>تذكير بالمستحقات المالية</h2>\n    \r\n        <div style=\'direction: rtl; text-align: right; font-family: Arial, sans-serif;\'>\r\n            <p>عزيزي anas anas،</p>\r\n            <p>لديك مستحقات بقيمة 1,000,000.00 للقضية Test.</p>\r\n            <p>يرجى دفع المبلغ المستحق في أقرب وقت ممكن لضمان استمرار خدماتنا.</p>\r\n            <p>شكرًا.</p>\r\n        </div>\n</div>\n', 'anas0alnajjar@gmail.com', 'Email', 39),
(73, '2024-07-27', 274, 88, NULL, '\r\n        عزيزي/عزيزتي anas anas ،\r\n        لديك مستحقات بقيمة 100 للقضية تجارة عبيد .\r\n        يرجى دفع المبلغ المستحق في أقرب وقت ممكن لضمان استمرار خدماتنا.\r\n        شكرًا.\r\n    ', '0117750318', 'Whatsupp', 40),
(74, '2024-07-27', 274, 88, NULL, '<div style=\'direction: rtl; text-align: right; font-family: Arial, sans-serif;\'>\n    <h2 style=\'color: #4CAF50;\'>تذكير بالمستحقات المالية</h2>\n    \r\n        <div style=\'direction: rtl; text-align: right; font-family: Arial, sans-serif;\'>\r\n            <p>عزيزي anas anas،</p>\r\n            <p>لديك مستحقات بقيمة 100.00 للقضية تجارة عبيد.</p>\r\n            <p>يرجى دفع المبلغ المستحق في أقرب وقت ممكن لضمان استمرار خدماتنا.</p>\r\n            <p>شكرًا.</p>\r\n        </div>\n</div>\n', 'anas0alnajjar@gmail.com', 'Email', 40),
(75, '2024-07-28', 271, 83, NULL, '\r\n        عزيزي/عزيزتي said ali ،\r\n        لديك مستحقات بقيمة 3,000 للقضية احوال مدنيه .\r\n        يرجى دفع المبلغ المستحق في أقرب وقت ممكن لضمان استمرار خدماتنا.\r\n        شكرًا.\r\n    ', '0100 503 0067', 'Whatsupp', 37),
(76, '2024-07-28', 273, 85, NULL, '\r\n        عزيزي/عزيزتي mohsen ali ،\r\n        لديك مستحقات بقيمة 1,000 للقضية احوال مدنيه .\r\n        يرجى دفع المبلغ المستحق في أقرب وقت ممكن لضمان استمرار خدماتنا.\r\n        شكرًا.\r\n    ', '+20 106 882 5572', 'Whatsupp', 36),
(77, '2024-07-28', 271, 83, NULL, '<div style=\'direction: rtl; text-align: right; font-family: Arial, sans-serif;\'>\n    <h2 style=\'color: #4CAF50;\'>تذكير بالمستحقات المالية</h2>\n    \r\n        <div style=\'direction: rtl; text-align: right; font-family: Arial, sans-serif;\'>\r\n            <p>عزيزي said ali،</p>\r\n            <p>لديك مستحقات بقيمة 3,000.00 للقضية احوال مدنيه.</p>\r\n            <p>يرجى دفع المبلغ المستحق في أقرب وقت ممكن لضمان استمرار خدماتنا.</p>\r\n            <p>شكرًا.</p>\r\n        </div>\n</div>\n', 'said@gmail.com', 'Email', 37),
(78, '2024-07-28', 273, 85, NULL, '<div style=\'direction: rtl; text-align: right; font-family: Arial, sans-serif;\'>\n    <h2 style=\'color: #4CAF50;\'>تذكير بالمستحقات المالية</h2>\n    \r\n        <div style=\'direction: rtl; text-align: right; font-family: Arial, sans-serif;\'>\r\n            <p>عزيزي mohsen ali،</p>\r\n            <p>لديك مستحقات بقيمة 1,000.00 للقضية احوال مدنيه.</p>\r\n            <p>يرجى دفع المبلغ المستحق في أقرب وقت ممكن لضمان استمرار خدماتنا.</p>\r\n            <p>شكرًا.</p>\r\n        </div>\n</div>\n', 'mohsen@gmail.com', 'Email', 36),
(79, '2024-08-02', 271, 83, NULL, '\r\n        عزيزي/عزيزتي said ali ،\r\n        لديك مستحقات بقيمة 3,000 للقضية احوال مدنيه .\r\n        يرجى دفع المبلغ المستحق في أقرب وقت ممكن لضمان استمرار خدماتنا.\r\n        شكرًا.\r\n    ', '0100 503 0067', 'Whatsupp', 37),
(80, '2024-08-02', 273, 85, NULL, '\r\n        عزيزي/عزيزتي mohsen ali ،\r\n        لديك مستحقات بقيمة 1,000 للقضية احوال مدنيه .\r\n        يرجى دفع المبلغ المستحق في أقرب وقت ممكن لضمان استمرار خدماتنا.\r\n        شكرًا.\r\n    ', '+20 106 882 5572', 'Whatsupp', 36),
(81, '2024-08-02', 271, 83, NULL, '<div style=\'direction: rtl; text-align: right; font-family: Arial, sans-serif;\'>\n    <h2 style=\'color: #4CAF50;\'>تذكير بالمستحقات المالية</h2>\n    \r\n        <div style=\'direction: rtl; text-align: right; font-family: Arial, sans-serif;\'>\r\n            <p>عزيزي said ali،</p>\r\n            <p>لديك مستحقات بقيمة 3,000.00 للقضية احوال مدنيه.</p>\r\n            <p>يرجى دفع المبلغ المستحق في أقرب وقت ممكن لضمان استمرار خدماتنا.</p>\r\n            <p>شكرًا.</p>\r\n        </div>\n</div>\n', 'said@gmail.com', 'Email', 37),
(82, '2024-08-02', 273, 85, NULL, '<div style=\'direction: rtl; text-align: right; font-family: Arial, sans-serif;\'>\n    <h2 style=\'color: #4CAF50;\'>تذكير بالمستحقات المالية</h2>\n    \r\n        <div style=\'direction: rtl; text-align: right; font-family: Arial, sans-serif;\'>\r\n            <p>عزيزي mohsen ali،</p>\r\n            <p>لديك مستحقات بقيمة 1,000.00 للقضية احوال مدنيه.</p>\r\n            <p>يرجى دفع المبلغ المستحق في أقرب وقت ممكن لضمان استمرار خدماتنا.</p>\r\n            <p>شكرًا.</p>\r\n        </div>\n</div>\n', 'mohsen@gmail.com', 'Email', 36),
(83, '2024-08-07', 271, 83, NULL, '\r\n        عزيزي/عزيزتي said ali ،\r\n        لديك مستحقات بقيمة 3,000 للقضية احوال مدنيه .\r\n        يرجى دفع المبلغ المستحق في أقرب وقت ممكن لضمان استمرار خدماتنا.\r\n        شكرًا.\r\n    ', '0100 503 0067', 'Whatsupp', 37),
(84, '2024-08-07', 273, 85, NULL, '\r\n        عزيزي/عزيزتي mohsen ali ،\r\n        لديك مستحقات بقيمة 1,000 للقضية احوال مدنيه .\r\n        يرجى دفع المبلغ المستحق في أقرب وقت ممكن لضمان استمرار خدماتنا.\r\n        شكرًا.\r\n    ', '+20 106 882 5572', 'Whatsupp', 36),
(85, '2024-08-07', 271, 83, NULL, '<div style=\'direction: rtl; text-align: right; font-family: Arial, sans-serif;\'>\n    <h2 style=\'color: #4CAF50;\'>تذكير بالمستحقات المالية</h2>\n    \r\n        <div style=\'direction: rtl; text-align: right; font-family: Arial, sans-serif;\'>\r\n            <p>عزيزي said ali،</p>\r\n            <p>لديك مستحقات بقيمة 3,000.00 للقضية احوال مدنيه.</p>\r\n            <p>يرجى دفع المبلغ المستحق في أقرب وقت ممكن لضمان استمرار خدماتنا.</p>\r\n            <p>شكرًا.</p>\r\n        </div>\n</div>\n', 'said@gmail.com', 'Email', 37),
(86, '2024-08-07', 273, 85, NULL, '<div style=\'direction: rtl; text-align: right; font-family: Arial, sans-serif;\'>\n    <h2 style=\'color: #4CAF50;\'>تذكير بالمستحقات المالية</h2>\n    \r\n        <div style=\'direction: rtl; text-align: right; font-family: Arial, sans-serif;\'>\r\n            <p>عزيزي mohsen ali،</p>\r\n            <p>لديك مستحقات بقيمة 1,000.00 للقضية احوال مدنيه.</p>\r\n            <p>يرجى دفع المبلغ المستحق في أقرب وقت ممكن لضمان استمرار خدماتنا.</p>\r\n            <p>شكرًا.</p>\r\n        </div>\n</div>\n', 'mohsen@gmail.com', 'Email', 36),
(87, '2024-08-13', 271, 83, NULL, '\r\n        عزيزي/عزيزتي said ali ،\r\n        لديك مستحقات بقيمة 3,000 للقضية احوال مدنيه .\r\n        يرجى دفع المبلغ المستحق في أقرب وقت ممكن لضمان استمرار خدماتنا.\r\n        شكرًا.\r\n    ', '0100 503 0067', 'Whatsupp', 37),
(88, '2024-08-13', 273, 85, NULL, '\r\n        عزيزي/عزيزتي mohsen ali ،\r\n        لديك مستحقات بقيمة 1,000 للقضية احوال مدنيه .\r\n        يرجى دفع المبلغ المستحق في أقرب وقت ممكن لضمان استمرار خدماتنا.\r\n        شكرًا.\r\n    ', '+20 106 882 5572', 'Whatsupp', 36),
(89, '2024-08-13', 271, 83, NULL, '<div style=\'direction: rtl; text-align: right; font-family: Arial, sans-serif;\'>\n    <h2 style=\'color: #4CAF50;\'>تذكير بالمستحقات المالية</h2>\n    \r\n        <div style=\'direction: rtl; text-align: right; font-family: Arial, sans-serif;\'>\r\n            <p>عزيزي said ali،</p>\r\n            <p>لديك مستحقات بقيمة 3,000.00 للقضية احوال مدنيه.</p>\r\n            <p>يرجى دفع المبلغ المستحق في أقرب وقت ممكن لضمان استمرار خدماتنا.</p>\r\n            <p>شكرًا.</p>\r\n        </div>\n</div>\n', 'said@gmail.com', 'Email', 37),
(90, '2024-08-13', 273, 85, NULL, '<div style=\'direction: rtl; text-align: right; font-family: Arial, sans-serif;\'>\n    <h2 style=\'color: #4CAF50;\'>تذكير بالمستحقات المالية</h2>\n    \r\n        <div style=\'direction: rtl; text-align: right; font-family: Arial, sans-serif;\'>\r\n            <p>عزيزي mohsen ali،</p>\r\n            <p>لديك مستحقات بقيمة 1,000.00 للقضية احوال مدنيه.</p>\r\n            <p>يرجى دفع المبلغ المستحق في أقرب وقت ممكن لضمان استمرار خدماتنا.</p>\r\n            <p>شكرًا.</p>\r\n        </div>\n</div>\n', 'mohsen@gmail.com', 'Email', 36),
(91, '2024-08-18', 271, 83, NULL, '\r\n        عزيزي/عزيزتي said ali ،\r\n        لديك مستحقات بقيمة 3,000 للقضية احوال مدنيه .\r\n        يرجى دفع المبلغ المستحق في أقرب وقت ممكن لضمان استمرار خدماتنا.\r\n        شكرًا.\r\n    ', '0100 503 0067', 'Whatsupp', 37),
(92, '2024-08-18', 273, 85, NULL, '\r\n        عزيزي/عزيزتي mohsen ali ،\r\n        لديك مستحقات بقيمة 1,000 للقضية احوال مدنيه .\r\n        يرجى دفع المبلغ المستحق في أقرب وقت ممكن لضمان استمرار خدماتنا.\r\n        شكرًا.\r\n    ', '+20 106 882 5572', 'Whatsupp', 36),
(93, '2024-08-18', 271, 83, NULL, '<div style=\'direction: rtl; text-align: right; font-family: Arial, sans-serif;\'>\n    <h2 style=\'color: #4CAF50;\'>تذكير بالمستحقات المالية</h2>\n    \r\n        <div style=\'direction: rtl; text-align: right; font-family: Arial, sans-serif;\'>\r\n            <p>عزيزي said ali،</p>\r\n            <p>لديك مستحقات بقيمة 3,000.00 للقضية احوال مدنيه.</p>\r\n            <p>يرجى دفع المبلغ المستحق في أقرب وقت ممكن لضمان استمرار خدماتنا.</p>\r\n            <p>شكرًا.</p>\r\n        </div>\n</div>\n', 'said@gmail.com', 'Email', 37),
(94, '2024-08-18', 273, 85, NULL, '<div style=\'direction: rtl; text-align: right; font-family: Arial, sans-serif;\'>\n    <h2 style=\'color: #4CAF50;\'>تذكير بالمستحقات المالية</h2>\n    \r\n        <div style=\'direction: rtl; text-align: right; font-family: Arial, sans-serif;\'>\r\n            <p>عزيزي mohsen ali،</p>\r\n            <p>لديك مستحقات بقيمة 1,000.00 للقضية احوال مدنيه.</p>\r\n            <p>يرجى دفع المبلغ المستحق في أقرب وقت ممكن لضمان استمرار خدماتنا.</p>\r\n            <p>شكرًا.</p>\r\n        </div>\n</div>\n', 'mohsen@gmail.com', 'Email', 36),
(95, '2024-08-23', 271, 83, NULL, '\r\n        عزيزي/عزيزتي said ali ،\r\n        لديك مستحقات بقيمة 3,000 للقضية احوال مدنيه .\r\n        يرجى دفع المبلغ المستحق في أقرب وقت ممكن لضمان استمرار خدماتنا.\r\n        شكرًا.\r\n    ', '0100 503 0067', 'Whatsupp', 37),
(96, '2024-08-23', 273, 85, NULL, '\r\n        عزيزي/عزيزتي mohsen ali ،\r\n        لديك مستحقات بقيمة 1,000 للقضية احوال مدنيه .\r\n        يرجى دفع المبلغ المستحق في أقرب وقت ممكن لضمان استمرار خدماتنا.\r\n        شكرًا.\r\n    ', '+20 106 882 5572', 'Whatsupp', 36),
(97, '2024-08-23', 271, 83, NULL, '<div style=\'direction: rtl; text-align: right; font-family: Arial, sans-serif;\'>\n    <h2 style=\'color: #4CAF50;\'>تذكير بالمستحقات المالية</h2>\n    \r\n        <div style=\'direction: rtl; text-align: right; font-family: Arial, sans-serif;\'>\r\n            <p>عزيزي said ali،</p>\r\n            <p>لديك مستحقات بقيمة 3,000.00 للقضية احوال مدنيه.</p>\r\n            <p>يرجى دفع المبلغ المستحق في أقرب وقت ممكن لضمان استمرار خدماتنا.</p>\r\n            <p>شكرًا.</p>\r\n        </div>\n</div>\n', 'said@gmail.com', 'Email', 37),
(98, '2024-08-23', 273, 85, NULL, '<div style=\'direction: rtl; text-align: right; font-family: Arial, sans-serif;\'>\n    <h2 style=\'color: #4CAF50;\'>تذكير بالمستحقات المالية</h2>\n    \r\n        <div style=\'direction: rtl; text-align: right; font-family: Arial, sans-serif;\'>\r\n            <p>عزيزي mohsen ali،</p>\r\n            <p>لديك مستحقات بقيمة 1,000.00 للقضية احوال مدنيه.</p>\r\n            <p>يرجى دفع المبلغ المستحق في أقرب وقت ممكن لضمان استمرار خدماتنا.</p>\r\n            <p>شكرًا.</p>\r\n        </div>\n</div>\n', 'mohsen@gmail.com', 'Email', 36),
(99, '2024-08-28', 271, 83, NULL, '\r\n        عزيزي/عزيزتي said ali ،\r\n        لديك مستحقات بقيمة 3,000 للقضية احوال مدنيه .\r\n        يرجى دفع المبلغ المستحق في أقرب وقت ممكن لضمان استمرار خدماتنا.\r\n        شكرًا.\r\n    ', '0100 503 0067', 'Whatsupp', 37),
(100, '2024-08-28', 273, 85, NULL, '\r\n        عزيزي/عزيزتي mohsen ali ،\r\n        لديك مستحقات بقيمة 1,000 للقضية احوال مدنيه .\r\n        يرجى دفع المبلغ المستحق في أقرب وقت ممكن لضمان استمرار خدماتنا.\r\n        شكرًا.\r\n    ', '+20 106 882 5572', 'Whatsupp', 36),
(101, '2024-08-28', 271, 83, NULL, '<div style=\'direction: rtl; text-align: right; font-family: Arial, sans-serif;\'>\n    <h2 style=\'color: #4CAF50;\'>تذكير بالمستحقات المالية</h2>\n    \r\n        <div style=\'direction: rtl; text-align: right; font-family: Arial, sans-serif;\'>\r\n            <p>عزيزي said ali،</p>\r\n            <p>لديك مستحقات بقيمة 3,000.00 للقضية احوال مدنيه.</p>\r\n            <p>يرجى دفع المبلغ المستحق في أقرب وقت ممكن لضمان استمرار خدماتنا.</p>\r\n            <p>شكرًا.</p>\r\n        </div>\n</div>\n', 'said@gmail.com', 'Email', 37),
(102, '2024-08-28', 273, 85, NULL, '<div style=\'direction: rtl; text-align: right; font-family: Arial, sans-serif;\'>\n    <h2 style=\'color: #4CAF50;\'>تذكير بالمستحقات المالية</h2>\n    \r\n        <div style=\'direction: rtl; text-align: right; font-family: Arial, sans-serif;\'>\r\n            <p>عزيزي mohsen ali،</p>\r\n            <p>لديك مستحقات بقيمة 1,000.00 للقضية احوال مدنيه.</p>\r\n            <p>يرجى دفع المبلغ المستحق في أقرب وقت ممكن لضمان استمرار خدماتنا.</p>\r\n            <p>شكرًا.</p>\r\n        </div>\n</div>\n', 'mohsen@gmail.com', 'Email', 36),
(103, '2024-09-02', 271, 83, NULL, '\r\n        عزيزي/عزيزتي said ali ،\r\n        لديك مستحقات بقيمة 3,000 للقضية احوال مدنيه .\r\n        يرجى دفع المبلغ المستحق في أقرب وقت ممكن لضمان استمرار خدماتنا.\r\n        شكرًا.\r\n    ', '0100 503 0067', 'Whatsupp', 37),
(104, '2024-09-02', 273, 85, NULL, '\r\n        عزيزي/عزيزتي mohsen ali ،\r\n        لديك مستحقات بقيمة 1,000 للقضية احوال مدنيه .\r\n        يرجى دفع المبلغ المستحق في أقرب وقت ممكن لضمان استمرار خدماتنا.\r\n        شكرًا.\r\n    ', '+20 106 882 5572', 'Whatsupp', 36),
(105, '2024-09-02', 271, 83, NULL, '<div style=\'direction: rtl; text-align: right; font-family: Arial, sans-serif;\'>\n    <h2 style=\'color: #4CAF50;\'>تذكير بالمستحقات المالية</h2>\n    \r\n        <div style=\'direction: rtl; text-align: right; font-family: Arial, sans-serif;\'>\r\n            <p>عزيزي said ali،</p>\r\n            <p>لديك مستحقات بقيمة 3,000.00 للقضية احوال مدنيه.</p>\r\n            <p>يرجى دفع المبلغ المستحق في أقرب وقت ممكن لضمان استمرار خدماتنا.</p>\r\n            <p>شكرًا.</p>\r\n        </div>\n</div>\n', 'said@gmail.com', 'Email', 37),
(106, '2024-09-02', 273, 85, NULL, '<div style=\'direction: rtl; text-align: right; font-family: Arial, sans-serif;\'>\n    <h2 style=\'color: #4CAF50;\'>تذكير بالمستحقات المالية</h2>\n    \r\n        <div style=\'direction: rtl; text-align: right; font-family: Arial, sans-serif;\'>\r\n            <p>عزيزي mohsen ali،</p>\r\n            <p>لديك مستحقات بقيمة 1,000.00 للقضية احوال مدنيه.</p>\r\n            <p>يرجى دفع المبلغ المستحق في أقرب وقت ممكن لضمان استمرار خدماتنا.</p>\r\n            <p>شكرًا.</p>\r\n        </div>\n</div>\n', 'mohsen@gmail.com', 'Email', 36);

-- --------------------------------------------------------

--
-- Table structure for table `requests`
--

CREATE TABLE `requests` (
  `id` int(11) NOT NULL,
  `automatic_acceptance` int(11) NOT NULL DEFAULT 0,
  `days` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `requests`
--

INSERT INTO `requests` (`id`, `automatic_acceptance`, `days`) VALUES
(1, 1, '13');

-- --------------------------------------------------------

--
-- Table structure for table `sent_notifications_sessions`
--

CREATE TABLE `sent_notifications_sessions` (
  `id` int(11) NOT NULL,
  `case_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `recipient_email` varchar(250) NOT NULL,
  `recipient_phone` varchar(30) DEFAULT NULL,
  `sent_date` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `sent_notifications_sessions`
--

INSERT INTO `sent_notifications_sessions` (`id`, `case_id`, `session_id`, `recipient_email`, `recipient_phone`, `sent_date`) VALUES
(164, 87, 220, 'anas0alnajjar@gmail.com', NULL, '2024-07-23'),
(165, 87, 220, 'youssef@shahboun.com', NULL, '2024-07-23'),
(168, 87, 220, '', '0117750318', '2024-07-23'),
(169, 87, 220, '', '01064029402', '2024-07-23'),
(172, 87, 221, 'anas0alnajjar@gmail.com', NULL, '2024-07-23'),
(173, 87, 221, 'youssef@shahboun.com', NULL, '2024-07-23'),
(174, 87, 221, 'anas0alnajjar@gmail.com', NULL, '2024-07-23'),
(175, 87, 221, '', '0117750318', '2024-07-23'),
(176, 87, 221, '', '01064029402', '2024-07-23'),
(177, 87, 221, '', '+31 117 750 318', '2024-07-23'),
(178, 83, 215, 'said@gmail.com', NULL, '2024-07-24'),
(179, 83, 215, 'ahmedazb80@gmail.com', NULL, '2024-07-24'),
(180, 83, 215, '', '0100 503 0067', '2024-07-24'),
(181, 83, 215, '', '01061926672', '2024-07-24'),
(182, 83, 215, 'tota79749@gmail.com', NULL, '2024-07-25'),
(183, 83, 215, '', '01147215216', '2024-07-25'),
(184, 88, 224, 'anas0alnajjar@gmail.com', NULL, '2024-07-27'),
(185, 88, 224, 'anas0alnajjar@gmail.com', NULL, '2024-07-27'),
(186, 88, 224, '', '0117750318', '2024-07-27'),
(187, 88, 224, '', '0117 750 318', '2024-07-27'),
(188, 89, 226, 'anas0alnajjar@gmail.com', NULL, '2024-07-28'),
(189, 89, 226, 'anas0alnajjar@gmail.com', NULL, '2024-07-28'),
(190, 89, 226, '', '+1 201 555 1234', '2024-07-28'),
(191, 89, 226, '', '0117 750 318', '2024-07-28'),
(192, 85, 218, 'mohsen@gmail.com', NULL, '2024-07-29'),
(193, 85, 218, 'elazab@gmail.com', NULL, '2024-07-29'),
(194, 85, 218, '', '+20 106 882 5572', '2024-07-29'),
(195, 85, 218, '', '01061626672', '2024-07-29'),
(196, 83, 216, 'said@gmail.com', NULL, '2024-08-02'),
(197, 83, 216, 'ahmedazb80@gmail.com', NULL, '2024-08-02'),
(198, 84, 217, 'saber@gmail.com', NULL, '2024-08-02'),
(199, 84, 217, 'elazab@gmail.com', NULL, '2024-08-02'),
(200, 83, 216, '', '0100 503 0067', '2024-08-02'),
(201, 83, 216, '', '01061926672', '2024-08-02'),
(202, 84, 217, '', '+20 106 482 5572', '2024-08-02'),
(203, 84, 217, '', '01061626672', '2024-08-02'),
(204, 90, 227, 'youssef@shahboun.com', NULL, '2024-08-03'),
(205, 90, 227, 'youssef@shahboun.com', NULL, '2024-08-03'),
(206, 90, 227, '', '+20 106 402 9402', '2024-08-03'),
(207, 90, 227, '', '+20 106 402 9402', '2024-08-03'),
(208, 93, 234, 'youssef@shahboun.com', NULL, '2024-08-13'),
(209, 93, 234, 'youssef@shahboun.com', NULL, '2024-08-13'),
(210, 93, 234, '', '+20 106 402 9402', '2024-08-13'),
(211, 93, 234, '', '01064029402', '2024-08-13'),
(212, 93, 234, 'youssef1@shahboun.com', NULL, '2024-08-18'),
(213, 95, 235, 'Wwwee@gmail.com', NULL, '2024-08-22'),
(214, 95, 235, 'wael.a.elhamid@gmail.com', NULL, '2024-08-22'),
(215, 95, 235, '', '+20 101 010 1010', '2024-08-22'),
(216, 95, 235, '', '٠١٠٤٠٨٨٠٨٧٣', '2024-08-22'),
(217, 98, 236, 'mada0743@gmail.com', NULL, '2024-08-24'),
(218, 98, 236, 'mada0743@gmail.com', NULL, '2024-08-24'),
(219, 98, 236, '', '+20 106 523 7079', '2024-08-24'),
(220, 98, 236, '', '01065237079', '2024-08-24');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `sessions_id` int(11) NOT NULL,
  `case_id` int(11) NOT NULL,
  `session_number` varchar(250) NOT NULL,
  `session_date` date NOT NULL,
  `session_hour` time DEFAULT NULL,
  `session_date_hjri` varchar(30) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `assistant_lawyer` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`sessions_id`, `case_id`, `session_number`, `session_date`, `session_hour`, `session_date_hjri`, `notes`, `assistant_lawyer`) VALUES
(215, 83, '102030', '2024-07-31', '14:00:00', '1446-01-25', 'لا يوجد', 38),
(216, 83, '102040', '2024-08-09', '11:03:00', '1446-02-05', '', 0),
(217, 84, '1022230', '2024-08-09', '15:00:00', '1446-02-05', '?؟؟', NULL),
(218, 85, '1023230', '2024-08-05', '16:06:00', '1446-02-01', '?', 0),
(223, 83, '45676', '2024-07-01', '18:06:00', '1445-12-25', NULL, 29),
(225, 88, '454878', '2024-07-15', '13:52:00', '1446-01-09', '', 0),
(226, 89, '454878', '2024-07-29', '13:54:00', '1446-01-23', '', 0),
(227, 90, '4444', '2024-08-04', '11:11:00', '1446-01-29', 'سسس', NULL),
(228, 91, '102030', '2024-11-21', '12:00:00', '1446-05-19', '?', 50),
(229, 92, '1', '2024-08-01', '09:28:00', '1446-01-26', '', NULL),
(230, 92, '2', '2024-08-08', '01:53:00', '1446-02-04', 'ىىىىىىىىىىىىىىىىىىىىىىىىىىىىىىىطهنطرعطؤكف فل', NULL),
(231, 0, '١١٢٣', '2024-08-11', '22:00:00', '1446-02-07', NULL, NULL),
(232, 0, '١١٢٣', '2024-08-11', '22:00:00', '1446-02-07', NULL, NULL),
(233, 0, '١١٣٣', '2024-08-12', '02:46:00', '1446-02-08', NULL, NULL),
(234, 93, '233', '2024-08-20', '11:00:00', '1446-02-16', 'ممتاز', NULL),
(235, 95, '٣٤٥', '2024-08-27', '08:55:00', '1446-02-23', '?', NULL),
(236, 98, '5646', '2024-08-24', '14:30:00', '1446-02-20', '', NULL),
(237, 99, '1', '2023-03-19', '09:00:00', '1444-08-27', 'توكيل', NULL),
(238, 100, '1', '2024-10-13', '08:00:00', '1446-04-10', '', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `setting`
--

CREATE TABLE `setting` (
  `id` int(11) NOT NULL,
  `current_year` int(11) NOT NULL,
  `company_name` varchar(100) NOT NULL,
  `slogan` text NOT NULL,
  `about` text NOT NULL,
  `host_email` text DEFAULT NULL,
  `username_email` text DEFAULT NULL,
  `password_email` text DEFAULT NULL,
  `port_email` text DEFAULT NULL,
  `host_whatsapp` text DEFAULT NULL,
  `token_whatsapp` text DEFAULT NULL,
  `logo` text DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `site_key` text DEFAULT NULL,
  `secret_key` text DEFAULT NULL,
  `api_map` text DEFAULT NULL,
  `allow_joining` int(11) DEFAULT 0,
  `allow_check` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `setting`
--

INSERT INTO `setting` (`id`, `current_year`, `company_name`, `slogan`, `about`, `host_email`, `username_email`, `password_email`, `port_email`, `host_whatsapp`, `token_whatsapp`, `logo`, `admin_id`, `site_key`, `secret_key`, `api_map`, `allow_joining`, `allow_check`) VALUES
(1, 2006, 'أهل القانون', 'شركة \"وقتك للتحول الرقمي\" تمتلك تاريخاً طويلاً في تقديم الخدمات الرقمية منذ عام 2006 تحت اسم \"شركة الحبيب\". أعادت إطلاق نفسها في عام 2012 بنفس الاسم، واستمرت في تقديم خدمات عالية الجودة. في عام 2017، أعادت تسمية نفسها إلى \"وقتك للتحول الرقمي\"، لتعكس التزامها بتقديم حلول رقمية مبتكرة. تطمح الشركة الآن إلى الريادة في مجال الخدمات الرقمية، بفضل فريقها المتخصص وخبرتها الواسعة.', 'حلول رقمية متكاملة للمحامين المتميزين\r\n\r\nمرحباً بكم في عائلة أهل القانون! نقدم دعماً قوياً للمحامين الذين يطمحون للتميز من خلال سوفت وير متقدم يوفر لك الأدوات اللازمة لمتابعة المواعيد، وإدارة الوثائق، وتحليل البيانات. تحكم كامل بأعمالك القانونية بكفاءة ودقة عالية من خلال سوفت وير يناسب متطلبات مكاتب المحاماة المتنوعة. احصل على صفحة ويب احترافية مع أهل القانون بسعر مميز لمحامين النقابة، وانضم إلى نخبة المحامين واستفد من خدماتنا المتقدمة لتعزيز تواجدك الرقمي.', 'mail.ahl-alqanon.com', 'no-reply@ahl-alqanon.com', '!}?L32^^-wZo', '465', 'https://api.ultramsg.com/instance87402/messages/chat', 'kp38uy15lk2zncmn', 'logo66adf010291066.71703575_03d405231f1b1880.png', 1, '6Le-q_8pAAAAACcbLo8X12umu7K4zZgrosnd7V0A', '6Le-q_8pAAAAADxTVyqPsD_t7t0vek16OmtJqppc', 'AIzaSyB0cgyKQIXa2ewJBtiosPqAdAzDEpww4zM', 1, 0),
(7, 0, '', '', '', 'anas0alnajjar@gmail.com', 'anas0alnajjar@gmail.com', '', '', '', '', '286dda508c8a2cb3ce1354fc87c248d0.png', 9, NULL, NULL, NULL, 0, NULL),
(8, 0, '', '', '', '', '', '', '', '', '', '079eff1664a2bb2af8f8cfa8bcdc8ad2.png', 10, NULL, NULL, NULL, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `templates`
--

CREATE TABLE `templates` (
  `id` int(11) NOT NULL,
  `message_text` text NOT NULL,
  `type_template` int(11) DEFAULT NULL,
  `office_id` int(11) DEFAULT NULL,
  `for_whom` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `todos`
--

CREATE TABLE `todos` (
  `id` int(11) NOT NULL,
  `title` text NOT NULL,
  `date_time` datetime NOT NULL DEFAULT current_timestamp(),
  `checked` tinyint(1) NOT NULL DEFAULT 0,
  `client_id` int(11) NOT NULL,
  `lawyer_id` int(11) NOT NULL,
  `case_id` int(11) NOT NULL,
  `session_id` int(11) DEFAULT NULL,
  `helper_id` int(11) DEFAULT NULL,
  `priority` varchar(30) NOT NULL DEFAULT 'طبيعية',
  `task_title` text DEFAULT NULL,
  `task_attach` text DEFAULT NULL,
  `read_by_admin` int(11) NOT NULL DEFAULT 0,
  `read_by_lawyer` int(11) DEFAULT NULL,
  `read_by_client` int(11) DEFAULT NULL,
  `read_by_helper` int(11) DEFAULT NULL,
  `read_by_manager` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `todos`
--

INSERT INTO `todos` (`id`, `title`, `date_time`, `checked`, `client_id`, `lawyer_id`, `case_id`, `session_id`, `helper_id`, `priority`, `task_title`, `task_attach`, `read_by_admin`, `read_by_lawyer`, `read_by_client`, `read_by_helper`, `read_by_manager`) VALUES
(244, 'اي حاجة', '2024-07-13 03:25:43', 1, 261, 29, 0, NULL, 0, 'خلال اسبوع', 'عمل فلتر علي المكتب', '1720866343_المحامي2_First_Frame.png', 0, NULL, NULL, NULL, 0),
(245, 'مشكلة', '2024-07-13 03:26:35', 1, 271, 29, 0, NULL, NULL, 'خلال يومين', 'عنوان المهمه', '1720866395_المحامي2_Thumbnails.png', 0, 1, NULL, NULL, 0),
(248, 'dssd', '2024-07-16 03:48:03', 1, 0, 35, 0, NULL, 0, 'خلال يوم', 'تجربة ثانية', '1721126883_document (1).pdf', 0, NULL, NULL, NULL, 0),
(249, 'قضية ايجار', '2024-07-17 03:25:10', 1, 272, 36, 0, NULL, 0, 'خلال يوم', 'قضية ايجار', '1721211910_نموذج-عقد-ايجار-موقع-النموذج.pdf', 0, 1, NULL, NULL, 0),
(250, 'frfcrdcrdfcrf', '2024-07-20 12:13:27', 1, 259, 27, 0, NULL, 0, 'بأسرع وقت', 'عمل فلتر علي المكتب', '', 0, NULL, NULL, NULL, 0),
(251, 'تفاصيل ابمهم', '2024-07-22 09:02:36', 1, 275, 41, 0, NULL, 0, 'خلال ثلاثة', 'عمل فلتر علي المكتب', '', 0, 0, NULL, NULL, 0),
(252, 'لديك جلسة للقضية \'Test\' بتاريخ 2024-07-24 في الساعة 17:29:00.', '2024-07-23 03:53:31', 0, 274, 41, 87, 220, NULL, 'طبيعية', NULL, NULL, 0, NULL, NULL, NULL, 0),
(254, 'لديك جلسة للقضية \'Test\' بتاريخ 2024-07-25 في الساعة 18:00:00.', '2024-07-23 04:06:03', 0, 274, 41, 87, 221, NULL, 'طبيعية', NULL, NULL, 0, NULL, NULL, NULL, 1),
(255, 'لديك جلسة للقضية \'احوال مدنيه\' بتاريخ 2024-07-31 في الساعة 14:00:00.', '2024-07-24 00:00:32', 0, 271, 29, 83, 215, NULL, 'طبيعية', NULL, NULL, 0, 1, NULL, NULL, 0),
(256, 'وصف مهمه واحد', '2024-07-25 03:17:15', 0, 259, 27, 0, NULL, 0, 'بأسرع وقت', 'مهمه 1', '', 0, NULL, NULL, NULL, 0),
(257, 'لديك جلسة للقضية \'تجارة عبيد\' بتاريخ 2024-07-30 في الساعة 16:30:00.', '2024-07-27 13:31:04', 0, 274, 40, 88, 224, NULL, 'طبيعية', NULL, NULL, 0, 1, NULL, NULL, 0),
(258, 'لديك جلسة للقضية \'Test\' بتاريخ 2024-07-29 في الساعة 13:54:00.', '2024-07-28 12:55:05', 0, 290, 40, 89, 226, NULL, 'بأسرع وقت', '', NULL, 0, NULL, NULL, NULL, 0),
(259, 'لديك جلسة للقضية \'احوال مدنيه\' بتاريخ 2024-08-05 في الساعة 16:06:00.', '2024-07-29 00:00:11', 0, 273, 36, 85, 218, NULL, 'طبيعية', NULL, NULL, 0, NULL, NULL, NULL, 0),
(260, 'لديك جلسة للقضية \'احوال مدنيه\' بتاريخ 2024-08-09 في الساعة 11:03:00.', '2024-08-02 00:00:12', 0, 271, 29, 83, 216, NULL, 'طبيعية', NULL, NULL, 0, NULL, NULL, NULL, 0),
(261, 'لديك جلسة للقضية \'احوال مدنيه\' بتاريخ 2024-08-09 في الساعة 15:00:00.', '2024-08-02 00:00:12', 0, 272, 36, 84, 217, NULL, 'طبيعية', NULL, NULL, 0, NULL, NULL, NULL, 0),
(262, 'لديك جلسة للقضية \'قضية 23سيؤي\' بتاريخ 2024-08-04 في الساعة 11:11:00.', '2024-08-03 05:48:03', 0, 292, 47, 90, 227, NULL, '', '', NULL, 0, 0, NULL, NULL, 0),
(263, 'ضروري ..................... الخ', '2024-08-11 01:07:48', 0, 295, 65, 0, NULL, 0, 'خلال يومين', 'اثبات تاريخ', '', 0, 1, NULL, NULL, 0),
(264, 'لديك جلسة للقضية \'نزاع علي الملكية\' بتاريخ 2024-08-20 في الساعة 11:00:00.', '2024-08-13 00:00:09', 0, 295, 65, 93, 234, NULL, 'طبيعية', NULL, NULL, 0, NULL, NULL, NULL, 0),
(265, 'مهمه', '2024-08-16 01:29:20', 0, 271, 29, 0, NULL, 0, 'بأسرع وقت', '', '', 0, NULL, NULL, NULL, 0),
(266, 'لديك جلسة للقضية \'شرعي\' بتاريخ 2024-08-27 في الساعة 08:55:00.', '2024-08-22 22:56:04', 0, 300, 104, 95, 235, NULL, 'طبيعية', NULL, NULL, 0, 1, NULL, NULL, 0),
(267, 'عمل إثبات حالة', '2024-08-22 23:34:41', 0, 301, 104, 0, NULL, 0, 'خلال يوم', 'محضر', '1724394881_1724394831426763242271533885387.jpg', 0, 1, NULL, NULL, 0),
(268, 'لديك جلسة للقضية \'cvgnb\' بتاريخ 2024-08-24 في الساعة 14:30:00.', '2024-08-24 04:29:05', 0, 302, 115, 98, 236, NULL, 'طبيعية', NULL, NULL, 0, 1, NULL, NULL, 0),
(269, 'ورق القضية', '2024-08-27 02:29:19', 0, 304, 135, 0, NULL, 0, 'خلال يوم', 'تصوير', '', 0, 0, NULL, NULL, 0),
(270, 'بالمحضر', '2024-08-27 02:30:19', 0, 305, 135, 0, NULL, 0, 'خلال ثلاثة', 'اتصال', '', 0, NULL, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `types_of_cases`
--

CREATE TABLE `types_of_cases` (
  `id` int(11) NOT NULL,
  `type_case` text NOT NULL,
  `office_id` int(11) DEFAULT NULL,
  `public` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `types_of_cases`
--

INSERT INTO `types_of_cases` (`id`, `type_case`, `office_id`, `public`) VALUES
(18, 'نوع القضية 1', 20, NULL),
(19, 'نوع القضية 2', 20, NULL),
(20, 'جنح', 22, NULL),
(21, 'أسرة', 22, NULL),
(22, 'مدني', 22, NULL),
(23, 'جنائي', 22, NULL),
(24, 'مدني', 20, NULL),
(27, 'اسرة', 20, NULL),
(28, 'شهر عقاري', 20, 1),
(29, 'استئناف عالي', 20, NULL),
(30, 'استئناف عالي', 20, 1),
(31, 'تسجيل علامات تجارية', 20, 1),
(32, 'رخص محلات', 20, 1),
(33, 'جنح', 20, 1),
(34, 'تاسيس شركات', 20, 1),
(35, 'اسرة', 20, 1),
(36, 'أسره', 20, NULL),
(37, '445', 36, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `adversaries`
--
ALTER TABLE `adversaries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ask_join`
--
ALTER TABLE `ask_join`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `cases`
--
ALTER TABLE `cases`
  ADD PRIMARY KEY (`case_id`);

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`client_id`);

--
-- Indexes for table `costs_type`
--
ALTER TABLE `costs_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `courts`
--
ALTER TABLE `courts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`document_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `headers`
--
ALTER TABLE `headers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `helpers`
--
ALTER TABLE `helpers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lawyer`
--
ALTER TABLE `lawyer`
  ADD PRIMARY KEY (`lawyer_id`);

--
-- Indexes for table `managers_office`
--
ALTER TABLE `managers_office`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`message_id`);

--
-- Indexes for table `offices`
--
ALTER TABLE `offices`
  ADD PRIMARY KEY (`office_id`);

--
-- Indexes for table `overhead_costs`
--
ALTER TABLE `overhead_costs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `page_permissions`
--
ALTER TABLE `page_permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `powers`
--
ALTER TABLE `powers`
  ADD PRIMARY KEY (`power_id`);

--
-- Indexes for table `profiles`
--
ALTER TABLE `profiles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reminder_due`
--
ALTER TABLE `reminder_due`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `requests`
--
ALTER TABLE `requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sent_notifications_sessions`
--
ALTER TABLE `sent_notifications_sessions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`sessions_id`);

--
-- Indexes for table `setting`
--
ALTER TABLE `setting`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `templates`
--
ALTER TABLE `templates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `todos`
--
ALTER TABLE `todos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `types_of_cases`
--
ALTER TABLE `types_of_cases`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `adversaries`
--
ALTER TABLE `adversaries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `ask_join`
--
ALTER TABLE `ask_join`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `cases`
--
ALTER TABLE `cases`
  MODIFY `case_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=103;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `client_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=309;

--
-- AUTO_INCREMENT for table `costs_type`
--
ALTER TABLE `costs_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `courts`
--
ALTER TABLE `courts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `document_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=138;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `files`
--
ALTER TABLE `files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=112;

--
-- AUTO_INCREMENT for table `headers`
--
ALTER TABLE `headers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `helpers`
--
ALTER TABLE `helpers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `lawyer`
--
ALTER TABLE `lawyer`
  MODIFY `lawyer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=143;

--
-- AUTO_INCREMENT for table `managers_office`
--
ALTER TABLE `managers_office`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `message`
--
ALTER TABLE `message`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `offices`
--
ALTER TABLE `offices`
  MODIFY `office_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `overhead_costs`
--
ALTER TABLE `overhead_costs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `page_permissions`
--
ALTER TABLE `page_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6049;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `powers`
--
ALTER TABLE `powers`
  MODIFY `power_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `profiles`
--
ALTER TABLE `profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `reminder_due`
--
ALTER TABLE `reminder_due`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=107;

--
-- AUTO_INCREMENT for table `requests`
--
ALTER TABLE `requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `sent_notifications_sessions`
--
ALTER TABLE `sent_notifications_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=221;

--
-- AUTO_INCREMENT for table `sessions`
--
ALTER TABLE `sessions`
  MODIFY `sessions_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=239;

--
-- AUTO_INCREMENT for table `setting`
--
ALTER TABLE `setting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `templates`
--
ALTER TABLE `templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `todos`
--
ALTER TABLE `todos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=271;

--
-- AUTO_INCREMENT for table `types_of_cases`
--
ALTER TABLE `types_of_cases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
