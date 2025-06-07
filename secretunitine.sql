-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Време на генериране: 26 май 2025 в 09:15
-- Версия на сървъра: 10.4.32-MariaDB
-- Версия на PHP: 8.0.30

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

--
-- Indexes for dumped tables
--

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
