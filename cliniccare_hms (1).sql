-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 22, 2026 at 05:27 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cliniccare_hms`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
  `doctor_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `status` enum('scheduled','confirmed','completed','cancelled','no_show') NOT NULL DEFAULT 'scheduled',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `model_type` varchar(255) DEFAULT NULL,
  `model_id` bigint(20) UNSIGNED DEFAULT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `model_type`, `model_id`, `old_values`, `new_values`, `ip_address`, `user_agent`, `created_at`, `updated_at`) VALUES
(1, 2, 'patient_registered', 'App\\Models\\PatientVisit', 1, NULL, '{\"visit_number\":\"VIS2605200001\",\"patient_id\":1,\"branch_id\":1,\"department_id\":1,\"doctor_id\":1,\"receptionist_id\":2,\"token_number\":1,\"queue_number\":1,\"visit_type\":\"opd\",\"priority\":\"normal\",\"status\":\"registered\",\"weight\":null,\"height\":null,\"bp\":\"\",\"sugar_rbs\":\"\",\"temperature\":null,\"spo2\":null,\"symptoms\":\"\",\"chief_complaint\":\"\",\"referred_by\":null,\"notes\":null,\"registered_at\":\"2026-05-20T18:56:28.000000Z\",\"updated_at\":\"2026-05-20T18:56:28.000000Z\",\"created_at\":\"2026-05-20T18:56:28.000000Z\",\"id\":1}', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-20 13:26:28', '2026-05-20 13:26:28'),
(2, 2, 'sent_to_doctor', 'App\\Models\\PatientVisit', 1, NULL, '{\"id\":1,\"visit_number\":\"VIS2605200001\",\"patient_id\":1,\"branch_id\":1,\"department_id\":1,\"doctor_id\":1,\"receptionist_id\":2,\"token_number\":1,\"queue_number\":1,\"visit_type\":\"opd\",\"priority\":\"normal\",\"status\":\"waiting\",\"weight\":null,\"height\":null,\"bp\":\"\",\"sugar_rbs\":\"\",\"temperature\":null,\"spo2\":null,\"symptoms\":\"\",\"chief_complaint\":\"\",\"referred_by\":null,\"notes\":null,\"registered_at\":\"2026-05-20T18:56:28.000000Z\",\"sent_to_doctor_at\":\"2026-05-20T18:58:50.000000Z\",\"consultation_started_at\":null,\"sent_to_pharmacy_at\":null,\"completed_at\":null,\"created_at\":\"2026-05-20T18:56:28.000000Z\",\"updated_at\":\"2026-05-20T18:58:50.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-20 13:28:50', '2026-05-20 13:28:50'),
(3, 3, 'sent_to_pharmacy', 'App\\Models\\PatientVisit', 1, NULL, '{\"id\":1,\"visit_number\":\"VIS2605200001\",\"patient_id\":1,\"branch_id\":1,\"department_id\":1,\"doctor_id\":1,\"receptionist_id\":2,\"token_number\":1,\"queue_number\":1,\"visit_type\":\"opd\",\"priority\":\"normal\",\"status\":\"at_pharmacy\",\"weight\":null,\"height\":null,\"bp\":\"\",\"sugar_rbs\":\"\",\"temperature\":null,\"spo2\":null,\"symptoms\":\"\",\"chief_complaint\":\"\",\"referred_by\":null,\"notes\":null,\"registered_at\":\"2026-05-20T18:56:28.000000Z\",\"sent_to_doctor_at\":\"2026-05-20T18:58:50.000000Z\",\"consultation_started_at\":\"2026-05-20T18:58:55.000000Z\",\"sent_to_pharmacy_at\":\"2026-05-20T19:00:01.000000Z\",\"completed_at\":null,\"created_at\":\"2026-05-20T18:56:28.000000Z\",\"updated_at\":\"2026-05-20T19:00:01.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-20 13:30:01', '2026-05-20 13:30:01'),
(4, 4, 'pharmacy_completed', 'App\\Models\\PharmacyOrder', 2, NULL, '{\"id\":2,\"order_number\":\"PH260520190048\",\"patient_visit_id\":1,\"prescription_id\":2,\"pharmacist_id\":4,\"status\":\"completed\",\"subtotal\":\"0.00\",\"discount\":\"0.00\",\"tax\":\"0.00\",\"total\":\"0.00\",\"notes\":null,\"completed_at\":\"2026-05-20T19:01:02.763889Z\",\"created_at\":\"2026-05-20T19:00:48.000000Z\",\"updated_at\":\"2026-05-20T19:01:02.000000Z\",\"deleted_at\":null,\"prescription\":{\"id\":2,\"prescription_number\":\"RX26052019000142\",\"consultation_id\":1,\"patient_visit_id\":1,\"doctor_id\":1,\"visibility\":\"public\",\"instructions\":\"\",\"status\":\"dispensed\",\"created_at\":\"2026-05-20T19:00:01.000000Z\",\"updated_at\":\"2026-05-20T19:01:02.000000Z\",\"deleted_at\":null},\"patient_visit\":{\"id\":1,\"visit_number\":\"VIS2605200001\",\"patient_id\":1,\"branch_id\":1,\"department_id\":1,\"doctor_id\":1,\"receptionist_id\":2,\"token_number\":1,\"queue_number\":1,\"visit_type\":\"opd\",\"priority\":\"normal\",\"status\":\"billing\",\"weight\":null,\"height\":null,\"bp\":\"\",\"sugar_rbs\":\"\",\"temperature\":null,\"spo2\":null,\"symptoms\":\"\",\"chief_complaint\":\"\",\"referred_by\":null,\"notes\":null,\"registered_at\":\"2026-05-20T18:56:28.000000Z\",\"sent_to_doctor_at\":\"2026-05-20T18:58:50.000000Z\",\"consultation_started_at\":\"2026-05-20T18:58:55.000000Z\",\"sent_to_pharmacy_at\":\"2026-05-20T19:00:01.000000Z\",\"completed_at\":null,\"created_at\":\"2026-05-20T18:56:28.000000Z\",\"updated_at\":\"2026-05-20T19:01:02.000000Z\",\"deleted_at\":null,\"patient\":{\"id\":1,\"patient_id\":\"PAT26000001\",\"barcode\":\"CC00000001\",\"name\":\"Faijalkhan belim\",\"mobile\":\"9876543222\",\"alternate_mobile\":null,\"gender\":\"male\",\"age\":12,\"dob\":\"2004-06-12T00:00:00.000000Z\",\"blood_group\":null,\"address\":\"Rajkot\",\"city\":\"Rakjot\",\"state\":\"Gujarat\",\"pincode\":\"362240\",\"aadhaar\":null,\"occupation\":null,\"marital_status\":null,\"allergies\":null,\"existing_diseases\":null,\"photo_path\":null,\"branch_id\":1,\"registered_by\":2,\"created_at\":\"2026-05-20T18:56:28.000000Z\",\"updated_at\":\"2026-05-20T18:56:28.000000Z\",\"deleted_at\":null}},\"items\":[{\"id\":6,\"pharmacy_order_id\":2,\"medicine_id\":null,\"prescription_item_id\":6,\"medicine_name\":\"Amoxicillin 250mg\",\"quantity\":1,\"unit_price\":\"0.00\",\"gst_percent\":\"0.00\",\"discount\":\"0.00\",\"total\":\"0.00\",\"is_given\":0,\"is_otc\":0,\"batch_number\":null,\"created_at\":\"2026-05-20T19:00:48.000000Z\",\"updated_at\":\"2026-05-20T19:00:48.000000Z\"},{\"id\":7,\"pharmacy_order_id\":2,\"medicine_id\":null,\"prescription_item_id\":7,\"medicine_name\":\"Cetirizine 10mg\",\"quantity\":1,\"unit_price\":\"0.00\",\"gst_percent\":\"0.00\",\"discount\":\"0.00\",\"total\":\"0.00\",\"is_given\":0,\"is_otc\":0,\"batch_number\":null,\"created_at\":\"2026-05-20T19:00:48.000000Z\",\"updated_at\":\"2026-05-20T19:00:48.000000Z\"},{\"id\":8,\"pharmacy_order_id\":2,\"medicine_id\":null,\"prescription_item_id\":8,\"medicine_name\":\"Omeprazole 20mg\",\"quantity\":1,\"unit_price\":\"0.00\",\"gst_percent\":\"0.00\",\"discount\":\"0.00\",\"total\":\"0.00\",\"is_given\":0,\"is_otc\":0,\"batch_number\":null,\"created_at\":\"2026-05-20T19:00:48.000000Z\",\"updated_at\":\"2026-05-20T19:00:48.000000Z\"},{\"id\":9,\"pharmacy_order_id\":2,\"medicine_id\":null,\"prescription_item_id\":9,\"medicine_name\":\"ORS Sachet\",\"quantity\":1,\"unit_price\":\"0.00\",\"gst_percent\":\"0.00\",\"discount\":\"0.00\",\"total\":\"0.00\",\"is_given\":0,\"is_otc\":0,\"batch_number\":null,\"created_at\":\"2026-05-20T19:00:48.000000Z\",\"updated_at\":\"2026-05-20T19:00:48.000000Z\"},{\"id\":10,\"pharmacy_order_id\":2,\"medicine_id\":null,\"prescription_item_id\":10,\"medicine_name\":\"Paracetamol 500mg\",\"quantity\":1,\"unit_price\":\"0.00\",\"gst_percent\":\"0.00\",\"discount\":\"0.00\",\"total\":\"0.00\",\"is_given\":0,\"is_otc\":0,\"batch_number\":null,\"created_at\":\"2026-05-20T19:00:48.000000Z\",\"updated_at\":\"2026-05-20T19:00:48.000000Z\"}]}', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-20 13:31:02', '2026-05-20 13:31:02'),
(5, 2, 'patient_registered', 'App\\Models\\PatientVisit', 2, NULL, '{\"visit_number\":\"VIS2605200002\",\"patient_id\":2,\"branch_id\":1,\"department_id\":1,\"doctor_id\":null,\"receptionist_id\":2,\"token_number\":2,\"queue_number\":2,\"visit_type\":\"opd\",\"priority\":\"normal\",\"status\":\"registered\",\"weight\":80,\"height\":null,\"bp\":\"120\\/80\",\"sugar_rbs\":\"120\",\"temperature\":null,\"spo2\":0,\"symptoms\":\"\",\"chief_complaint\":\"Stumk\",\"referred_by\":null,\"notes\":null,\"registered_at\":\"2026-05-20T19:22:53.000000Z\",\"updated_at\":\"2026-05-20T19:22:53.000000Z\",\"created_at\":\"2026-05-20T19:22:53.000000Z\",\"id\":2}', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-20 13:52:53', '2026-05-20 13:52:53'),
(6, 2, 'sent_to_doctor', 'App\\Models\\PatientVisit', 2, NULL, '{\"id\":2,\"visit_number\":\"VIS2605200002\",\"patient_id\":2,\"branch_id\":1,\"department_id\":1,\"doctor_id\":null,\"receptionist_id\":2,\"token_number\":2,\"queue_number\":2,\"visit_type\":\"opd\",\"priority\":\"normal\",\"status\":\"waiting\",\"weight\":\"80.00\",\"height\":null,\"bp\":\"120\\/80\",\"sugar_rbs\":\"120\",\"temperature\":null,\"spo2\":0,\"symptoms\":\"\",\"chief_complaint\":\"Stumk\",\"referred_by\":null,\"notes\":null,\"registered_at\":\"2026-05-20T19:22:53.000000Z\",\"sent_to_doctor_at\":\"2026-05-20T19:24:29.000000Z\",\"consultation_started_at\":null,\"sent_to_pharmacy_at\":null,\"completed_at\":null,\"created_at\":\"2026-05-20T19:22:53.000000Z\",\"updated_at\":\"2026-05-20T19:24:29.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-20 13:54:29', '2026-05-20 13:54:29'),
(7, 3, 'sent_to_pharmacy', 'App\\Models\\PatientVisit', 2, NULL, '{\"id\":2,\"visit_number\":\"VIS2605200002\",\"patient_id\":2,\"branch_id\":1,\"department_id\":1,\"doctor_id\":1,\"receptionist_id\":2,\"token_number\":2,\"queue_number\":2,\"visit_type\":\"opd\",\"priority\":\"normal\",\"status\":\"at_pharmacy\",\"weight\":\"80.00\",\"height\":null,\"bp\":\"120\\/80\",\"sugar_rbs\":\"120\",\"temperature\":null,\"spo2\":0,\"symptoms\":\"\",\"chief_complaint\":\"Stumk\",\"referred_by\":null,\"notes\":null,\"registered_at\":\"2026-05-20T19:22:53.000000Z\",\"sent_to_doctor_at\":\"2026-05-20T19:24:29.000000Z\",\"consultation_started_at\":\"2026-05-20T19:24:35.000000Z\",\"sent_to_pharmacy_at\":\"2026-05-20T19:25:24.000000Z\",\"completed_at\":null,\"created_at\":\"2026-05-20T19:22:53.000000Z\",\"updated_at\":\"2026-05-20T19:25:24.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-20 13:55:24', '2026-05-20 13:55:24'),
(8, 4, 'pharmacy_completed', 'App\\Models\\PharmacyOrder', 3, NULL, '{\"id\":3,\"order_number\":\"PH260520192600\",\"patient_visit_id\":2,\"prescription_id\":null,\"pharmacist_id\":4,\"status\":\"completed\",\"subtotal\":\"0.00\",\"discount\":\"0.00\",\"tax\":\"0.00\",\"total\":\"0.00\",\"notes\":null,\"completed_at\":\"2026-05-20T19:26:16.630268Z\",\"created_at\":\"2026-05-20T19:26:00.000000Z\",\"updated_at\":\"2026-05-20T19:26:16.000000Z\",\"deleted_at\":null,\"prescription\":null,\"patient_visit\":{\"id\":2,\"visit_number\":\"VIS2605200002\",\"patient_id\":2,\"branch_id\":1,\"department_id\":1,\"doctor_id\":1,\"receptionist_id\":2,\"token_number\":2,\"queue_number\":2,\"visit_type\":\"opd\",\"priority\":\"normal\",\"status\":\"billing\",\"weight\":\"80.00\",\"height\":null,\"bp\":\"120\\/80\",\"sugar_rbs\":\"120\",\"temperature\":null,\"spo2\":0,\"symptoms\":\"\",\"chief_complaint\":\"Stumk\",\"referred_by\":null,\"notes\":null,\"registered_at\":\"2026-05-20T19:22:53.000000Z\",\"sent_to_doctor_at\":\"2026-05-20T19:24:29.000000Z\",\"consultation_started_at\":\"2026-05-20T19:24:35.000000Z\",\"sent_to_pharmacy_at\":\"2026-05-20T19:25:24.000000Z\",\"completed_at\":null,\"created_at\":\"2026-05-20T19:22:53.000000Z\",\"updated_at\":\"2026-05-20T19:26:16.000000Z\",\"deleted_at\":null,\"patient\":{\"id\":2,\"patient_id\":\"PAT26000002\",\"barcode\":\"CC00000002\",\"name\":\"Faijalkhan\",\"mobile\":\"9876543211\",\"alternate_mobile\":null,\"gender\":\"male\",\"age\":22,\"dob\":null,\"blood_group\":null,\"address\":null,\"city\":null,\"state\":null,\"pincode\":null,\"aadhaar\":null,\"occupation\":null,\"marital_status\":null,\"allergies\":null,\"existing_diseases\":null,\"photo_path\":null,\"branch_id\":1,\"registered_by\":2,\"created_at\":\"2026-05-20T19:22:53.000000Z\",\"updated_at\":\"2026-05-20T19:22:53.000000Z\",\"deleted_at\":null}},\"items\":[]}', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-20 13:56:16', '2026-05-20 13:56:16'),
(9, 2, 'patient_registered', 'App\\Models\\PatientVisit', 3, NULL, '{\"visit_number\":\"VIS2605200003\",\"patient_id\":3,\"branch_id\":1,\"department_id\":1,\"doctor_id\":null,\"receptionist_id\":2,\"token_number\":3,\"queue_number\":3,\"visit_type\":\"opd\",\"priority\":\"normal\",\"status\":\"registered\",\"weight\":50,\"height\":null,\"bp\":\"100\\/90\",\"sugar_rbs\":\"normal\",\"temperature\":null,\"spo2\":null,\"symptoms\":\"symptoms nothing\",\"chief_complaint\":\"uib igdsif sdfishd fnsdfosn\",\"referred_by\":null,\"notes\":null,\"registered_at\":\"2026-05-20T19:47:58.000000Z\",\"updated_at\":\"2026-05-20T19:47:58.000000Z\",\"created_at\":\"2026-05-20T19:47:58.000000Z\",\"id\":3}', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-20 14:17:58', '2026-05-20 14:17:58'),
(10, 2, 'sent_to_doctor', 'App\\Models\\PatientVisit', 3, NULL, '{\"id\":3,\"visit_number\":\"VIS2605200003\",\"patient_id\":3,\"branch_id\":1,\"department_id\":1,\"doctor_id\":null,\"receptionist_id\":2,\"token_number\":3,\"queue_number\":3,\"visit_type\":\"opd\",\"priority\":\"normal\",\"status\":\"waiting\",\"weight\":\"50.00\",\"height\":null,\"bp\":\"100\\/90\",\"sugar_rbs\":\"normal\",\"temperature\":null,\"spo2\":null,\"symptoms\":\"symptoms nothing\",\"chief_complaint\":\"uib igdsif sdfishd fnsdfosn\",\"referred_by\":null,\"notes\":null,\"registered_at\":\"2026-05-20T19:47:58.000000Z\",\"sent_to_doctor_at\":\"2026-05-20T19:48:48.000000Z\",\"consultation_started_at\":null,\"sent_to_pharmacy_at\":null,\"completed_at\":null,\"created_at\":\"2026-05-20T19:47:58.000000Z\",\"updated_at\":\"2026-05-20T19:48:48.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-20 14:18:48', '2026-05-20 14:18:48'),
(11, 3, 'sent_to_pharmacy', 'App\\Models\\PatientVisit', 3, NULL, '{\"id\":3,\"visit_number\":\"VIS2605200003\",\"patient_id\":3,\"branch_id\":1,\"department_id\":1,\"doctor_id\":1,\"receptionist_id\":2,\"token_number\":3,\"queue_number\":3,\"visit_type\":\"opd\",\"priority\":\"normal\",\"status\":\"at_pharmacy\",\"weight\":\"50.00\",\"height\":null,\"bp\":\"100\\/90\",\"sugar_rbs\":\"normal\",\"temperature\":null,\"spo2\":null,\"symptoms\":\"symptoms nothing\",\"chief_complaint\":\"uib igdsif sdfishd fnsdfosn\",\"referred_by\":null,\"notes\":null,\"registered_at\":\"2026-05-20T19:47:58.000000Z\",\"sent_to_doctor_at\":\"2026-05-20T19:48:48.000000Z\",\"consultation_started_at\":\"2026-05-20T19:49:08.000000Z\",\"sent_to_pharmacy_at\":\"2026-05-20T19:51:15.000000Z\",\"completed_at\":null,\"created_at\":\"2026-05-20T19:47:58.000000Z\",\"updated_at\":\"2026-05-20T19:51:15.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-20 14:21:15', '2026-05-20 14:21:15'),
(12, 4, 'pharmacy_completed', 'App\\Models\\PharmacyOrder', 4, NULL, '{\"id\":4,\"order_number\":\"PH260520195140\",\"patient_visit_id\":3,\"prescription_id\":4,\"pharmacist_id\":4,\"status\":\"completed\",\"subtotal\":\"0.00\",\"discount\":\"0.00\",\"tax\":\"0.00\",\"total\":\"0.00\",\"notes\":null,\"completed_at\":\"2026-05-20T19:51:48.190129Z\",\"created_at\":\"2026-05-20T19:51:40.000000Z\",\"updated_at\":\"2026-05-20T19:51:48.000000Z\",\"deleted_at\":null,\"prescription\":{\"id\":4,\"prescription_number\":\"RX26052019511540\",\"consultation_id\":3,\"patient_visit_id\":3,\"doctor_id\":1,\"visibility\":\"public\",\"instructions\":\"Prescription Intrucaiton\",\"status\":\"dispensed\",\"created_at\":\"2026-05-20T19:51:15.000000Z\",\"updated_at\":\"2026-05-20T19:51:48.000000Z\",\"deleted_at\":null},\"patient_visit\":{\"id\":3,\"visit_number\":\"VIS2605200003\",\"patient_id\":3,\"branch_id\":1,\"department_id\":1,\"doctor_id\":1,\"receptionist_id\":2,\"token_number\":3,\"queue_number\":3,\"visit_type\":\"opd\",\"priority\":\"normal\",\"status\":\"billing\",\"weight\":\"50.00\",\"height\":null,\"bp\":\"100\\/90\",\"sugar_rbs\":\"normal\",\"temperature\":null,\"spo2\":null,\"symptoms\":\"symptoms nothing\",\"chief_complaint\":\"uib igdsif sdfishd fnsdfosn\",\"referred_by\":null,\"notes\":null,\"registered_at\":\"2026-05-20T19:47:58.000000Z\",\"sent_to_doctor_at\":\"2026-05-20T19:48:48.000000Z\",\"consultation_started_at\":\"2026-05-20T19:49:08.000000Z\",\"sent_to_pharmacy_at\":\"2026-05-20T19:51:15.000000Z\",\"completed_at\":null,\"created_at\":\"2026-05-20T19:47:58.000000Z\",\"updated_at\":\"2026-05-20T19:51:48.000000Z\",\"deleted_at\":null,\"patient\":{\"id\":3,\"patient_id\":\"PAT26000003\",\"barcode\":\"CC00000003\",\"name\":\"Kamrankhan\",\"mobile\":\"9876543210\",\"alternate_mobile\":null,\"gender\":\"male\",\"age\":19,\"dob\":null,\"blood_group\":null,\"address\":null,\"city\":null,\"state\":null,\"pincode\":null,\"aadhaar\":null,\"occupation\":null,\"marital_status\":null,\"allergies\":null,\"existing_diseases\":null,\"photo_path\":null,\"branch_id\":1,\"registered_by\":2,\"created_at\":\"2026-05-20T19:47:58.000000Z\",\"updated_at\":\"2026-05-20T19:47:58.000000Z\",\"deleted_at\":null}},\"items\":[{\"id\":11,\"pharmacy_order_id\":4,\"medicine_id\":null,\"prescription_item_id\":14,\"medicine_name\":\"Amoxicillin 250mg\",\"quantity\":1,\"unit_price\":\"0.00\",\"gst_percent\":\"0.00\",\"discount\":\"0.00\",\"total\":\"0.00\",\"is_given\":0,\"is_otc\":0,\"batch_number\":null,\"created_at\":\"2026-05-20T19:51:40.000000Z\",\"updated_at\":\"2026-05-20T19:51:40.000000Z\"},{\"id\":12,\"pharmacy_order_id\":4,\"medicine_id\":null,\"prescription_item_id\":15,\"medicine_name\":\"Amoxicillin 250mg\",\"quantity\":1,\"unit_price\":\"0.00\",\"gst_percent\":\"0.00\",\"discount\":\"0.00\",\"total\":\"0.00\",\"is_given\":0,\"is_otc\":0,\"batch_number\":null,\"created_at\":\"2026-05-20T19:51:40.000000Z\",\"updated_at\":\"2026-05-20T19:51:40.000000Z\"},{\"id\":13,\"pharmacy_order_id\":4,\"medicine_id\":null,\"prescription_item_id\":16,\"medicine_name\":\"ORS Sachet\",\"quantity\":1,\"unit_price\":\"0.00\",\"gst_percent\":\"0.00\",\"discount\":\"0.00\",\"total\":\"0.00\",\"is_given\":0,\"is_otc\":0,\"batch_number\":null,\"created_at\":\"2026-05-20T19:51:40.000000Z\",\"updated_at\":\"2026-05-20T19:51:40.000000Z\"}]}', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-20 14:21:48', '2026-05-20 14:21:48'),
(13, 1, 'patient_registered', 'App\\Models\\PatientVisit', 4, NULL, '{\"visit_number\":\"VIS2605200004\",\"patient_id\":1,\"branch_id\":1,\"department_id\":null,\"doctor_id\":null,\"receptionist_id\":1,\"token_number\":4,\"queue_number\":4,\"visit_type\":\"opd\",\"priority\":\"normal\",\"status\":\"registered\",\"weight\":80,\"height\":null,\"bp\":\"120\\/80\",\"sugar_rbs\":\"normal\",\"temperature\":null,\"spo2\":null,\"symptoms\":\"nothing\",\"chief_complaint\":\"Chief Complaint\",\"referred_by\":null,\"notes\":null,\"registered_at\":\"2026-05-20T19:54:19.000000Z\",\"updated_at\":\"2026-05-20T19:54:19.000000Z\",\"created_at\":\"2026-05-20T19:54:19.000000Z\",\"id\":4}', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-20 14:24:19', '2026-05-20 14:24:19'),
(14, 1, 'sent_to_doctor', 'App\\Models\\PatientVisit', 4, NULL, '{\"id\":4,\"visit_number\":\"VIS2605200004\",\"patient_id\":1,\"branch_id\":1,\"department_id\":null,\"doctor_id\":null,\"receptionist_id\":1,\"token_number\":4,\"queue_number\":4,\"visit_type\":\"opd\",\"priority\":\"normal\",\"status\":\"waiting\",\"weight\":\"80.00\",\"height\":null,\"bp\":\"120\\/80\",\"sugar_rbs\":\"normal\",\"temperature\":null,\"spo2\":null,\"symptoms\":\"nothing\",\"chief_complaint\":\"Chief Complaint\",\"referred_by\":null,\"notes\":null,\"registered_at\":\"2026-05-20T19:54:19.000000Z\",\"sent_to_doctor_at\":\"2026-05-20T19:54:32.000000Z\",\"consultation_started_at\":null,\"sent_to_pharmacy_at\":null,\"completed_at\":null,\"created_at\":\"2026-05-20T19:54:19.000000Z\",\"updated_at\":\"2026-05-20T19:54:32.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-20 14:24:32', '2026-05-20 14:24:32'),
(15, 1, 'sent_to_doctor', 'App\\Models\\PatientVisit', 4, NULL, '{\"id\":4,\"visit_number\":\"VIS2605200004\",\"patient_id\":1,\"branch_id\":1,\"department_id\":null,\"doctor_id\":null,\"receptionist_id\":1,\"token_number\":4,\"queue_number\":4,\"visit_type\":\"opd\",\"priority\":\"normal\",\"status\":\"waiting\",\"weight\":\"80.00\",\"height\":null,\"bp\":\"120\\/80\",\"sugar_rbs\":\"normal\",\"temperature\":null,\"spo2\":null,\"symptoms\":\"nothing\",\"chief_complaint\":\"Chief Complaint\",\"referred_by\":null,\"notes\":null,\"registered_at\":\"2026-05-20T19:54:19.000000Z\",\"sent_to_doctor_at\":\"2026-05-20T19:54:52.000000Z\",\"consultation_started_at\":null,\"sent_to_pharmacy_at\":null,\"completed_at\":null,\"created_at\":\"2026-05-20T19:54:19.000000Z\",\"updated_at\":\"2026-05-20T19:54:52.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-20 14:24:52', '2026-05-20 14:24:52'),
(16, 3, 'sent_to_pharmacy', 'App\\Models\\PatientVisit', 4, NULL, '{\"id\":4,\"visit_number\":\"VIS2605200004\",\"patient_id\":1,\"branch_id\":1,\"department_id\":null,\"doctor_id\":1,\"receptionist_id\":1,\"token_number\":4,\"queue_number\":4,\"visit_type\":\"opd\",\"priority\":\"normal\",\"status\":\"at_pharmacy\",\"weight\":\"80.00\",\"height\":null,\"bp\":\"120\\/80\",\"sugar_rbs\":\"normal\",\"temperature\":null,\"spo2\":null,\"symptoms\":\"nothing\",\"chief_complaint\":\"Chief Complaint\",\"referred_by\":null,\"notes\":null,\"registered_at\":\"2026-05-20T19:54:19.000000Z\",\"sent_to_doctor_at\":\"2026-05-20T19:54:52.000000Z\",\"consultation_started_at\":\"2026-05-20T19:55:06.000000Z\",\"sent_to_pharmacy_at\":\"2026-05-20T19:55:47.000000Z\",\"completed_at\":null,\"created_at\":\"2026-05-20T19:54:19.000000Z\",\"updated_at\":\"2026-05-20T19:55:47.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-20 14:25:47', '2026-05-20 14:25:47'),
(18, 1, 'patient_registered', 'App\\Models\\PatientVisit', 6, NULL, '{\"visit_number\":\"VIS2605210001\",\"patient_id\":5,\"branch_id\":1,\"department_id\":1,\"doctor_id\":null,\"receptionist_id\":1,\"token_number\":1,\"queue_number\":1,\"visit_type\":\"opd\",\"priority\":\"normal\",\"status\":\"registered\",\"weight\":45,\"height\":null,\"bp\":\"120\\/80\",\"sugar_rbs\":\"normal\",\"temperature\":null,\"spo2\":null,\"symptoms\":\"Symptoms Symptoms Symptoms Symptoms\",\"chief_complaint\":\"Complaint Complaint\",\"referred_by\":null,\"notes\":null,\"registered_at\":\"2026-05-21T02:18:34.000000Z\",\"updated_at\":\"2026-05-21T02:18:34.000000Z\",\"created_at\":\"2026-05-21T02:18:34.000000Z\",\"id\":6}', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-20 20:48:34', '2026-05-20 20:48:34'),
(19, 1, 'sent_to_doctor', 'App\\Models\\PatientVisit', 6, NULL, '{\"id\":6,\"visit_number\":\"VIS2605210001\",\"patient_id\":5,\"branch_id\":1,\"department_id\":1,\"doctor_id\":null,\"receptionist_id\":1,\"token_number\":1,\"queue_number\":1,\"visit_type\":\"opd\",\"priority\":\"normal\",\"status\":\"waiting\",\"weight\":\"45.00\",\"height\":null,\"bp\":\"120\\/80\",\"sugar_rbs\":\"normal\",\"temperature\":null,\"spo2\":null,\"symptoms\":\"Symptoms Symptoms Symptoms Symptoms\",\"chief_complaint\":\"Complaint Complaint\",\"referred_by\":null,\"notes\":null,\"registered_at\":\"2026-05-21T02:18:34.000000Z\",\"sent_to_doctor_at\":\"2026-05-21T02:19:01.000000Z\",\"consultation_started_at\":null,\"sent_to_pharmacy_at\":null,\"completed_at\":null,\"created_at\":\"2026-05-21T02:18:34.000000Z\",\"updated_at\":\"2026-05-21T02:19:01.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-20 20:49:02', '2026-05-20 20:49:02'),
(20, 3, 'sent_to_pharmacy', 'App\\Models\\PatientVisit', 6, NULL, '{\"id\":6,\"visit_number\":\"VIS2605210001\",\"patient_id\":5,\"branch_id\":1,\"department_id\":1,\"doctor_id\":1,\"receptionist_id\":1,\"token_number\":1,\"queue_number\":1,\"visit_type\":\"opd\",\"priority\":\"normal\",\"status\":\"at_pharmacy\",\"weight\":\"45.00\",\"height\":null,\"bp\":\"120\\/80\",\"sugar_rbs\":\"normal\",\"temperature\":null,\"spo2\":null,\"symptoms\":\"Symptoms Symptoms Symptoms Symptoms\",\"chief_complaint\":\"Complaint Complaint\",\"referred_by\":null,\"notes\":null,\"registered_at\":\"2026-05-21T02:18:34.000000Z\",\"sent_to_doctor_at\":\"2026-05-21T02:19:01.000000Z\",\"consultation_started_at\":\"2026-05-21T02:19:06.000000Z\",\"sent_to_pharmacy_at\":\"2026-05-21T02:20:35.000000Z\",\"completed_at\":null,\"created_at\":\"2026-05-21T02:18:34.000000Z\",\"updated_at\":\"2026-05-21T02:20:35.000000Z\",\"deleted_at\":null}', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-20 20:50:35', '2026-05-20 20:50:35'),
(21, 4, 'pharmacy_completed', 'App\\Models\\PharmacyOrder', 5, NULL, '{\"id\":5,\"order_number\":\"PH260521022046\",\"patient_visit_id\":6,\"prescription_id\":7,\"pharmacist_id\":4,\"status\":\"completed\",\"subtotal\":\"30.00\",\"discount\":\"0.00\",\"tax\":\"0.75\",\"total\":\"30.75\",\"notes\":null,\"completed_at\":\"2026-05-21T02:22:39.601474Z\",\"created_at\":\"2026-05-21T02:20:46.000000Z\",\"updated_at\":\"2026-05-21T02:22:39.000000Z\",\"deleted_at\":null,\"prescription\":{\"id\":7,\"prescription_number\":\"RX26052102203585\",\"consultation_id\":5,\"patient_visit_id\":6,\"doctor_id\":1,\"visibility\":\"public\",\"instructions\":\"prescription\",\"status\":\"dispensed\",\"created_at\":\"2026-05-21T02:20:35.000000Z\",\"updated_at\":\"2026-05-21T02:22:39.000000Z\",\"deleted_at\":null},\"patient_visit\":{\"id\":6,\"visit_number\":\"VIS2605210001\",\"patient_id\":5,\"branch_id\":1,\"department_id\":1,\"doctor_id\":1,\"receptionist_id\":1,\"token_number\":1,\"queue_number\":1,\"visit_type\":\"opd\",\"priority\":\"normal\",\"status\":\"billing\",\"weight\":\"45.00\",\"height\":null,\"bp\":\"120\\/80\",\"sugar_rbs\":\"normal\",\"temperature\":null,\"spo2\":null,\"symptoms\":\"Symptoms Symptoms Symptoms Symptoms\",\"chief_complaint\":\"Complaint Complaint\",\"referred_by\":null,\"notes\":null,\"registered_at\":\"2026-05-21T02:18:34.000000Z\",\"sent_to_doctor_at\":\"2026-05-21T02:19:01.000000Z\",\"consultation_started_at\":\"2026-05-21T02:19:06.000000Z\",\"sent_to_pharmacy_at\":\"2026-05-21T02:20:35.000000Z\",\"completed_at\":null,\"created_at\":\"2026-05-21T02:18:34.000000Z\",\"updated_at\":\"2026-05-21T02:22:39.000000Z\",\"deleted_at\":null,\"patient\":{\"id\":5,\"patient_id\":\"PAT26000004\",\"barcode\":\"CC00000004\",\"name\":\"Asadkhan\",\"mobile\":\"0987654321\",\"alternate_mobile\":null,\"gender\":\"male\",\"age\":15,\"dob\":null,\"blood_group\":null,\"address\":null,\"city\":null,\"state\":null,\"pincode\":null,\"aadhaar\":null,\"occupation\":null,\"marital_status\":null,\"allergies\":null,\"existing_diseases\":null,\"photo_path\":null,\"branch_id\":1,\"registered_by\":1,\"created_at\":\"2026-05-21T02:18:34.000000Z\",\"updated_at\":\"2026-05-21T02:18:34.000000Z\",\"deleted_at\":null}},\"items\":[{\"id\":14,\"pharmacy_order_id\":5,\"medicine_id\":null,\"prescription_item_id\":21,\"medicine_name\":\"Amoxicillin 250mg\",\"quantity\":1,\"unit_price\":\"0.00\",\"gst_percent\":\"0.00\",\"discount\":\"0.00\",\"total\":\"0.00\",\"is_given\":0,\"is_otc\":0,\"batch_number\":null,\"created_at\":\"2026-05-21T02:20:46.000000Z\",\"updated_at\":\"2026-05-21T02:20:46.000000Z\"},{\"id\":15,\"pharmacy_order_id\":5,\"medicine_id\":null,\"prescription_item_id\":22,\"medicine_name\":\"Omeprazole 20mg\",\"quantity\":1,\"unit_price\":\"0.00\",\"gst_percent\":\"0.00\",\"discount\":\"0.00\",\"total\":\"0.00\",\"is_given\":0,\"is_otc\":0,\"batch_number\":null,\"created_at\":\"2026-05-21T02:20:46.000000Z\",\"updated_at\":\"2026-05-21T02:20:46.000000Z\"},{\"id\":16,\"pharmacy_order_id\":5,\"medicine_id\":null,\"prescription_item_id\":23,\"medicine_name\":\"Paracetamol 500mg\",\"quantity\":1,\"unit_price\":\"0.00\",\"gst_percent\":\"0.00\",\"discount\":\"0.00\",\"total\":\"0.00\",\"is_given\":0,\"is_otc\":0,\"batch_number\":null,\"created_at\":\"2026-05-21T02:20:46.000000Z\",\"updated_at\":\"2026-05-21T02:21:23.000000Z\"},{\"id\":17,\"pharmacy_order_id\":5,\"medicine_id\":null,\"prescription_item_id\":null,\"medicine_name\":\"ORS Sachet\",\"quantity\":1,\"unit_price\":\"15.00\",\"gst_percent\":\"0.00\",\"discount\":\"0.00\",\"total\":\"15.00\",\"is_given\":0,\"is_otc\":1,\"batch_number\":null,\"created_at\":\"2026-05-21T02:21:05.000000Z\",\"updated_at\":\"2026-05-21T02:21:05.000000Z\"},{\"id\":18,\"pharmacy_order_id\":5,\"medicine_id\":null,\"prescription_item_id\":null,\"medicine_name\":\"Paracetamol 500mg\",\"quantity\":1,\"unit_price\":\"2.00\",\"gst_percent\":\"5.00\",\"discount\":\"0.00\",\"total\":\"2.00\",\"is_given\":0,\"is_otc\":1,\"batch_number\":null,\"created_at\":\"2026-05-21T02:21:32.000000Z\",\"updated_at\":\"2026-05-21T02:21:32.000000Z\"},{\"id\":19,\"pharmacy_order_id\":5,\"medicine_id\":null,\"prescription_item_id\":null,\"medicine_name\":\"Omeprazole 20mg\",\"quantity\":1,\"unit_price\":\"5.00\",\"gst_percent\":\"5.00\",\"discount\":\"0.00\",\"total\":\"5.00\",\"is_given\":0,\"is_otc\":1,\"batch_number\":null,\"created_at\":\"2026-05-21T02:22:05.000000Z\",\"updated_at\":\"2026-05-21T02:22:05.000000Z\"},{\"id\":20,\"pharmacy_order_id\":5,\"medicine_id\":null,\"prescription_item_id\":null,\"medicine_name\":\"Amoxicillin 250mg\",\"quantity\":1,\"unit_price\":\"8.00\",\"gst_percent\":\"5.00\",\"discount\":\"0.00\",\"total\":\"8.00\",\"is_given\":0,\"is_otc\":1,\"batch_number\":null,\"created_at\":\"2026-05-21T02:22:11.000000Z\",\"updated_at\":\"2026-05-21T02:22:11.000000Z\"}]}', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-20 20:52:39', '2026-05-20 20:52:39'),
(22, 2, 'patient_registered', 'App\\Models\\PatientVisit', 7, NULL, '{\"visit_number\":\"VIS2605210002\",\"patient_id\":6,\"branch_id\":1,\"department_id\":1,\"doctor_id\":null,\"receptionist_id\":2,\"token_number\":2,\"queue_number\":2,\"visit_type\":\"opd\",\"priority\":\"normal\",\"status\":\"registered\",\"weight\":40,\"height\":null,\"bp\":\"100\\/90\",\"sugar_rbs\":\"120\",\"temperature\":null,\"spo2\":null,\"symptoms\":\"Symptoms Symptoms Symptoms \",\"chief_complaint\":\"Chief Complaint\\n\",\"referred_by\":null,\"notes\":null,\"registered_at\":\"2026-05-21T02:32:59.000000Z\",\"updated_at\":\"2026-05-21T02:32:59.000000Z\",\"created_at\":\"2026-05-21T02:32:59.000000Z\",\"id\":7}', '192.168.1.10', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-20 21:02:59', '2026-05-20 21:02:59'),
(23, 2, 'sent_to_doctor', 'App\\Models\\PatientVisit', 7, NULL, '{\"id\":7,\"visit_number\":\"VIS2605210002\",\"patient_id\":6,\"branch_id\":1,\"department_id\":1,\"doctor_id\":null,\"receptionist_id\":2,\"token_number\":2,\"queue_number\":2,\"visit_type\":\"opd\",\"priority\":\"normal\",\"status\":\"waiting\",\"weight\":\"40.00\",\"height\":null,\"bp\":\"100\\/90\",\"sugar_rbs\":\"120\",\"temperature\":null,\"spo2\":null,\"symptoms\":\"Symptoms Symptoms Symptoms \",\"chief_complaint\":\"Chief Complaint\\n\",\"referred_by\":null,\"notes\":null,\"registered_at\":\"2026-05-21T02:32:59.000000Z\",\"sent_to_doctor_at\":\"2026-05-21T02:33:29.000000Z\",\"consultation_started_at\":null,\"sent_to_pharmacy_at\":null,\"completed_at\":null,\"created_at\":\"2026-05-21T02:32:59.000000Z\",\"updated_at\":\"2026-05-21T02:33:29.000000Z\",\"deleted_at\":null}', '192.168.1.10', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-20 21:03:29', '2026-05-20 21:03:29'),
(24, 2, 'patient_registered', 'App\\Models\\PatientVisit', 8, NULL, '{\"visit_number\":\"VIS2605210003\",\"patient_id\":3,\"branch_id\":1,\"department_id\":null,\"doctor_id\":null,\"receptionist_id\":2,\"token_number\":3,\"queue_number\":3,\"visit_type\":\"opd\",\"priority\":\"normal\",\"status\":\"registered\",\"weight\":35,\"height\":null,\"bp\":\"100\\/80\",\"sugar_rbs\":\"100\",\"temperature\":null,\"spo2\":null,\"symptoms\":\"mathud pet and legs pains\",\"chief_complaint\":\"pema dukhe che\",\"referred_by\":null,\"notes\":null,\"registered_at\":\"2026-05-21T02:34:39.000000Z\",\"updated_at\":\"2026-05-21T02:34:39.000000Z\",\"created_at\":\"2026-05-21T02:34:39.000000Z\",\"id\":8}', '192.168.1.10', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-20 21:04:39', '2026-05-20 21:04:39'),
(25, 2, 'sent_to_doctor', 'App\\Models\\PatientVisit', 8, NULL, '{\"id\":8,\"visit_number\":\"VIS2605210003\",\"patient_id\":3,\"branch_id\":1,\"department_id\":null,\"doctor_id\":null,\"receptionist_id\":2,\"token_number\":3,\"queue_number\":3,\"visit_type\":\"opd\",\"priority\":\"normal\",\"status\":\"waiting\",\"weight\":\"35.00\",\"height\":null,\"bp\":\"100\\/80\",\"sugar_rbs\":\"100\",\"temperature\":null,\"spo2\":null,\"symptoms\":\"mathud pet and legs pains\",\"chief_complaint\":\"pema dukhe che\",\"referred_by\":null,\"notes\":null,\"registered_at\":\"2026-05-21T02:34:39.000000Z\",\"sent_to_doctor_at\":\"2026-05-21T02:34:52.000000Z\",\"consultation_started_at\":null,\"sent_to_pharmacy_at\":null,\"completed_at\":null,\"created_at\":\"2026-05-21T02:34:39.000000Z\",\"updated_at\":\"2026-05-21T02:34:52.000000Z\",\"deleted_at\":null}', '192.168.1.10', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-20 21:04:52', '2026-05-20 21:04:52'),
(26, 2, 'patient_registered', 'App\\Models\\PatientVisit', 9, NULL, '{\"visit_number\":\"VIS2605210004\",\"patient_id\":7,\"branch_id\":1,\"department_id\":1,\"doctor_id\":null,\"receptionist_id\":2,\"token_number\":3,\"queue_number\":3,\"visit_type\":\"opd\",\"priority\":\"normal\",\"status\":\"registered\",\"weight\":30,\"height\":null,\"bp\":\"100\\/90\",\"sugar_rbs\":\"100\",\"temperature\":null,\"spo2\":null,\"symptoms\":\"pet mathu and legs pain\",\"chief_complaint\":\"oiay\",\"referred_by\":null,\"notes\":null,\"registered_at\":\"2026-05-21T02:35:48.000000Z\",\"updated_at\":\"2026-05-21T02:35:48.000000Z\",\"created_at\":\"2026-05-21T02:35:48.000000Z\",\"id\":9}', '192.168.1.10', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-20 21:05:48', '2026-05-20 21:05:48'),
(27, 2, 'sent_to_doctor', 'App\\Models\\PatientVisit', 9, NULL, '{\"id\":9,\"visit_number\":\"VIS2605210004\",\"patient_id\":7,\"branch_id\":1,\"department_id\":1,\"doctor_id\":null,\"receptionist_id\":2,\"token_number\":3,\"queue_number\":3,\"visit_type\":\"opd\",\"priority\":\"normal\",\"status\":\"waiting\",\"weight\":\"30.00\",\"height\":null,\"bp\":\"100\\/90\",\"sugar_rbs\":\"100\",\"temperature\":null,\"spo2\":null,\"symptoms\":\"pet mathu and legs pain\",\"chief_complaint\":\"oiay\",\"referred_by\":null,\"notes\":null,\"registered_at\":\"2026-05-21T02:35:48.000000Z\",\"sent_to_doctor_at\":\"2026-05-21T02:36:03.000000Z\",\"consultation_started_at\":null,\"sent_to_pharmacy_at\":null,\"completed_at\":null,\"created_at\":\"2026-05-21T02:35:48.000000Z\",\"updated_at\":\"2026-05-21T02:36:03.000000Z\",\"deleted_at\":null}', '192.168.1.10', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-20 21:06:03', '2026-05-20 21:06:03'),
(28, 3, 'sent_to_pharmacy', 'App\\Models\\PatientVisit', 7, NULL, '{\"id\":7,\"visit_number\":\"VIS2605210002\",\"patient_id\":6,\"branch_id\":1,\"department_id\":1,\"doctor_id\":1,\"receptionist_id\":2,\"token_number\":2,\"queue_number\":2,\"visit_type\":\"opd\",\"priority\":\"normal\",\"status\":\"at_pharmacy\",\"weight\":\"40.00\",\"height\":null,\"bp\":\"100\\/90\",\"sugar_rbs\":\"120\",\"temperature\":null,\"spo2\":null,\"symptoms\":\"Symptoms Symptoms Symptoms \",\"chief_complaint\":\"Chief Complaint\\n\",\"referred_by\":null,\"notes\":null,\"registered_at\":\"2026-05-21T02:32:59.000000Z\",\"sent_to_doctor_at\":\"2026-05-21T02:33:29.000000Z\",\"consultation_started_at\":\"2026-05-21T02:33:41.000000Z\",\"sent_to_pharmacy_at\":\"2026-05-21T02:37:29.000000Z\",\"completed_at\":null,\"created_at\":\"2026-05-21T02:32:59.000000Z\",\"updated_at\":\"2026-05-21T02:37:29.000000Z\",\"deleted_at\":null}', '192.168.1.4', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/29.0 Chrome/136.0.0.0 Mobile Safari/537.36', '2026-05-20 21:07:29', '2026-05-20 21:07:29'),
(29, 4, 'pharmacy_completed', 'App\\Models\\PharmacyOrder', 6, NULL, '{\"id\":6,\"order_number\":\"PH260521023814\",\"patient_visit_id\":7,\"prescription_id\":9,\"pharmacist_id\":4,\"status\":\"completed\",\"subtotal\":\"8.00\",\"discount\":\"0.00\",\"tax\":\"0.40\",\"total\":\"8.40\",\"notes\":null,\"completed_at\":\"2026-05-21T02:38:45.943516Z\",\"created_at\":\"2026-05-21T02:38:14.000000Z\",\"updated_at\":\"2026-05-21T02:38:45.000000Z\",\"deleted_at\":null,\"prescription\":{\"id\":9,\"prescription_number\":\"RX26052102372979\",\"consultation_id\":6,\"patient_visit_id\":7,\"doctor_id\":1,\"visibility\":\"public\",\"instructions\":\"Instruction\",\"status\":\"dispensed\",\"created_at\":\"2026-05-21T02:37:29.000000Z\",\"updated_at\":\"2026-05-21T02:38:45.000000Z\",\"deleted_at\":null},\"patient_visit\":{\"id\":7,\"visit_number\":\"VIS2605210002\",\"patient_id\":6,\"branch_id\":1,\"department_id\":1,\"doctor_id\":1,\"receptionist_id\":2,\"token_number\":2,\"queue_number\":2,\"visit_type\":\"opd\",\"priority\":\"normal\",\"status\":\"billing\",\"weight\":\"40.00\",\"height\":null,\"bp\":\"100\\/90\",\"sugar_rbs\":\"120\",\"temperature\":null,\"spo2\":null,\"symptoms\":\"Symptoms Symptoms Symptoms \",\"chief_complaint\":\"Chief Complaint\\n\",\"referred_by\":null,\"notes\":null,\"registered_at\":\"2026-05-21T02:32:59.000000Z\",\"sent_to_doctor_at\":\"2026-05-21T02:33:29.000000Z\",\"consultation_started_at\":\"2026-05-21T02:33:41.000000Z\",\"sent_to_pharmacy_at\":\"2026-05-21T02:37:29.000000Z\",\"completed_at\":null,\"created_at\":\"2026-05-21T02:32:59.000000Z\",\"updated_at\":\"2026-05-21T02:38:45.000000Z\",\"deleted_at\":null,\"patient\":{\"id\":6,\"patient_id\":\"PAT26000005\",\"barcode\":\"CC00000006\",\"name\":\"ansarkhan aslamkhan belim\",\"mobile\":\"9876513211\",\"alternate_mobile\":null,\"gender\":\"male\",\"age\":17,\"dob\":null,\"blood_group\":null,\"address\":null,\"city\":null,\"state\":null,\"pincode\":null,\"aadhaar\":null,\"occupation\":null,\"marital_status\":null,\"allergies\":null,\"existing_diseases\":null,\"photo_path\":null,\"branch_id\":1,\"registered_by\":2,\"created_at\":\"2026-05-21T02:32:59.000000Z\",\"updated_at\":\"2026-05-21T02:32:59.000000Z\",\"deleted_at\":null}},\"items\":[{\"id\":21,\"pharmacy_order_id\":6,\"medicine_id\":null,\"prescription_item_id\":25,\"medicine_name\":\"Amoxicillin 250mg\",\"quantity\":1,\"unit_price\":\"0.00\",\"gst_percent\":\"0.00\",\"discount\":\"0.00\",\"total\":\"0.00\",\"is_given\":0,\"is_otc\":0,\"batch_number\":null,\"created_at\":\"2026-05-21T02:38:14.000000Z\",\"updated_at\":\"2026-05-21T02:38:22.000000Z\"},{\"id\":22,\"pharmacy_order_id\":6,\"medicine_id\":null,\"prescription_item_id\":null,\"medicine_name\":\"Amoxicillin 250mg\",\"quantity\":1,\"unit_price\":\"8.00\",\"gst_percent\":\"5.00\",\"discount\":\"0.00\",\"total\":\"8.00\",\"is_given\":0,\"is_otc\":1,\"batch_number\":null,\"created_at\":\"2026-05-21T02:38:38.000000Z\",\"updated_at\":\"2026-05-21T02:38:38.000000Z\"}]}', '192.168.1.10', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-20 21:08:46', '2026-05-20 21:08:46'),
(30, 3, 'sent_to_pharmacy', 'App\\Models\\PatientVisit', 9, NULL, '{\"id\":9,\"visit_number\":\"VIS2605210004\",\"patient_id\":7,\"branch_id\":1,\"department_id\":1,\"doctor_id\":1,\"receptionist_id\":2,\"token_number\":3,\"queue_number\":3,\"visit_type\":\"opd\",\"priority\":\"normal\",\"status\":\"at_pharmacy\",\"weight\":\"30.00\",\"height\":null,\"bp\":\"100\\/90\",\"sugar_rbs\":\"100\",\"temperature\":null,\"spo2\":null,\"symptoms\":\"pet mathu and legs pain\",\"chief_complaint\":\"oiay\",\"referred_by\":null,\"notes\":null,\"registered_at\":\"2026-05-21T02:35:48.000000Z\",\"sent_to_doctor_at\":\"2026-05-21T02:36:03.000000Z\",\"consultation_started_at\":\"2026-05-21T02:37:49.000000Z\",\"sent_to_pharmacy_at\":\"2026-05-21T02:39:43.000000Z\",\"completed_at\":null,\"created_at\":\"2026-05-21T02:35:48.000000Z\",\"updated_at\":\"2026-05-21T02:39:43.000000Z\",\"deleted_at\":null}', '192.168.1.4', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/29.0 Chrome/136.0.0.0 Mobile Safari/537.36', '2026-05-20 21:09:43', '2026-05-20 21:09:43'),
(31, 3, 'sent_to_pharmacy', 'App\\Models\\PatientVisit', 8, NULL, '{\"id\":8,\"visit_number\":\"VIS2605210003\",\"patient_id\":3,\"branch_id\":1,\"department_id\":null,\"doctor_id\":1,\"receptionist_id\":2,\"token_number\":3,\"queue_number\":3,\"visit_type\":\"opd\",\"priority\":\"normal\",\"status\":\"at_pharmacy\",\"weight\":\"35.00\",\"height\":null,\"bp\":\"100\\/80\",\"sugar_rbs\":\"100\",\"temperature\":null,\"spo2\":null,\"symptoms\":\"mathud pet and legs pains\",\"chief_complaint\":\"pema dukhe che\",\"referred_by\":null,\"notes\":null,\"registered_at\":\"2026-05-21T02:34:39.000000Z\",\"sent_to_doctor_at\":\"2026-05-21T02:34:52.000000Z\",\"consultation_started_at\":\"2026-05-21T02:39:48.000000Z\",\"sent_to_pharmacy_at\":\"2026-05-21T02:40:02.000000Z\",\"completed_at\":null,\"created_at\":\"2026-05-21T02:34:39.000000Z\",\"updated_at\":\"2026-05-21T02:40:02.000000Z\",\"deleted_at\":null}', '192.168.1.4', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/29.0 Chrome/136.0.0.0 Mobile Safari/537.36', '2026-05-20 21:10:02', '2026-05-20 21:10:02'),
(32, 4, 'pharmacy_completed', 'App\\Models\\PharmacyOrder', 7, NULL, '{\"id\":7,\"order_number\":\"PH260521024009\",\"patient_visit_id\":8,\"prescription_id\":null,\"pharmacist_id\":4,\"status\":\"completed\",\"subtotal\":\"23.00\",\"discount\":\"0.00\",\"tax\":\"0.40\",\"total\":\"23.40\",\"notes\":null,\"completed_at\":\"2026-05-21T02:40:29.979907Z\",\"created_at\":\"2026-05-21T02:40:09.000000Z\",\"updated_at\":\"2026-05-21T02:40:29.000000Z\",\"deleted_at\":null,\"prescription\":null,\"patient_visit\":{\"id\":8,\"visit_number\":\"VIS2605210003\",\"patient_id\":3,\"branch_id\":1,\"department_id\":null,\"doctor_id\":1,\"receptionist_id\":2,\"token_number\":3,\"queue_number\":3,\"visit_type\":\"opd\",\"priority\":\"normal\",\"status\":\"billing\",\"weight\":\"35.00\",\"height\":null,\"bp\":\"100\\/80\",\"sugar_rbs\":\"100\",\"temperature\":null,\"spo2\":null,\"symptoms\":\"mathud pet and legs pains\",\"chief_complaint\":\"pema dukhe che\",\"referred_by\":null,\"notes\":null,\"registered_at\":\"2026-05-21T02:34:39.000000Z\",\"sent_to_doctor_at\":\"2026-05-21T02:34:52.000000Z\",\"consultation_started_at\":\"2026-05-21T02:39:48.000000Z\",\"sent_to_pharmacy_at\":\"2026-05-21T02:40:02.000000Z\",\"completed_at\":null,\"created_at\":\"2026-05-21T02:34:39.000000Z\",\"updated_at\":\"2026-05-21T02:40:29.000000Z\",\"deleted_at\":null,\"patient\":{\"id\":3,\"patient_id\":\"PAT26000003\",\"barcode\":\"CC00000003\",\"name\":\"Kamrankhan\",\"mobile\":\"9876543210\",\"alternate_mobile\":null,\"gender\":\"male\",\"age\":19,\"dob\":null,\"blood_group\":null,\"address\":null,\"city\":null,\"state\":null,\"pincode\":null,\"aadhaar\":null,\"occupation\":null,\"marital_status\":null,\"allergies\":null,\"existing_diseases\":null,\"photo_path\":null,\"branch_id\":1,\"registered_by\":2,\"created_at\":\"2026-05-20T19:47:58.000000Z\",\"updated_at\":\"2026-05-20T19:47:58.000000Z\",\"deleted_at\":null}},\"items\":[{\"id\":23,\"pharmacy_order_id\":7,\"medicine_id\":null,\"prescription_item_id\":null,\"medicine_name\":\"Amoxicillin 250mg\",\"quantity\":1,\"unit_price\":\"8.00\",\"gst_percent\":\"5.00\",\"discount\":\"0.00\",\"total\":\"8.00\",\"is_given\":0,\"is_otc\":1,\"batch_number\":null,\"created_at\":\"2026-05-21T02:40:21.000000Z\",\"updated_at\":\"2026-05-21T02:40:21.000000Z\"},{\"id\":24,\"pharmacy_order_id\":7,\"medicine_id\":null,\"prescription_item_id\":null,\"medicine_name\":\"ORS Sachet\",\"quantity\":1,\"unit_price\":\"15.00\",\"gst_percent\":\"0.00\",\"discount\":\"0.00\",\"total\":\"15.00\",\"is_given\":0,\"is_otc\":1,\"batch_number\":null,\"created_at\":\"2026-05-21T02:40:24.000000Z\",\"updated_at\":\"2026-05-21T02:40:24.000000Z\"}]}', '192.168.1.10', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-20 21:10:30', '2026-05-20 21:10:30'),
(33, 4, 'pharmacy_completed', 'App\\Models\\PharmacyOrder', 8, NULL, '{\"id\":8,\"order_number\":\"PH260521024032\",\"patient_visit_id\":9,\"prescription_id\":null,\"pharmacist_id\":4,\"status\":\"completed\",\"subtotal\":\"23.00\",\"discount\":\"3.40\",\"tax\":\"0.40\",\"total\":\"20.00\",\"notes\":null,\"completed_at\":\"2026-05-21T02:41:04.190279Z\",\"created_at\":\"2026-05-21T02:40:32.000000Z\",\"updated_at\":\"2026-05-21T02:41:04.000000Z\",\"deleted_at\":null,\"prescription\":null,\"patient_visit\":{\"id\":9,\"visit_number\":\"VIS2605210004\",\"patient_id\":7,\"branch_id\":1,\"department_id\":1,\"doctor_id\":1,\"receptionist_id\":2,\"token_number\":3,\"queue_number\":3,\"visit_type\":\"opd\",\"priority\":\"normal\",\"status\":\"billing\",\"weight\":\"30.00\",\"height\":null,\"bp\":\"100\\/90\",\"sugar_rbs\":\"100\",\"temperature\":null,\"spo2\":null,\"symptoms\":\"pet mathu and legs pain\",\"chief_complaint\":\"oiay\",\"referred_by\":null,\"notes\":null,\"registered_at\":\"2026-05-21T02:35:48.000000Z\",\"sent_to_doctor_at\":\"2026-05-21T02:36:03.000000Z\",\"consultation_started_at\":\"2026-05-21T02:37:49.000000Z\",\"sent_to_pharmacy_at\":\"2026-05-21T02:39:43.000000Z\",\"completed_at\":null,\"created_at\":\"2026-05-21T02:35:48.000000Z\",\"updated_at\":\"2026-05-21T02:41:04.000000Z\",\"deleted_at\":null,\"patient\":{\"id\":7,\"patient_id\":\"PAT26000006\",\"barcode\":\"CC00000007\",\"name\":\"Junedkhan Imtiyaj\",\"mobile\":\"0098765432\",\"alternate_mobile\":null,\"gender\":\"male\",\"age\":19,\"dob\":null,\"blood_group\":null,\"address\":null,\"city\":null,\"state\":null,\"pincode\":null,\"aadhaar\":null,\"occupation\":null,\"marital_status\":null,\"allergies\":null,\"existing_diseases\":null,\"photo_path\":null,\"branch_id\":1,\"registered_by\":2,\"created_at\":\"2026-05-21T02:35:48.000000Z\",\"updated_at\":\"2026-05-21T02:35:48.000000Z\",\"deleted_at\":null}},\"items\":[{\"id\":25,\"pharmacy_order_id\":8,\"medicine_id\":null,\"prescription_item_id\":null,\"medicine_name\":\"Cetirizine 10mg\",\"quantity\":1,\"unit_price\":\"3.00\",\"gst_percent\":\"5.00\",\"discount\":\"0.00\",\"total\":\"3.00\",\"is_given\":0,\"is_otc\":1,\"batch_number\":null,\"created_at\":\"2026-05-21T02:40:36.000000Z\",\"updated_at\":\"2026-05-21T02:40:36.000000Z\"},{\"id\":26,\"pharmacy_order_id\":8,\"medicine_id\":null,\"prescription_item_id\":null,\"medicine_name\":\"ORS Sachet\",\"quantity\":1,\"unit_price\":\"15.00\",\"gst_percent\":\"0.00\",\"discount\":\"0.00\",\"total\":\"15.00\",\"is_given\":0,\"is_otc\":1,\"batch_number\":null,\"created_at\":\"2026-05-21T02:40:41.000000Z\",\"updated_at\":\"2026-05-21T02:40:41.000000Z\"},{\"id\":27,\"pharmacy_order_id\":8,\"medicine_id\":null,\"prescription_item_id\":null,\"medicine_name\":\"Omeprazole 20mg\",\"quantity\":1,\"unit_price\":\"5.00\",\"gst_percent\":\"5.00\",\"discount\":\"0.00\",\"total\":\"5.00\",\"is_given\":0,\"is_otc\":1,\"batch_number\":null,\"created_at\":\"2026-05-21T02:40:45.000000Z\",\"updated_at\":\"2026-05-21T02:40:45.000000Z\"}]}', '192.168.1.10', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-20 21:11:04', '2026-05-20 21:11:04'),
(34, 1, 'patient_registered', 'App\\Models\\PatientVisit', 10, NULL, '{\"visit_number\":\"VIS2605210005\",\"patient_id\":3,\"branch_id\":1,\"department_id\":null,\"doctor_id\":null,\"receptionist_id\":1,\"token_number\":4,\"queue_number\":4,\"visit_type\":\"opd\",\"priority\":\"normal\",\"status\":\"registered\",\"weight\":null,\"height\":null,\"bp\":\"\",\"sugar_rbs\":\"\",\"temperature\":null,\"spo2\":null,\"symptoms\":\"\",\"chief_complaint\":\"adsf\",\"referred_by\":null,\"notes\":null,\"registered_at\":\"2026-05-21T02:43:00.000000Z\",\"updated_at\":\"2026-05-21T02:43:00.000000Z\",\"created_at\":\"2026-05-21T02:43:00.000000Z\",\"id\":10}', '192.168.1.10', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-20 21:13:00', '2026-05-20 21:13:00');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `model_type`, `model_id`, `old_values`, `new_values`, `ip_address`, `user_agent`, `created_at`, `updated_at`) VALUES
(35, 1, 'sent_to_doctor', 'App\\Models\\PatientVisit', 10, NULL, '{\"id\":10,\"visit_number\":\"VIS2605210005\",\"patient_id\":3,\"branch_id\":1,\"department_id\":null,\"doctor_id\":null,\"receptionist_id\":1,\"token_number\":4,\"queue_number\":4,\"visit_type\":\"opd\",\"priority\":\"normal\",\"status\":\"waiting\",\"weight\":null,\"height\":null,\"bp\":\"\",\"sugar_rbs\":\"\",\"temperature\":null,\"spo2\":null,\"symptoms\":\"\",\"chief_complaint\":\"adsf\",\"referred_by\":null,\"notes\":null,\"registered_at\":\"2026-05-21T02:43:00.000000Z\",\"sent_to_doctor_at\":\"2026-05-21T02:44:17.000000Z\",\"consultation_started_at\":null,\"sent_to_pharmacy_at\":null,\"completed_at\":null,\"created_at\":\"2026-05-21T02:43:00.000000Z\",\"updated_at\":\"2026-05-21T02:44:17.000000Z\",\"deleted_at\":null}', '192.168.1.10', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-20 21:14:17', '2026-05-20 21:14:17'),
(36, 3, 'sent_to_pharmacy', 'App\\Models\\PatientVisit', 10, NULL, '{\"id\":10,\"visit_number\":\"VIS2605210005\",\"patient_id\":3,\"branch_id\":1,\"department_id\":null,\"doctor_id\":1,\"receptionist_id\":1,\"token_number\":4,\"queue_number\":4,\"visit_type\":\"opd\",\"priority\":\"normal\",\"status\":\"at_pharmacy\",\"weight\":null,\"height\":null,\"bp\":\"\",\"sugar_rbs\":\"\",\"temperature\":null,\"spo2\":null,\"symptoms\":\"\",\"chief_complaint\":\"adsf\",\"referred_by\":null,\"notes\":null,\"registered_at\":\"2026-05-21T02:43:00.000000Z\",\"sent_to_doctor_at\":\"2026-05-21T02:44:17.000000Z\",\"consultation_started_at\":\"2026-05-21T02:44:31.000000Z\",\"sent_to_pharmacy_at\":\"2026-05-21T02:46:30.000000Z\",\"completed_at\":null,\"created_at\":\"2026-05-21T02:43:00.000000Z\",\"updated_at\":\"2026-05-21T02:46:30.000000Z\",\"deleted_at\":null}', '192.168.1.10', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-20 21:16:30', '2026-05-20 21:16:30'),
(37, 1, 'pharmacy_completed', 'App\\Models\\PharmacyOrder', 10, NULL, '{\"id\":10,\"order_number\":\"PH260521025147\",\"patient_visit_id\":10,\"prescription_id\":10,\"pharmacist_id\":1,\"status\":\"completed\",\"subtotal\":\"8.00\",\"discount\":\"0.00\",\"tax\":\"0.40\",\"total\":\"8.40\",\"notes\":null,\"completed_at\":\"2026-05-21T02:52:02.921988Z\",\"created_at\":\"2026-05-21T02:51:47.000000Z\",\"updated_at\":\"2026-05-21T02:52:02.000000Z\",\"deleted_at\":null,\"prescription\":{\"id\":10,\"prescription_number\":\"RX26052102463072\",\"consultation_id\":9,\"patient_visit_id\":10,\"doctor_id\":1,\"visibility\":\"public\",\"instructions\":\"\",\"status\":\"dispensed\",\"created_at\":\"2026-05-21T02:46:30.000000Z\",\"updated_at\":\"2026-05-21T02:52:02.000000Z\",\"deleted_at\":null},\"patient_visit\":{\"id\":10,\"visit_number\":\"VIS2605210005\",\"patient_id\":3,\"branch_id\":1,\"department_id\":null,\"doctor_id\":1,\"receptionist_id\":1,\"token_number\":4,\"queue_number\":4,\"visit_type\":\"opd\",\"priority\":\"normal\",\"status\":\"billing\",\"weight\":null,\"height\":null,\"bp\":\"\",\"sugar_rbs\":\"\",\"temperature\":null,\"spo2\":null,\"symptoms\":\"\",\"chief_complaint\":\"adsf\",\"referred_by\":null,\"notes\":null,\"registered_at\":\"2026-05-21T02:43:00.000000Z\",\"sent_to_doctor_at\":\"2026-05-21T02:44:17.000000Z\",\"consultation_started_at\":\"2026-05-21T02:44:31.000000Z\",\"sent_to_pharmacy_at\":\"2026-05-21T02:46:30.000000Z\",\"completed_at\":null,\"created_at\":\"2026-05-21T02:43:00.000000Z\",\"updated_at\":\"2026-05-21T02:52:02.000000Z\",\"deleted_at\":null,\"patient\":{\"id\":3,\"patient_id\":\"PAT26000003\",\"barcode\":\"CC00000003\",\"name\":\"Kamrankhan\",\"mobile\":\"9876543210\",\"alternate_mobile\":null,\"gender\":\"male\",\"age\":19,\"dob\":null,\"blood_group\":null,\"address\":null,\"city\":null,\"state\":null,\"pincode\":null,\"aadhaar\":null,\"occupation\":null,\"marital_status\":null,\"allergies\":null,\"existing_diseases\":null,\"photo_path\":null,\"branch_id\":1,\"registered_by\":2,\"created_at\":\"2026-05-20T19:47:58.000000Z\",\"updated_at\":\"2026-05-20T19:47:58.000000Z\",\"deleted_at\":null}},\"items\":[{\"id\":30,\"pharmacy_order_id\":10,\"medicine_id\":null,\"prescription_item_id\":26,\"medicine_name\":\"Amoxicillin 250mg\",\"quantity\":1,\"unit_price\":\"0.00\",\"gst_percent\":\"0.00\",\"discount\":\"0.00\",\"total\":\"0.00\",\"is_given\":0,\"is_otc\":0,\"batch_number\":null,\"created_at\":\"2026-05-21T02:51:47.000000Z\",\"updated_at\":\"2026-05-21T02:51:47.000000Z\"},{\"id\":31,\"pharmacy_order_id\":10,\"medicine_id\":null,\"prescription_item_id\":27,\"medicine_name\":\"ORS Sachet\",\"quantity\":1,\"unit_price\":\"0.00\",\"gst_percent\":\"0.00\",\"discount\":\"0.00\",\"total\":\"0.00\",\"is_given\":0,\"is_otc\":0,\"batch_number\":null,\"created_at\":\"2026-05-21T02:51:47.000000Z\",\"updated_at\":\"2026-05-21T02:51:47.000000Z\"},{\"id\":32,\"pharmacy_order_id\":10,\"medicine_id\":null,\"prescription_item_id\":null,\"medicine_name\":\"Amoxicillin 250mg\",\"quantity\":1,\"unit_price\":\"8.00\",\"gst_percent\":\"5.00\",\"discount\":\"0.00\",\"total\":\"8.00\",\"is_given\":0,\"is_otc\":1,\"batch_number\":null,\"created_at\":\"2026-05-21T02:51:55.000000Z\",\"updated_at\":\"2026-05-21T02:51:55.000000Z\"}]}', '192.168.1.4', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/29.0 Chrome/136.0.0.0 Mobile Safari/537.36', '2026-05-20 21:22:03', '2026-05-20 21:22:03'),
(38, 1, 'patient_registered', 'App\\Models\\PatientVisit', 11, NULL, '{\"visit_number\":\"VIS2605210006\",\"patient_id\":7,\"branch_id\":1,\"department_id\":null,\"doctor_id\":null,\"receptionist_id\":1,\"token_number\":5,\"queue_number\":5,\"visit_type\":\"opd\",\"priority\":\"normal\",\"status\":\"registered\",\"weight\":30,\"height\":null,\"bp\":\"100\\/90\",\"sugar_rbs\":\"100\",\"temperature\":null,\"spo2\":null,\"symptoms\":\"Symptoms\",\"chief_complaint\":\"Chief Complaint\\n\",\"referred_by\":null,\"notes\":null,\"registered_at\":\"2026-05-21T03:01:33.000000Z\",\"updated_at\":\"2026-05-21T03:01:33.000000Z\",\"created_at\":\"2026-05-21T03:01:33.000000Z\",\"id\":11}', '192.168.1.10', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-20 21:31:33', '2026-05-20 21:31:33'),
(39, 1, 'sent_to_doctor', 'App\\Models\\PatientVisit', 11, NULL, '{\"id\":11,\"visit_number\":\"VIS2605210006\",\"patient_id\":7,\"branch_id\":1,\"department_id\":null,\"doctor_id\":null,\"receptionist_id\":1,\"token_number\":5,\"queue_number\":5,\"visit_type\":\"opd\",\"priority\":\"normal\",\"status\":\"waiting\",\"weight\":\"30.00\",\"height\":null,\"bp\":\"100\\/90\",\"sugar_rbs\":\"100\",\"temperature\":null,\"spo2\":null,\"symptoms\":\"Symptoms\",\"chief_complaint\":\"Chief Complaint\\n\",\"referred_by\":null,\"notes\":null,\"registered_at\":\"2026-05-21T03:01:33.000000Z\",\"sent_to_doctor_at\":\"2026-05-21T03:02:22.000000Z\",\"consultation_started_at\":null,\"sent_to_pharmacy_at\":null,\"completed_at\":null,\"created_at\":\"2026-05-21T03:01:33.000000Z\",\"updated_at\":\"2026-05-21T03:02:22.000000Z\",\"deleted_at\":null}', '192.168.1.10', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-20 21:32:22', '2026-05-20 21:32:22'),
(40, 3, 'sent_to_pharmacy', 'App\\Models\\PatientVisit', 11, NULL, '{\"id\":11,\"visit_number\":\"VIS2605210006\",\"patient_id\":7,\"branch_id\":1,\"department_id\":null,\"doctor_id\":1,\"receptionist_id\":1,\"token_number\":5,\"queue_number\":5,\"visit_type\":\"opd\",\"priority\":\"normal\",\"status\":\"at_pharmacy\",\"weight\":\"30.00\",\"height\":null,\"bp\":\"100\\/90\",\"sugar_rbs\":\"100\",\"temperature\":null,\"spo2\":null,\"symptoms\":\"Symptoms\",\"chief_complaint\":\"Chief Complaint\\n\",\"referred_by\":null,\"notes\":null,\"registered_at\":\"2026-05-21T03:01:33.000000Z\",\"sent_to_doctor_at\":\"2026-05-21T03:02:22.000000Z\",\"consultation_started_at\":\"2026-05-21T03:02:38.000000Z\",\"sent_to_pharmacy_at\":\"2026-05-21T03:03:46.000000Z\",\"completed_at\":null,\"created_at\":\"2026-05-21T03:01:33.000000Z\",\"updated_at\":\"2026-05-21T03:03:46.000000Z\",\"deleted_at\":null}', '192.168.1.4', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/29.0 Chrome/136.0.0.0 Mobile Safari/537.36', '2026-05-20 21:33:46', '2026-05-20 21:33:46'),
(41, 4, 'pharmacy_completed', 'App\\Models\\PharmacyOrder', 11, NULL, '{\"id\":11,\"order_number\":\"PH260521030348\",\"patient_visit_id\":11,\"prescription_id\":11,\"pharmacist_id\":4,\"status\":\"completed\",\"subtotal\":\"23.00\",\"discount\":\"0.00\",\"tax\":\"0.40\",\"total\":\"23.40\",\"notes\":null,\"completed_at\":\"2026-05-21T03:04:22.147446Z\",\"created_at\":\"2026-05-21T03:03:48.000000Z\",\"updated_at\":\"2026-05-21T03:04:22.000000Z\",\"deleted_at\":null,\"prescription\":{\"id\":11,\"prescription_number\":\"RX26052103034620\",\"consultation_id\":10,\"patient_visit_id\":11,\"doctor_id\":1,\"visibility\":\"public\",\"instructions\":\"Hsjbsjs jw wjwvjgs jw \",\"status\":\"dispensed\",\"created_at\":\"2026-05-21T03:03:46.000000Z\",\"updated_at\":\"2026-05-21T03:04:22.000000Z\",\"deleted_at\":null},\"patient_visit\":{\"id\":11,\"visit_number\":\"VIS2605210006\",\"patient_id\":7,\"branch_id\":1,\"department_id\":null,\"doctor_id\":1,\"receptionist_id\":1,\"token_number\":5,\"queue_number\":5,\"visit_type\":\"opd\",\"priority\":\"normal\",\"status\":\"billing\",\"weight\":\"30.00\",\"height\":null,\"bp\":\"100\\/90\",\"sugar_rbs\":\"100\",\"temperature\":null,\"spo2\":null,\"symptoms\":\"Symptoms\",\"chief_complaint\":\"Chief Complaint\\n\",\"referred_by\":null,\"notes\":null,\"registered_at\":\"2026-05-21T03:01:33.000000Z\",\"sent_to_doctor_at\":\"2026-05-21T03:02:22.000000Z\",\"consultation_started_at\":\"2026-05-21T03:02:38.000000Z\",\"sent_to_pharmacy_at\":\"2026-05-21T03:03:46.000000Z\",\"completed_at\":null,\"created_at\":\"2026-05-21T03:01:33.000000Z\",\"updated_at\":\"2026-05-21T03:04:22.000000Z\",\"deleted_at\":null,\"patient\":{\"id\":7,\"patient_id\":\"PAT26000006\",\"barcode\":\"CC00000007\",\"name\":\"Junedkhan Imtiyaj\",\"mobile\":\"0098765432\",\"alternate_mobile\":null,\"gender\":\"male\",\"age\":19,\"dob\":null,\"blood_group\":null,\"address\":null,\"city\":null,\"state\":null,\"pincode\":null,\"aadhaar\":null,\"occupation\":null,\"marital_status\":null,\"allergies\":null,\"existing_diseases\":null,\"photo_path\":null,\"branch_id\":1,\"registered_by\":2,\"created_at\":\"2026-05-21T02:35:48.000000Z\",\"updated_at\":\"2026-05-21T02:35:48.000000Z\",\"deleted_at\":null}},\"items\":[{\"id\":33,\"pharmacy_order_id\":11,\"medicine_id\":2,\"prescription_item_id\":28,\"medicine_name\":\"Amoxicillin 250mg\",\"sku\":\"MED002\",\"quantity\":1,\"unit_price\":\"8.00\",\"gst_percent\":\"5.00\",\"discount\":\"0.00\",\"total\":\"8.00\",\"is_given\":0,\"is_otc\":0,\"notes\":null,\"batch_number\":null,\"created_at\":\"2026-05-21T03:03:49.000000Z\",\"updated_at\":\"2026-05-21T03:03:57.000000Z\"},{\"id\":34,\"pharmacy_order_id\":11,\"medicine_id\":5,\"prescription_item_id\":29,\"medicine_name\":\"ORS Sachet\",\"sku\":\"MED005\",\"quantity\":1,\"unit_price\":\"15.00\",\"gst_percent\":\"0.00\",\"discount\":\"0.00\",\"total\":\"15.00\",\"is_given\":0,\"is_otc\":0,\"notes\":null,\"batch_number\":null,\"created_at\":\"2026-05-21T03:03:49.000000Z\",\"updated_at\":\"2026-05-21T03:03:49.000000Z\"}]}', '192.168.1.10', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-20 21:34:22', '2026-05-20 21:34:22'),
(42, 2, 'patient_registered', 'App\\Models\\PatientVisit', 12, NULL, '{\"visit_number\":\"VIS2605210007\",\"patient_id\":8,\"branch_id\":1,\"department_id\":1,\"doctor_id\":null,\"receptionist_id\":2,\"token_number\":4,\"queue_number\":4,\"visit_type\":\"opd\",\"priority\":\"normal\",\"status\":\"registered\",\"weight\":51,\"height\":null,\"bp\":\"120\\/80\",\"sugar_rbs\":\"110\",\"temperature\":null,\"spo2\":null,\"symptoms\":\"Symptoms\",\"chief_complaint\":\"petma dukhe mathu dukhe\",\"referred_by\":null,\"notes\":null,\"registered_at\":\"2026-05-21T18:25:41.000000Z\",\"updated_at\":\"2026-05-21T18:25:41.000000Z\",\"created_at\":\"2026-05-21T18:25:41.000000Z\",\"id\":12}', '10.185.42.209', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-21 12:55:41', '2026-05-21 12:55:41'),
(43, 2, 'sent_to_doctor', 'App\\Models\\PatientVisit', 12, NULL, '{\"id\":12,\"visit_number\":\"VIS2605210007\",\"patient_id\":8,\"branch_id\":1,\"department_id\":1,\"doctor_id\":null,\"receptionist_id\":2,\"token_number\":4,\"queue_number\":4,\"visit_type\":\"opd\",\"priority\":\"normal\",\"status\":\"waiting\",\"weight\":\"51.00\",\"height\":null,\"bp\":\"120\\/80\",\"sugar_rbs\":\"110\",\"temperature\":null,\"spo2\":null,\"symptoms\":\"Symptoms\",\"chief_complaint\":\"petma dukhe mathu dukhe\",\"referred_by\":null,\"notes\":null,\"registered_at\":\"2026-05-21T18:25:41.000000Z\",\"sent_to_doctor_at\":\"2026-05-21T18:26:11.000000Z\",\"consultation_started_at\":null,\"sent_to_pharmacy_at\":null,\"completed_at\":null,\"created_at\":\"2026-05-21T18:25:41.000000Z\",\"updated_at\":\"2026-05-21T18:26:11.000000Z\",\"deleted_at\":null}', '10.185.42.209', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-21 12:56:11', '2026-05-21 12:56:11'),
(44, 2, 'patient_registered', 'App\\Models\\PatientVisit', 13, NULL, '{\"visit_number\":\"VIS2605210008\",\"patient_id\":9,\"branch_id\":1,\"department_id\":1,\"doctor_id\":null,\"receptionist_id\":2,\"token_number\":5,\"queue_number\":5,\"visit_type\":\"opd\",\"priority\":\"normal\",\"status\":\"registered\",\"weight\":50,\"height\":null,\"bp\":\"110\\/90\",\"sugar_rbs\":\"100\",\"temperature\":null,\"spo2\":null,\"symptoms\":\"Symptoms\",\"chief_complaint\":\"Chief Complaint\\n\",\"referred_by\":null,\"notes\":null,\"registered_at\":\"2026-05-21T18:27:07.000000Z\",\"updated_at\":\"2026-05-21T18:27:07.000000Z\",\"created_at\":\"2026-05-21T18:27:07.000000Z\",\"id\":13}', '10.185.42.209', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-21 12:57:07', '2026-05-21 12:57:07'),
(45, 2, 'sent_to_doctor', 'App\\Models\\PatientVisit', 13, NULL, '{\"id\":13,\"visit_number\":\"VIS2605210008\",\"patient_id\":9,\"branch_id\":1,\"department_id\":1,\"doctor_id\":null,\"receptionist_id\":2,\"token_number\":5,\"queue_number\":5,\"visit_type\":\"opd\",\"priority\":\"normal\",\"status\":\"waiting\",\"weight\":\"50.00\",\"height\":null,\"bp\":\"110\\/90\",\"sugar_rbs\":\"100\",\"temperature\":null,\"spo2\":null,\"symptoms\":\"Symptoms\",\"chief_complaint\":\"Chief Complaint\\n\",\"referred_by\":null,\"notes\":null,\"registered_at\":\"2026-05-21T18:27:07.000000Z\",\"sent_to_doctor_at\":\"2026-05-21T18:27:17.000000Z\",\"consultation_started_at\":null,\"sent_to_pharmacy_at\":null,\"completed_at\":null,\"created_at\":\"2026-05-21T18:27:07.000000Z\",\"updated_at\":\"2026-05-21T18:27:17.000000Z\",\"deleted_at\":null}', '10.185.42.209', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-21 12:57:17', '2026-05-21 12:57:17'),
(46, 3, 'sent_to_pharmacy', 'App\\Models\\PatientVisit', 12, NULL, '{\"id\":12,\"visit_number\":\"VIS2605210007\",\"patient_id\":8,\"branch_id\":1,\"department_id\":1,\"doctor_id\":1,\"receptionist_id\":2,\"token_number\":4,\"queue_number\":4,\"visit_type\":\"opd\",\"priority\":\"normal\",\"status\":\"at_pharmacy\",\"weight\":\"51.00\",\"height\":null,\"bp\":\"120\\/80\",\"sugar_rbs\":\"110\",\"temperature\":null,\"spo2\":null,\"symptoms\":\"Symptoms\",\"chief_complaint\":\"petma dukhe mathu dukhe\",\"referred_by\":null,\"notes\":null,\"registered_at\":\"2026-05-21T18:25:41.000000Z\",\"sent_to_doctor_at\":\"2026-05-21T18:26:11.000000Z\",\"consultation_started_at\":\"2026-05-21T18:27:30.000000Z\",\"sent_to_pharmacy_at\":\"2026-05-21T18:28:44.000000Z\",\"completed_at\":null,\"created_at\":\"2026-05-21T18:25:41.000000Z\",\"updated_at\":\"2026-05-21T18:28:44.000000Z\",\"deleted_at\":null}', '10.185.42.228', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', '2026-05-21 12:58:44', '2026-05-21 12:58:44'),
(47, 4, 'pharmacy_completed', 'App\\Models\\PharmacyOrder', 12, NULL, '{\"id\":12,\"order_number\":\"PH260521182848\",\"patient_visit_id\":12,\"prescription_id\":13,\"pharmacist_id\":4,\"status\":\"completed\",\"subtotal\":\"25.00\",\"discount\":\"0.00\",\"tax\":\"0.50\",\"total\":\"25.50\",\"notes\":null,\"completed_at\":\"2026-05-21T18:29:10.899984Z\",\"created_at\":\"2026-05-21T18:28:48.000000Z\",\"updated_at\":\"2026-05-21T18:29:10.000000Z\",\"deleted_at\":null,\"prescription\":{\"id\":13,\"prescription_number\":\"RX26052118284420\",\"consultation_id\":11,\"patient_visit_id\":12,\"doctor_id\":1,\"visibility\":\"public\",\"instructions\":\"Hshsbsh\",\"status\":\"dispensed\",\"created_at\":\"2026-05-21T18:28:44.000000Z\",\"updated_at\":\"2026-05-21T18:29:10.000000Z\",\"deleted_at\":null},\"patient_visit\":{\"id\":12,\"visit_number\":\"VIS2605210007\",\"patient_id\":8,\"branch_id\":1,\"department_id\":1,\"doctor_id\":1,\"receptionist_id\":2,\"token_number\":4,\"queue_number\":4,\"visit_type\":\"opd\",\"priority\":\"normal\",\"status\":\"billing\",\"weight\":\"51.00\",\"height\":null,\"bp\":\"120\\/80\",\"sugar_rbs\":\"110\",\"temperature\":null,\"spo2\":null,\"symptoms\":\"Symptoms\",\"chief_complaint\":\"petma dukhe mathu dukhe\",\"referred_by\":null,\"notes\":null,\"registered_at\":\"2026-05-21T18:25:41.000000Z\",\"sent_to_doctor_at\":\"2026-05-21T18:26:11.000000Z\",\"consultation_started_at\":\"2026-05-21T18:27:30.000000Z\",\"sent_to_pharmacy_at\":\"2026-05-21T18:28:44.000000Z\",\"completed_at\":null,\"created_at\":\"2026-05-21T18:25:41.000000Z\",\"updated_at\":\"2026-05-21T18:29:10.000000Z\",\"deleted_at\":null,\"patient\":{\"id\":8,\"patient_id\":\"PAT26000007\",\"barcode\":\"CC00000008\",\"name\":\"Hannan Lathiya\",\"mobile\":\"0987654333\",\"alternate_mobile\":null,\"gender\":\"male\",\"age\":22,\"dob\":null,\"blood_group\":null,\"address\":null,\"city\":null,\"state\":null,\"pincode\":null,\"aadhaar\":null,\"occupation\":null,\"marital_status\":null,\"allergies\":null,\"existing_diseases\":null,\"photo_path\":null,\"branch_id\":1,\"registered_by\":2,\"created_at\":\"2026-05-21T18:25:41.000000Z\",\"updated_at\":\"2026-05-21T18:25:41.000000Z\",\"deleted_at\":null}},\"items\":[{\"id\":35,\"pharmacy_order_id\":12,\"medicine_id\":2,\"prescription_item_id\":32,\"medicine_name\":\"Amoxicillin 250mg\",\"sku\":\"MED002\",\"quantity\":1,\"unit_price\":\"8.00\",\"gst_percent\":\"5.00\",\"discount\":\"0.00\",\"total\":\"8.00\",\"is_given\":0,\"is_otc\":0,\"notes\":null,\"batch_number\":null,\"created_at\":\"2026-05-21T18:28:48.000000Z\",\"updated_at\":\"2026-05-21T18:28:48.000000Z\"},{\"id\":36,\"pharmacy_order_id\":12,\"medicine_id\":1,\"prescription_item_id\":33,\"medicine_name\":\"Paracetamol 500mg\",\"sku\":\"MED001\",\"quantity\":1,\"unit_price\":\"2.00\",\"gst_percent\":\"5.00\",\"discount\":\"0.00\",\"total\":\"2.00\",\"is_given\":0,\"is_otc\":0,\"notes\":null,\"batch_number\":null,\"created_at\":\"2026-05-21T18:28:48.000000Z\",\"updated_at\":\"2026-05-21T18:28:48.000000Z\"},{\"id\":37,\"pharmacy_order_id\":12,\"medicine_id\":5,\"prescription_item_id\":null,\"medicine_name\":\"ORS Sachet\",\"sku\":\"MED005\",\"quantity\":1,\"unit_price\":\"15.00\",\"gst_percent\":\"0.00\",\"discount\":\"0.00\",\"total\":\"15.00\",\"is_given\":0,\"is_otc\":1,\"notes\":null,\"batch_number\":null,\"created_at\":\"2026-05-21T18:28:56.000000Z\",\"updated_at\":\"2026-05-21T18:28:56.000000Z\"}]}', '10.185.42.209', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-21 12:59:10', '2026-05-21 12:59:10'),
(48, 3, 'sent_to_pharmacy', 'App\\Models\\PatientVisit', 13, NULL, '{\"id\":13,\"visit_number\":\"VIS2605210008\",\"patient_id\":9,\"branch_id\":1,\"department_id\":1,\"doctor_id\":1,\"receptionist_id\":2,\"token_number\":5,\"queue_number\":5,\"visit_type\":\"opd\",\"priority\":\"normal\",\"status\":\"at_pharmacy\",\"weight\":\"50.00\",\"height\":null,\"bp\":\"110\\/90\",\"sugar_rbs\":\"100\",\"temperature\":null,\"spo2\":null,\"symptoms\":\"Symptoms\",\"chief_complaint\":\"Chief Complaint\\n\",\"referred_by\":null,\"notes\":null,\"registered_at\":\"2026-05-21T18:27:07.000000Z\",\"sent_to_doctor_at\":\"2026-05-21T18:27:17.000000Z\",\"consultation_started_at\":\"2026-05-21T18:29:36.000000Z\",\"sent_to_pharmacy_at\":\"2026-05-21T18:29:43.000000Z\",\"completed_at\":null,\"created_at\":\"2026-05-21T18:27:07.000000Z\",\"updated_at\":\"2026-05-21T18:29:43.000000Z\",\"deleted_at\":null}', '10.185.42.228', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', '2026-05-21 12:59:43', '2026-05-21 12:59:43'),
(49, 4, 'pharmacy_completed', 'App\\Models\\PharmacyOrder', 13, NULL, '{\"id\":13,\"order_number\":\"PH260521182947\",\"patient_visit_id\":13,\"prescription_id\":null,\"pharmacist_id\":4,\"status\":\"completed\",\"subtotal\":\"23.00\",\"discount\":\"0.00\",\"tax\":\"0.40\",\"total\":\"23.40\",\"notes\":null,\"completed_at\":\"2026-05-21T18:29:57.060869Z\",\"created_at\":\"2026-05-21T18:29:47.000000Z\",\"updated_at\":\"2026-05-21T18:29:57.000000Z\",\"deleted_at\":null,\"prescription\":null,\"patient_visit\":{\"id\":13,\"visit_number\":\"VIS2605210008\",\"patient_id\":9,\"branch_id\":1,\"department_id\":1,\"doctor_id\":1,\"receptionist_id\":2,\"token_number\":5,\"queue_number\":5,\"visit_type\":\"opd\",\"priority\":\"normal\",\"status\":\"billing\",\"weight\":\"50.00\",\"height\":null,\"bp\":\"110\\/90\",\"sugar_rbs\":\"100\",\"temperature\":null,\"spo2\":null,\"symptoms\":\"Symptoms\",\"chief_complaint\":\"Chief Complaint\\n\",\"referred_by\":null,\"notes\":null,\"registered_at\":\"2026-05-21T18:27:07.000000Z\",\"sent_to_doctor_at\":\"2026-05-21T18:27:17.000000Z\",\"consultation_started_at\":\"2026-05-21T18:29:36.000000Z\",\"sent_to_pharmacy_at\":\"2026-05-21T18:29:43.000000Z\",\"completed_at\":null,\"created_at\":\"2026-05-21T18:27:07.000000Z\",\"updated_at\":\"2026-05-21T18:29:57.000000Z\",\"deleted_at\":null,\"patient\":{\"id\":9,\"patient_id\":\"PAT26000008\",\"barcode\":\"CC00000009\",\"name\":\"Madin\",\"mobile\":\"99999999999\",\"alternate_mobile\":null,\"gender\":\"male\",\"age\":24,\"dob\":null,\"blood_group\":null,\"address\":null,\"city\":null,\"state\":null,\"pincode\":null,\"aadhaar\":null,\"occupation\":null,\"marital_status\":null,\"allergies\":null,\"existing_diseases\":null,\"photo_path\":null,\"branch_id\":1,\"registered_by\":2,\"created_at\":\"2026-05-21T18:27:07.000000Z\",\"updated_at\":\"2026-05-21T18:27:07.000000Z\",\"deleted_at\":null}},\"items\":[{\"id\":38,\"pharmacy_order_id\":13,\"medicine_id\":2,\"prescription_item_id\":null,\"medicine_name\":\"Amoxicillin 250mg\",\"sku\":\"MED002\",\"quantity\":1,\"unit_price\":\"8.00\",\"gst_percent\":\"5.00\",\"discount\":\"0.00\",\"total\":\"8.00\",\"is_given\":0,\"is_otc\":1,\"notes\":null,\"batch_number\":null,\"created_at\":\"2026-05-21T18:29:50.000000Z\",\"updated_at\":\"2026-05-21T18:29:50.000000Z\"},{\"id\":39,\"pharmacy_order_id\":13,\"medicine_id\":5,\"prescription_item_id\":null,\"medicine_name\":\"ORS Sachet\",\"sku\":\"MED005\",\"quantity\":1,\"unit_price\":\"15.00\",\"gst_percent\":\"0.00\",\"discount\":\"0.00\",\"total\":\"15.00\",\"is_given\":0,\"is_otc\":1,\"notes\":null,\"batch_number\":null,\"created_at\":\"2026-05-21T18:29:52.000000Z\",\"updated_at\":\"2026-05-21T18:29:52.000000Z\"}]}', '10.185.42.209', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-21 12:59:57', '2026-05-21 12:59:57'),
(50, 2, 'patient_registered', 'App\\Models\\PatientVisit', 14, NULL, '{\"visit_number\":\"VIS2605220001\",\"patient_id\":10,\"branch_id\":1,\"department_id\":1,\"doctor_id\":null,\"receptionist_id\":2,\"token_number\":1,\"queue_number\":1,\"visit_type\":\"opd\",\"priority\":\"normal\",\"status\":\"registered\",\"weight\":22,\"height\":null,\"bp\":\"120\\/90\",\"sugar_rbs\":\"100\",\"temperature\":0.1,\"spo2\":null,\"symptoms\":\"Symptoms\\nSymptoms\\nSymptoms\\nSymptoms\\n\",\"chief_complaint\":\"pharmacypharmacypharmacypharmacy\",\"referred_by\":null,\"notes\":null,\"registered_at\":\"2026-05-22T05:52:32.000000Z\",\"updated_at\":\"2026-05-22T05:52:32.000000Z\",\"created_at\":\"2026-05-22T05:52:32.000000Z\",\"id\":14}', '10.41.223.209', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-22 00:22:32', '2026-05-22 00:22:32'),
(51, 2, 'sent_to_doctor', 'App\\Models\\PatientVisit', 14, NULL, '{\"id\":14,\"visit_number\":\"VIS2605220001\",\"patient_id\":10,\"branch_id\":1,\"department_id\":1,\"doctor_id\":null,\"receptionist_id\":2,\"token_number\":1,\"queue_number\":1,\"visit_type\":\"opd\",\"priority\":\"normal\",\"status\":\"waiting\",\"weight\":\"22.00\",\"height\":null,\"bp\":\"120\\/90\",\"sugar_rbs\":\"100\",\"temperature\":\"0.10\",\"spo2\":null,\"symptoms\":\"Symptoms\\nSymptoms\\nSymptoms\\nSymptoms\\n\",\"chief_complaint\":\"pharmacypharmacypharmacypharmacy\",\"referred_by\":null,\"notes\":null,\"registered_at\":\"2026-05-22T05:52:32.000000Z\",\"sent_to_doctor_at\":\"2026-05-22T05:53:21.000000Z\",\"consultation_started_at\":null,\"sent_to_pharmacy_at\":null,\"completed_at\":null,\"created_at\":\"2026-05-22T05:52:32.000000Z\",\"updated_at\":\"2026-05-22T05:53:21.000000Z\",\"deleted_at\":null}', '10.41.223.209', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-22 00:23:21', '2026-05-22 00:23:21'),
(52, 3, 'sent_to_pharmacy', 'App\\Models\\PatientVisit', 14, NULL, '{\"id\":14,\"visit_number\":\"VIS2605220001\",\"patient_id\":10,\"branch_id\":1,\"department_id\":1,\"doctor_id\":1,\"receptionist_id\":2,\"token_number\":1,\"queue_number\":1,\"visit_type\":\"opd\",\"priority\":\"normal\",\"status\":\"at_pharmacy\",\"weight\":\"22.00\",\"height\":null,\"bp\":\"120\\/90\",\"sugar_rbs\":\"100\",\"temperature\":\"0.10\",\"spo2\":null,\"symptoms\":\"Symptoms\\nSymptoms\\nSymptoms\\nSymptoms\\n\",\"chief_complaint\":\"pharmacypharmacypharmacypharmacy\",\"referred_by\":null,\"notes\":null,\"registered_at\":\"2026-05-22T05:52:32.000000Z\",\"sent_to_doctor_at\":\"2026-05-22T05:53:21.000000Z\",\"consultation_started_at\":\"2026-05-22T05:53:34.000000Z\",\"sent_to_pharmacy_at\":\"2026-05-22T05:55:31.000000Z\",\"completed_at\":null,\"created_at\":\"2026-05-22T05:52:32.000000Z\",\"updated_at\":\"2026-05-22T05:55:31.000000Z\",\"deleted_at\":null}', '10.41.223.181', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-22 00:25:31', '2026-05-22 00:25:31'),
(53, 4, 'pharmacy_completed', 'App\\Models\\PharmacyOrder', 14, NULL, '{\"id\":14,\"order_number\":\"PH260522055536\",\"patient_visit_id\":14,\"prescription_id\":16,\"pharmacist_id\":4,\"status\":\"completed\",\"subtotal\":\"162232.00\",\"discount\":\"0.00\",\"tax\":\"8111.60\",\"total\":\"170343.60\",\"notes\":null,\"completed_at\":\"2026-05-22T05:56:10.422044Z\",\"created_at\":\"2026-05-22T05:55:36.000000Z\",\"updated_at\":\"2026-05-22T05:56:10.000000Z\",\"deleted_at\":null,\"prescription\":{\"id\":16,\"prescription_number\":\"RX26052205553118\",\"consultation_id\":13,\"patient_visit_id\":14,\"doctor_id\":1,\"visibility\":\"public\",\"instructions\":\"\",\"status\":\"dispensed\",\"created_at\":\"2026-05-22T05:55:31.000000Z\",\"updated_at\":\"2026-05-22T05:56:10.000000Z\",\"deleted_at\":null},\"patient_visit\":{\"id\":14,\"visit_number\":\"VIS2605220001\",\"patient_id\":10,\"branch_id\":1,\"department_id\":1,\"doctor_id\":1,\"receptionist_id\":2,\"token_number\":1,\"queue_number\":1,\"visit_type\":\"opd\",\"priority\":\"normal\",\"status\":\"billing\",\"weight\":\"22.00\",\"height\":null,\"bp\":\"120\\/90\",\"sugar_rbs\":\"100\",\"temperature\":\"0.10\",\"spo2\":null,\"symptoms\":\"Symptoms\\nSymptoms\\nSymptoms\\nSymptoms\\n\",\"chief_complaint\":\"pharmacypharmacypharmacypharmacy\",\"referred_by\":null,\"notes\":null,\"registered_at\":\"2026-05-22T05:52:32.000000Z\",\"sent_to_doctor_at\":\"2026-05-22T05:53:21.000000Z\",\"consultation_started_at\":\"2026-05-22T05:53:34.000000Z\",\"sent_to_pharmacy_at\":\"2026-05-22T05:55:31.000000Z\",\"completed_at\":null,\"created_at\":\"2026-05-22T05:52:32.000000Z\",\"updated_at\":\"2026-05-22T05:56:10.000000Z\",\"deleted_at\":null,\"patient\":{\"id\":10,\"patient_id\":\"PAT26000009\",\"barcode\":\"CC00000010\",\"name\":\"amzadkhan belim\",\"mobile\":\"0187654333\",\"alternate_mobile\":null,\"gender\":\"male\",\"age\":null,\"dob\":null,\"blood_group\":null,\"address\":null,\"city\":null,\"state\":null,\"pincode\":null,\"aadhaar\":null,\"occupation\":null,\"marital_status\":null,\"allergies\":null,\"existing_diseases\":null,\"photo_path\":null,\"branch_id\":1,\"registered_by\":2,\"created_at\":\"2026-05-22T05:52:32.000000Z\",\"updated_at\":\"2026-05-22T05:52:32.000000Z\",\"deleted_at\":null}},\"items\":[{\"id\":40,\"pharmacy_order_id\":14,\"medicine_id\":1,\"prescription_item_id\":37,\"medicine_name\":\"Paracetamol 500mg\",\"sku\":\"MED001\",\"quantity\":81111,\"unit_price\":\"2.00\",\"gst_percent\":\"5.00\",\"discount\":\"0.00\",\"total\":\"162222.00\",\"is_given\":0,\"is_otc\":0,\"notes\":null,\"batch_number\":null,\"created_at\":\"2026-05-22T05:55:37.000000Z\",\"updated_at\":\"2026-05-22T05:55:37.000000Z\"},{\"id\":41,\"pharmacy_order_id\":14,\"medicine_id\":2,\"prescription_item_id\":38,\"medicine_name\":\"Amoxicillin 250mg\",\"sku\":\"MED002\",\"quantity\":1,\"unit_price\":\"8.00\",\"gst_percent\":\"5.00\",\"discount\":\"0.00\",\"total\":\"8.00\",\"is_given\":0,\"is_otc\":0,\"notes\":null,\"batch_number\":null,\"created_at\":\"2026-05-22T05:55:37.000000Z\",\"updated_at\":\"2026-05-22T05:55:37.000000Z\"},{\"id\":42,\"pharmacy_order_id\":14,\"medicine_id\":1,\"prescription_item_id\":null,\"medicine_name\":\"Paracetamol 500mg\",\"sku\":\"MED001\",\"quantity\":1,\"unit_price\":\"2.00\",\"gst_percent\":\"5.00\",\"discount\":\"0.00\",\"total\":\"2.00\",\"is_given\":0,\"is_otc\":1,\"notes\":null,\"batch_number\":null,\"created_at\":\"2026-05-22T05:55:46.000000Z\",\"updated_at\":\"2026-05-22T05:55:46.000000Z\"}]}', '10.41.223.209', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-22 00:26:10', '2026-05-22 00:26:10');

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(20) NOT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `pincode` varchar(10) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `gst_number` varchar(30) DEFAULT NULL,
  `logo_path` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`id`, `name`, `code`, `address`, `city`, `state`, `pincode`, `phone`, `email`, `gst_number`, `logo_path`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'ClinicCare Charitable Trust Hospital', 'MAIN', '123 Medical Campus Road', 'Mumbai', 'Maharashtra', '400001', '+91 98765 43210', 'info@cliniccare.org', '27AAAAA0000A1Z5', NULL, 1, '2026-05-20 13:23:16', '2026-05-20 13:23:16', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('cliniccare-hms-cache-setting..gst_number', 's:0:\"\";', 1779432958),
('cliniccare-hms-cache-setting..hospital_address', 's:55:\"Behid Bus Station, Railway Crossing, Okha Port - 361350\";', 1779432753),
('cliniccare-hms-cache-setting..hospital_name', 's:27:\"CosyClinic Charitable Trust\";', 1779432753),
('cliniccare-hms-cache-setting..invoice_footer', 's:49:\"Thank you for visiting CosyClinic. Get well soon!\";', 1779432958);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(20) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `branch_id`, `name`, `code`, `description`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'General OPD', 'GEN', 'General Out Patient Department', 1, '2026-05-20 13:23:16', '2026-05-20 13:23:16', NULL),
(2, 1, 'Pediatrics', 'PED', 'Child Care OPD', 1, '2026-05-20 13:23:16', '2026-05-20 13:23:16', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `department_id` bigint(20) UNSIGNED DEFAULT NULL,
  `registration_number` varchar(255) DEFAULT NULL,
  `specialization` varchar(255) DEFAULT NULL,
  `consultation_fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`id`, `user_id`, `department_id`, `registration_number`, `specialization`, `consultation_fee`, `is_available`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 3, 1, 'MCI-12345', 'General Medicine', 200.00, 1, '2026-05-20 13:23:23', '2026-05-20 13:23:23', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `doctor_consultations`
--

CREATE TABLE `doctor_consultations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `patient_visit_id` bigint(20) UNSIGNED NOT NULL,
  `doctor_id` bigint(20) UNSIGNED NOT NULL,
  `diagnosis` text DEFAULT NULL,
  `clinical_notes` text DEFAULT NULL,
  `internal_notes` text DEFAULT NULL,
  `public_notes` text DEFAULT NULL,
  `medical_advice` text DEFAULT NULL,
  `diet_plan` text DEFAULT NULL,
  `follow_up_date` date DEFAULT NULL,
  `show_diagnosis_to_pharmacy` tinyint(1) NOT NULL DEFAULT 1,
  `show_prescription_notes` tinyint(1) NOT NULL DEFAULT 1,
  `show_reports` tinyint(1) NOT NULL DEFAULT 0,
  `show_consultation_charges` tinyint(1) NOT NULL DEFAULT 1,
  `show_instructions` tinyint(1) NOT NULL DEFAULT 1,
  `consultation_charge` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('in_progress','completed') NOT NULL DEFAULT 'in_progress',
  `started_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `doctor_consultations`
--

INSERT INTO `doctor_consultations` (`id`, `patient_visit_id`, `doctor_id`, `diagnosis`, `clinical_notes`, `internal_notes`, `public_notes`, `medical_advice`, `diet_plan`, `follow_up_date`, `show_diagnosis_to_pharmacy`, `show_prescription_notes`, `show_reports`, `show_consultation_charges`, `show_instructions`, `consultation_charge`, `status`, `started_at`, `completed_at`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 'zxcv', 'xcv', '', 'xzcv', 'zxcvz', 'xzcvz', '2026-05-28', 1, 1, 0, 1, 1, 0.00, 'completed', '2026-05-20 13:28:55', '2026-05-20 13:30:01', '2026-05-20 13:28:55', '2026-05-20 13:30:01', NULL),
(2, 2, 1, 'asldjk asdfha;sldfj s;adfh;asd jf;lasjd flasdjfas ;dfj;lasd fasdjf;lasd ', 'jasldfj ljas;ldfj sohdafnasdlfj ;sdhfoasdjk ;', '', 'lsajdf isd ;ofjsaodif ', 'asdfoi l', 'oasdinf ', '2026-05-28', 1, 1, 0, 1, 1, 0.00, 'completed', '2026-05-20 13:54:35', '2026-05-20 13:55:24', '2026-05-20 13:54:35', '2026-05-20 13:55:24', NULL),
(3, 3, 1, 'Diagnosis Diagnosis Diagnosis', 'Clinical Notes (Private) Clinical Notes (Private)', '', 'Public Notes Public Notes', 'Medical Advice Medical Advice', 'Diet Plan\nDiet Plan\nDiet Plan\nDiet Plan', '2026-05-28', 1, 1, 0, 1, 1, 0.00, 'completed', '2026-05-20 14:19:08', '2026-05-20 14:21:15', '2026-05-20 14:19:08', '2026-05-20 14:21:15', NULL),
(4, 4, 1, '', '', '', '', '', '', NULL, 1, 1, 0, 1, 1, 0.00, 'completed', '2026-05-20 14:25:06', '2026-05-20 14:25:47', '2026-05-20 14:25:06', '2026-05-20 14:25:47', NULL),
(5, 6, 1, 'Diagnosis Diagnosis Diagnosis DiagnosisDiagnosis', 'Clinical Notes (Private)\n Clinical Notes (Private)\n', '', 'Public Notes\nPublic Notes\n', 'Medical Advice\nMedical Advice\n', 'Diet Plan\nDiet Plan\n', '2026-05-28', 1, 1, 0, 1, 1, 0.00, 'completed', '2026-05-20 20:49:06', '2026-05-20 20:50:35', '2026-05-20 20:49:06', '2026-05-20 20:50:35', NULL),
(6, 7, 1, 'Jdj', 'Jejej', '', 'Jdj', 'Jsh', 'Jzj', '2026-05-28', 1, 1, 0, 1, 1, 0.00, 'completed', '2026-05-20 21:03:41', '2026-05-20 21:07:29', '2026-05-20 21:03:41', '2026-05-20 21:07:29', NULL),
(7, 9, 1, 'Sj', 'Dj', '', 'Sj', 'Ej', 'Ej', '2026-05-28', 1, 1, 0, 1, 1, 0.00, 'completed', '2026-05-20 21:07:49', '2026-05-20 21:09:43', '2026-05-20 21:07:49', '2026-05-20 21:09:43', NULL),
(8, 8, 1, 'Dh', 'Dj', '', 'Dj', 'Dj', 'Dj', '2026-05-29', 1, 1, 0, 1, 1, 0.00, 'completed', '2026-05-20 21:09:48', '2026-05-20 21:10:02', '2026-05-20 21:09:48', '2026-05-20 21:10:02', NULL),
(9, 10, 1, 'asdf', 'asdf', '', 'asd', 'asdf', 'asdf', '2026-05-30', 1, 1, 0, 1, 1, 0.00, 'completed', '2026-05-20 21:14:31', '2026-05-20 21:16:30', '2026-05-20 21:14:31', '2026-05-20 21:16:30', NULL),
(10, 11, 1, 'Shs', 'Au', '', 'Sh', 'Ah', 'Ksj', '2026-05-29', 1, 1, 0, 1, 1, 0.00, 'completed', '2026-05-20 21:32:38', '2026-05-20 21:33:46', '2026-05-20 21:32:38', '2026-05-20 21:33:46', NULL),
(11, 12, 1, 'Gshs', 'Jssjsj', '', 'Ajsj', 'Ajsj', 'Najs', '2026-05-28', 1, 1, 0, 1, 1, 0.00, 'completed', '2026-05-21 12:57:30', '2026-05-21 12:58:44', '2026-05-21 12:57:30', '2026-05-21 12:58:44', NULL),
(12, 13, 1, '', '', '', '', '', '', NULL, 1, 1, 0, 1, 1, 0.00, 'completed', '2026-05-21 12:59:36', '2026-05-21 12:59:43', '2026-05-21 12:59:36', '2026-05-21 12:59:43', NULL),
(13, 14, 1, 'jh', 'no', '', 'hi', 'hihij', 'huhihij', NULL, 1, 1, 0, 1, 1, 0.00, 'completed', '2026-05-22 00:23:34', '2026-05-22 00:25:31', '2026-05-22 00:23:34', '2026-05-22 00:25:31', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` varchar(255) NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_settings`
--

CREATE TABLE `hospital_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `group` varchar(255) NOT NULL DEFAULT 'general',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hospital_settings`
--

INSERT INTO `hospital_settings` (`id`, `branch_id`, `key`, `value`, `group`, `created_at`, `updated_at`) VALUES
(1, 1, 'hospital_name', 'CosyClinic Charitable Trust', 'general', '2026-05-20 13:23:26', '2026-05-20 21:41:34'),
(2, 1, 'hospital_address', 'Behid Bus Station, Railway Crossing, Okha Port - 361350', 'general', '2026-05-20 13:23:26', '2026-05-20 21:41:34'),
(3, 1, 'hospital_phone', '+91 78784 78692', 'general', '2026-05-20 13:23:26', '2026-05-20 21:41:34'),
(4, 1, 'gst_number', NULL, 'general', '2026-05-20 13:23:26', '2026-05-20 21:41:34'),
(5, 1, 'prescription_header', 'CosyClinic HMS - OPD Prescription', 'general', '2026-05-20 13:23:26', '2026-05-20 21:41:34'),
(6, 1, 'invoice_footer', 'Thank you for visiting CosyClinic. Get well soon!', 'general', '2026-05-20 13:23:26', '2026-05-20 21:41:34');

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice_number` varchar(30) NOT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
  `patient_visit_id` bigint(20) UNSIGNED DEFAULT NULL,
  `type` enum('opd','consultation','pharmacy','lab','final') NOT NULL DEFAULT 'opd',
  `subtotal` decimal(12,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `tax` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `paid_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `due_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `payment_status` enum('unpaid','partial','paid') NOT NULL DEFAULT 'unpaid',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`id`, `invoice_number`, `patient_id`, `patient_visit_id`, `type`, `subtotal`, `discount`, `tax`, `total`, `paid_amount`, `due_amount`, `payment_status`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'INV26052000001', 1, 1, 'pharmacy', 0.00, 0.00, 0.00, 0.00, 100.00, 0.00, 'paid', 4, '2026-05-20 13:31:02', '2026-05-20 20:53:23', NULL),
(2, 'INV26052000002', 2, 2, 'pharmacy', 0.00, 0.00, 0.00, 0.00, 10.00, 0.00, 'paid', 4, '2026-05-20 13:56:16', '2026-05-20 20:53:15', NULL),
(3, 'INV26052000003', 3, 3, 'pharmacy', 0.00, 0.00, 0.00, 0.00, 10.00, 0.00, 'paid', 4, '2026-05-20 14:21:48', '2026-05-20 20:53:10', NULL),
(4, 'INV26052100004', 5, 6, 'pharmacy', 30.00, 0.00, 0.75, 30.75, 30.75, 0.00, 'paid', 4, '2026-05-20 20:52:39', '2026-05-20 20:52:39', NULL),
(5, 'INV26052100005', 6, 7, 'pharmacy', 8.00, 0.00, 0.40, 8.40, 8.40, 0.00, 'paid', 4, '2026-05-20 21:08:45', '2026-05-20 21:08:45', NULL),
(6, 'INV26052100006', 3, 8, 'pharmacy', 23.00, 0.00, 0.40, 23.40, 23.40, 0.00, 'paid', 4, '2026-05-20 21:10:30', '2026-05-20 21:10:30', NULL),
(7, 'INV26052100007', 7, 9, 'pharmacy', 23.00, 3.40, 0.40, 20.00, 20.00, 0.00, 'paid', 4, '2026-05-20 21:11:04', '2026-05-20 21:11:04', NULL),
(8, 'INV26052100008', 3, 10, 'pharmacy', 8.00, 0.00, 0.40, 8.40, 8.40, 0.00, 'paid', 1, '2026-05-20 21:22:02', '2026-05-20 21:22:03', NULL),
(9, 'INV26052100009', 7, 11, 'pharmacy', 23.00, 0.00, 0.40, 23.40, 23.40, 0.00, 'paid', 4, '2026-05-20 21:34:22', '2026-05-20 21:34:22', NULL),
(10, 'INV26052100010', 8, 12, 'pharmacy', 25.00, 0.00, 0.50, 25.50, 25.50, 0.00, 'paid', 4, '2026-05-21 12:59:10', '2026-05-21 12:59:10', NULL),
(11, 'INV26052100011', 9, 13, 'pharmacy', 23.00, 0.00, 0.40, 23.40, 23.40, 0.00, 'paid', 4, '2026-05-21 12:59:57', '2026-05-21 12:59:57', NULL),
(12, 'INV26052200012', 10, 14, 'pharmacy', 162232.00, 0.00, 8111.60, 170343.60, 170343.60, 0.00, 'paid', 4, '2026-05-22 00:26:10', '2026-05-22 00:26:10', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `invoice_items`
--

CREATE TABLE `invoice_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice_id` bigint(20) UNSIGNED NOT NULL,
  `description` varchar(255) NOT NULL,
  `quantity` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `unit_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `tax` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `invoice_items`
--

INSERT INTO `invoice_items` (`id`, `invoice_id`, `description`, `quantity`, `unit_price`, `tax`, `total`, `created_at`, `updated_at`) VALUES
(1, 1, 'Amoxicillin 250mg', 1, 0.00, 0.00, 0.00, '2026-05-20 13:31:02', '2026-05-20 13:31:02'),
(2, 1, 'Cetirizine 10mg', 1, 0.00, 0.00, 0.00, '2026-05-20 13:31:02', '2026-05-20 13:31:02'),
(3, 1, 'Omeprazole 20mg', 1, 0.00, 0.00, 0.00, '2026-05-20 13:31:02', '2026-05-20 13:31:02'),
(4, 1, 'ORS Sachet', 1, 0.00, 0.00, 0.00, '2026-05-20 13:31:02', '2026-05-20 13:31:02'),
(5, 1, 'Paracetamol 500mg', 1, 0.00, 0.00, 0.00, '2026-05-20 13:31:02', '2026-05-20 13:31:02'),
(6, 3, 'Amoxicillin 250mg', 1, 0.00, 0.00, 0.00, '2026-05-20 14:21:48', '2026-05-20 14:21:48'),
(7, 3, 'Amoxicillin 250mg', 1, 0.00, 0.00, 0.00, '2026-05-20 14:21:48', '2026-05-20 14:21:48'),
(8, 3, 'ORS Sachet', 1, 0.00, 0.00, 0.00, '2026-05-20 14:21:48', '2026-05-20 14:21:48'),
(9, 4, 'Amoxicillin 250mg', 1, 0.00, 0.00, 0.00, '2026-05-20 20:52:39', '2026-05-20 20:52:39'),
(10, 4, 'Omeprazole 20mg', 1, 0.00, 0.00, 0.00, '2026-05-20 20:52:39', '2026-05-20 20:52:39'),
(11, 4, 'Paracetamol 500mg', 1, 0.00, 0.00, 0.00, '2026-05-20 20:52:39', '2026-05-20 20:52:39'),
(12, 4, 'ORS Sachet', 1, 15.00, 0.00, 15.00, '2026-05-20 20:52:39', '2026-05-20 20:52:39'),
(13, 4, 'Paracetamol 500mg', 1, 2.00, 0.10, 2.10, '2026-05-20 20:52:39', '2026-05-20 20:52:39'),
(14, 4, 'Omeprazole 20mg', 1, 5.00, 0.25, 5.25, '2026-05-20 20:52:39', '2026-05-20 20:52:39'),
(15, 4, 'Amoxicillin 250mg', 1, 8.00, 0.40, 8.40, '2026-05-20 20:52:39', '2026-05-20 20:52:39'),
(16, 5, 'Amoxicillin 250mg', 1, 0.00, 0.00, 0.00, '2026-05-20 21:08:45', '2026-05-20 21:08:45'),
(17, 5, 'Amoxicillin 250mg', 1, 8.00, 0.40, 8.40, '2026-05-20 21:08:45', '2026-05-20 21:08:45'),
(18, 6, 'Amoxicillin 250mg', 1, 8.00, 0.40, 8.40, '2026-05-20 21:10:30', '2026-05-20 21:10:30'),
(19, 6, 'ORS Sachet', 1, 15.00, 0.00, 15.00, '2026-05-20 21:10:30', '2026-05-20 21:10:30'),
(20, 7, 'Cetirizine 10mg', 1, 3.00, 0.15, 3.15, '2026-05-20 21:11:04', '2026-05-20 21:11:04'),
(21, 7, 'ORS Sachet', 1, 15.00, 0.00, 15.00, '2026-05-20 21:11:04', '2026-05-20 21:11:04'),
(22, 7, 'Omeprazole 20mg', 1, 5.00, 0.25, 5.25, '2026-05-20 21:11:04', '2026-05-20 21:11:04'),
(23, 8, 'Amoxicillin 250mg', 1, 0.00, 0.00, 0.00, '2026-05-20 21:22:03', '2026-05-20 21:22:03'),
(24, 8, 'ORS Sachet', 1, 0.00, 0.00, 0.00, '2026-05-20 21:22:03', '2026-05-20 21:22:03'),
(25, 8, 'Amoxicillin 250mg', 1, 8.00, 0.40, 8.40, '2026-05-20 21:22:03', '2026-05-20 21:22:03'),
(26, 9, 'Amoxicillin 250mg', 1, 8.00, 0.40, 8.40, '2026-05-20 21:34:22', '2026-05-20 21:34:22'),
(27, 9, 'ORS Sachet', 1, 15.00, 0.00, 15.00, '2026-05-20 21:34:22', '2026-05-20 21:34:22'),
(28, 10, 'Amoxicillin 250mg', 1, 8.00, 0.40, 8.40, '2026-05-21 12:59:10', '2026-05-21 12:59:10'),
(29, 10, 'Paracetamol 500mg', 1, 2.00, 0.10, 2.10, '2026-05-21 12:59:10', '2026-05-21 12:59:10'),
(30, 10, 'ORS Sachet', 1, 15.00, 0.00, 15.00, '2026-05-21 12:59:10', '2026-05-21 12:59:10'),
(31, 11, 'Amoxicillin 250mg', 1, 8.00, 0.40, 8.40, '2026-05-21 12:59:57', '2026-05-21 12:59:57'),
(32, 11, 'ORS Sachet', 1, 15.00, 0.00, 15.00, '2026-05-21 12:59:57', '2026-05-21 12:59:57'),
(33, 12, 'Paracetamol 500mg', 81111, 2.00, 8111.10, 170333.10, '2026-05-22 00:26:10', '2026-05-22 00:26:10'),
(34, 12, 'Amoxicillin 250mg', 1, 8.00, 0.40, 8.40, '2026-05-22 00:26:10', '2026-05-22 00:26:10'),
(35, 12, 'Paracetamol 500mg', 1, 2.00, 0.10, 2.10, '2026-05-22 00:26:10', '2026-05-22 00:26:10');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` smallint(5) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lab_orders`
--

CREATE TABLE `lab_orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_number` varchar(30) NOT NULL,
  `patient_visit_id` bigint(20) UNSIGNED NOT NULL,
  `consultation_id` bigint(20) UNSIGNED DEFAULT NULL,
  `lab_test_id` bigint(20) UNSIGNED NOT NULL,
  `technician_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('pending','in_progress','completed','cancelled') NOT NULL DEFAULT 'pending',
  `result_values` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `report_path` varchar(255) DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lab_tests`
--

CREATE TABLE `lab_tests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(30) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lab_tests`
--

INSERT INTO `lab_tests` (`id`, `name`, `code`, `category`, `price`, `description`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Complete Blood Count', 'CBC', NULL, 350.00, NULL, 1, '2026-05-20 13:23:26', '2026-05-20 13:23:26', NULL),
(2, 'Blood Sugar (Fasting)', 'BSF', NULL, 80.00, NULL, 1, '2026-05-20 13:23:26', '2026-05-20 13:23:26', NULL),
(3, 'Lipid Profile', 'LIPID', NULL, 600.00, NULL, 1, '2026-05-20 13:23:26', '2026-05-20 13:23:26', NULL),
(4, 'Urine Routine', 'URINE', NULL, 120.00, NULL, 1, '2026-05-20 13:23:26', '2026-05-20 13:23:26', NULL),
(5, 'Thyroid Profile', 'THY', NULL, 900.00, NULL, 1, '2026-05-20 13:23:26', '2026-05-20 13:23:26', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `medicines`
--

CREATE TABLE `medicines` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `generic_name` varchar(255) DEFAULT NULL,
  `sku` varchar(50) DEFAULT NULL,
  `barcode` varchar(50) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `medicine_type` varchar(50) DEFAULT NULL,
  `strength` varchar(50) DEFAULT NULL,
  `manufacturer` varchar(255) DEFAULT NULL,
  `unit` varchar(20) NOT NULL DEFAULT 'strip',
  `mrp` decimal(10,2) NOT NULL DEFAULT 0.00,
  `selling_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `purchase_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `gst_percent` decimal(5,2) NOT NULL DEFAULT 0.00,
  `reorder_level` int(10) UNSIGNED NOT NULL DEFAULT 10,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `medicines`
--

INSERT INTO `medicines` (`id`, `name`, `generic_name`, `sku`, `barcode`, `category`, `medicine_type`, `strength`, `manufacturer`, `unit`, `mrp`, `selling_price`, `purchase_price`, `gst_percent`, `reorder_level`, `description`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Paracetamol 500mg', 'Paracetamol', 'MED001', NULL, NULL, 'tablet', NULL, NULL, 'strip', 2.40, 2.00, 0.00, 5.00, 10, NULL, 1, '2026-05-20 13:23:26', '2026-05-20 21:30:29', NULL),
(2, 'Amoxicillin 250mg', 'Amoxicillin', 'MED002', '8901234567890', NULL, 'tablet', NULL, 'ABC Pharma', 'strip', 9.60, 8.00, 0.00, 5.00, 10, 'Sample row — delete before import', 1, '2026-05-20 13:23:26', '2026-05-20 21:30:17', NULL),
(3, 'Cetirizine 10mg', 'Cetirizine', 'MED003', NULL, NULL, NULL, NULL, NULL, 'strip', 3.60, 3.00, 0.00, 5.00, 10, NULL, 1, '2026-05-20 13:23:26', '2026-05-20 13:23:26', NULL),
(4, 'Omeprazole 20mg', 'Omeprazole', 'MED004', NULL, NULL, NULL, NULL, NULL, 'strip', 6.00, 5.00, 0.00, 5.00, 10, NULL, 1, '2026-05-20 13:23:26', '2026-05-20 13:23:26', NULL),
(5, 'ORS Sachet', 'Oral Rehydration', 'MED005', NULL, NULL, NULL, NULL, NULL, 'strip', 18.00, 15.00, 0.00, 0.00, 10, NULL, 1, '2026-05-20 13:23:26', '2026-05-20 13:23:26', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `medicine_batches`
--

CREATE TABLE `medicine_batches` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `medicine_id` bigint(20) UNSIGNED NOT NULL,
  `batch_number` varchar(50) NOT NULL,
  `expiry_date` date NOT NULL,
  `quantity` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `purchase_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `selling_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `medicine_batches`
--

INSERT INTO `medicine_batches` (`id`, `medicine_id`, `batch_number`, `expiry_date`, `quantity`, `purchase_price`, `selling_price`, `branch_id`, `created_at`, `updated_at`) VALUES
(1, 1, 'BATCH-1', '2027-05-20', 500, 1.40, 2.00, 1, '2026-05-20 13:23:26', '2026-05-20 13:23:26'),
(2, 2, 'BATCH-2', '2027-05-20', 500, 5.60, 8.00, 1, '2026-05-20 13:23:26', '2026-05-20 13:23:26'),
(3, 3, 'BATCH-3', '2027-05-20', 500, 2.10, 3.00, 1, '2026-05-20 13:23:26', '2026-05-20 13:23:26'),
(4, 4, 'BATCH-4', '2027-05-20', 500, 3.50, 5.00, 1, '2026-05-20 13:23:26', '2026-05-20 13:23:26'),
(5, 5, 'BATCH-5', '2027-05-20', 500, 10.50, 15.00, 1, '2026-05-20 13:23:26', '2026-05-20 13:23:26');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_05_20_183020_create_permission_tables', 1),
(5, '2026_05_20_183021_create_personal_access_tokens_table', 1),
(6, '2026_05_20_200000_create_cliniccare_hms_tables', 1),
(7, '2026_05_21_120000_enhance_medicines_workflow', 2);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(2, 'App\\Models\\User', 2),
(3, 'App\\Models\\User', 3),
(4, 'App\\Models\\User', 4),
(5, 'App\\Models\\User', 5),
(6, 'App\\Models\\User', 6),
(7, 'App\\Models\\User', 7);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` varchar(30) NOT NULL,
  `barcode` varchar(50) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `alternate_mobile` varchar(20) DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `age` tinyint(3) UNSIGNED DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `blood_group` varchar(10) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `pincode` varchar(10) DEFAULT NULL,
  `aadhaar` varchar(20) DEFAULT NULL,
  `occupation` varchar(255) DEFAULT NULL,
  `marital_status` enum('single','married','divorced','widowed') DEFAULT NULL,
  `allergies` text DEFAULT NULL,
  `existing_diseases` text DEFAULT NULL,
  `photo_path` varchar(255) DEFAULT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `registered_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`id`, `patient_id`, `barcode`, `name`, `mobile`, `alternate_mobile`, `gender`, `age`, `dob`, `blood_group`, `address`, `city`, `state`, `pincode`, `aadhaar`, `occupation`, `marital_status`, `allergies`, `existing_diseases`, `photo_path`, `branch_id`, `registered_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'PAT26000001', 'CC00000001', 'Faijalkhan belim', '9876543222', NULL, 'male', 12, '2004-06-12', NULL, 'Rajkot', 'Rakjot', 'Gujarat', '362240', NULL, NULL, NULL, NULL, NULL, NULL, 1, 2, '2026-05-20 13:26:28', '2026-05-20 13:26:28', NULL),
(2, 'PAT26000002', 'CC00000002', 'Faijalkhan', '9876543211', NULL, 'male', 22, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 2, '2026-05-20 13:52:53', '2026-05-20 13:52:53', NULL),
(3, 'PAT26000003', 'CC00000003', 'Kamrankhan', '9876543210', NULL, 'male', 19, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 2, '2026-05-20 14:17:58', '2026-05-20 14:17:58', NULL),
(5, 'PAT26000004', 'CC00000004', 'Asadkhan', '0987654321', NULL, 'male', 15, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, '2026-05-20 20:48:34', '2026-05-20 20:48:34', NULL),
(6, 'PAT26000005', 'CC00000006', 'ansarkhan aslamkhan belim', '9876513211', NULL, 'male', 17, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 2, '2026-05-20 21:02:59', '2026-05-20 21:02:59', NULL),
(7, 'PAT26000006', 'CC00000007', 'Junedkhan Imtiyaj', '0098765432', NULL, 'male', 19, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 2, '2026-05-20 21:05:48', '2026-05-20 21:05:48', NULL),
(8, 'PAT26000007', 'CC00000008', 'Hannan Lathiya', '0987654333', NULL, 'male', 22, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 2, '2026-05-21 12:55:41', '2026-05-21 12:55:41', NULL),
(9, 'PAT26000008', 'CC00000009', 'Madin', '99999999999', NULL, 'male', 24, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 2, '2026-05-21 12:57:07', '2026-05-21 12:57:07', NULL),
(10, 'PAT26000009', 'CC00000010', 'amzadkhan belim', '0187654333', NULL, 'male', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 2, '2026-05-22 00:22:32', '2026-05-22 00:22:32', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `patient_reports`
--

CREATE TABLE `patient_reports` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
  `patient_visit_id` bigint(20) UNSIGNED DEFAULT NULL,
  `consultation_id` bigint(20) UNSIGNED DEFAULT NULL,
  `uploaded_by` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `type` enum('lab','xray','mri','ct','image','voice','other') NOT NULL DEFAULT 'other',
  `file_path` varchar(255) NOT NULL,
  `is_private` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `patient_visits`
--

CREATE TABLE `patient_visits` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `visit_number` varchar(30) NOT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `department_id` bigint(20) UNSIGNED DEFAULT NULL,
  `doctor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `receptionist_id` bigint(20) UNSIGNED DEFAULT NULL,
  `token_number` int(10) UNSIGNED DEFAULT NULL,
  `queue_number` int(10) UNSIGNED DEFAULT NULL,
  `visit_type` enum('opd','emergency','follow_up','walk_in') NOT NULL DEFAULT 'opd',
  `priority` enum('normal','emergency') NOT NULL DEFAULT 'normal',
  `status` enum('registered','waiting','with_doctor','prescribed','at_pharmacy','billing','lab_pending','completed','cancelled') NOT NULL DEFAULT 'registered',
  `weight` decimal(8,2) DEFAULT NULL,
  `height` decimal(8,2) DEFAULT NULL,
  `bp` varchar(20) DEFAULT NULL,
  `sugar_rbs` varchar(20) DEFAULT NULL,
  `temperature` decimal(5,2) DEFAULT NULL,
  `spo2` tinyint(3) UNSIGNED DEFAULT NULL,
  `symptoms` text DEFAULT NULL,
  `chief_complaint` text DEFAULT NULL,
  `referred_by` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `registered_at` timestamp NULL DEFAULT NULL,
  `sent_to_doctor_at` timestamp NULL DEFAULT NULL,
  `consultation_started_at` timestamp NULL DEFAULT NULL,
  `sent_to_pharmacy_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `patient_visits`
--

INSERT INTO `patient_visits` (`id`, `visit_number`, `patient_id`, `branch_id`, `department_id`, `doctor_id`, `receptionist_id`, `token_number`, `queue_number`, `visit_type`, `priority`, `status`, `weight`, `height`, `bp`, `sugar_rbs`, `temperature`, `spo2`, `symptoms`, `chief_complaint`, `referred_by`, `notes`, `registered_at`, `sent_to_doctor_at`, `consultation_started_at`, `sent_to_pharmacy_at`, `completed_at`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'VIS2605200001', 1, 1, 1, 1, 2, 1, 1, 'opd', 'normal', 'billing', NULL, NULL, '', '', NULL, NULL, '', '', NULL, NULL, '2026-05-20 13:26:28', '2026-05-20 13:28:50', '2026-05-20 13:28:55', '2026-05-20 13:30:01', NULL, '2026-05-20 13:26:28', '2026-05-20 13:31:02', NULL),
(2, 'VIS2605200002', 2, 1, 1, 1, 2, 2, 2, 'opd', 'normal', 'billing', 80.00, NULL, '120/80', '120', NULL, 0, '', 'Stumk', NULL, NULL, '2026-05-20 13:52:53', '2026-05-20 13:54:29', '2026-05-20 13:54:35', '2026-05-20 13:55:24', NULL, '2026-05-20 13:52:53', '2026-05-20 13:56:16', NULL),
(3, 'VIS2605200003', 3, 1, 1, 1, 2, 3, 3, 'opd', 'normal', 'billing', 50.00, NULL, '100/90', 'normal', NULL, NULL, 'symptoms nothing', 'uib igdsif sdfishd fnsdfosn', NULL, NULL, '2026-05-20 14:17:58', '2026-05-20 14:18:48', '2026-05-20 14:19:08', '2026-05-20 14:21:15', NULL, '2026-05-20 14:17:58', '2026-05-20 14:21:48', NULL),
(4, 'VIS2605200004', 1, 1, NULL, 1, 1, 4, 4, 'opd', 'normal', 'at_pharmacy', 80.00, NULL, '120/80', 'normal', NULL, NULL, 'nothing', 'Chief Complaint', NULL, NULL, '2026-05-20 14:24:19', '2026-05-20 14:24:52', '2026-05-20 14:25:06', '2026-05-20 14:25:47', NULL, '2026-05-20 14:24:19', '2026-05-20 14:25:47', NULL),
(6, 'VIS2605210001', 5, 1, 1, 1, 1, 1, 1, 'opd', 'normal', 'billing', 45.00, NULL, '120/80', 'normal', NULL, NULL, 'Symptoms Symptoms Symptoms Symptoms', 'Complaint Complaint', NULL, NULL, '2026-05-20 20:48:34', '2026-05-20 20:49:01', '2026-05-20 20:49:06', '2026-05-20 20:50:35', NULL, '2026-05-20 20:48:34', '2026-05-20 20:52:39', NULL),
(7, 'VIS2605210002', 6, 1, 1, 1, 2, 2, 2, 'opd', 'normal', 'billing', 40.00, NULL, '100/90', '120', NULL, NULL, 'Symptoms Symptoms Symptoms ', 'Chief Complaint\n', NULL, NULL, '2026-05-20 21:02:59', '2026-05-20 21:03:29', '2026-05-20 21:03:41', '2026-05-20 21:07:29', NULL, '2026-05-20 21:02:59', '2026-05-20 21:08:45', NULL),
(8, 'VIS2605210003', 3, 1, NULL, 1, 2, 3, 3, 'opd', 'normal', 'billing', 35.00, NULL, '100/80', '100', NULL, NULL, 'mathud pet and legs pains', 'pema dukhe che', NULL, NULL, '2026-05-20 21:04:39', '2026-05-20 21:04:52', '2026-05-20 21:09:48', '2026-05-20 21:10:02', NULL, '2026-05-20 21:04:39', '2026-05-20 21:10:29', NULL),
(9, 'VIS2605210004', 7, 1, 1, 1, 2, 3, 3, 'opd', 'normal', 'billing', 30.00, NULL, '100/90', '100', NULL, NULL, 'pet mathu and legs pain', 'oiay', NULL, NULL, '2026-05-20 21:05:48', '2026-05-20 21:06:03', '2026-05-20 21:07:49', '2026-05-20 21:09:43', NULL, '2026-05-20 21:05:48', '2026-05-20 21:11:04', NULL),
(10, 'VIS2605210005', 3, 1, NULL, 1, 1, 4, 4, 'opd', 'normal', 'billing', NULL, NULL, '', '', NULL, NULL, '', 'adsf', NULL, NULL, '2026-05-20 21:13:00', '2026-05-20 21:14:17', '2026-05-20 21:14:31', '2026-05-20 21:16:30', NULL, '2026-05-20 21:13:00', '2026-05-20 21:22:02', NULL),
(11, 'VIS2605210006', 7, 1, NULL, 1, 1, 5, 5, 'opd', 'normal', 'billing', 30.00, NULL, '100/90', '100', NULL, NULL, 'Symptoms', 'Chief Complaint\n', NULL, NULL, '2026-05-20 21:31:33', '2026-05-20 21:32:22', '2026-05-20 21:32:38', '2026-05-20 21:33:46', NULL, '2026-05-20 21:31:33', '2026-05-20 21:34:22', NULL),
(12, 'VIS2605210007', 8, 1, 1, 1, 2, 4, 4, 'opd', 'normal', 'billing', 51.00, NULL, '120/80', '110', NULL, NULL, 'Symptoms', 'petma dukhe mathu dukhe', NULL, NULL, '2026-05-21 12:55:41', '2026-05-21 12:56:11', '2026-05-21 12:57:30', '2026-05-21 12:58:44', NULL, '2026-05-21 12:55:41', '2026-05-21 12:59:10', NULL),
(13, 'VIS2605210008', 9, 1, 1, 1, 2, 5, 5, 'opd', 'normal', 'billing', 50.00, NULL, '110/90', '100', NULL, NULL, 'Symptoms', 'Chief Complaint\n', NULL, NULL, '2026-05-21 12:57:07', '2026-05-21 12:57:17', '2026-05-21 12:59:36', '2026-05-21 12:59:43', NULL, '2026-05-21 12:57:07', '2026-05-21 12:59:57', NULL),
(14, 'VIS2605220001', 10, 1, 1, 1, 2, 1, 1, 'opd', 'normal', 'billing', 22.00, NULL, '120/90', '100', 0.10, NULL, 'Symptoms\nSymptoms\nSymptoms\nSymptoms\n', 'pharmacypharmacypharmacypharmacy', NULL, NULL, '2026-05-22 00:22:32', '2026-05-22 00:23:21', '2026-05-22 00:23:34', '2026-05-22 00:25:31', NULL, '2026-05-22 00:22:32', '2026-05-22 00:26:10', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `method` enum('cash','card','upi','online','other') NOT NULL DEFAULT 'cash',
  `transaction_ref` varchar(255) DEFAULT NULL,
  `received_by` bigint(20) UNSIGNED DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `invoice_id`, `amount`, `method`, `transaction_ref`, `received_by`, `paid_at`, `created_at`, `updated_at`) VALUES
(1, 4, 30.75, 'cash', NULL, 4, '2026-05-20 20:52:39', '2026-05-20 20:52:39', '2026-05-20 20:52:39'),
(2, 3, 10.00, 'upi', NULL, 1, '2026-05-20 20:53:10', '2026-05-20 20:53:10', '2026-05-20 20:53:10'),
(3, 2, 10.00, 'cash', NULL, 1, '2026-05-20 20:53:15', '2026-05-20 20:53:15', '2026-05-20 20:53:15'),
(4, 1, 100.00, 'cash', NULL, 1, '2026-05-20 20:53:23', '2026-05-20 20:53:23', '2026-05-20 20:53:23'),
(5, 5, 8.40, 'cash', NULL, 4, '2026-05-20 21:08:45', '2026-05-20 21:08:45', '2026-05-20 21:08:45'),
(6, 6, 23.40, 'cash', NULL, 4, '2026-05-20 21:10:30', '2026-05-20 21:10:30', '2026-05-20 21:10:30'),
(7, 7, 20.00, 'cash', NULL, 4, '2026-05-20 21:11:04', '2026-05-20 21:11:04', '2026-05-20 21:11:04'),
(8, 8, 8.40, 'cash', NULL, 1, '2026-05-20 21:22:03', '2026-05-20 21:22:03', '2026-05-20 21:22:03'),
(9, 9, 23.40, 'upi', NULL, 4, '2026-05-20 21:34:22', '2026-05-20 21:34:22', '2026-05-20 21:34:22'),
(10, 10, 25.50, 'cash', NULL, 4, '2026-05-21 12:59:10', '2026-05-21 12:59:10', '2026-05-21 12:59:10'),
(11, 11, 23.40, 'cash', NULL, 4, '2026-05-21 12:59:57', '2026-05-21 12:59:57', '2026-05-21 12:59:57'),
(12, 12, 170343.60, 'cash', NULL, 4, '2026-05-22 00:26:10', '2026-05-22 00:26:10', '2026-05-22 00:26:10');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'dashboard.view', 'web', '2026-05-20 13:23:16', '2026-05-20 13:23:16'),
(2, 'patients.view', 'web', '2026-05-20 13:23:16', '2026-05-20 13:23:16'),
(3, 'patients.create', 'web', '2026-05-20 13:23:16', '2026-05-20 13:23:16'),
(4, 'patients.edit', 'web', '2026-05-20 13:23:16', '2026-05-20 13:23:16'),
(5, 'visits.view', 'web', '2026-05-20 13:23:16', '2026-05-20 13:23:16'),
(6, 'visits.create', 'web', '2026-05-20 13:23:16', '2026-05-20 13:23:16'),
(7, 'visits.send-doctor', 'web', '2026-05-20 13:23:16', '2026-05-20 13:23:16'),
(8, 'visits.send-pharmacy', 'web', '2026-05-20 13:23:16', '2026-05-20 13:23:16'),
(9, 'consultations.view', 'web', '2026-05-20 13:23:16', '2026-05-20 13:23:16'),
(10, 'consultations.create', 'web', '2026-05-20 13:23:16', '2026-05-20 13:23:16'),
(11, 'prescriptions.create', 'web', '2026-05-20 13:23:16', '2026-05-20 13:23:16'),
(12, 'pharmacy.view', 'web', '2026-05-20 13:23:16', '2026-05-20 13:23:16'),
(13, 'pharmacy.dispense', 'web', '2026-05-20 13:23:16', '2026-05-20 13:23:16'),
(14, 'pharmacy.inventory', 'web', '2026-05-20 13:23:16', '2026-05-20 13:23:16'),
(15, 'billing.view', 'web', '2026-05-20 13:23:16', '2026-05-20 13:23:16'),
(16, 'billing.create', 'web', '2026-05-20 13:23:16', '2026-05-20 13:23:16'),
(17, 'billing.payment', 'web', '2026-05-20 13:23:16', '2026-05-20 13:23:16'),
(18, 'lab.view', 'web', '2026-05-20 13:23:16', '2026-05-20 13:23:16'),
(19, 'lab.process', 'web', '2026-05-20 13:23:16', '2026-05-20 13:23:16'),
(20, 'lab.upload', 'web', '2026-05-20 13:23:16', '2026-05-20 13:23:16'),
(21, 'medicines.manage', 'web', '2026-05-20 13:23:16', '2026-05-20 13:23:16'),
(22, 'users.manage', 'web', '2026-05-20 13:23:16', '2026-05-20 13:23:16'),
(23, 'settings.manage', 'web', '2026-05-20 13:23:16', '2026-05-20 13:23:16'),
(24, 'reports.view', 'web', '2026-05-20 13:23:16', '2026-05-20 13:23:16'),
(25, 'audit.view', 'web', '2026-05-20 13:23:16', '2026-05-20 13:23:16');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pharmacy_orders`
--

CREATE TABLE `pharmacy_orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_number` varchar(30) NOT NULL,
  `patient_visit_id` bigint(20) UNSIGNED NOT NULL,
  `prescription_id` bigint(20) UNSIGNED DEFAULT NULL,
  `pharmacist_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('pending','processing','completed','cancelled') NOT NULL DEFAULT 'pending',
  `subtotal` decimal(12,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `tax` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pharmacy_orders`
--

INSERT INTO `pharmacy_orders` (`id`, `order_number`, `patient_visit_id`, `prescription_id`, `pharmacist_id`, `status`, `subtotal`, `discount`, `tax`, `total`, `notes`, `completed_at`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'PH260520190035', 1, 2, 4, 'pending', 0.00, 0.00, 0.00, 0.00, NULL, NULL, '2026-05-20 13:30:35', '2026-05-20 13:30:35', NULL),
(2, 'PH260520190048', 1, 2, 4, 'completed', 0.00, 0.00, 0.00, 0.00, NULL, '2026-05-20 13:31:02', '2026-05-20 13:30:48', '2026-05-20 13:31:02', NULL),
(3, 'PH260520192600', 2, NULL, 4, 'completed', 0.00, 0.00, 0.00, 0.00, NULL, '2026-05-20 13:56:16', '2026-05-20 13:56:00', '2026-05-20 13:56:16', NULL),
(4, 'PH260520195140', 3, 4, 4, 'completed', 0.00, 0.00, 0.00, 0.00, NULL, '2026-05-20 14:21:48', '2026-05-20 14:21:40', '2026-05-20 14:21:48', NULL),
(5, 'PH260521022046', 6, 7, 4, 'completed', 30.00, 0.00, 0.75, 30.75, NULL, '2026-05-20 20:52:39', '2026-05-20 20:50:46', '2026-05-20 20:52:39', NULL),
(6, 'PH260521023814', 7, 9, 4, 'completed', 8.00, 0.00, 0.40, 8.40, NULL, '2026-05-20 21:08:45', '2026-05-20 21:08:14', '2026-05-20 21:08:45', NULL),
(7, 'PH260521024009', 8, NULL, 4, 'completed', 23.00, 0.00, 0.40, 23.40, NULL, '2026-05-20 21:10:29', '2026-05-20 21:10:09', '2026-05-20 21:10:29', NULL),
(8, 'PH260521024032', 9, NULL, 4, 'completed', 23.00, 3.40, 0.40, 20.00, NULL, '2026-05-20 21:11:04', '2026-05-20 21:10:32', '2026-05-20 21:11:04', NULL),
(9, 'PH260521024636', 10, 10, 4, 'pending', 0.00, 0.00, 0.00, 0.00, NULL, NULL, '2026-05-20 21:16:36', '2026-05-20 21:16:36', NULL),
(10, 'PH260521025147', 10, 10, 1, 'completed', 8.00, 0.00, 0.40, 8.40, NULL, '2026-05-20 21:22:02', '2026-05-20 21:21:47', '2026-05-20 21:22:02', NULL),
(11, 'PH260521030348', 11, 11, 4, 'completed', 23.00, 0.00, 0.40, 23.40, NULL, '2026-05-20 21:34:22', '2026-05-20 21:33:48', '2026-05-20 21:34:22', NULL),
(12, 'PH260521182848', 12, 13, 4, 'completed', 25.00, 0.00, 0.50, 25.50, NULL, '2026-05-21 12:59:10', '2026-05-21 12:58:48', '2026-05-21 12:59:10', NULL),
(13, 'PH260521182947', 13, NULL, 4, 'completed', 23.00, 0.00, 0.40, 23.40, NULL, '2026-05-21 12:59:57', '2026-05-21 12:59:47', '2026-05-21 12:59:57', NULL),
(14, 'PH260522055536', 14, 16, 4, 'completed', 162232.00, 0.00, 8111.60, 170343.60, NULL, '2026-05-22 00:26:10', '2026-05-22 00:25:36', '2026-05-22 00:26:10', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `pharmacy_order_items`
--

CREATE TABLE `pharmacy_order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pharmacy_order_id` bigint(20) UNSIGNED NOT NULL,
  `medicine_id` bigint(20) UNSIGNED DEFAULT NULL,
  `prescription_item_id` bigint(20) UNSIGNED DEFAULT NULL,
  `medicine_name` varchar(255) NOT NULL,
  `sku` varchar(50) DEFAULT NULL,
  `quantity` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `unit_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `gst_percent` decimal(5,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `is_given` tinyint(1) NOT NULL DEFAULT 0,
  `is_otc` tinyint(1) NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL,
  `batch_number` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pharmacy_order_items`
--

INSERT INTO `pharmacy_order_items` (`id`, `pharmacy_order_id`, `medicine_id`, `prescription_item_id`, `medicine_name`, `sku`, `quantity`, `unit_price`, `gst_percent`, `discount`, `total`, `is_given`, `is_otc`, `notes`, `batch_number`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 6, 'Amoxicillin 250mg', NULL, 1, 0.00, 0.00, 0.00, 0.00, 0, 0, NULL, NULL, '2026-05-20 13:30:35', '2026-05-20 13:30:35'),
(2, 1, NULL, 7, 'Cetirizine 10mg', NULL, 1, 0.00, 0.00, 0.00, 0.00, 0, 0, NULL, NULL, '2026-05-20 13:30:35', '2026-05-20 13:30:35'),
(3, 1, NULL, 8, 'Omeprazole 20mg', NULL, 1, 0.00, 0.00, 0.00, 0.00, 0, 0, NULL, NULL, '2026-05-20 13:30:35', '2026-05-20 13:30:35'),
(4, 1, NULL, 9, 'ORS Sachet', NULL, 1, 0.00, 0.00, 0.00, 0.00, 0, 0, NULL, NULL, '2026-05-20 13:30:35', '2026-05-20 13:30:35'),
(5, 1, NULL, 10, 'Paracetamol 500mg', NULL, 1, 0.00, 0.00, 0.00, 0.00, 0, 0, NULL, NULL, '2026-05-20 13:30:35', '2026-05-20 13:30:35'),
(6, 2, NULL, 6, 'Amoxicillin 250mg', NULL, 1, 0.00, 0.00, 0.00, 0.00, 0, 0, NULL, NULL, '2026-05-20 13:30:48', '2026-05-20 13:30:48'),
(7, 2, NULL, 7, 'Cetirizine 10mg', NULL, 1, 0.00, 0.00, 0.00, 0.00, 0, 0, NULL, NULL, '2026-05-20 13:30:48', '2026-05-20 13:30:48'),
(8, 2, NULL, 8, 'Omeprazole 20mg', NULL, 1, 0.00, 0.00, 0.00, 0.00, 0, 0, NULL, NULL, '2026-05-20 13:30:48', '2026-05-20 13:30:48'),
(9, 2, NULL, 9, 'ORS Sachet', NULL, 1, 0.00, 0.00, 0.00, 0.00, 0, 0, NULL, NULL, '2026-05-20 13:30:48', '2026-05-20 13:30:48'),
(10, 2, NULL, 10, 'Paracetamol 500mg', NULL, 1, 0.00, 0.00, 0.00, 0.00, 0, 0, NULL, NULL, '2026-05-20 13:30:48', '2026-05-20 13:30:48'),
(11, 4, NULL, 14, 'Amoxicillin 250mg', NULL, 1, 0.00, 0.00, 0.00, 0.00, 0, 0, NULL, NULL, '2026-05-20 14:21:40', '2026-05-20 14:21:40'),
(12, 4, NULL, 15, 'Amoxicillin 250mg', NULL, 1, 0.00, 0.00, 0.00, 0.00, 0, 0, NULL, NULL, '2026-05-20 14:21:40', '2026-05-20 14:21:40'),
(13, 4, NULL, 16, 'ORS Sachet', NULL, 1, 0.00, 0.00, 0.00, 0.00, 0, 0, NULL, NULL, '2026-05-20 14:21:40', '2026-05-20 14:21:40'),
(14, 5, NULL, 21, 'Amoxicillin 250mg', NULL, 1, 0.00, 0.00, 0.00, 0.00, 0, 0, NULL, NULL, '2026-05-20 20:50:46', '2026-05-20 20:50:46'),
(15, 5, NULL, 22, 'Omeprazole 20mg', NULL, 1, 0.00, 0.00, 0.00, 0.00, 0, 0, NULL, NULL, '2026-05-20 20:50:46', '2026-05-20 20:50:46'),
(16, 5, NULL, 23, 'Paracetamol 500mg', NULL, 1, 0.00, 0.00, 0.00, 0.00, 0, 0, NULL, NULL, '2026-05-20 20:50:46', '2026-05-20 20:51:23'),
(17, 5, NULL, NULL, 'ORS Sachet', NULL, 1, 15.00, 0.00, 0.00, 15.00, 0, 1, NULL, NULL, '2026-05-20 20:51:05', '2026-05-20 20:51:05'),
(18, 5, NULL, NULL, 'Paracetamol 500mg', NULL, 1, 2.00, 5.00, 0.00, 2.00, 0, 1, NULL, NULL, '2026-05-20 20:51:32', '2026-05-20 20:51:32'),
(19, 5, NULL, NULL, 'Omeprazole 20mg', NULL, 1, 5.00, 5.00, 0.00, 5.00, 0, 1, NULL, NULL, '2026-05-20 20:52:05', '2026-05-20 20:52:05'),
(20, 5, NULL, NULL, 'Amoxicillin 250mg', NULL, 1, 8.00, 5.00, 0.00, 8.00, 0, 1, NULL, NULL, '2026-05-20 20:52:11', '2026-05-20 20:52:11'),
(21, 6, NULL, 25, 'Amoxicillin 250mg', NULL, 1, 0.00, 0.00, 0.00, 0.00, 0, 0, NULL, NULL, '2026-05-20 21:08:14', '2026-05-20 21:08:22'),
(22, 6, NULL, NULL, 'Amoxicillin 250mg', NULL, 1, 8.00, 5.00, 0.00, 8.00, 0, 1, NULL, NULL, '2026-05-20 21:08:38', '2026-05-20 21:08:38'),
(23, 7, NULL, NULL, 'Amoxicillin 250mg', NULL, 1, 8.00, 5.00, 0.00, 8.00, 0, 1, NULL, NULL, '2026-05-20 21:10:21', '2026-05-20 21:10:21'),
(24, 7, NULL, NULL, 'ORS Sachet', NULL, 1, 15.00, 0.00, 0.00, 15.00, 0, 1, NULL, NULL, '2026-05-20 21:10:24', '2026-05-20 21:10:24'),
(25, 8, NULL, NULL, 'Cetirizine 10mg', NULL, 1, 3.00, 5.00, 0.00, 3.00, 0, 1, NULL, NULL, '2026-05-20 21:10:36', '2026-05-20 21:10:36'),
(26, 8, NULL, NULL, 'ORS Sachet', NULL, 1, 15.00, 0.00, 0.00, 15.00, 0, 1, NULL, NULL, '2026-05-20 21:10:41', '2026-05-20 21:10:41'),
(27, 8, NULL, NULL, 'Omeprazole 20mg', NULL, 1, 5.00, 5.00, 0.00, 5.00, 0, 1, NULL, NULL, '2026-05-20 21:10:45', '2026-05-20 21:10:45'),
(28, 9, NULL, 26, 'Amoxicillin 250mg', NULL, 1, 0.00, 0.00, 0.00, 0.00, 0, 0, NULL, NULL, '2026-05-20 21:16:36', '2026-05-20 21:16:36'),
(29, 9, NULL, 27, 'ORS Sachet', NULL, 1, 0.00, 0.00, 0.00, 0.00, 0, 0, NULL, NULL, '2026-05-20 21:16:36', '2026-05-20 21:16:36'),
(30, 10, NULL, 26, 'Amoxicillin 250mg', NULL, 1, 0.00, 0.00, 0.00, 0.00, 0, 0, NULL, NULL, '2026-05-20 21:21:47', '2026-05-20 21:21:47'),
(31, 10, NULL, 27, 'ORS Sachet', NULL, 1, 0.00, 0.00, 0.00, 0.00, 0, 0, NULL, NULL, '2026-05-20 21:21:47', '2026-05-20 21:21:47'),
(32, 10, NULL, NULL, 'Amoxicillin 250mg', NULL, 1, 8.00, 5.00, 0.00, 8.00, 0, 1, NULL, NULL, '2026-05-20 21:21:55', '2026-05-20 21:21:55'),
(33, 11, 2, 28, 'Amoxicillin 250mg', 'MED002', 1, 8.00, 5.00, 0.00, 8.00, 0, 0, NULL, NULL, '2026-05-20 21:33:49', '2026-05-20 21:33:57'),
(34, 11, 5, 29, 'ORS Sachet', 'MED005', 1, 15.00, 0.00, 0.00, 15.00, 0, 0, NULL, NULL, '2026-05-20 21:33:49', '2026-05-20 21:33:49'),
(35, 12, 2, 32, 'Amoxicillin 250mg', 'MED002', 1, 8.00, 5.00, 0.00, 8.00, 0, 0, NULL, NULL, '2026-05-21 12:58:48', '2026-05-21 12:58:48'),
(36, 12, 1, 33, 'Paracetamol 500mg', 'MED001', 1, 2.00, 5.00, 0.00, 2.00, 0, 0, NULL, NULL, '2026-05-21 12:58:48', '2026-05-21 12:58:48'),
(37, 12, 5, NULL, 'ORS Sachet', 'MED005', 1, 15.00, 0.00, 0.00, 15.00, 0, 1, NULL, NULL, '2026-05-21 12:58:56', '2026-05-21 12:58:56'),
(38, 13, 2, NULL, 'Amoxicillin 250mg', 'MED002', 1, 8.00, 5.00, 0.00, 8.00, 0, 1, NULL, NULL, '2026-05-21 12:59:50', '2026-05-21 12:59:50'),
(39, 13, 5, NULL, 'ORS Sachet', 'MED005', 1, 15.00, 0.00, 0.00, 15.00, 0, 1, NULL, NULL, '2026-05-21 12:59:52', '2026-05-21 12:59:52'),
(40, 14, 1, 37, 'Paracetamol 500mg', 'MED001', 81111, 2.00, 5.00, 0.00, 162222.00, 0, 0, NULL, NULL, '2026-05-22 00:25:37', '2026-05-22 00:25:37'),
(41, 14, 2, 38, 'Amoxicillin 250mg', 'MED002', 1, 8.00, 5.00, 0.00, 8.00, 0, 0, NULL, NULL, '2026-05-22 00:25:37', '2026-05-22 00:25:37'),
(42, 14, 1, NULL, 'Paracetamol 500mg', 'MED001', 1, 2.00, 5.00, 0.00, 2.00, 0, 1, NULL, NULL, '2026-05-22 00:25:46', '2026-05-22 00:25:46');

-- --------------------------------------------------------

--
-- Table structure for table `prescriptions`
--

CREATE TABLE `prescriptions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `prescription_number` varchar(30) NOT NULL,
  `consultation_id` bigint(20) UNSIGNED NOT NULL,
  `patient_visit_id` bigint(20) UNSIGNED NOT NULL,
  `doctor_id` bigint(20) UNSIGNED NOT NULL,
  `visibility` enum('public','private') NOT NULL DEFAULT 'public',
  `instructions` text DEFAULT NULL,
  `status` enum('draft','active','dispensed','cancelled') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `prescriptions`
--

INSERT INTO `prescriptions` (`id`, `prescription_number`, `consultation_id`, `patient_visit_id`, `doctor_id`, `visibility`, `instructions`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'RX26052019000046', 1, 1, 1, 'public', '', 'active', '2026-05-20 13:30:00', '2026-05-20 13:30:00', NULL),
(2, 'RX26052019000142', 1, 1, 1, 'public', '', 'dispensed', '2026-05-20 13:30:01', '2026-05-20 13:31:02', NULL),
(3, 'RX26052019510960', 3, 3, 1, 'public', 'Prescription Intrucaiton', 'active', '2026-05-20 14:21:09', '2026-05-20 14:21:09', NULL),
(4, 'RX26052019511540', 3, 3, 1, 'public', 'Prescription Intrucaiton', 'dispensed', '2026-05-20 14:21:15', '2026-05-20 14:21:48', NULL),
(5, 'RX26052019554797', 4, 4, 1, 'public', '', 'active', '2026-05-20 14:25:47', '2026-05-20 14:25:47', NULL),
(6, 'RX26052102202528', 5, 6, 1, 'public', '', 'active', '2026-05-20 20:50:25', '2026-05-20 20:50:25', NULL),
(7, 'RX26052102203585', 5, 6, 1, 'public', 'prescription', 'dispensed', '2026-05-20 20:50:35', '2026-05-20 20:52:39', NULL),
(8, 'RX26052102372770', 6, 7, 1, 'public', 'Instruction', 'active', '2026-05-20 21:07:27', '2026-05-20 21:07:27', NULL),
(9, 'RX26052102372979', 6, 7, 1, 'public', 'Instruction', 'dispensed', '2026-05-20 21:07:29', '2026-05-20 21:08:45', NULL),
(10, 'RX26052102463072', 9, 10, 1, 'public', '', 'dispensed', '2026-05-20 21:16:30', '2026-05-20 21:22:02', NULL),
(11, 'RX26052103034620', 10, 11, 1, 'public', 'Hsjbsjs jw wjwvjgs jw ', 'dispensed', '2026-05-20 21:33:46', '2026-05-20 21:34:22', NULL),
(12, 'RX26052118283080', 11, 12, 1, 'public', 'Hshsbsh', 'active', '2026-05-21 12:58:30', '2026-05-21 12:58:30', NULL),
(13, 'RX26052118284420', 11, 12, 1, 'public', 'Hshsbsh', 'dispensed', '2026-05-21 12:58:44', '2026-05-21 12:59:10', NULL),
(14, 'RX26052205552075', 13, 14, 1, 'public', '', 'active', '2026-05-22 00:25:20', '2026-05-22 00:25:20', NULL),
(15, 'RX26052205552866', 13, 14, 1, 'public', '', 'active', '2026-05-22 00:25:28', '2026-05-22 00:25:28', NULL),
(16, 'RX26052205553118', 13, 14, 1, 'public', '', 'dispensed', '2026-05-22 00:25:31', '2026-05-22 00:26:10', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `prescription_items`
--

CREATE TABLE `prescription_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `prescription_id` bigint(20) UNSIGNED NOT NULL,
  `medicine_id` bigint(20) UNSIGNED DEFAULT NULL,
  `medicine_name` varchar(255) NOT NULL,
  `dosage` varchar(255) DEFAULT NULL,
  `frequency` varchar(255) DEFAULT NULL,
  `morning` tinyint(1) NOT NULL DEFAULT 0,
  `afternoon` tinyint(1) NOT NULL DEFAULT 0,
  `night` tinyint(1) NOT NULL DEFAULT 0,
  `food_timing` enum('before','after','any') DEFAULT NULL,
  `days` smallint(5) UNSIGNED DEFAULT NULL,
  `quantity` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `unit_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `gst_percent` decimal(5,2) NOT NULL DEFAULT 0.00,
  `sku` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `prescription_items`
--

INSERT INTO `prescription_items` (`id`, `prescription_id`, `medicine_id`, `medicine_name`, `dosage`, `frequency`, `morning`, `afternoon`, `night`, `food_timing`, `days`, `quantity`, `unit_price`, `gst_percent`, `sku`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 'Amoxicillin 250mg', '100', '101', 0, 0, 0, 'after', 5, 1, 0.00, 0.00, NULL, NULL, '2026-05-20 13:30:00', '2026-05-20 13:30:00'),
(2, 1, NULL, 'Cetirizine 10mg', '100', '101', 0, 0, 0, 'after', 5, 1, 0.00, 0.00, NULL, NULL, '2026-05-20 13:30:00', '2026-05-20 13:30:00'),
(3, 1, NULL, 'Omeprazole 20mg', '100', '101', 0, 0, 0, 'after', 5, 1, 0.00, 0.00, NULL, NULL, '2026-05-20 13:30:00', '2026-05-20 13:30:00'),
(4, 1, NULL, 'ORS Sachet', '100', '101', 0, 0, 0, 'after', 5, 1, 0.00, 0.00, NULL, NULL, '2026-05-20 13:30:00', '2026-05-20 13:30:00'),
(5, 1, NULL, 'Paracetamol 500mg', '100', '101', 0, 0, 0, 'after', 5, 1, 0.00, 0.00, NULL, NULL, '2026-05-20 13:30:00', '2026-05-20 13:30:00'),
(6, 2, NULL, 'Amoxicillin 250mg', '100', '101', 0, 0, 0, 'after', 5, 1, 0.00, 0.00, NULL, NULL, '2026-05-20 13:30:01', '2026-05-20 13:30:01'),
(7, 2, NULL, 'Cetirizine 10mg', '100', '101', 0, 0, 0, 'after', 5, 1, 0.00, 0.00, NULL, NULL, '2026-05-20 13:30:01', '2026-05-20 13:30:01'),
(8, 2, NULL, 'Omeprazole 20mg', '100', '101', 0, 0, 0, 'after', 5, 1, 0.00, 0.00, NULL, NULL, '2026-05-20 13:30:01', '2026-05-20 13:30:01'),
(9, 2, NULL, 'ORS Sachet', '100', '101', 0, 0, 0, 'after', 5, 1, 0.00, 0.00, NULL, NULL, '2026-05-20 13:30:01', '2026-05-20 13:30:01'),
(10, 2, NULL, 'Paracetamol 500mg', '100', '101', 0, 0, 0, 'after', 5, 1, 0.00, 0.00, NULL, NULL, '2026-05-20 13:30:01', '2026-05-20 13:30:01'),
(11, 3, NULL, 'Amoxicillin 250mg', '100', '1-0-1', 1, 1, 1, 'after', 5, 1, 0.00, 0.00, NULL, NULL, '2026-05-20 14:21:09', '2026-05-20 14:21:09'),
(12, 3, NULL, 'Amoxicillin 250mg', '100', '1-0-1', 1, 1, 1, 'after', 5, 1, 0.00, 0.00, NULL, NULL, '2026-05-20 14:21:09', '2026-05-20 14:21:09'),
(13, 3, NULL, 'ORS Sachet', '100', '1-0-1', 1, 0, 0, 'after', 5, 1, 0.00, 0.00, NULL, NULL, '2026-05-20 14:21:09', '2026-05-20 14:21:09'),
(14, 4, NULL, 'Amoxicillin 250mg', '100', '1-0-1', 1, 1, 1, 'after', 5, 1, 0.00, 0.00, NULL, NULL, '2026-05-20 14:21:15', '2026-05-20 14:21:15'),
(15, 4, NULL, 'Amoxicillin 250mg', '100', '1-0-1', 1, 1, 1, 'after', 5, 1, 0.00, 0.00, NULL, NULL, '2026-05-20 14:21:15', '2026-05-20 14:21:15'),
(16, 4, NULL, 'ORS Sachet', '100', '1-0-1', 1, 0, 0, 'after', 5, 1, 0.00, 0.00, NULL, NULL, '2026-05-20 14:21:15', '2026-05-20 14:21:15'),
(17, 5, NULL, 'Amoxicillin 250mg', '100', '1-0-1', 1, 0, 0, 'after', 5, 1, 0.00, 0.00, NULL, NULL, '2026-05-20 14:25:47', '2026-05-20 14:25:47'),
(18, 6, NULL, 'Amoxicillin 250mg', '100', '1-0-1', 1, 0, 0, 'after', 5, 1, 0.00, 0.00, NULL, NULL, '2026-05-20 20:50:25', '2026-05-20 20:50:25'),
(19, 6, NULL, 'Omeprazole 20mg', '100', '1-0-1', 0, 1, 0, 'after', 5, 1, 0.00, 0.00, NULL, NULL, '2026-05-20 20:50:25', '2026-05-20 20:50:25'),
(20, 6, NULL, 'Paracetamol 500mg', '100', '1-0-1', 1, 1, 1, 'after', 5, 1, 0.00, 0.00, NULL, NULL, '2026-05-20 20:50:25', '2026-05-20 20:50:25'),
(21, 7, NULL, 'Amoxicillin 250mg', '100', '1-0-1', 1, 0, 0, 'after', 5, 1, 0.00, 0.00, NULL, NULL, '2026-05-20 20:50:35', '2026-05-20 20:50:35'),
(22, 7, NULL, 'Omeprazole 20mg', '100', '1-0-1', 0, 1, 0, 'after', 5, 1, 0.00, 0.00, NULL, NULL, '2026-05-20 20:50:35', '2026-05-20 20:50:35'),
(23, 7, NULL, 'Paracetamol 500mg', '100', '1-0-1', 1, 1, 1, 'after', 5, 1, 0.00, 0.00, NULL, NULL, '2026-05-20 20:50:35', '2026-05-20 20:50:35'),
(24, 8, NULL, 'Amoxicillin 250mg', '100', '1', 0, 1, 0, 'after', 5, 1, 0.00, 0.00, NULL, NULL, '2026-05-20 21:07:27', '2026-05-20 21:07:27'),
(25, 9, NULL, 'Amoxicillin 250mg', '100', '1', 0, 1, 0, 'after', 5, 1, 0.00, 0.00, NULL, NULL, '2026-05-20 21:07:29', '2026-05-20 21:07:29'),
(26, 10, NULL, 'Amoxicillin 250mg', '100', '1', 0, 1, 0, 'after', 5, 1, 0.00, 0.00, NULL, NULL, '2026-05-20 21:16:30', '2026-05-20 21:16:30'),
(27, 10, NULL, 'ORS Sachet', '100', '1', 0, 1, 0, 'after', 5, 1, 0.00, 0.00, NULL, NULL, '2026-05-20 21:16:30', '2026-05-20 21:16:30'),
(28, 11, 2, 'Amoxicillin 250mg', '100', '', 1, 0, 0, 'after', 5, 1, 8.00, 5.00, 'MED002', NULL, '2026-05-20 21:33:46', '2026-05-20 21:33:46'),
(29, 11, 5, 'ORS Sachet', '100', '', 1, 1, 1, 'after', 5, 1, 15.00, 0.00, 'MED005', NULL, '2026-05-20 21:33:46', '2026-05-20 21:33:46'),
(30, 12, 2, 'Amoxicillin 250mg', '100', '', 1, 0, 0, 'after', 5, 1, 8.00, 5.00, 'MED002', NULL, '2026-05-21 12:58:30', '2026-05-21 12:58:30'),
(31, 12, 1, 'Paracetamol 500mg', '500', '', 1, 1, 1, 'after', 5, 1, 2.00, 5.00, 'MED001', NULL, '2026-05-21 12:58:30', '2026-05-21 12:58:30'),
(32, 13, 2, 'Amoxicillin 250mg', '100', '', 1, 0, 0, 'after', 5, 1, 8.00, 5.00, 'MED002', NULL, '2026-05-21 12:58:44', '2026-05-21 12:58:44'),
(33, 13, 1, 'Paracetamol 500mg', '500', '', 1, 1, 1, 'after', 5, 1, 2.00, 5.00, 'MED001', NULL, '2026-05-21 12:58:44', '2026-05-21 12:58:44'),
(34, 14, 1, 'Paracetamol 500mg', '200', '', 1, 0, 0, 'after', 4, 81111, 2.00, 5.00, 'MED001', NULL, '2026-05-22 00:25:20', '2026-05-22 00:25:20'),
(35, 15, 1, 'Paracetamol 500mg', '200', '', 1, 0, 0, 'after', 4, 81111, 2.00, 5.00, 'MED001', NULL, '2026-05-22 00:25:28', '2026-05-22 00:25:28'),
(36, 15, 2, 'Amoxicillin 250mg', '800', '', 1, 0, 1, 'after', 4, 1, 8.00, 5.00, 'MED002', NULL, '2026-05-22 00:25:28', '2026-05-22 00:25:28'),
(37, 16, 1, 'Paracetamol 500mg', '200', '', 1, 0, 0, 'after', 4, 81111, 2.00, 5.00, 'MED001', NULL, '2026-05-22 00:25:31', '2026-05-22 00:25:31'),
(38, 16, 2, 'Amoxicillin 250mg', '800', '', 1, 0, 1, 'after', 4, 1, 8.00, 5.00, 'MED002', NULL, '2026-05-22 00:25:31', '2026-05-22 00:25:31');

-- --------------------------------------------------------

--
-- Table structure for table `queue_displays`
--

CREATE TABLE `queue_displays` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `department_id` bigint(20) UNSIGNED DEFAULT NULL,
  `current_token` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `waiting_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'super-admin', 'web', '2026-05-20 13:23:16', '2026-05-20 13:23:16'),
(2, 'receptionist', 'web', '2026-05-20 13:23:16', '2026-05-20 13:23:16'),
(3, 'doctor', 'web', '2026-05-20 13:23:16', '2026-05-20 13:23:16'),
(4, 'pharmacist', 'web', '2026-05-20 13:23:16', '2026-05-20 13:23:16'),
(5, 'accountant', 'web', '2026-05-20 13:23:16', '2026-05-20 13:23:16'),
(6, 'lab-technician', 'web', '2026-05-20 13:23:16', '2026-05-20 13:23:16'),
(7, 'nurse', 'web', '2026-05-20 13:23:16', '2026-05-20 13:23:16'),
(8, 'patient', 'web', '2026-05-20 13:23:16', '2026-05-20 13:23:16');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(2, 1),
(2, 2),
(2, 3),
(2, 4),
(2, 7),
(2, 8),
(3, 1),
(3, 2),
(4, 1),
(4, 2),
(5, 1),
(5, 2),
(5, 3),
(5, 4),
(5, 7),
(6, 1),
(6, 2),
(6, 7),
(7, 1),
(7, 2),
(8, 1),
(8, 3),
(9, 1),
(9, 3),
(10, 1),
(10, 3),
(11, 1),
(11, 3),
(12, 1),
(12, 4),
(13, 1),
(13, 4),
(14, 1),
(14, 4),
(15, 1),
(15, 5),
(16, 1),
(16, 5),
(17, 1),
(17, 5),
(18, 1),
(18, 6),
(19, 1),
(19, 6),
(20, 1),
(20, 6),
(21, 1),
(22, 1),
(23, 1),
(24, 1),
(24, 5),
(25, 1);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('c8vBAhvHDHb2wAT6Oi0kYHsdwGMGKjudStSTWqT5', 4, '10.41.223.209', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJQa1lVNWFGRzBUOTM2OXduU2NYZlREVE85bDdNSmtDWHFwbWl2ZkxvIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEwLjQxLjIyMy4yMDk6ODAwMFwvcXVldWVcL2Rpc3BsYXkiLCJyb3V0ZSI6InF1ZXVlLmRpc3BsYXkifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6NH0=', 1779434213),
('Tyj64RpmSwUqjmcRDEZhwLZVrDEWqJsfCvgULb1Q', 2, '10.41.223.209', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJtY1NnWndKeWtOYXh5cFZOMXN6YjBRY2VrYzRBMlpCU1Q0amIyS1M3IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEwLjQxLjIyMy4yMDk6ODAwMFwvcXVldWVcL2Rpc3BsYXkiLCJyb3V0ZSI6InF1ZXVlLmRpc3BsYXkifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6Mn0=', 1779433541),
('zgPQcl2CdoMsZxTvoc6WrmpvpdlbAFRJuI3vA2vb', 1, '10.41.223.181', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJTSnVySGE2T0hldHJVbUN6ZEQzOE16U3hGbTd2SEZ5SkJoYXpoclNZIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTAuNDEuMjIzLjIwOTo4MDAwXC9hZG1pblwvbWVkaWNpbmVzIiwicm91dGUiOiJhZG1pbi5tZWRpY2luZXMifSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjF9', 1779429553);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `employee_id` varchar(50) DEFAULT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `department_id` bigint(20) UNSIGNED DEFAULT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `signature_path` varchar(255) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `employee_id`, `branch_id`, `department_id`, `designation`, `signature_path`, `avatar`, `is_active`, `last_login_at`, `deleted_at`) VALUES
(1, 'Super Admin', 'admin@cosyclinic.test', '9000000001', NULL, '$2y$12$fHQN8o.x8Bhub78HSLRroe48FXIRB35P6n8/MTENTWCkaUX2NuOoG', NULL, '2026-05-20 13:23:17', '2026-05-20 13:23:17', NULL, 1, 1, 'Administrator', NULL, NULL, 1, NULL, NULL),
(2, 'Reception Desk', 'reception@cosyclinic.test', '9000000001', NULL, '$2y$12$HM9YfYBX/udXbGxQTf10G.YdlFnWc3PiVfD0wYv.DupIqF66e/nPS', NULL, '2026-05-20 13:23:18', '2026-05-20 13:23:18', NULL, 1, 1, 'Receptionist', NULL, NULL, 1, NULL, NULL),
(3, 'Dr. Rajesh Kumar', 'doctor@cosyclinic.test', '9000000001', NULL, '$2y$12$9B2qV2Ub8wdCquRFVoOJge7QakYiFmVH3L8Hr6Hcz00ltrFic5acu', NULL, '2026-05-20 13:23:19', '2026-05-20 13:23:19', NULL, 1, 1, 'Senior Physician', NULL, NULL, 1, NULL, NULL),
(4, 'Pharmacy Counter', 'pharmacy@cosyclinic.test', '9000000001', NULL, '$2y$12$M6RQoXix6cIpFN3vriBMNuKJHO.tfSphXfQsGTNbHVsBrmzmES1/u', NULL, '2026-05-20 13:23:23', '2026-05-20 13:23:23', NULL, 1, 1, 'Pharmacist', NULL, NULL, 1, NULL, NULL),
(5, 'Accounts Desk', 'accounts@cosyclinic.test', '9000000001', NULL, '$2y$12$lG3d2VRlFLphbWEfsOSZjObyt4YT44YbYG9BhvifCx.30VLILplY2', NULL, '2026-05-20 13:23:24', '2026-05-20 13:23:24', NULL, 1, 1, 'Accountant', NULL, NULL, 1, NULL, NULL),
(6, 'Lab Technician', 'lab@cosyclinic.test', '9000000001', NULL, '$2y$12$Cy2ulIcrTkkVB5Wilx/w5u0oPXmba.47xD2QnsiUGsdeEIBtRuIZm', NULL, '2026-05-20 13:23:25', '2026-05-20 13:23:25', NULL, 1, 1, 'Lab Technician', NULL, NULL, 1, NULL, NULL),
(7, 'Nurse Station', 'nurse@cosyclinic.test', '9000000001', NULL, '$2y$12$pf/QA.Q9gK2sQGjSokQSyOPadAuwT7NBfLG2ue7R1CFFK3kxgWJsq', NULL, '2026-05-20 13:23:25', '2026-05-20 13:23:25', NULL, 1, 1, 'Staff Nurse', NULL, NULL, 1, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `appointments_patient_id_foreign` (`patient_id`),
  ADD KEY `appointments_doctor_id_foreign` (`doctor_id`),
  ADD KEY `appointments_branch_id_foreign` (`branch_id`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `audit_logs_user_id_foreign` (`user_id`),
  ADD KEY `audit_logs_model_type_model_id_index` (`model_type`,`model_id`);

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `branches_code_unique` (`code`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `departments_branch_id_foreign` (`branch_id`);

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `doctors_user_id_foreign` (`user_id`),
  ADD KEY `doctors_department_id_foreign` (`department_id`);

--
-- Indexes for table `doctor_consultations`
--
ALTER TABLE `doctor_consultations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `doctor_consultations_patient_visit_id_foreign` (`patient_visit_id`),
  ADD KEY `doctor_consultations_doctor_id_foreign` (`doctor_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  ADD KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`);

--
-- Indexes for table `hospital_settings`
--
ALTER TABLE `hospital_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hospital_settings_branch_id_key_unique` (`branch_id`,`key`),
  ADD KEY `hospital_settings_key_index` (`key`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoices_invoice_number_unique` (`invoice_number`),
  ADD KEY `invoices_patient_id_foreign` (`patient_id`),
  ADD KEY `invoices_patient_visit_id_foreign` (`patient_visit_id`),
  ADD KEY `invoices_created_by_foreign` (`created_by`);

--
-- Indexes for table `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_items_invoice_id_foreign` (`invoice_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lab_orders`
--
ALTER TABLE `lab_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lab_orders_order_number_unique` (`order_number`),
  ADD KEY `lab_orders_patient_visit_id_foreign` (`patient_visit_id`),
  ADD KEY `lab_orders_consultation_id_foreign` (`consultation_id`),
  ADD KEY `lab_orders_lab_test_id_foreign` (`lab_test_id`),
  ADD KEY `lab_orders_technician_id_foreign` (`technician_id`),
  ADD KEY `lab_orders_status_index` (`status`);

--
-- Indexes for table `lab_tests`
--
ALTER TABLE `lab_tests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lab_tests_code_unique` (`code`);

--
-- Indexes for table `medicines`
--
ALTER TABLE `medicines`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `medicines_sku_unique` (`sku`),
  ADD KEY `medicines_name_index` (`name`);

--
-- Indexes for table `medicine_batches`
--
ALTER TABLE `medicine_batches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `medicine_batches_medicine_id_batch_number_unique` (`medicine_id`,`batch_number`),
  ADD KEY `medicine_batches_branch_id_foreign` (`branch_id`),
  ADD KEY `medicine_batches_expiry_date_index` (`expiry_date`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `patients_patient_id_unique` (`patient_id`),
  ADD UNIQUE KEY `patients_barcode_unique` (`barcode`),
  ADD KEY `patients_branch_id_foreign` (`branch_id`),
  ADD KEY `patients_registered_by_foreign` (`registered_by`),
  ADD KEY `patients_name_mobile_index` (`name`,`mobile`),
  ADD KEY `patients_mobile_index` (`mobile`);

--
-- Indexes for table `patient_reports`
--
ALTER TABLE `patient_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_reports_patient_id_foreign` (`patient_id`),
  ADD KEY `patient_reports_patient_visit_id_foreign` (`patient_visit_id`),
  ADD KEY `patient_reports_consultation_id_foreign` (`consultation_id`),
  ADD KEY `patient_reports_uploaded_by_foreign` (`uploaded_by`);

--
-- Indexes for table `patient_visits`
--
ALTER TABLE `patient_visits`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `patient_visits_visit_number_unique` (`visit_number`),
  ADD KEY `patient_visits_patient_id_foreign` (`patient_id`),
  ADD KEY `patient_visits_branch_id_foreign` (`branch_id`),
  ADD KEY `patient_visits_department_id_foreign` (`department_id`),
  ADD KEY `patient_visits_doctor_id_foreign` (`doctor_id`),
  ADD KEY `patient_visits_receptionist_id_foreign` (`receptionist_id`),
  ADD KEY `patient_visits_status_created_at_index` (`status`,`created_at`),
  ADD KEY `patient_visits_status_index` (`status`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payments_invoice_id_foreign` (`invoice_id`),
  ADD KEY `payments_received_by_foreign` (`received_by`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indexes for table `pharmacy_orders`
--
ALTER TABLE `pharmacy_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pharmacy_orders_order_number_unique` (`order_number`),
  ADD KEY `pharmacy_orders_patient_visit_id_foreign` (`patient_visit_id`),
  ADD KEY `pharmacy_orders_prescription_id_foreign` (`prescription_id`),
  ADD KEY `pharmacy_orders_pharmacist_id_foreign` (`pharmacist_id`),
  ADD KEY `pharmacy_orders_status_index` (`status`);

--
-- Indexes for table `pharmacy_order_items`
--
ALTER TABLE `pharmacy_order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pharmacy_order_items_pharmacy_order_id_foreign` (`pharmacy_order_id`),
  ADD KEY `pharmacy_order_items_medicine_id_foreign` (`medicine_id`),
  ADD KEY `pharmacy_order_items_prescription_item_id_foreign` (`prescription_item_id`);

--
-- Indexes for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `prescriptions_prescription_number_unique` (`prescription_number`),
  ADD KEY `prescriptions_consultation_id_foreign` (`consultation_id`),
  ADD KEY `prescriptions_patient_visit_id_foreign` (`patient_visit_id`),
  ADD KEY `prescriptions_doctor_id_foreign` (`doctor_id`);

--
-- Indexes for table `prescription_items`
--
ALTER TABLE `prescription_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `prescription_items_prescription_id_foreign` (`prescription_id`),
  ADD KEY `prescription_items_medicine_id_foreign` (`medicine_id`);

--
-- Indexes for table `queue_displays`
--
ALTER TABLE `queue_displays`
  ADD PRIMARY KEY (`id`),
  ADD KEY `queue_displays_branch_id_foreign` (`branch_id`),
  ADD KEY `queue_displays_department_id_foreign` (`department_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_employee_id_unique` (`employee_id`),
  ADD KEY `users_branch_id_foreign` (`branch_id`),
  ADD KEY `users_department_id_foreign` (`department_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `doctor_consultations`
--
ALTER TABLE `doctor_consultations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_settings`
--
ALTER TABLE `hospital_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `invoice_items`
--
ALTER TABLE `invoice_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lab_orders`
--
ALTER TABLE `lab_orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lab_tests`
--
ALTER TABLE `lab_tests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `medicines`
--
ALTER TABLE `medicines`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `medicine_batches`
--
ALTER TABLE `medicine_batches`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `patient_reports`
--
ALTER TABLE `patient_reports`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `patient_visits`
--
ALTER TABLE `patient_visits`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pharmacy_orders`
--
ALTER TABLE `pharmacy_orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `pharmacy_order_items`
--
ALTER TABLE `pharmacy_order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `prescriptions`
--
ALTER TABLE `prescriptions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `prescription_items`
--
ALTER TABLE `prescription_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `queue_displays`
--
ALTER TABLE `queue_displays`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `appointments_doctor_id_foreign` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `departments`
--
ALTER TABLE `departments`
  ADD CONSTRAINT `departments_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `doctors`
--
ALTER TABLE `doctors`
  ADD CONSTRAINT `doctors_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `doctors_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `doctor_consultations`
--
ALTER TABLE `doctor_consultations`
  ADD CONSTRAINT `doctor_consultations_doctor_id_foreign` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `doctor_consultations_patient_visit_id_foreign` FOREIGN KEY (`patient_visit_id`) REFERENCES `patient_visits` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hospital_settings`
--
ALTER TABLE `hospital_settings`
  ADD CONSTRAINT `hospital_settings_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `invoices_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `invoices_patient_visit_id_foreign` FOREIGN KEY (`patient_visit_id`) REFERENCES `patient_visits` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD CONSTRAINT `invoice_items_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lab_orders`
--
ALTER TABLE `lab_orders`
  ADD CONSTRAINT `lab_orders_consultation_id_foreign` FOREIGN KEY (`consultation_id`) REFERENCES `doctor_consultations` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `lab_orders_lab_test_id_foreign` FOREIGN KEY (`lab_test_id`) REFERENCES `lab_tests` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lab_orders_patient_visit_id_foreign` FOREIGN KEY (`patient_visit_id`) REFERENCES `patient_visits` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lab_orders_technician_id_foreign` FOREIGN KEY (`technician_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `medicine_batches`
--
ALTER TABLE `medicine_batches`
  ADD CONSTRAINT `medicine_batches_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `medicine_batches_medicine_id_foreign` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `patients`
--
ALTER TABLE `patients`
  ADD CONSTRAINT `patients_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `patients_registered_by_foreign` FOREIGN KEY (`registered_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `patient_reports`
--
ALTER TABLE `patient_reports`
  ADD CONSTRAINT `patient_reports_consultation_id_foreign` FOREIGN KEY (`consultation_id`) REFERENCES `doctor_consultations` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `patient_reports_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `patient_reports_patient_visit_id_foreign` FOREIGN KEY (`patient_visit_id`) REFERENCES `patient_visits` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `patient_reports_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `patient_visits`
--
ALTER TABLE `patient_visits`
  ADD CONSTRAINT `patient_visits_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `patient_visits_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `patient_visits_doctor_id_foreign` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `patient_visits_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `patient_visits_receptionist_id_foreign` FOREIGN KEY (`receptionist_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_received_by_foreign` FOREIGN KEY (`received_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `pharmacy_orders`
--
ALTER TABLE `pharmacy_orders`
  ADD CONSTRAINT `pharmacy_orders_patient_visit_id_foreign` FOREIGN KEY (`patient_visit_id`) REFERENCES `patient_visits` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pharmacy_orders_pharmacist_id_foreign` FOREIGN KEY (`pharmacist_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pharmacy_orders_prescription_id_foreign` FOREIGN KEY (`prescription_id`) REFERENCES `prescriptions` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `pharmacy_order_items`
--
ALTER TABLE `pharmacy_order_items`
  ADD CONSTRAINT `pharmacy_order_items_medicine_id_foreign` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pharmacy_order_items_pharmacy_order_id_foreign` FOREIGN KEY (`pharmacy_order_id`) REFERENCES `pharmacy_orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pharmacy_order_items_prescription_item_id_foreign` FOREIGN KEY (`prescription_item_id`) REFERENCES `prescription_items` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD CONSTRAINT `prescriptions_consultation_id_foreign` FOREIGN KEY (`consultation_id`) REFERENCES `doctor_consultations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `prescriptions_doctor_id_foreign` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `prescriptions_patient_visit_id_foreign` FOREIGN KEY (`patient_visit_id`) REFERENCES `patient_visits` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `prescription_items`
--
ALTER TABLE `prescription_items`
  ADD CONSTRAINT `prescription_items_medicine_id_foreign` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `prescription_items_prescription_id_foreign` FOREIGN KEY (`prescription_id`) REFERENCES `prescriptions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `queue_displays`
--
ALTER TABLE `queue_displays`
  ADD CONSTRAINT `queue_displays_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `queue_displays_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `users_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
