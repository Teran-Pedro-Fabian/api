<?php
    class Model {
        protected $db;

        function __construct() {
            $this->db = new PDO('mysql:host='. MYSQL_HOST .';dbname='. MYSQL_DB .';charset=utf8', MYSQL_USER, MYSQL_PASS);
            $this->deploy();
        }

        function deploy() {
            // Chequear si hay tablas
            $query = $this->db->query('SHOW TABLES');
            $tables = $query->fetchAll(); // Nos devuelve todas las tablas de la db
            if(count($tables)==0) {
                // Si no hay crearlas
                $sql =<<<END
                --
                -- Estructura de tabla para la tabla `tareas`
                --
                
                CREATE TABLE `tareas` (
                  `id` int(11) NOT NULL,
                  `titulo` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
                  `descripcion` text COLLATE utf8_unicode_ci DEFAULT NULL,
                  `prioridad` int(11) NOT NULL,
                  `finalizada` tinyint(1) NOT NULL DEFAULT 0
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
                
                --
                -- Volcado de datos para la tabla `tareas`
                --
                
                INSERT INTO `tareas` (`id`, `titulo`, `descripcion`, `prioridad`, `finalizada`) VALUES
                (25, 'asdsa', 'asdasd', 1, 0),
                (26, 'Titulo modificado', 'Una descripcion', 87, 0),
                (27, 'Nuevo titulo', 'Otra descripcion', 2, 0);
                
                --
                -- Índices para tablas volcadas
                --
                
                --
                -- Indices de la tabla `tareas`
                --
                ALTER TABLE `tareas`
                  ADD PRIMARY KEY (`id`);
                
                --
                -- AUTO_INCREMENT de las tablas volcadas
                --
                
                --
                -- AUTO_INCREMENT de la tabla `tareas`
                --
                ALTER TABLE `tareas`
                  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;
                COMMIT;
                END;
                $this->db->query($sql);
            }
            
        }
    }