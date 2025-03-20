-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Czas generowania: 24 Lis 2024, 16:14
-- Wersja serwera: 10.1.37-MariaDB
-- Wersja PHP: 7.3.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `chess_manager`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `ranking_history`
--

CREATE TABLE `ranking_history` (
  `HistoryID` int(11) NOT NULL,
  `UserID` int(11) DEFAULT NULL,
  `Ranking` int(11) DEFAULT NULL,
  `ChangeDate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Zrzut danych tabeli `ranking_history`
--

INSERT INTO `ranking_history` (`HistoryID`, `UserID`, `Ranking`, `ChangeDate`) VALUES
(8, 2, 2495, '2024-11-24'),
(9, 17, 2440, '2024-11-24'),
(10, 19, 2532, '2024-11-24'),
(11, 20, 2438, '2024-11-24'),
(12, 18, 2366, '2024-11-24'),
(13, 1, 2710, '2024-11-24'),
(14, 16, 2578, '2024-11-24'),
(15, 21, 2300, '2024-11-24'),
(16, 18, 2376, '2024-11-24'),
(17, 19, 2522, '2024-11-24'),
(18, 21, 2300, '2024-11-24'),
(19, 1, 2710, '2024-11-24'),
(20, 2, 2505, '2024-11-24'),
(21, 20, 2428, '2024-11-24'),
(22, 17, 2440, '2024-11-24'),
(23, 16, 2578, '2024-11-24'),
(24, 21, 2300, '2024-11-24'),
(25, 2, 2505, '2024-11-24'),
(26, 20, 2438, '2024-11-24'),
(27, 18, 2366, '2024-11-24'),
(28, 16, 2568, '2024-11-24'),
(29, 17, 2450, '2024-11-24'),
(30, 19, 2512, '2024-11-24'),
(31, 1, 2720, '2024-11-24'),
(32, 18, 2366, '2024-11-24'),
(33, 2, 2505, '2024-11-24'),
(34, 21, 2300, '2024-11-24'),
(35, 20, 2438, '2024-11-24'),
(36, 19, 2512, '2024-11-24'),
(37, 17, 2450, '2024-11-24'),
(38, 1, 2720, '2024-11-24'),
(39, 16, 2568, '2024-11-24'),
(40, 2, 2495, '2024-11-24'),
(41, 18, 2376, '2024-11-24'),
(42, 1, 2730, '2024-11-24'),
(43, 16, 2558, '2024-11-24'),
(44, 21, 2300, '2024-11-24'),
(45, 17, 2450, '2024-11-24'),
(46, 20, 2428, '2024-11-24'),
(47, 19, 2522, '2024-11-24'),
(48, 19, 2532, '2024-11-24'),
(49, 17, 2440, '2024-11-24'),
(50, 18, 2386, '2024-11-24'),
(51, 2, 2485, '2024-11-24'),
(52, 16, 2568, '2024-11-24'),
(53, 21, 2290, '2024-11-24'),
(54, 1, 2740, '2024-11-24'),
(55, 20, 2418, '2024-11-24'),
(56, 2, 2475, '2024-11-24'),
(57, 6, 2332, '2024-11-24'),
(58, 4, 2550, '2024-11-24'),
(59, 5, 2465, '2024-11-24'),
(60, 3, 2408, '2024-11-24'),
(61, 1, 2730, '2024-11-24'),
(62, 5, 2465, '2024-11-24'),
(63, 4, 2550, '2024-11-24'),
(64, 2, 2475, '2024-11-24'),
(65, 6, 2332, '2024-11-24'),
(66, 3, 2418, '2024-11-24'),
(67, 1, 2720, '2024-11-24'),
(68, 5, 2475, '2024-11-24'),
(69, 3, 2408, '2024-11-24'),
(70, 6, 2342, '2024-11-24'),
(71, 2, 2465, '2024-11-24'),
(72, 1, 2730, '2024-11-24'),
(73, 4, 2540, '2024-11-24'),
(74, 4, 2530, '2024-11-24'),
(75, 1, 2740, '2024-11-24'),
(76, 2, 2455, '2024-11-24'),
(77, 3, 2418, '2024-11-24'),
(78, 5, 2465, '2024-11-24'),
(79, 6, 2352, '2024-11-24'),
(80, 2, 2465, '2024-11-24'),
(81, 6, 2342, '2024-11-24'),
(82, 4, 2530, '2024-11-24'),
(83, 3, 2418, '2024-11-24'),
(84, 5, 2455, '2024-11-24'),
(85, 1, 2750, '2024-11-24'),
(86, 4, 2540, '2024-11-24'),
(87, 1, 2740, '2024-11-24'),
(88, 6, 2352, '2024-11-24'),
(89, 5, 2445, '2024-11-24'),
(90, 3, 2428, '2024-11-24'),
(91, 2, 2455, '2024-11-24'),
(92, 2, 2455, '2024-11-24'),
(93, 4, 2540, '2024-11-24'),
(94, 5, 2455, '2024-11-24'),
(95, 6, 2342, '2024-11-24'),
(96, 1, 2740, '2024-11-24'),
(97, 3, 2428, '2024-11-24');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `rounds`
--

CREATE TABLE `rounds` (
  `RoundID` int(11) NOT NULL,
  `TournamentID` int(11) DEFAULT NULL,
  `RoundNumber` int(11) DEFAULT NULL,
  `Date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Zrzut danych tabeli `rounds`
--

INSERT INTO `rounds` (`RoundID`, `TournamentID`, `RoundNumber`, `Date`) VALUES
(7, 3, 1, '2024-11-16'),
(8, 3, 2, '2024-11-16'),
(9, 3, 3, '2024-11-16'),
(10, 3, 4, '2024-11-16'),
(11, 3, 5, '2024-11-16'),
(12, 3, 6, '2024-11-16'),
(13, 3, 7, '2024-11-16'),
(14, 4, 1, '2024-11-02'),
(15, 4, 2, '2024-11-02'),
(16, 4, 3, '2024-11-02'),
(17, 4, 4, '2024-11-02'),
(18, 4, 5, '2024-11-02'),
(19, 4, 6, '2024-11-02'),
(20, 5, 1, '2025-04-09'),
(21, 5, 2, '2025-04-09'),
(22, 5, 3, '2025-04-09'),
(23, 5, 4, '2025-04-09'),
(24, 5, 5, '2025-04-09'),
(25, 5, 6, '2025-04-09'),
(26, 5, 7, '2025-04-09');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `round_results`
--

CREATE TABLE `round_results` (
  `RoundResultID` int(11) NOT NULL,
  `RoundID` int(11) DEFAULT NULL,
  `UserID` int(11) DEFAULT NULL,
  `OpponentID` int(11) DEFAULT NULL,
  `Result` enum('win','draw','loss') COLLATE utf8_polish_ci NOT NULL,
  `Points` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Zrzut danych tabeli `round_results`
--

INSERT INTO `round_results` (`RoundResultID`, `RoundID`, `UserID`, `OpponentID`, `Result`, `Points`) VALUES
(7, 14, 2, 17, 'win', 1),
(8, 14, 19, 20, 'win', 1),
(9, 14, 18, 1, 'loss', 0),
(10, 14, 16, 21, 'draw', 0.5),
(11, 15, 18, 19, 'win', 1),
(12, 15, 21, 1, 'draw', 0.5),
(13, 15, 2, 20, 'win', 1),
(14, 15, 17, 16, 'draw', 0.5),
(15, 16, 21, 2, 'draw', 0.5),
(16, 16, 20, 18, 'win', 1),
(17, 16, 16, 17, 'loss', 0),
(18, 16, 19, 1, 'loss', 0),
(19, 17, 18, 2, 'draw', 0.5),
(20, 17, 21, 20, 'draw', 0.5),
(21, 17, 19, 17, 'draw', 0.5),
(22, 17, 1, 16, 'draw', 0.5),
(23, 18, 2, 18, 'loss', 0),
(24, 18, 1, 16, 'win', 1),
(25, 18, 21, 17, 'draw', 0.5),
(26, 18, 20, 19, 'loss', 0),
(27, 19, 19, 17, 'win', 1),
(28, 19, 18, 2, 'win', 1),
(29, 19, 16, 21, 'win', 1),
(30, 19, 1, 20, 'win', 1),
(31, 7, 2, 6, 'loss', 0),
(32, 7, 4, 5, 'draw', 0.5),
(33, 7, 3, 1, 'win', 1),
(34, 8, 5, 4, 'draw', 0.5),
(35, 8, 2, 6, 'draw', 0.5),
(36, 8, 3, 1, 'win', 1),
(37, 9, 5, 3, 'win', 1),
(38, 9, 6, 2, 'win', 1),
(39, 9, 1, 4, 'win', 1),
(40, 10, 4, 1, 'loss', 0),
(41, 10, 2, 3, 'loss', 0),
(42, 10, 5, 6, 'loss', 0),
(43, 11, 2, 6, 'win', 1),
(44, 11, 4, 3, 'draw', 0.5),
(45, 11, 5, 1, 'loss', 0),
(46, 12, 4, 1, 'win', 1),
(47, 12, 6, 5, 'win', 1),
(48, 12, 3, 2, 'win', 1),
(49, 13, 2, 4, 'draw', 0.5),
(50, 13, 5, 6, 'win', 1),
(51, 13, 1, 3, 'draw', 0.5);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `tournaments`
--

CREATE TABLE `tournaments` (
  `TournamentID` int(11) NOT NULL,
  `TournamentName` varchar(255) COLLATE utf8_polish_ci NOT NULL,
  `StartDate` date DEFAULT NULL,
  `EndDate` date DEFAULT NULL,
  `Location` varchar(255) COLLATE utf8_polish_ci DEFAULT NULL,
  `GameTempo` varchar(50) COLLATE utf8_polish_ci DEFAULT NULL,
  `Arbiter` varchar(100) COLLATE utf8_polish_ci DEFAULT NULL,
  `Organizer` varchar(100) COLLATE utf8_polish_ci DEFAULT NULL,
  `CompletedRounds` int(11) DEFAULT NULL,
  `TotalRounds` int(11) DEFAULT NULL,
  `System` varchar(50) COLLATE utf8_polish_ci DEFAULT NULL,
  `PlayerCount` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Zrzut danych tabeli `tournaments`
--

INSERT INTO `tournaments` (`TournamentID`, `TournamentName`, `StartDate`, `EndDate`, `Location`, `GameTempo`, `Arbiter`, `Organizer`, `CompletedRounds`, `TotalRounds`, `System`, `PlayerCount`) VALUES
(3, 'Świętokrzyska Liga Szachowa', '2024-11-16', '2024-11-16', 'Kielce', '30\' + 30\'\' na ruch', 'Adam Nowak', 'LKS Skała Tumlin', 7, 7, 'Losowy', 6),
(4, 'XVIII Otwarte Szachowe Grand Prix Starachowic', '2024-11-02', '2024-11-02', 'Starachowice', '10\'', 'Roman Kaput', 'Klub Szachowy Gambit', 6, 6, 'Losowy', 8),
(5, 'Warsaw Chess Festival 2025', '2025-04-09', '2025-04-11', 'Warszawa', '90\' + 30\'\' na ruch', 'Zbigniew Pyda', 'Polski Związek Szachowy', 0, 7, 'Losowy', 1);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `tournament_registrations`
--

CREATE TABLE `tournament_registrations` (
  `RegistrationID` int(11) NOT NULL,
  `UserID` int(11) DEFAULT NULL,
  `TournamentID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Zrzut danych tabeli `tournament_registrations`
--

INSERT INTO `tournament_registrations` (`RegistrationID`, `UserID`, `TournamentID`) VALUES
(3, 1, 3),
(10, 1, 4),
(2, 1, 5),
(4, 2, 3),
(17, 2, 4),
(5, 3, 3),
(6, 4, 3),
(7, 5, 3),
(8, 6, 3),
(11, 16, 4),
(12, 17, 4),
(13, 18, 4),
(14, 19, 4),
(15, 20, 4),
(16, 21, 4);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `tournament_results`
--

CREATE TABLE `tournament_results` (
  `ResultID` int(11) NOT NULL,
  `TournamentID` int(11) DEFAULT NULL,
  `UserID` int(11) DEFAULT NULL,
  `Title` varchar(50) COLLATE utf8_polish_ci DEFAULT NULL,
  `LastName` varchar(255) COLLATE utf8_polish_ci DEFAULT NULL,
  `FirstName` varchar(255) COLLATE utf8_polish_ci DEFAULT NULL,
  `Points` float DEFAULT NULL,
  `Position` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Zrzut danych tabeli `tournament_results`
--

INSERT INTO `tournament_results` (`ResultID`, `TournamentID`, `UserID`, `Title`, `LastName`, `FirstName`, `Points`, `Position`) VALUES
(7, 4, 1, 'Arcymistrz', 'Kowalski', 'Jan', 5, 1),
(8, 4, 19, 'Arcymistrz', 'Kmiec', 'Piotr', 3.5, 2),
(9, 4, 18, 'Mistrz FIDE', 'Tokarski', 'Michał', 3.5, 3),
(10, 4, 2, 'Arcymistrz', 'Nowak', 'Adam', 3, 4),
(11, 4, 17, 'Mistrz Międzynarodowy', 'Kubiak', 'Zbigniew', 2.5, 5),
(12, 4, 21, 'Mistrz FIDE', 'Nowicki', 'Bartosz', 2.5, 6),
(13, 4, 16, 'Arcymistrz', 'Sobolewski', 'Jan', 2.5, 7),
(14, 4, 20, 'Mistrz Międzynarodowy', 'Wojciechowski', 'Jan', 1.5, 8),
(15, 3, 3, 'Mistrz Międzynarodowy', 'Wiśniewski', 'Tomasz', 5, 1),
(16, 3, 6, 'Mistrz FIDE', 'Wójcik', 'Paweł', 4.5, 2),
(17, 3, 1, 'Arcymistrz', 'Kowalski', 'Jan', 3.5, 3),
(18, 3, 4, 'Arcymistrz', 'Dąbrowski', 'Marek', 3, 4),
(19, 3, 5, 'Mistrz Międzynarodowy', 'Szymczak', 'Krzysztof', 3, 5),
(20, 3, 2, 'Arcymistrz', 'Nowak', 'Adam', 2, 6);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `users`
--

CREATE TABLE `users` (
  `UserID` int(11) NOT NULL,
  `UserName` varchar(50) COLLATE utf8_polish_ci NOT NULL,
  `Password` varchar(255) COLLATE utf8_polish_ci NOT NULL,
  `FirstName` varchar(255) COLLATE utf8_polish_ci NOT NULL,
  `LastName` varchar(255) COLLATE utf8_polish_ci NOT NULL,
  `Email` varchar(255) COLLATE utf8_polish_ci NOT NULL,
  `Ranking` int(11) DEFAULT NULL,
  `Role` enum('player','arbiter') COLLATE utf8_polish_ci DEFAULT 'player',
  `ProfilePicture` varchar(255) COLLATE utf8_polish_ci DEFAULT '../uploads/default.png',
  `CreatedAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Club` varchar(255) COLLATE utf8_polish_ci DEFAULT NULL,
  `Birthday` date DEFAULT NULL,
  `Title` varchar(50) COLLATE utf8_polish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Zrzut danych tabeli `users`
--

INSERT INTO `users` (`UserID`, `UserName`, `Password`, `FirstName`, `LastName`, `Email`, `Ranking`, `Role`, `ProfilePicture`, `CreatedAt`, `Club`, `Birthday`, `Title`) VALUES
(1, 'test', 'test', 'Jan', 'Kowalski', 'jkowalski@example.com', 2740, 'arbiter', '../img/default_photo.png', '2023-09-30 22:00:00', 'Cebularz Lublin', '1984-02-15', 'Arcymistrz'),
(2, 'anowak', 'abc12345', 'Adam', 'Nowak', 'anowak@example.com', 2455, 'player', '../uploads/2.png', '2023-09-30 22:00:00', 'UKS Hetman Ostrowiec', '1991-06-25', 'Arcymistrz'),
(3, 'twiśniewski', 'xyz98765', 'Tomasz', 'Wiśniewski', 'twisniewski@example.com', 2428, 'player', '../uploads/3.png', '2023-09-30 22:00:00', 'UKS Hetman Ostrowiec', '1988-05-12', 'Mistrz Międzynarodowy'),
(4, 'mdąbrowski', 'chess456', 'Marek', 'Dąbrowski', 'mdabrowski@example.com', 2540, 'player', '../uploads/4.png', '2023-09-30 22:00:00', 'LUKKS Kielce', '1986-12-04', 'Arcymistrz'),
(5, 'kszymczak', 'knight1', 'Krzysztof', 'Szymczak', 'kszymczak@example.com', 2455, 'player', '../uploads/5.png', '2023-09-30 22:00:00', 'LUKKS Kielce', '1989-05-30', 'Mistrz Międzynarodowy'),
(6, 'pwojcik', 'pawn8901', 'Paweł', 'Wójcik', 'pwojcik@example.com', 2342, 'player', '../uploads/6.png', '2023-09-30 22:00:00', 'LUKKS Kielce', '1992-10-21', 'Mistrz FIDE'),
(7, 'jkaczmarek', 'rook4567', 'Jakub', 'Kaczmarek', 'jkaczmarek@example.com', 2601, 'player', '../uploads/7.png', '2023-09-30 22:00:00', 'Katolicki Klub Szachowy', '1987-11-04', 'Arcymistrz'),
(8, 'mzawadzki', 'bishop2', 'Mateusz', 'Zawadzki', 'mzawadzki@example.com', 2470, 'player', '../uploads/8.png', '2023-09-30 22:00:00', 'Katolicki Klub Szachowy', '1993-09-16', 'Mistrz Międzynarodowy'),
(9, 'ajankowski', 'queen321', 'Andrzej', 'Jankowski', 'ajankowski@example.com', 2345, 'player', '../uploads/9.png', '2023-09-30 22:00:00', 'Katolicki Klub Szachowy', '1990-04-27', 'Mistrz FIDE'),
(10, 'mgłowacki', 'king5678', 'Michał', 'Głowacki', 'mglowacki@example.com', 2585, 'player', '../uploads/10.png', '2023-09-30 22:00:00', 'Cebularz Lublin', '1985-07-02', 'Arcymistrz'),
(11, 'msadowski', 'check789', 'Michał', 'Sadowski', 'msadowski@example.com', 2412, 'player', '../uploads/11.png', '2023-09-30 22:00:00', 'Cebularz Lublin', '1991-03-21', 'Mistrz Międzynarodowy'),
(12, 'pzielinski', 'mate6543', 'Paweł', 'Zieliński', 'pzielinski@example.com', 2308, 'player', '../uploads/12.png', '2023-09-30 22:00:00', 'Cebularz Lublin', '1994-09-30', 'Mistrz FIDE'),
(13, 'kkowalczyk', 'castle12', 'Karol', 'Kowalczyk', 'kkowalczyk@example.com', 2635, 'player', '../uploads/13.png', '2023-09-30 22:00:00', 'LUKS Lubartów', '1983-04-22', 'Arcymistrz'),
(14, 'rgajewski', 'board123', 'Rafał', 'Gajewski', 'rgajewski@example.com', 2499, 'player', '../uploads/14.png', '2023-09-30 22:00:00', 'LUKS Lubartów', '1987-08-14', 'Mistrz Międzynarodowy'),
(15, 'wolski', 'move8901', 'Wojciech', 'Olski', 'wolski@example.com', 2350, 'player', '../img/default_photo.png', '2023-09-30 22:00:00', 'LUKS Lubartów', '1992-02-05', 'Mistrz FIDE'),
(16, 'jsobolewski', 'game4321', 'Jan', 'Sobolewski', 'jsobolewski@example.com', 2568, 'player', '../uploads/16.png', '2023-09-30 22:00:00', 'Szach Mat Radom', '1985-11-19', 'Arcymistrz'),
(17, 'zkubiak', 'match876', 'Zbigniew', 'Kubiak', 'zkubiak@example.com', 2440, 'player', '../uploads/17.png', '2023-09-30 22:00:00', 'Szach Mat Radom', '1988-01-25', 'Mistrz Międzynarodowy'),
(18, 'mtokarski', 'rank1234', 'Michał', 'Tokarski', 'mtokarski@example.com', 2386, 'player', '../uploads/18.png', '2023-09-30 22:00:00', 'Szach Mat Radom', '1990-06-12', 'Mistrz FIDE'),
(19, 'pkmiec', 'play9876', 'Piotr', 'Kmiec', 'pkmiec@example.com', 2532, 'player', '../uploads/19.png', '2023-09-30 22:00:00', 'Hetman Katowice', '1986-08-09', 'Arcymistrz'),
(20, 'jwojciechowski', 'score12', 'Jan', 'Wojciechowski', 'jwojciechowski@example.com', 2418, 'player', '../uploads/20.png', '2023-09-30 22:00:00', 'Hetman Katowice', '1990-12-03', 'Mistrz Międzynarodowy'),
(21, 'bnowicki', 'tourney3', 'Bartosz', 'Nowicki', 'bnowicki@example.com', 2290, 'player', '../uploads/21.png', '2023-09-30 22:00:00', 'Hetman Katowice', '1993-05-29', 'Mistrz FIDE'),
(22, 'dstankiewicz', 'round456', 'Dariusz', 'Stankiewicz', 'dstankiewicz@example.com', 2610, 'player', '../uploads/22.png', '2023-09-30 22:00:00', 'Szachowy Klub Gdynia', '1984-09-17', 'Arcymistrz'),
(23, 'pmalinski', 'result1', 'Piotr', 'Maliński', 'pmalinski@example.com', 2475, 'player', '../img/default_photo.png', '2023-09-30 22:00:00', 'Szachowy Klub Gdynia', '1989-04-08', 'Mistrz Międzynarodowy'),
(24, 'rkrawczyk', 'master12', 'Rafał', 'Krawczyk', 'rkrawczyk@example.com', 2330, 'player', '../uploads/24.png', '2023-09-30 22:00:00', 'Szachowy Klub Gdynia', '1992-10-26', 'Mistrz FIDE'),
(25, 'mwozniak', 'final678', 'Michał', 'Woźniak', 'mwozniak@example.com', 2580, 'player', '../uploads/25.png', '2023-09-30 22:00:00', 'MUKS Częstochowa', '1986-07-16', 'Arcymistrz'),
(26, 'gdudek', 'win3210', 'Grzegorz', 'Dudek', 'gdudek@example.com', 2482, 'player', '../uploads/26.png', '2023-09-30 22:00:00', 'MUKS Częstochowa', '1989-01-24', 'Mistrz Międzynarodowy'),
(27, 'rdziuba', 'chess234', 'Radosław', 'Dziuba', 'rdziuba@example.com', 2358, 'player', '../uploads/27.png', '2023-09-30 22:00:00', 'MUKS Częstochowa', '1994-08-15', 'Mistrz FIDE');

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `ranking_history`
--
ALTER TABLE `ranking_history`
  ADD PRIMARY KEY (`HistoryID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indeksy dla tabeli `rounds`
--
ALTER TABLE `rounds`
  ADD PRIMARY KEY (`RoundID`),
  ADD KEY `TournamentID` (`TournamentID`);

--
-- Indeksy dla tabeli `round_results`
--
ALTER TABLE `round_results`
  ADD PRIMARY KEY (`RoundResultID`),
  ADD KEY `RoundID` (`RoundID`),
  ADD KEY `UserID` (`UserID`),
  ADD KEY `OpponentID` (`OpponentID`);

--
-- Indeksy dla tabeli `tournaments`
--
ALTER TABLE `tournaments`
  ADD PRIMARY KEY (`TournamentID`);

--
-- Indeksy dla tabeli `tournament_registrations`
--
ALTER TABLE `tournament_registrations`
  ADD PRIMARY KEY (`RegistrationID`),
  ADD UNIQUE KEY `UserID` (`UserID`,`TournamentID`),
  ADD KEY `TournamentID` (`TournamentID`);

--
-- Indeksy dla tabeli `tournament_results`
--
ALTER TABLE `tournament_results`
  ADD PRIMARY KEY (`ResultID`),
  ADD KEY `TournamentID` (`TournamentID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indeksy dla tabeli `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `UserName` (`UserName`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT dla tabeli `ranking_history`
--
ALTER TABLE `ranking_history`
  MODIFY `HistoryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

--
-- AUTO_INCREMENT dla tabeli `rounds`
--
ALTER TABLE `rounds`
  MODIFY `RoundID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT dla tabeli `round_results`
--
ALTER TABLE `round_results`
  MODIFY `RoundResultID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT dla tabeli `tournaments`
--
ALTER TABLE `tournaments`
  MODIFY `TournamentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT dla tabeli `tournament_registrations`
--
ALTER TABLE `tournament_registrations`
  MODIFY `RegistrationID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT dla tabeli `tournament_results`
--
ALTER TABLE `tournament_results`
  MODIFY `ResultID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT dla tabeli `users`
--
ALTER TABLE `users`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- Ograniczenia dla zrzutów tabel
--

--
-- Ograniczenia dla tabeli `ranking_history`
--
ALTER TABLE `ranking_history`
  ADD CONSTRAINT `ranking_history_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`);

--
-- Ograniczenia dla tabeli `rounds`
--
ALTER TABLE `rounds`
  ADD CONSTRAINT `rounds_ibfk_1` FOREIGN KEY (`TournamentID`) REFERENCES `tournaments` (`TournamentID`);

--
-- Ograniczenia dla tabeli `round_results`
--
ALTER TABLE `round_results`
  ADD CONSTRAINT `round_results_ibfk_1` FOREIGN KEY (`RoundID`) REFERENCES `rounds` (`RoundID`),
  ADD CONSTRAINT `round_results_ibfk_2` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`),
  ADD CONSTRAINT `round_results_ibfk_3` FOREIGN KEY (`OpponentID`) REFERENCES `users` (`UserID`);

--
-- Ograniczenia dla tabeli `tournament_registrations`
--
ALTER TABLE `tournament_registrations`
  ADD CONSTRAINT `tournament_registrations_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`),
  ADD CONSTRAINT `tournament_registrations_ibfk_2` FOREIGN KEY (`TournamentID`) REFERENCES `tournaments` (`TournamentID`);

--
-- Ograniczenia dla tabeli `tournament_results`
--
ALTER TABLE `tournament_results`
  ADD CONSTRAINT `tournament_results_ibfk_1` FOREIGN KEY (`TournamentID`) REFERENCES `tournaments` (`TournamentID`),
  ADD CONSTRAINT `tournament_results_ibfk_2` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
