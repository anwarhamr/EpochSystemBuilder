-- phpMyAdmin SQL Dump
-- version 4.6.4deb1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 24, 2017 at 11:00 AM
-- Server version: 5.7.17-0ubuntu0.16.10.1
-- PHP Version: 7.0.15-0ubuntu0.16.10.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `epoch`
--

-- --------------------------------------------------------

--
-- Table structure for table `epoch_animal`
--

CREATE TABLE `epoch_animal` (
  `id` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  `preselect` tinyint(1) NOT NULL DEFAULT '0',
  `enable` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `epoch_animal`
--

INSERT INTO `epoch_animal` (`id`, `description`, `preselect`, `enable`) VALUES
('adult-mouse', 'Adult Mouse (P21+)', 0, 1),
('adult-rat', 'Adult Rat (P21+)', 1, 1),
('mouse-pup', 'Mouse Pup (P10+)', 0, 1),
('rat-pup', 'Rat Pup (P6+)', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `epoch_biopotential`
--

CREATE TABLE `epoch_biopotential` (
  `id` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  `preselect` tinyint(1) NOT NULL DEFAULT '0',
  `enable` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `epoch_biopotential`
--

INSERT INTO `epoch_biopotential` (`id`, `description`, `preselect`, `enable`) VALUES
('ecg', 'ECG', 0, 1),
('eeg', 'EEG', 1, 1),
('eeg-ecg', 'EEG/ECG (Differential)', 0, 1),
('eeg-eeg', 'EEG/EEG (Differential)', 0, 1),
('eeg-emg', 'EEG/EMG (Differential)', 0, 1),
('emg', 'EMG', 0, 1),
('emg-emg', 'EMG/EMG (Differential)', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `epoch_channels`
--

CREATE TABLE `epoch_channels` (
  `id` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  `preselect` tinyint(1) NOT NULL DEFAULT '0',
  `enable` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `epoch_channels`
--

INSERT INTO `epoch_channels` (`id`, `description`, `preselect`, `enable`) VALUES
('1', '1-Ch', 0, 1),
('2', '2-Ch', 1, 1),
('4', '4-Ch', 0, 1),
('6', '6-Ch', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `epoch_duration`
--

CREATE TABLE `epoch_duration` (
  `id` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  `preselect` tinyint(1) NOT NULL DEFAULT '0',
  `enable` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `epoch_duration`
--

INSERT INTO `epoch_duration` (`id`, `description`, `preselect`, `enable`) VALUES
('2-month', '2 months or less', 1, 1),
('2-week', '2 weeks or less', 0, 1),
('6-month', '6 months or less', 0, 1),
('reusable', 'more than 6 months (reusable 2 month increments)', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `epoch_message`
--

CREATE TABLE `epoch_message` (
  `id` bigint(20) NOT NULL,
  `description` varchar(255) NOT NULL,
  `preselect` tinyint(1) NOT NULL DEFAULT '0',
  `enable` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `epoch_message`
--

INSERT INTO `epoch_message` (`id`, `description`, `preselect`, `enable`) VALUES
(1, 'Due to the size of a 2 month transmitter it isn\'t practical to use with mice pups.', 0, 1),
(2, 'Due to the size of a 6 month transmitter it isn\'t practical to use with mice.', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `epoch_receiver`
--

CREATE TABLE `epoch_receiver` (
  `id` varchar(50) NOT NULL,
  `system_id` varchar(255) NOT NULL,
  `hertz` varchar(255) NOT NULL,
  `enable` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `epoch_receiver`
--

INSERT INTO `epoch_receiver` (`id`, `system_id`, `hertz`, `enable`) VALUES
('10021', 'classic', '', 0),
('10022', 'classic', '', 0),
('10072', 'pup', '', 1),
('10198', 'epoch6', '(60/60/60/60/60/60)', 1),
('10199', 'epoch6', '(60/60/60/60/60/60)', 1),
('10206', 'epoch2-100-100', '(100/100)', 1),
('10207', 'epoch2-100-100', '(100/100)', 1),
('10229', 'epoch2-200-200', '(200/200)', 1),
('10230', 'epoch2-200-200', '(200/200)', 1),
('10231', 'epoch2-100-200', '(100/200)', 1),
('10232', 'epoch2-100-200', '(100/200)', 1);

-- --------------------------------------------------------

--
-- Table structure for table `epoch_system`
--

CREATE TABLE `epoch_system` (
  `id` varchar(255) NOT NULL,
  `description` varchar(55) NOT NULL,
  `preselect` tinyint(1) NOT NULL DEFAULT '0',
  `enable` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `epoch_system`
--

INSERT INTO `epoch_system` (`id`, `description`, `preselect`, `enable`) VALUES
('classic', 'Classic - Blue', 0, 1),
('epoch2-100-100', 'Epoch 2 (100/100) - Red', 0, 1),
('epoch2-100-200', 'Epoch 2 (100/200) - Red', 0, 1),
('epoch2-200-200', 'Epoch 2 (200/200) - Red', 0, 1),
('epoch6', 'Epoch 6 - Turquoise', 0, 1),
('none', 'None', 1, 1),
('pup', 'Pup - TBD', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `epoch_transmitter`
--

CREATE TABLE `epoch_transmitter` (
  `id` bigint(20) NOT NULL,
  `part_number` varchar(50) NOT NULL,
  `receiver_id` varchar(50) NOT NULL,
  `animal_id` varchar(50) NOT NULL,
  `biopotential_id` varchar(50) NOT NULL,
  `default_gain1_id` varchar(255) NOT NULL,
  `default_gain2_id` varchar(255) NOT NULL,
  `channels_id` varchar(50) NOT NULL,
  `duration_id` varchar(50) NOT NULL,
  `message_id` bigint(20) NOT NULL DEFAULT '0',
  `preselect` tinyint(1) NOT NULL DEFAULT '0',
  `enable` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `epoch_transmitter`
--

INSERT INTO `epoch_transmitter` (`id`, `part_number`, `receiver_id`, `animal_id`, `biopotential_id`, `default_gain1_id`, `default_gain2_id`, `channels_id`, `duration_id`, `message_id`, `preselect`, `enable`) VALUES
(1, '10165', '10072', 'rat-pup', 'eeg', '1', '', '2', '2-week', 0, 0, 1),
(2, '10165', '10072', 'mouse-pup', 'eeg', '1', '', '2', '2-week', 0, 0, 1),
(3, '10128', '10072', 'rat-pup', 'eeg', '1', '', '2', '2-month', 0, 0, 1),
(4, '10208', '10199', 'adult-rat', 'eeg', '2', '', '6', '2-month', 0, 0, 1),
(5, '10208', '10198', 'adult-mouse', 'eeg', '2', '', '6', '2-month', 0, 0, 1),
(6, '10209', '10199', 'adult-rat', 'eeg', '2', '', '6', '6-month', 0, 0, 1),
(7, '10210', '10199', 'adult-rat', 'eeg', '2', '', '4', '2-month', 0, 0, 1),
(8, '10210', '10198', 'adult-mouse', 'eeg', '2', '', '4', '2-month', 0, 0, 1),
(9, '10211', '10199', 'adult-rat', 'eeg', '2', '', '4', '6-month', 0, 0, 1),
(10, '10212', '10199', 'adult-rat', 'eeg', '2', '', '2', '2-month', 0, 0, 1),
(11, '10212', '10198', 'adult-mouse', 'eeg', '2', '', '2', '2-month', 0, 0, 1),
(12, '10213', '10199', 'adult-rat', 'eeg', '2', '', '2', '6-month', 0, 0, 1),
(13, '10214', '10199', 'adult-rat', 'eeg', '2', '', '2', 'reusable', 0, 0, 1),
(14, '10214', '10198', 'adult-mouse', 'eeg', '2', '', '2', 'reusable', 0, 0, 1),
(15, '10212', '10207', 'adult-rat', 'eeg', '2', '', '2', '2-month', 0, 0, 1),
(16, '10212', '10206', 'adult-mouse', 'eeg', '2', '', '2', '2-month', 0, 0, 1),
(17, '10213', '10207', 'adult-rat', 'eeg', '2', '', '2', '6-month', 0, 0, 1),
(18, '10214', '10207', 'adult-rat', 'eeg', '2', '', '2', 'reusable', 0, 0, 1),
(19, '10214', '10206', 'adult-mouse', 'eeg', '2', '', '2', 'reusable', 0, 0, 1),
(20, '10216', '10207', 'adult-rat', 'eeg-eeg', '2', '2', '2', '2-month', 0, 0, 1),
(21, '10215', '10206', 'adult-mouse', 'eeg-eeg', '2', '2', '2', '2-month', 0, 0, 1),
(22, '10216', '10207', 'adult-rat', 'eeg-eeg', '2', '2', '2', '6-month', 0, 0, 1),
(23, '10215', '10232', 'adult-rat', 'eeg-emg', '2', '5', '2', '2-month', 0, 0, 1),
(24, '10215', '10231', 'adult-mouse', 'eeg-emg', '2', '5', '2', '2-month', 0, 0, 1),
(25, '10216', '10232', 'adult-rat', 'eeg-emg', '2', '5', '2', '6-month', 0, 0, 1),
(26, '10216', '10232', 'adult-rat', 'eeg-ecg', '2', '2', '2', '2-month', 0, 0, 1),
(27, '10215', '10231', 'adult-mouse', 'eeg-ecg', '2', '2', '2', '2-month', 0, 0, 1),
(28, '10216', '10232', 'adult-rat', 'eeg-ecg', '2', '2', '2', '6-month', 0, 0, 1),
(29, '10216', '10230', 'adult-rat', 'ecg-emg', '2', '5', '2', '2-month', 0, 0, 1),
(30, '10215', '10229', 'adult-mouse', 'ecg-emg', '2', '5', '2', '2-month', 0, 0, 1),
(31, '10216', '10230', 'adult-rat', 'ecg-emg', '2', '5', '2', '6-month', 0, 0, 1),
(32, '10216', '10230', 'adult-rat', 'emg-emg', '5', '5', '2', '2-month', 0, 0, 1),
(33, '10215', '10229', 'adult-mouse', 'emg-emg', '5', '5', '2', '2-month', 0, 0, 1),
(34, '10216', '10230', 'adult-rat', 'emg-emg', '5', '5', '2', '6-month', 0, 0, 1),
(35, '10161', '10230', 'adult-rat', 'ecg', '2', '', '1', '2-month', 0, 0, 1),
(36, '10161', '10229', 'adult-mouse', 'ecg', '2', '', '1', '2-month', 0, 0, 1),
(37, '10162', '10230', 'adult-rat', 'ecg', '2', '', '1', '6-month', 0, 0, 1),
(38, '10161', '10230', 'adult-rat', 'emg', '5', '', '1', '2-month', 0, 0, 1),
(39, '10161', '10229', 'adult-mouse', 'emg', '5', '', '1', '2-month', 0, 0, 1),
(40, '10162', '10230', 'adult-rat', 'emg', '5', '', '1', '6-month', 0, 0, 1),
(41, '10128', '10022', 'adult-rat', 'eeg', '2', '', '2', '2-month', 0, 0, 0),
(42, '10128', '10021', 'adult-mouse', 'eeg', '2', '', '2', '2-month', 0, 0, 0),
(43, '10129', '10022', 'adult-rat', 'eeg', '2', '', '2', '6-month', 0, 0, 0),
(44, '10238', '10022', 'adult-rat', 'eeg', '2', '', '2', 'reusable', 0, 0, 0),
(45, '10238', '10021', 'adult-mouse', 'eeg', '2', '', '2', 'reusable', 0, 0, 0),
(46, '', '10072', 'mouse-pup', 'eeg', '', '', '2', '2-month', 1, 0, 1),
(47, '', '10198', 'adult-mouse', 'egg', '', '', '6', '6-month', 2, 0, 1),
(48, '', '10198', 'adult-mouse', 'eeg', '', '', '4', '6-month', 2, 0, 1),
(49, '', '10198', 'adult-mouse', 'eeg', '', '', '2', '6-month', 2, 0, 1),
(50, '', '10206', 'adult-mouse', 'eeg', '', '', '2', '6-month', 2, 0, 1),
(51, '', '10231', 'adult-mouse', 'eeg-emg', '', '', '2', '6-month', 2, 0, 1),
(52, '', '10229', 'adult-mouse', 'ecg', '', '', '1', '6-month', 2, 0, 1),
(53, '', '10206', 'adult-mouse', 'eeg-eeg', '', '', '2', '6-month', 2, 0, 1),
(54, '', '10231', 'adult-mouse', 'eeg-ecg', '', '', '2', '6-month', 2, 0, 1),
(55, '', '10229', 'adult-mouse', 'emg', '', '', '1', '6-month', 2, 0, 1),
(56, '', '10229', 'adult-mouse', 'ecg-emg', '', '', '2', '6-month', 2, 0, 1),
(57, '', '10072', 'mouse-pup', 'eeg', '', '', '2', '2-month', 1, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `epoch_transmitter_gain`
--

CREATE TABLE `epoch_transmitter_gain` (
  `id` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  `preselect` tinyint(1) NOT NULL DEFAULT '0',
  `enable` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `epoch_transmitter_gain`
--

INSERT INTO `epoch_transmitter_gain` (`id`, `description`, `preselect`, `enable`) VALUES
('1', '1mV±', 0, 1),
('10', '10mV±', 0, 1),
('2', '2mV±', 0, 1),
('5', '5mV±', 0, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `epoch_animal`
--
ALTER TABLE `epoch_animal`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `epoch_biopotential`
--
ALTER TABLE `epoch_biopotential`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `epoch_channels`
--
ALTER TABLE `epoch_channels`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `epoch_duration`
--
ALTER TABLE `epoch_duration`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `epoch_message`
--
ALTER TABLE `epoch_message`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `id_2` (`id`);

--
-- Indexes for table `epoch_receiver`
--
ALTER TABLE `epoch_receiver`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_2` (`id`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `epoch_system`
--
ALTER TABLE `epoch_system`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_2` (`id`),
  ADD KEY `id` (`id`),
  ADD KEY `id_3` (`id`);

--
-- Indexes for table `epoch_transmitter`
--
ALTER TABLE `epoch_transmitter`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_2` (`id`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `epoch_transmitter_gain`
--
ALTER TABLE `epoch_transmitter_gain`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `epoch_message`
--
ALTER TABLE `epoch_message`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `epoch_transmitter`
--
ALTER TABLE `epoch_transmitter`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
