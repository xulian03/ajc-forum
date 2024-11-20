-- phpMyAdmin SQL Dump
-- version 3.4.4
-- http://www.phpmyadmin.net
--
-- Servidor: mysql.webcindario.com
-- Tiempo de generación: 20-11-2024 a las 14:09:58
-- Versión del servidor: 5.7.30
-- Versión de PHP: 5.6.40-77+0~20240606.85+debian12~1.gbpd4d5eb

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `ajc_forum`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `new`
--

CREATE TABLE IF NOT EXISTS `new` (
  `id` varchar(35) NOT NULL,
  `user` varchar(15) NOT NULL,
  `title` varchar(100) NOT NULL,
  `content` varchar(1000) NOT NULL,
  `images` mediumtext,
  `date` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `new`
--

INSERT INTO `new` (`id`, `user`, `title`, `content`, `images`, `date`) VALUES
('new_673928b61e7ab3.84847401', 'vegamjuli', 'ExpoCamacho', '¡Prepárate para la ExpoCamacho! Este miércoles 20 de noviembre tienes una cita imperdible: ven y descubre los increíbles proyectos que los estudiantes de cada especialidad han creado con mucho esfuerzo y creatividad. ¡No te lo pierdas!', '["images/vegamjuli/673928b62008f6.98030745.jpg"]', '1731799222'),
('new_673929d791a044.51323390', 'vegamjuli', 'Foro Camacho', '¡Bienvenido a Foro Camacho! Un espacio diseñado para que te informes, compartas ideas, participes activamente y juntos transformemos nuestra realidad educativa.', '[]', '1731799511');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `post`
--

CREATE TABLE IF NOT EXISTS `post` (
  `id` varchar(35) NOT NULL,
  `content` varchar(1000) NOT NULL,
  `images` mediumtext,
  `likes` int(10) NOT NULL DEFAULT '0',
  `date` varchar(20) NOT NULL,
  `user` varchar(15) NOT NULL,
  `parent` varchar(35) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `post`
--

INSERT INTO `post` (`id`, `content`, `images`, `likes`, `date`, `user`, `parent`) VALUES
('post_673cb5be0b68f7.52045489', 'no gana', '[]', 1, '1732031934', 'vegamjuli', 'thread_673cb56e279574.00842883'),
('post_673cb5c9481e75.71879949', 'nose', '[]', 0, '1732031945', 'vegamjuli', 'post_673cb5be0b68f7.52045489');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `survey`
--

CREATE TABLE IF NOT EXISTS `survey` (
  `id` varchar(35) NOT NULL,
  `title` varchar(100) NOT NULL,
  `votes` mediumtext NOT NULL,
  `multi_select` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `survey`
--

INSERT INTO `survey` (`id`, `title`, `votes`, `multi_select`) VALUES
('thread_671a391fa6ce7', 'sadas', '{"asdasdas":0,"sadsad":1,"sadasdas":0,"asdsa":0}', 0),
('thread_672cefd684b71', 'cierto?', '{"Si":2,"Sii":2,"Siiii":2,"no":1}', 1),
('thread_67353525a1155', 'Hola', '{"Hola":2,"Chao":1}', 1),
('thread_67367688a82368.134', 'Messi o cr7', '{"si":1,"no":0}', 0),
('thread_673a04d69e4880.47198400', 'Ddd', '{"Najs":1,"Jajaj":1}', 1),
('thread_673cb49069fb81.39884059', '¿Mejor departamento?', '{"si":0,"no":1}', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `thread`
--

CREATE TABLE IF NOT EXISTS `thread` (
  `id` varchar(35) NOT NULL,
  `title` varchar(200) NOT NULL,
  `content` varchar(1000) NOT NULL,
  `survey` tinyint(1) NOT NULL,
  `images` mediumtext,
  `likes` int(10) NOT NULL DEFAULT '0',
  `date` varchar(20) NOT NULL,
  `open` tinyint(1) NOT NULL DEFAULT '1',
  `user` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `thread`
--

INSERT INTO `thread` (`id`, `title`, `content`, `survey`, `images`, `likes`, `date`, `open`, `user`) VALUES
('thread_673cb49069fb81.39884059', 'Hola', 'Hola', 1, '[]', 1, '1732031632', 0, 'vegamjuli'),
('thread_673cb56e279574.00842883', 'Máximo gana', 'El proyecto de máximo está súper', 0, '[]', 2, '1732031854', 1, 'gabyyy');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` varchar(15) NOT NULL,
  `document` varchar(25) NOT NULL,
  `name` varchar(50) NOT NULL,
  `password` varchar(25) NOT NULL,
  `icon` varchar(1000) NOT NULL DEFAULT 'profile-none.jpg',
  `cover` varchar(10) NOT NULL DEFAULT '#d4d4d4',
  `description` varchar(1000) NOT NULL DEFAULT 'Soy yo!',
  `verified` tinyint(1) NOT NULL DEFAULT '0',
  `threads` mediumtext NOT NULL,
  `surveys` mediumtext NOT NULL,
  `likes` mediumtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `user`
--

INSERT INTO `user` (`id`, `document`, `name`, `password`, `icon`, `cover`, `description`, `verified`, `threads`, `surveys`, `likes`) VALUES
('vegamjuli', '1109921817', 'Julian Vega', '12345678', 'profile-none.jpg', '#330f42', 'Top 1 de Haiti en Haxball.', 1, '["thread_67198b3323409","thread_6719b86aa207d","thread_671a391fa6ce7","thread_672c27efb2a9c","thread_67367688a82368.13469357","thread_6736774bb5a1c2.05091322","thread_6736777f338766.64663819","thread_67367802839e56.40808843","thread_673678797d9639.85416329","thread_67373778d011d7.76579130","thread_673a04471b3051.14196348","thread_673a04d69e4880.47198400","thread_673cabf85f3df3.12775700","thread_673cb49069fb81.39884059"]', '{"thread_671a391fa6ce7":"sadsad","thread_672cefd684b71":["Si","Sii","Siiii"],"thread_67353525a1155":["Hola","Chao"],"thread_673a04d69e4880.47198400":["Najs","Jajaj"],"thread_67367688a82368.134":"si","thread_673cb49069fb81.39884059":{"1":"no"}}', '["thread_6710af9982fbf","post_671989b1a94c5","thread_67198b3323409","thread_6710aea236e08","post_6710b03f7842f","post_6719afb32b4a7","post_6719afae3e0f9","post_67198b5ba4654","post_6710b02d8df1a","post_671989aa6cf31","post_671989a426d5d","post_6719899e37b97","post_6710b437ec476","post_6710b39f7c33e","post_6710b28b147af","post_6710b2876db49","thread_6719b86aa207d","post_6722ebe91e0df","thread_671a391fa6ce7","post_67295dd13c274","post_67296eea6e18e","thread_672c27efb2a9c","post_67343bbd59c85","thread_672cf012e2f92","post_673410792f6e6","post_67295dc48d91c","post_67352c14e61c0","thread_67353525a1155","post_6735383094838","thread_673678797d9639.85416329","post_673678db5586d2.51593644","post_673678e470f3b0.87194494","thread_67373778d011d7.76579130","post_67389cf28f9a10.57229382","post_67389d0e18cc87.87292853","post_67389d5b31fc56.45422988","post_673a2ffd8c0526.48192933","thread_67367688a82368.134","post_673b54eb68fa96.47271617","thread_673cb49069fb81.39884059","post_673cb5be0b68f7.52045489","thread_673cb56e279574.00842883"]'),
('vkatherineb', '21032193121212', 'Kat', 'valeria123', 'https://i.imgur.com/uKS36bf.png', '#d4d4d4', 'Soy yo!', 0, '["thread_672cefd684b71","thread_672cf012e2f92"]', '{"thread_672cefd684b71":["Si","Sii","Siiii","no"]}', '["post_672ce09f9adb7","thread_672cf012e2f92","post_6733ff376990d","post_673410792f6e6","thread_672cefd684b71","post_67341075e73c0","post_6734107151cca","post_67352c14e61c0"]'),
('Zuryy', '12345678', 'Zury Marulanda', '12345678', 'profile-none.jpg', '#d4d4d4', 'Soy yo!', 0, '', '', ''),
('by_midoriya', '1111111111', 'Brayan Paramo', '12345678', 'profile-none.jpg', '#d4d4d4', 'Soy yo!', 0, '["thread_67353525a1155"]', '{"thread_67353525a1155":["Hola"]}', ''),
('Andrew', '11111111', 'Andrew', 'andrew02', 'profile-none.jpg', '#d4d4d4', 'Soy yo!', 0, '', '', ''),
('gabyyy', '1107851034', 'Gabriela', '12131415', 'profile-none.jpg', '#d4d4d4', 'Soy yo!', 0, '["thread_673cb56e279574.00842883"]', '', '["thread_673cb56e279574.00842883"]');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
