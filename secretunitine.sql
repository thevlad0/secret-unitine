-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1

-- Време на генериране:  8 юни 2025 в 21:52
-- Версия на сървъра: 10.4.32-MariaDB
-- Версия на PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данни: `secretunitine`
--

-- --------------------------------------------------------

--
-- Структура на таблица `groups`
--

CREATE TABLE `groups` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `adminId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура на таблица `group_members`
--

CREATE TABLE `group_members` (
  `groupId` int(11) NOT NULL,
  `userId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура на таблица `messages`
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

--
-- Схема на данните от таблица `messages`
--

INSERT INTO `messages` (`id`, `senderId`, `sentAt`, `topic`, `content`, `chainNumber`, `isAnonymous`) VALUES
(1, 2, '2025-06-08 16:20:53', 'Interesting topic', 'Hello world!', 0, 1),
(2, 3, '2025-06-08 16:20:53', 'New topic', 'Bye!', 0, 1),
(4, 3, '2025-06-08 19:38:35', 'Interesting topic', 'Hello world!', 0, 1);

-- --------------------------------------------------------

--
-- Структура на таблица `message_folders`
--

CREATE TABLE `message_folders` (
  `id` int(11) NOT NULL,
  `folderName` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Схема на данните от таблица `message_folders`
--

INSERT INTO `message_folders` (`id`, `folderName`) VALUES
(1, 'Inbox'),
(2, 'SentMessages'),
(3, 'Deleted');

-- --------------------------------------------------------

--
-- Структура на таблица `message_recipients`
--

CREATE TABLE `message_recipients` (
  `messageId` int(11) NOT NULL,
  `recipientId` int(11) NOT NULL,
  `recipientGroupId` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Схема на данните от таблица `message_recipients`
--

INSERT INTO `message_recipients` (`messageId`, `recipientId`, `recipientGroupId`) VALUES
(1, 4, NULL),
(2, 2, NULL),
(2, 4, NULL),
(4, 4, NULL);

-- --------------------------------------------------------

--
-- Структура на таблица `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `type` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура на таблица `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fn` varchar(10) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `username` varchar(20) NOT NULL,
  `name` varchar(10) NOT NULL,
  `surname` varchar(10) NOT NULL,
  `role` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Схема на данните от таблица `users`
--

INSERT INTO `users` (`id`, `fn`, `email`, `password`, `username`, `name`, `surname`, `role`) VALUES
(0, '', 'hrisi.hf@gmail.com', '$2y$10$M.XGXGHIJKaKi25OIy0dlOKJAi/yLtgYlK.P3929Mwn', 'hfrangova', 'Hrisi', 'Vasileva', 'REGLAR_USER'),
(2, '', 'stefka@gmail.com', '$2y$10$ETm7QdgA6CMy3oBGSOOQhOT4ayyKEAeyicbpf4CQthE', 'stefka', 'Stefka', 'Lyaskaliev', 'student'),
(3, '', 'iliyana@gmail.com', '$2y$10$75AwRxkA3N2DcTr53yfYXO5NqY2/G1LxoWqxpLjt5Vg', 'iliyana', 'Iliyana', 'Frangova', 'student'),
(4, '', 'vladi@gmail.com', '$2y$10$PqUkGeREkuBqG.q5MyFvgeBJrjUDBHw7kRmJMsPJ8iF', 'vlado', 'Vladi', 'NeZnam', 'teacher'),
(5, '', 'newUser@gmail.com', '$2y$10$Z6D2CT4jGvWJnyJm0VFNyefS99G6nmhaCqjUyPdh/E4', 'newUser', 'NewUserNam', 'NewUserSur', 'teacher');

-- --------------------------------------------------------

--
-- Структура на таблица `user_messages_status`
--

CREATE TABLE `user_messages_status` (
  `messageId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `messageFolderId` int(11) NOT NULL,
  `isRead` tinyint(1) NOT NULL DEFAULT 0,
  `isStarred` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Схема на данните от таблица `user_messages_status`
--

INSERT INTO `user_messages_status` (`messageId`, `userId`, `messageFolderId`, `isRead`, `isStarred`) VALUES
(1, 4, 3, 1, 0),
(2, 2, 1, 1, 1),
(2, 3, 2, 0, 0),
(2, 4, 1, 0, 0),
(4, 3, 2, 1, 0),
(4, 4, 1, 0, 0);

--
-- Indexes for dumped tables
--

--
-- Индекси за таблица `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `adminId_FK` (`adminId`);

--
-- Индекси за таблица `group_members`
--
ALTER TABLE `group_members`
  ADD PRIMARY KEY (`groupId`,`userId`),
  ADD KEY `memberOfGroup_FK` (`userId`);

--
-- Индекси за таблица `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `senderId_FK` (`senderId`);

--
-- Индекси за таблица `message_folders`
--
ALTER TABLE `message_folders`
  ADD PRIMARY KEY (`id`);

--
-- Индекси за таблица `message_recipients`
--
ALTER TABLE `message_recipients`
  ADD PRIMARY KEY (`messageId`,`recipientId`),
  ADD KEY `recipientId_FK` (`recipientId`),
  ADD KEY `recipientGroupId_FK` (`recipientGroupId`);

--
-- Индекси за таблица `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Индекси за таблица `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Индекси за таблица `user_messages_status`
--
ALTER TABLE `user_messages_status`
  ADD PRIMARY KEY (`messageId`,`userId`,`messageFolderId`),
  ADD KEY `messageFolderId_FK` (`messageFolderId`),
  ADD KEY `userId_FK` (`userId`);

--
-- AUTO_INCREMENT for dumped tables
--

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
-- AUTO_INCREMENT for table `user_messages_status`
--
ALTER TABLE `user_messages_status`
  MODIFY `messageId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Ограничения за дъмпнати таблици
--

--
-- Ограничения за таблица `groups`
--
ALTER TABLE `groups`
  ADD CONSTRAINT `adminId_FK` FOREIGN KEY (`adminId`) REFERENCES `users` (`id`);

--
-- Ограничения за таблица `group_members`
--
ALTER TABLE `group_members`
  ADD CONSTRAINT `groupId_FK` FOREIGN KEY (`groupId`) REFERENCES `groups` (`id`),
  ADD CONSTRAINT `memberOfGroup_FK` FOREIGN KEY (`userId`) REFERENCES `users` (`id`);

--
-- Ограничения за таблица `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `senderId_FK` FOREIGN KEY (`senderId`) REFERENCES `users` (`id`);

--
-- Ограничения за таблица `message_recipients`
--
ALTER TABLE `message_recipients`
  ADD CONSTRAINT `receivedMessageId_FK` FOREIGN KEY (`messageId`) REFERENCES `messages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `recipientGroupId_FK` FOREIGN KEY (`recipientGroupId`) REFERENCES `groups` (`id`),
  ADD CONSTRAINT `recipientId_FK` FOREIGN KEY (`recipientId`) REFERENCES `users` (`id`);

--
-- Ограничения за таблица `user_messages_status`
--
ALTER TABLE `user_messages_status`
  ADD CONSTRAINT `messageFolderId_FK` FOREIGN KEY (`messageFolderId`) REFERENCES `message_folders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `messageId_FK` FOREIGN KEY (`messageId`) REFERENCES `messages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `userId_FK` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
