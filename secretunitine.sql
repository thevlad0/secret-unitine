-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 09, 2025 at 12:45 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `secretunitine`
--

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `id` int(11) NOT NULL,
  `groupName` varchar(50) NOT NULL,
  `ownerId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `groupName`, `ownerId`) VALUES
(6, 'test', 7),
(7, 'test2', 7),
(8, 'test3', 7),
(9, 'test4', 7),
(10, 'test5', 7),
(11, 'test6', 7),
(12, 'test7', 7);

-- --------------------------------------------------------

--
-- Table structure for table `group_members`
--

CREATE TABLE `group_members` (
  `id` int(11) NOT NULL,
  `groupId` int(11) NOT NULL,
  `memberId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `group_members`
--

INSERT INTO `group_members` (`id`, `groupId`, `memberId`) VALUES
(3, 6, 7),
(4, 7, 7),
(5, 8, 7),
(6, 9, 7),
(7, 10, 7),
(8, 11, 7),
(9, 12, 7);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `senderId` int(11) NOT NULL,
  `sentAt` datetime NOT NULL DEFAULT current_timestamp(),
  `topic` varchar(1024) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `chainNumber` int(11) DEFAULT NULL,
  `isAnonymous` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `message_folders`
--

CREATE TABLE `message_folders` (
  `id` int(11) NOT NULL,
  `folderName` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `message_folders`
--

INSERT INTO `message_folders` (`id`, `folderName`) VALUES
(1, 'Inbox'),
(2, 'SentMessages'),
(3, 'Deleted');

-- --------------------------------------------------------

--
-- Table structure for table `message_recipients`
--

CREATE TABLE `message_recipients` (
  `messageId` int(11) NOT NULL,
  `recipientId` int(11) NOT NULL,
  `recipientGroupId` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `username` varchar(25) NOT NULL,
  `resetToken` varchar(6) NOT NULL,
  `expiresAt` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`username`, `resetToken`, `expiresAt`) VALUES
('velinov1', 'b1dd6c', '2025-06-08 03:14:21'),
('velinov1', '5cdac9', '2025-06-08 03:16:31'),
('velinov1', '1cf0e4', '2025-06-08 09:57:58'),
('velinov1', '7fefc7', '2025-06-08 10:38:38'),
('velinov1', 'a3b4d1', '2025-06-08 10:39:12'),
('velinov1', 'eedeb5', '2025-06-08 10:41:35'),
('velinov1', 'c29eb7', '2025-06-08 10:54:43'),
('velinov1', '1f1dfa', '2025-06-08 11:01:20'),
('velinov1', '3e2672', '2025-06-08 11:03:22'),
('velinov1', '765f33', '2025-06-08 11:10:25'),
('velinov1', '1d713a', '2025-06-08 11:31:48'),
('velinov1', 'c681ca', '2025-06-08 11:36:10'),
('velinov1', '9c8660', '2025-06-08 11:38:50'),
('velinov1', 'bf5047', '2025-06-08 11:39:45'),
('velinov1', 'd4b6e4', '2025-06-08 11:50:36'),
('velinov1', 'a9c70c', '2025-06-08 11:53:28'),
('velinov1', '6df8ec', '2025-06-08 11:54:04'),
('velinov1', '7bf837', '2025-06-09 02:23:28');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `type` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fn` varchar(10) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(1000) NOT NULL,
  `username` varchar(20) NOT NULL,
  `name` varchar(10) NOT NULL,
  `lastname` varchar(10) NOT NULL,
  `role` varchar(50) NOT NULL,
  `recoveryEmail` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fn`, `email`, `password`, `username`, `name`, `lastname`, `role`, `recoveryEmail`) VALUES
(7, '0MI0600204', 'velinov1@uni-sofia.fmi.bg', '$2y$10$TcUkgeMKdbpZeeN5B141I.jfMX87GQbsOAzxoWXBXddb1uNxzwNQm', 'velinov1', 'Владимир', 'Великов', 'студент', 'the_vlad0@icloud.com');

-- --------------------------------------------------------

--
-- Table structure for table `user_messages_status`
--

CREATE TABLE `user_messages_status` (
  `messageId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `messageFolderId` int(11) NOT NULL,
  `isRead` tinyint(1) NOT NULL DEFAULT 0,
  `isStarred` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `groupNameIndex` (`groupName`),
  ADD KEY `ownerId_FK` (`ownerId`);

--
-- Indexes for table `group_members`
--
ALTER TABLE `group_members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `groupId_FK` (`groupId`),
  ADD KEY `memberOfGroup_FK` (`memberId`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `senderId_FK` (`senderId`);

--
-- Indexes for table `message_folders`
--
ALTER TABLE `message_folders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `message_recipients`
--
ALTER TABLE `message_recipients`
  ADD PRIMARY KEY (`messageId`,`recipientId`),
  ADD KEY `recipientId_FK` (`recipientId`),
  ADD KEY `recipientGroupId_FK` (`recipientGroupId`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `user_messages_status`
--
ALTER TABLE `user_messages_status`
  ADD PRIMARY KEY (`messageId`,`userId`,`messageFolderId`),
  ADD KEY `messageFolderId_FK` (`messageFolderId`),
  ADD KEY `userId_FK` (`userId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `group_members`
--
ALTER TABLE `group_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `message_folders`
--
ALTER TABLE `message_folders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `message_recipients`
--
ALTER TABLE `message_recipients`
  MODIFY `messageId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `user_messages_status`
--
ALTER TABLE `user_messages_status`
  MODIFY `messageId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `groups`
--
ALTER TABLE `groups`
  ADD CONSTRAINT `ownerId_FK` FOREIGN KEY (`ownerId`) REFERENCES `users` (`id`);

--
-- Constraints for table `group_members`
--
ALTER TABLE `group_members`
  ADD CONSTRAINT `groupId_FK` FOREIGN KEY (`groupId`) REFERENCES `groups` (`id`),
  ADD CONSTRAINT `memberOfGroup_FK` FOREIGN KEY (`memberId`) REFERENCES `users` (`id`);

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `senderId_FK` FOREIGN KEY (`senderId`) REFERENCES `users` (`id`);

--
-- Constraints for table `message_recipients`
--
ALTER TABLE `message_recipients`
  ADD CONSTRAINT `receivedMessageId_FK` FOREIGN KEY (`messageId`) REFERENCES `messages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `recipientGroupId_FK` FOREIGN KEY (`recipientGroupId`) REFERENCES `groups` (`id`),
  ADD CONSTRAINT `recipientId_FK` FOREIGN KEY (`recipientId`) REFERENCES `users` (`id`);

--
-- Constraints for table `user_messages_status`
--
ALTER TABLE `user_messages_status`
  ADD CONSTRAINT `messageFolderId_FK` FOREIGN KEY (`messageFolderId`) REFERENCES `message_folders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `messageId_FK` FOREIGN KEY (`messageId`) REFERENCES `messages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `userId_FK` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
