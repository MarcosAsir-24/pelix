-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 15-10-2025 a las 20:29:42
-- Versión del servidor: 8.0.17
-- Versión de PHP: 7.3.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `peliculas`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` text,
  `icono` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`, `descripcion`, `icono`) VALUES
(4, 'Acción', NULL, 'img/categorias/accion.png'),
(7, 'Comedia', NULL, 'img/categorias/comedia.png'),
(8, 'Terror', NULL, 'img/categorias/terror.png'),
(9, 'Próximamente', NULL, 'img/categorias/proximamente.png'),
(10, 'Ciencia Ficción', NULL, 'img/categorias/cienciaficcion.png'),
(11, 'Animación', NULL, 'img/categorias/animacion.png');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `favoritos`
--

CREATE TABLE `favoritos` (
  `usuario_id` int(11) NOT NULL,
  `pelicula_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `favoritos`
--

INSERT INTO `favoritos` (`usuario_id`, `pelicula_id`) VALUES
(99, 20),
(108, 19);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `stripe_payment_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `monto` decimal(6,2) NOT NULL,
  `estado` enum('exitoso','fallido','pendiente') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_pago` datetime DEFAULT CURRENT_TIMESTAMP,
  `suscripcion_inicio` date NOT NULL,
  `suscripcion_fin` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `peliculas`
--

CREATE TABLE `peliculas` (
  `id` int(11) NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `categoria_id` int(11) NOT NULL,
  `imagen` varchar(255) NOT NULL,
  `duracion` int(11) NOT NULL,
  `sinopsis` text NOT NULL,
  `destacada` tinyint(1) DEFAULT '0',
  `trailer_url` varchar(255) DEFAULT NULL,
  `video_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `peliculas`
--

INSERT INTO `peliculas` (`id`, `titulo`, `categoria_id`, `imagen`, `duracion`, `sinopsis`, `destacada`, `trailer_url`, `video_url`) VALUES
(19, 'John Wick', 4, 'img/peliculas/johnwick.jpg', 101, 'En Nueva York, John Wick, un asesino a sueldo retirado, vuelve otra vez a la acción para vengarse de los gánsteres que le quitaron todo.', 1, 'https://www.youtube.com/watch?v=C0BMx-qxsP4', NULL),
(20, 'Terminator', 4, 'img/peliculas/terminator.jpg', 107, 'En el año 2029 las máquinas dominan el mundo. Los rebeldes que luchan contra ellas tienen como líder a John Connor, un hombre que nació en los años ochenta.', 1, 'https://www.youtube.com/watch?v=nGrW-OR2uDk', NULL),
(21, 'El último boy scout', 4, 'img/peliculas/ultimoboyscout.jpg', 105, 'Un detective privado en horas bajas, cuya carrera terminó al enfrentarse con un político corrupto y una gran estrella del fútbol que cae en desgracia al verse involucrado en un supuesto escándalo, se unirán para resolver sus problemas.', 1, 'https://www.youtube.com/watch?v=GgYz2e99Vh4', NULL),
(22, 'Torrente', 7, 'img/peliculas/torrente.jpg', 97, 'Torrente es un policía español, machista, racista y alcohólico. Este magnífico representante de las fuerzas del orden vive, con su padre hemipléjico, en Madrid. Gracias a su olfato, descubre en su propio barrio una importante red de narcotraficantes.', 1, 'https://www.youtube.com/watch?v=ZIqX4Xzd-Xo', NULL),
(23, 'La vida de Brian', 7, 'img/peliculas/vidadebrian.jpg', 94, 'Durante la época bíblica, un hombre parece ser el Mesías y se ve puesto como líder de un movimiento religioso. Su vida estará marcada por su castrante madre, sus nuevos amigos del Frente Popular de Judea y su novia feminista.', 1, 'https://www.youtube.com/watch?v=0-E6bKzb1lw', NULL),
(24, 'Resacón en las Vegas', 7, 'img/peliculas/resacon.jpg', 100, 'Cuatro amigos celebran la despedida de soltero de uno de ellos en Las Vegas. Pero, cuando a la mañana siguiente no pueden encontrar al novio y no recuerdan nada, deberán intentar volver sobre sus pasos, antes de que llegue la hora de la boda.', 1, 'https://www.youtube.com/watch?v=MckEHmSMg-0', NULL),
(25, 'IT', 8, 'img/peliculas/it.jpg', 135, 'Varios niños de una pequeña ciudad del estado de Maine se alían para combatir a una entidad diabólica que adopta la forma de un payaso y desde hace mucho tiempo emerge cada 27 años para saciarse de sangre infantil.', 1, 'https://www.youtube.com/watch?v=xKJmEC5ieOk', NULL),
(26, 'Hereditary', 8, 'img/peliculas/hereditary.jpg', 127, 'Después de la muerte de la matriarca de los Graham, su hija, Annie, se muda a la casa con su familia. Annie espera olvidar los problemas que tuvo en su infancia allí, pero todo se tuerce cuando su hija empieza a ver figuras fantasmales.', 1, 'https://www.youtube.com/watch?v=V6wWKNij_1M', NULL),
(27, 'El silencio de los corderos', 8, 'img/peliculas/silencio.jpg', 118, 'Tras una serie de crímenes donde a las víctimas les faltaba parte de la piel, una agente del FBI inicia su carrera particular para dar con el asesino. Para resolver el caso la agente encargada deberá envtrevistarse con el doctor Hannibal Lecter.', 1, 'https://www.youtube.com/watch?v=mDKn4VL8o10', NULL),
(28, 'Interstellar', 10, 'img/peliculas/interstellar.jpg', 169, 'Un grupo de científicos y exploradores, encabezados por Cooper, se embarcan en un viaje espacial para encontrar un lugar con las condiciones necesarias para reemplazar a la Tierra y comenzar una nueva vida allí.', 1, 'https://www.youtube.com/watch?v=UoSSbmD9vqc', 'videos/Interstellar(2014).mp4'),
(29, 'Star Wars I', 10, 'img/peliculas/starwars1.jpg', 136, 'La República Galáctica está sumida en el caos. Los impuestos de las rutas comerciales a los sistemas estelares exteriores están en disputa. Esperando resolver el asunto con un bloqueo de poderosas naves de guerra, la codiciosa Federación del Comercio ha detenido todos los envíos al pequeño planeta de Naboo.', 1, 'https://www.youtube.com/watch?v=Izme__ZsURY', NULL),
(30, 'Star Wars II', 10, 'img/peliculas/starwars2.jpg', 142, 'En el Senado Galáctico reina la inquietud. Varios miles de sistemas solares han declarado su intención de abandonar la República. Este movimiento separatista, liderado por el misterioso conde Dooku, ha provocado que al limitado número de caballeros Jedi les resulte difícil mantener la paz y el orden en la galaxia.', 1, 'https://www.youtube.com/watch?v=DywnxIuPtUs', NULL),
(31, 'Star Wars III', 10, 'img/peliculas/starwars3.jpg', 140, '¡Guerra! La República se desmorona bajo los ataques del despiadado Lord Sith, el conde Dooku. Hay héroes en ambos bandos, pero el mal está por doquier.', 1, 'https://www.youtube.com/watch?v=kqkfjBKmWc4', NULL),
(32, 'Star Wars IV', 10, 'img/peliculas/starwars4.jpg', 121, 'La nave en la que viaja la princesa Leia es capturada por las tropas imperiales al mando del temible Darth Vader. Antes de ser atrapada, Leia consigue introducir un mensaje en su robot R2-D2, quien acompañado de su inseparable C-3PO logran escapar.', 1, 'https://www.youtube.com/watch?v=beAH5vea99k', NULL),
(33, 'Fast and Furious', 4, 'img/peliculas/fastandfurious.jpg', 106, 'Cada noche, Los Ángeles es testigo de alguna carrera de coches. Últimamente ha aparecido un nuevo corredor, todos saben que es duro y que es rápido, pero lo que no saben es que es un detective con la determinación de salir victorioso.', 1, 'https://www.youtube.com/watch?v=2TAOizOnNPo', NULL),
(34, 'Fast and Furious II', 4, 'img/peliculas/fastandfurious2.jpg', 107, 'Brian O\'Conner ayuda a la policía de Miami y se infiltra en el mundo de las carreras ilegales, esperando así redimirse ante sus superiores.', 1, 'https://www.youtube.com/watch?v=sWofeRh_53g', NULL),
(35, 'Fast and Furious III', 4, 'img/peliculas/fastandfurious3.jpg', 104, 'Shaun Boswell es un chico rebelde cuya única conexión con el mundo es a través de las carreras ilegales. Cuando la policía le amenaza con encarcelarle, se va a pasar una temporada con su tío, un militar destinado en Japón.', 1, 'https://www.youtube.com/watch?v=p8HQ2JLlc4E', NULL),
(36, 'Fast and Furious IV', 4, 'img/peliculas/fastandfurious4.jpg', 107, 'El ex convicto Dominic Toretto se une a su viejo adversario, Brian O\'Conner, que ahora trabaja para el FBI en Los Ángeles, con el fin de infiltrarse en una organización criminal que se dedica a introducir heroína en la ciudad.', 1, 'https://www.youtube.com/watch?v=_ixlOH9EzL4', NULL),
(37, 'Fast and Furious V', 4, 'img/peliculas/fastandfurious5.jpg', 130, 'Los viejos amigos Dominic y Mia Toretto y Brian O\'Conner se encuentran ahora en Río de Janeiro, donde pretenden dar un último golpe con el fin de obtener su libertad. Para ello reúnen a un grupo de élite de pilotos experimentados.', 1, 'https://www.youtube.com/watch?v=ndth_OIyfJw', NULL),
(38, 'Resacón II', 7, 'img/peliculas/resacon2.jpg', 102, 'Alan, Stu y Phil vuelven a despertarse en otra habitación de otro hotel y, para no perder la costumbre, en esta ocasión tampoco recuerdan nada. Esta vez sólo saben que están en Tailandia, a donde han viajado, junto a Doug, para asistir a la boda de Stu con Lauren.', 1, 'https://www.youtube.com/watch?v=zKSnp7Unjxw', NULL),
(39, 'Los caballeros de la mesa redonda', 7, 'img/peliculas/caballerosmesacuadrada.jpg', 91, 'La historia de las peripecias del rey Arturo y de sus caballeros de la mesa redonda durante la búsqueda del Santo Grial.', 1, 'https://www.youtube.com/watch?v=WGidnQmVTVQ', NULL),
(40, 'Resacón III', 7, 'img/peliculas/resacon3.jpg', 100, 'Tras la inesperada muerte de su padre, Alan es llevado por sus amigos Phil, Stu y Doug a un centro especializado para que mejore. Esta vez no hay boda ni fiesta de despedida, ¿qué puede ir mal?', 1, 'https://www.youtube.com/watch?v=pX2BDn4tG-E', NULL),
(41, 'Resacón en las Vegas: ellas también', 7, 'img/peliculas/resacon4.jpg', 90, 'Claire se va a casar y su alocada amiga Zoe le organiza un viaje a Las Vegas para celebrar su despedida de soltera. Con ellas van Leslie, la hermana de Claire, y Janet, la encargada del volante.', 1, 'https://www.youtube.com/watch?v=4zWirZHdYDs', NULL),
(42, 'Supercool', 7, 'img/peliculas/supercool.jpg', 119, 'Evan y Seth, dos adolescentes inadaptados, amigos desde la infancia, están a punto de graduarse en el instituto, pero antes de que sus vidas se separen para ir a la universidad, ambos van a intentar pasar una última e irrepetible noche de juerga.', 1, 'https://www.youtube.com/watch?v=XViu4tzzQ-s', NULL),
(43, 'Kraven el cazador', 9, 'img/peliculas/kraven.jpg', 127, 'El inmigrante ruso Sergei Kravinoff emprende una misión para demostrar que es el mejor cazador del mundo.', 1, 'https://www.youtube.com/watch?v=T0BYo7LXAZI', NULL),
(44, 'F1: La película', 9, 'img/peliculas/f1.jpg', 120, 'Se nos presenta el campeonato de carreras de Fórmula 1, creado en colaboración con la FIA, su organismo rector.', 1, 'https://www.youtube.com/watch?v=H4qYzIrxRds', NULL),
(45, 'Destino final 6: Lazos de Sangre', 9, 'img/peliculas/destinofinal.jpg', 109, 'Stefanie, una estudiante universitaria, se ve atormentada por pesadillas recurrentes sobre un accidente que le sucedió a su abuela hace décadas. Descubre que la muerte persigue a las familias de quienes sobrevivieron a accidentes, y su propia familia está en peligro. Para salvar a su familia, Stefanie debe encontrar la manera de detener la maldición que los persigue.', 1, 'https://www.youtube.com/watch?v=9dVeLRCW5Mc', NULL),
(46, 'Misión imposible: Sentencia final', 9, 'img/peliculas/misionimposibleprox.jpg', 170, 'Las nuevas aventuras del agente del FMI y líder de un equipo de operativos, Ethan Hunt.', 1, 'https://www.youtube.com/watch?v=YqtdLeJSM6o', NULL),
(47, 'Thunderbolts', 9, 'img/peliculas/thunderbolts.jpg', 126, 'Película de Marvel en la que -como si de un Escuadrón suicida se tratase- un grupo de villanos son enviados a misiones por encargo del gobierno.', 1, 'https://www.youtube.com/watch?v=M37eYEL8I5M', NULL),
(48, 'Minecraft', 9, 'img/peliculas/minecraft.jpg', 101, 'Cuatro inadaptados son absorbidos de repente por un misterioso portal hacia el Supramundo: un extraño y cúbico país de las maravillas que vive gracias a la imaginación. Para volver a casa, se embarcan en una búsqueda mágica con un artesano.', 1, 'https://www.youtube.com/watch?v=yxrjSE8XddA', NULL),
(51, 'La trama fenicia', 9, 'img/peliculas/tramafenicia.jpg', 105, 'Cuenta la historia de Zsa-zsa Korda, un magnate europeo de la industria de armamento y aviación, que tras una serie de intentos de asesinato decide preparar a su hija Liesl, una monja, como su heredera.', 1, 'https://www.youtube.com/watch?v=Mh2zCra1hHo', NULL),
(52, 'SAW IV', 8, 'img/peliculas/saw4.jpg', 93, 'Jigsaw y su aprendiz Amanda están muertos. Tras el asesinato de la detective Kerry, dos agentes del FBI se unen al detective Hoffman para investigar. Mientras tanto, el comandante Rigg es secuestrado y obligado a superar una serie de trampas en noventa minutos para salvar a un viejo amigo.', 1, 'https://www.youtube.com/watch?v=-A3g2cOnjTI', NULL),
(53, 'SAW V', 8, 'img/peliculas/saw5.jpg', 92, 'El agente Strahm sobrevive a una trampa de Jigsaw y sospecha que el detective Hoffman es su sucesor. Mientras tanto, cinco personas son forzadas a enfrentar pruebas interconectadas para sobrevivir.', 1, 'https://www.youtube.com/watch?v=1Xg2hhuYukE', NULL),
(54, 'SAW VI', 8, 'img/peliculas/saw6.jpg', 90, 'El agente Hoffman continúa el legado de Jigsaw mientras el FBI se acerca a descubrir su identidad. Simultáneamente, un ejecutivo de seguros enfrenta pruebas que lo obligan a evaluar el valor de la vida humana.', 1, 'https://www.youtube.com/watch?v=XKxcf50t1xE', NULL),
(55, 'SAW VII', 8, 'img/peliculas/saw7.jpg', 90, 'Un hombre que afirma haber sobrevivido a Jigsaw se convierte en el centro de atención, pero su historia oculta secretos. Mientras tanto, el detective Hoffman busca venganza y desata una nueva ola de terror.', 1, 'https://www.youtube.com/watch?v=wemGRv2mWH4', NULL),
(56, 'SAW VIII', 8, 'img/peliculas/saw8.jpg', 92, 'Una serie de asesinatos con el sello de Jigsaw desconcierta a la policía, a pesar de que el asesino lleva años muerto. La investigación revela un nuevo juego mortal que desafía la lógica.', 1, 'https://www.youtube.com/watch?v=oiKjzFfX44Q', NULL),
(57, 'SAW IX', 8, 'img/peliculas/saw9.jpg', 93, 'Un detective y su compañero investigan asesinatos que recuerdan los crímenes de Jigsaw. A medida que profundizan, descubren una conspiración que los involucra directamente en el juego.', 1, 'https://www.youtube.com/watch?v=KDqPsvJ8DeE', NULL),
(58, 'SAW X', 8, 'img/peliculas/saw10.jpg', 118, 'John Kramer, enfermo y desesperado, viaja a México para someterse a un tratamiento experimental contra el cáncer. Al descubrir que todo es una estafa, retoma su papel como Jigsaw para impartir su justicia a los estafadores.', 1, 'https://www.youtube.com/watch?v=NUaKBe9mdqU', NULL),
(59, 'SAW I', 8, 'img/peliculas/saw1.jpg', 103, 'Adam y Lawrence se despiertan encadenados en un sucio baño con un cadáver entre ellos. Su secuestrador es un loco conocido como Jigsaw cuyo juego consiste en forzar a sus cautivos a herirse a sí mismos o a otros con tal de permanecer vivos.', 1, 'https://www.youtube.com/watch?v=roj2cj8veeU', NULL),
(60, 'SAW II', 8, 'img/peliculas/saw2.jpg', 95, 'Aparece una nueva víctima de Jigsaw y Eric Matthews decide investigar. Jigsaw es detenido, aunque esto forma parte de su plan, y del que Matthews deberá participar si desea salvar a otras 8 personas.', 1, 'https://www.youtube.com/watch?v=GBDBu4GJqCg', NULL),
(61, 'SAW III', 8, 'img/peliculas/saw3.jpg', 108, 'Mientras la policía trata de atrapar a Jigsaw y a su ayudante Amanda, la doctora Lynn Denlon se convierte en la última pieza de su diabólico juego.', 1, 'https://www.youtube.com/watch?v=r8XAgmujwSo', NULL),
(62, 'CARS I', 11, 'img/peliculas/cars1.jpg', 117, 'El aspirante a campeón de carreras Rayo McQueen parece que está a punto de conseguir el éxito. Su actitud arrogante se desvanece cuando llega a una pequeña comunidad olvidada que le enseña las cosas importantes de la vida que había olvidado.', 1, 'https://www.youtube.com/watch?v=sXRAtBX5HRw', NULL),
(63, 'CARS II', 11, 'img/peliculas/cars2.jpg', 106, 'Rayo McQueen y la grúa Mate viajan al extranjero para participar en el primer Campeonato Mundial en el que se decidirá cuál es el coche más rápido de la tierra. Mate se convertirá en un espía secreto y McQueen competirá contra los mejores coches. El campeonato los llevará a Japón, París, Londres y por último, a Italia.', 1, 'https://www.youtube.com/watch?v=GsFnJc7NVZA', NULL),
(64, 'CARS III', 11, 'img/peliculas/cars3.jpg', 109, 'Eclipsado por los coches jóvenes, el veterano Rayo McQueen se ha visto expulsado del deporte que tanto ama. Sin embargo, no se rendirá tan fácilmente. Con la ayuda de sus amigos, Rayo aprende trucos nuevos para vencer al arrogante Jackson Storm.', 1, 'https://www.youtube.com/watch?v=MKeUsy-d3_k', NULL),
(65, 'CARS IV', 11, 'img/peliculas/cars4.jpg', 100, 'Rayo McQueen tendrá que enfrentarse a la realidad de la edad y la obsolescencia en el mundo de las carreras, sintiéndose como un campeón pero comparado con los nuevos autos como un Plymouth de Richard Petty.', 1, 'https://www.youtube.com/watch?v=HrWDIrjEQHk', NULL),
(66, 'Aviones', 11, 'img/peliculas/aviones.jpg', 91, 'Dusty es un avión que sueña con participar en una competición aérea de altos vuelos. Sin embargo, para conseguirlo primero tendrá que hacer frente a su miedo a las alturas. Para superar el miedo recurre a un experimentado aviador naval que le ayuda a clasificarse para la carrera.', 1, 'https://www.youtube.com/watch?v=BsV--WuDJGg', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellidos` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stripe_customer_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paypal_payer_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paypal_order_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paypal_data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `tarjeta_ultimos4` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tarjeta_marca` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tarjeta_expiracion` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Almacena hash de contraseña generado con password_hash()',
  `premium` tinyint(1) DEFAULT '0',
  `fecha_registro` datetime DEFAULT CURRENT_TIMESTAMP,
  `suscripcion_inicio` date DEFAULT NULL,
  `suscripcion_fin` date DEFAULT NULL,
  `foto_perfil` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `admin` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `apellidos`, `fecha_nacimiento`, `email`, `telefono`, `stripe_customer_id`, `paypal_payer_id`, `paypal_order_id`, `paypal_data`, `tarjeta_ultimos4`, `tarjeta_marca`, `tarjeta_expiracion`, `direccion`, `password_hash`, `premium`, `fecha_registro`, `suscripcion_inicio`, `suscripcion_fin`, `foto_perfil`, `admin`) VALUES
(11, 'Marcos', 'Galletero', '2004-06-03', 'marcos.galletero.asir@salesianosatocha.es', '123456789', NULL, 'UDABVM3TTCK7J', '3N051167F20057443', NULL, NULL, NULL, NULL, 'prueba', '$2y$10$aj2sz2fBgpM1qLtfAvvyaO36iuJq.0CxRTMME5W4KNfMgWuRs5RK', 1, '2025-06-05 03:28:49', NULL, NULL, NULL, 0),
(12, 'prueba', 'prueba', '1993-06-11', 'prueba@prueba.prueba', '123456789', NULL, 'UDABVM3TTCK7J', '0ST93958AP010150V', NULL, NULL, NULL, NULL, 'prueba', '$2y$10$0H5zlEPKhdOH5HZitKzWQe.scqMqsGiB4Gxmw0LQjEyial1h1w4C6', 1, '2025-06-05 03:37:30', NULL, NULL, 'perfil_12_1759958825.jpg', 0),
(24, 'prueba', 'prueba', '2003-06-12', 'prueba2@prueba.prueba', '123456789', NULL, 'UDABVM3TTCK7J', '6UN156716T0959421', NULL, NULL, NULL, NULL, 'prueba', '$2y$10$2Wc4GcgRXCkSDW4xeUosP.UXc.s4uoBS6fCRIRMhQGpIJYMYh0Qk6', 1, '2025-06-06 16:18:27', NULL, NULL, NULL, 0),
(99, 'prueba', 'prueba', '1111-11-11', 'xmarcoslopez204x@gmail.com', '123456789', NULL, 'UDABVM3TTCK7J', '5LD8421528494733B', NULL, NULL, NULL, NULL, 'prueba', '$2y$10$FfZQw0vOnqnoIAMja0Zla.a5ZlOpoBHTQIJRZiMa3bjjyOIE4Bh5O', 1, '2025-06-08 11:03:04', NULL, NULL, 'perfil_99_1749376663.png', 1),
(108, 'Marcos', 'Galletero', '1997-06-13', 'xninsuskyx@gmail.com', '142132131', NULL, 'UDABVM3TTCK7J', '0W392988JJ545594H', NULL, NULL, NULL, NULL, 'Mi casa', '$2y$10$cz1zK6Zri5mebj3a8TvZvuwn1HTPItmdNEQxCRuYwLkAHzH1RzOma', 1, '2025-06-08 20:48:23', NULL, NULL, 'perfil_108_1749408563.jpg', 0),
(109, 'Hola', 'Hola', '1999-11-11', 'hola@hola.es', '123456789', NULL, 'UDABVM3TTCK7J', '3S200903RB5761521', NULL, NULL, NULL, NULL, 'Mi casa', '$2y$10$h6RMOQ26upD9ky0C/SHBr.83DkIw5BQ2vSZ0y2.LHugHm2pYoWQv6', 1, '2025-10-09 00:56:58', NULL, NULL, NULL, 0);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `favoritos`
--
ALTER TABLE `favoritos`
  ADD PRIMARY KEY (`usuario_id`,`pelicula_id`),
  ADD KEY `fk_favoritos_usuario` (`usuario_id`),
  ADD KEY `fk_favoritos_pelicula` (`pelicula_id`);

--
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_usuario` (`usuario_id`);

--
-- Indices de la tabla `peliculas`
--
ALTER TABLE `peliculas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categoria_id` (`categoria_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `peliculas`
--
ALTER TABLE `peliculas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `favoritos`
--
ALTER TABLE `favoritos`
  ADD CONSTRAINT `fk_favoritos_pelicula` FOREIGN KEY (`pelicula_id`) REFERENCES `peliculas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_favoritos_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `fk_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `peliculas`
--
ALTER TABLE `peliculas`
  ADD CONSTRAINT `peliculas_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
