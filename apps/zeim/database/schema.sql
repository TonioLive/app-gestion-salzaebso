-- ZEIM: schema only, no real records, credentials or scheduled operations.
-- Import into a NEW empty database. Configure catalogue rows and an administrator separately.
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS=0;

CREATE TABLE `agenda` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_centro` int(11) DEFAULT 0,
  `Nombre_usu` varchar(50) DEFAULT NULL,
  `Titulo` varchar(100) DEFAULT NULL,
  `Texto` longtext DEFAULT NULL,
  `Fecha` date DEFAULT NULL,
  `Hora` time DEFAULT NULL,
  `id_aula` int(11) NOT NULL DEFAULT 0,
  `avisar` date DEFAULT NULL,
  `enviarmail` tinyint(4) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `id_centro` (`id_centro`,`Nombre_usu`,`Fecha`,`id_aula`,`avisar`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `agenda_alumnos_comidas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_alumno` int(11) DEFAULT NULL,
  `id_aula` int(11) DEFAULT 0,
  `dia` date DEFAULT NULL,
  `almuerzo` int(11) DEFAULT 0,
  `comida1` int(11) DEFAULT 0,
  `comida2` int(11) DEFAULT 0,
  `comida3` int(11) DEFAULT 0,
  `comida4` int(11) DEFAULT 0,
  `comida5` int(11) DEFAULT 0,
  `postre1` int(11) DEFAULT 0,
  `merienda1` int(11) DEFAULT 0,
  `merienda2` int(11) DEFAULT 0,
  `cant_almuerzo` int(11) DEFAULT 0,
  `cant_comida1` int(11) DEFAULT 0,
  `cant_comida2` int(11) DEFAULT 0,
  `cant_comida3` int(11) DEFAULT 0,
  `cant_comida4` int(11) DEFAULT 0,
  `cant_comida5` int(11) DEFAULT 0,
  `cant_postre1` int(11) DEFAULT 0,
  `cant_merienda1` int(11) DEFAULT 0,
  `cant_merienda2` int(11) DEFAULT 0,
  `observaciones_comida` text DEFAULT NULL,
  `siesta1` int(11) DEFAULT 0,
  `siesta2` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `indice1` (`id_alumno`,`id_aula`,`dia`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `agenda_alumnos_comidas_sp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_alumno` int(11) DEFAULT NULL,
  `id_aula` int(11) DEFAULT 0,
  `dia` date DEFAULT NULL,
  `almuerzo` int(11) DEFAULT 0,
  `comida1` int(11) DEFAULT 0,
  `comida2` int(11) DEFAULT 0,
  `comida3` int(11) DEFAULT 0,
  `comida4` int(11) DEFAULT 0,
  `comida5` int(11) DEFAULT 0,
  `postre1` int(11) DEFAULT 0,
  `merienda1` int(11) DEFAULT 0,
  `merienda2` int(11) DEFAULT 0,
  `cant_almuerzo` int(11) DEFAULT 0,
  `cant_comida1` int(11) DEFAULT 0,
  `cant_comida2` int(11) DEFAULT 0,
  `cant_comida3` int(11) DEFAULT 0,
  `cant_comida4` int(11) DEFAULT 0,
  `cant_comida5` int(11) DEFAULT 0,
  `cant_postre1` int(11) DEFAULT 0,
  `cant_merienda1` int(11) DEFAULT 0,
  `cant_merienda2` int(11) DEFAULT 0,
  `observaciones_comida` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `agenda_alumnos_observaciones_tipo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `observacion` text DEFAULT NULL,
  `tipo` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `agenda_alumnos_seguimiento` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `area` int(11) DEFAULT 0,
  `id_alumno` int(11) DEFAULT NULL,
  `dia` date DEFAULT NULL,
  `id_aula` int(11) DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `nota` text DEFAULT NULL,
  `tipo_nota` int(11) DEFAULT 0,
  `avisar` tinyint(4) DEFAULT 0,
  `realizada` tinyint(4) DEFAULT 0,
  `para_id_aula` int(11) DEFAULT 0,
  `para_id_centro` int(11) DEFAULT 0,
  `duracion_min` int(11) DEFAULT NULL,
  `materiales` longtext DEFAULT NULL,
  `lugar` varchar(150) DEFAULT NULL,
  `metodologia` int(11) DEFAULT 0,
  `area_prog` int(11) DEFAULT 0,
  `tiempo_de_trabajo` int(11) DEFAULT 0,
  `objetivo` int(11) DEFAULT 0,
  `introducida_por` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `area` (`area`,`id_alumno`,`dia`,`id_aula`,`tipo_nota`),
  KEY `indices2` (`para_id_aula`,`para_id_centro`,`hora`),
  KEY `indices3` (`realizada`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `agenda_alumnos_seguimiento_sp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `area` int(11) DEFAULT 0,
  `id_alumno` int(11) DEFAULT NULL,
  `dia` date DEFAULT NULL,
  `id_aula` int(11) DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `nota` text DEFAULT NULL,
  `tipo_nota` int(11) DEFAULT 0,
  `avisar` tinyint(4) DEFAULT 0,
  `realizada` tinyint(4) DEFAULT 0,
  `para_id_aula` int(11) DEFAULT 0,
  `para_id_centro` int(11) DEFAULT 0,
  `duracion_min` int(11) DEFAULT NULL,
  `materiales` longtext DEFAULT NULL,
  `lugar` varchar(150) DEFAULT NULL,
  `metodologia` int(11) DEFAULT 0,
  `area_prog` int(11) DEFAULT 0,
  `tiempo_de_trabajo` int(11) DEFAULT 0,
  `objetivo` int(11) DEFAULT 0,
  `introducida_por` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `agenda_alumnos_tipos_notas` (
  `id_tipo` int(11) NOT NULL AUTO_INCREMENT,
  `area` int(11) DEFAULT 0,
  `nombre_tipo` varchar(150) DEFAULT NULL,
  `mostrar_en` tinyint(4) DEFAULT 0,
  `inicio_agenda` tinyint(4) DEFAULT 0,
  `orden` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_tipo`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `agenda_alumnos_tipos_notas_sp` (
  `id_tipo` int(11) NOT NULL AUTO_INCREMENT,
  `area` int(11) DEFAULT 0,
  `nombre_tipo` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id_tipo`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `agenda_asistentes` (
  `id_asistencia` int(11) NOT NULL AUTO_INCREMENT,
  `id_cita` int(11) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `nivel` int(11) DEFAULT NULL,
  `confirmado` int(11) DEFAULT 0,
  `enviado` tinyint(4) DEFAULT 0,
  PRIMARY KEY (`id_asistencia`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `agenda_documentos_padres` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nombre_documento` text DEFAULT NULL,
  `fecha_lectura` date DEFAULT NULL,
  `hora_lectura` time DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `agenda_evaluacion_ingles_items` (
  `id_evaluacion` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(255) DEFAULT NULL,
  `descripcion_esp` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_evaluacion`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `agenda_evaluacion_items` (
  `id_evaluacion` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(255) DEFAULT NULL,
  `trimestre` int(11) DEFAULT 0,
  `editorial` int(11) DEFAULT 0,
  PRIMARY KEY (`id_evaluacion`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `agenda_evaluacion_items_menta` (
  `id_evaluacion` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(255) DEFAULT NULL,
  `trimestre` int(11) DEFAULT 0,
  `editorial` int(11) DEFAULT 0,
  PRIMARY KEY (`id_evaluacion`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `agenda_notas_padres` (
  `id_nota_padre` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_padre` int(11) DEFAULT NULL,
  `id_alumno` int(11) DEFAULT NULL,
  `id_remitente` int(11) DEFAULT NULL,
  `tipo_nota` int(11) DEFAULT 0,
  `asunto` varchar(255) DEFAULT NULL,
  `texto_nota` text DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `fecha_medicacion` date DEFAULT NULL,
  `hora_medicacion` time DEFAULT NULL,
  `periodicidad_medicacion` double DEFAULT 0,
  `fecha_fin_medicacion` date DEFAULT NULL,
  `medicamento` varchar(255) DEFAULT NULL,
  `cantidad` varchar(50) DEFAULT NULL,
  `estado` int(11) DEFAULT 0,
  `destino` tinyint(4) DEFAULT 0,
  `medicacion_agenda` tinyint(4) DEFAULT 0,
  `leido_por` text NOT NULL,
  `id_centro_alumno` int(11) DEFAULT 0,
  `notificado_app` tinyint(4) DEFAULT 0,
  PRIMARY KEY (`id_nota_padre`),
  KEY `indice1` (`id_padre`,`id_alumno`,`fecha`),
  KEY `indice2` (`estado`,`destino`),
  KEY `idx_modern_id_alumno_fecha` (`id_alumno`,`fecha`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `agenda_notas_padres_respuestas` (
  `id_respuesta` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_nota` int(11) DEFAULT NULL,
  `respondido_por` int(11) DEFAULT NULL,
  `fecha_respuesta` date DEFAULT NULL,
  `hora_respuesta` time DEFAULT NULL,
  `texto_respuesta` text DEFAULT NULL,
  `leida` tinyint(4) DEFAULT 0,
  `direccion` tinyint(4) DEFAULT 0,
  `leido_por` text NOT NULL,
  `notificado_app` tinyint(4) DEFAULT 0,
  PRIMARY KEY (`id_respuesta`),
  KEY `indices` (`respondido_por`,`leida`,`direccion`),
  KEY `idx_modern_id_nota` (`id_nota`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `agenda_programacion` (
  `id_prog` int(11) NOT NULL AUTO_INCREMENT,
  `id_aula` int(11) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `tiempo_trabajo` int(11) DEFAULT 1,
  `area` int(11) DEFAULT 0,
  `id_actividad` int(11) DEFAULT 0,
  `actividad` longtext DEFAULT NULL,
  `metodologia` int(11) DEFAULT 0,
  `tiempo` int(11) DEFAULT 0,
  `materiales` longtext DEFAULT NULL,
  `lugar` varchar(100) DEFAULT '0',
  `evaluacion` longtext DEFAULT NULL,
  `orden_actividad` int(11) DEFAULT 1,
  `objetivo` int(11) DEFAULT 0,
  `edad` int(11) DEFAULT 0,
  `objetivos` varchar(255) DEFAULT NULL,
  `turno` tinyint(4) DEFAULT 0,
  `grupo` int(11) DEFAULT 0,
  `realizada` int(11) DEFAULT 1,
  PRIMARY KEY (`id_prog`),
  KEY `id_aula` (`id_aula`,`fecha`,`tiempo_trabajo`,`edad`,`objetivo`,`objetivos`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `agenda_programacion_actividades` (
  `id_actividad` int(11) NOT NULL AUTO_INCREMENT,
  `id_objetivo` int(11) DEFAULT 0,
  `tipo_actividad` int(11) DEFAULT NULL,
  `descripcion_actividad` longtext DEFAULT NULL,
  `edad` int(11) DEFAULT NULL,
  `area` int(11) DEFAULT NULL,
  `unidad` int(11) DEFAULT 0,
  `editorial` int(11) DEFAULT 0,
  PRIMARY KEY (`id_actividad`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `agenda_programacion_actividades_menta` (
  `id_actividad` int(11) NOT NULL AUTO_INCREMENT,
  `id_objetivo` int(11) DEFAULT 0,
  `tipo_actividad` int(11) DEFAULT NULL,
  `descripcion_actividad` longtext DEFAULT NULL,
  `edad` int(11) DEFAULT NULL,
  `area` int(11) DEFAULT NULL,
  `unidad` int(11) DEFAULT 0,
  `editorial` int(11) DEFAULT 0,
  PRIMARY KEY (`id_actividad`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `agenda_programacion_actividades_old` (
  `id_actividad` int(11) NOT NULL AUTO_INCREMENT,
  `id_objetivo` int(11) DEFAULT 0,
  `tipo_actividad` int(11) DEFAULT NULL,
  `descripcion_actividad` longtext DEFAULT NULL,
  `edad` int(11) DEFAULT NULL,
  `area` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_actividad`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `agenda_programacion_evaluacion_alumnos` (
  `id_eval` int(11) NOT NULL AUTO_INCREMENT,
  `id_prog` int(11) DEFAULT 0,
  `id_alumno` int(11) DEFAULT 0,
  `asistencia` tinyint(4) DEFAULT 0,
  `superada` tinyint(4) DEFAULT 0,
  `observaciones` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_eval`),
  KEY `id_alumno` (`id_alumno`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `agenda_programacion_evaluaciones_comedor_estado_alumnos` (
  `id_estado` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_curso` int(11) DEFAULT NULL,
  `id_alumno` int(11) DEFAULT NULL,
  `trimestre` int(11) DEFAULT NULL,
  `periodo` int(11) DEFAULT NULL,
  `estado` int(11) DEFAULT 0,
  `observaciones` text DEFAULT NULL,
  `fecha_evaluacion` timestamp NULL DEFAULT current_timestamp(),
  `observaciones1` text DEFAULT NULL,
  `observaciones2` text DEFAULT NULL,
  PRIMARY KEY (`id_estado`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `agenda_programacion_evaluaciones_comedor_trimestrales` (
  `id_eval` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_curso` int(11) DEFAULT NULL,
  `id_aula` int(11) DEFAULT NULL,
  `trimestre` int(11) DEFAULT NULL,
  `periodo` int(11) DEFAULT NULL,
  `id_alumno` int(11) DEFAULT NULL,
  `id_evaluacion` int(11) DEFAULT NULL,
  `id_objetivo` int(11) DEFAULT NULL,
  `nota` int(11) DEFAULT NULL,
  `fecha_evaluacion` timestamp NULL DEFAULT current_timestamp(),
  `fecha_modificacion` timestamp NULL DEFAULT NULL,
  `modificado_por` varchar(100) DEFAULT '',
  `log` text NOT NULL,
  PRIMARY KEY (`id_eval`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `agenda_programacion_evaluaciones_estado` (
  `id_estado` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_curso` int(11) DEFAULT NULL,
  `id_aula` int(11) DEFAULT NULL,
  `trimestre` int(11) DEFAULT NULL,
  `estado` int(11) DEFAULT 0,
  PRIMARY KEY (`id_estado`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `agenda_programacion_evaluaciones_estado_alumnos` (
  `id_estado` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_curso` int(11) DEFAULT NULL,
  `id_alumno` int(11) DEFAULT NULL,
  `trimestre` int(11) DEFAULT NULL,
  `periodo` int(11) DEFAULT NULL,
  `estado` int(11) DEFAULT 0,
  `observaciones` text DEFAULT NULL,
  `observaciones_ingles` text DEFAULT NULL,
  `fecha_modificacion` timestamp NULL DEFAULT NULL,
  `modificado_por` varchar(150) DEFAULT NULL,
  `log` text NOT NULL,
  PRIMARY KEY (`id_estado`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `agenda_programacion_evaluaciones_trimestrales` (
  `id_eval` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_curso` int(11) DEFAULT NULL,
  `id_aula` int(11) DEFAULT NULL,
  `trimestre` int(11) DEFAULT NULL,
  `id_alumno` int(11) DEFAULT NULL,
  `id_evaluacion` int(11) DEFAULT NULL,
  `nota` int(11) DEFAULT 0,
  `fecha_modificacion` timestamp NULL DEFAULT NULL,
  `modificado_por` varchar(100) DEFAULT NULL,
  `log` text DEFAULT NULL,
  PRIMARY KEY (`id_eval`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `agenda_programacion_ingles` (
  `id_prog` int(11) NOT NULL AUTO_INCREMENT,
  `id_aula` int(11) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `tiempo_trabajo` int(11) DEFAULT 1,
  `area` int(11) DEFAULT 0,
  `id_actividad` int(11) DEFAULT 0,
  `actividad` longtext DEFAULT NULL,
  `metodologia` int(11) DEFAULT 0,
  `tiempo` int(11) DEFAULT 0,
  `materiales` longtext DEFAULT NULL,
  `lugar` varchar(100) DEFAULT '0',
  `evaluacion` longtext DEFAULT NULL,
  `orden_actividad` int(11) DEFAULT 1,
  `objetivo` int(11) DEFAULT 0,
  `edad` int(11) DEFAULT 0,
  `objetivos` varchar(255) DEFAULT NULL,
  `turno` tinyint(4) DEFAULT 0,
  PRIMARY KEY (`id_prog`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `agenda_programacion_ingles_actividades` (
  `id_actividad` int(11) NOT NULL AUTO_INCREMENT,
  `id_objetivo` int(11) DEFAULT 0,
  `tipo_actividad` int(11) DEFAULT NULL,
  `descripcion_actividad` longtext DEFAULT NULL,
  `edad` int(11) DEFAULT NULL,
  `area` int(11) DEFAULT NULL,
  `version` int(11) DEFAULT 1,
  PRIMARY KEY (`id_actividad`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `agenda_programacion_ingles_actividades_copy` (
  `id_actividad` int(11) NOT NULL AUTO_INCREMENT,
  `id_objetivo` int(11) DEFAULT 0,
  `tipo_actividad` int(11) DEFAULT NULL,
  `descripcion_actividad` longtext DEFAULT NULL,
  `edad` int(11) DEFAULT NULL,
  `area` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_actividad`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `agenda_programacion_ingles_calendario` (
  `id_dia` int(11) NOT NULL AUTO_INCREMENT,
  `id_programacion` int(11) DEFAULT NULL,
  `id_aula` int(11) DEFAULT NULL,
  `dia` date DEFAULT NULL,
  `estado` int(11) DEFAULT 0,
  PRIMARY KEY (`id_dia`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `agenda_programacion_ingles_copy` (
  `id_prog` int(11) NOT NULL AUTO_INCREMENT,
  `id_aula` int(11) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `tiempo_trabajo` int(11) DEFAULT 1,
  `area` int(11) DEFAULT 0,
  `id_actividad` int(11) DEFAULT 0,
  `actividad` longtext DEFAULT NULL,
  `metodologia` int(11) DEFAULT 0,
  `tiempo` int(11) DEFAULT 0,
  `materiales` longtext DEFAULT NULL,
  `lugar` varchar(100) DEFAULT '0',
  `evaluacion` longtext DEFAULT NULL,
  `orden_actividad` int(11) DEFAULT 1,
  `objetivo` int(11) DEFAULT 0,
  `edad` int(11) DEFAULT 0,
  `objetivos` varchar(255) DEFAULT NULL,
  `turno` tinyint(4) DEFAULT 0,
  PRIMARY KEY (`id_prog`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `agenda_programacion_ingles_evaluacion_alumnos` (
  `id_eval` int(11) NOT NULL AUTO_INCREMENT,
  `id_prog` int(11) DEFAULT 0,
  `id_alumno` int(11) DEFAULT 0,
  `asistencia` tinyint(4) DEFAULT 0,
  `superada` tinyint(4) DEFAULT 0,
  `observaciones` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_eval`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `agenda_programacion_ingles_evaluaciones_estado` (
  `id_estado` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_curso` int(11) DEFAULT NULL,
  `id_aula` int(11) DEFAULT NULL,
  `trimestre` int(11) DEFAULT NULL,
  `estado` int(11) DEFAULT 0,
  PRIMARY KEY (`id_estado`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `agenda_programacion_ingles_evaluaciones_trimestrales` (
  `id_eval` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_curso` int(11) DEFAULT NULL,
  `id_aula` int(11) DEFAULT NULL,
  `trimestre` int(11) DEFAULT NULL,
  `id_alumno` int(11) DEFAULT NULL,
  `id_evaluacion` int(11) DEFAULT NULL,
  `nota` int(11) DEFAULT 0,
  `fecha_modificacion` timestamp NULL DEFAULT NULL,
  `modificado_por` varchar(100) DEFAULT NULL,
  `log` text DEFAULT NULL,
  PRIMARY KEY (`id_eval`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `agenda_programacion_ingles_objetivos` (
  `id_objetivo` int(11) NOT NULL AUTO_INCREMENT,
  `id_evaluacion` int(11) DEFAULT 0,
  `id_area` int(11) NOT NULL DEFAULT 0,
  `nombre_objetivo` varchar(255) NOT NULL,
  `descripcion_objetivo` text DEFAULT NULL,
  `edad` int(11) DEFAULT 0,
  `mes` int(11) DEFAULT 0,
  `porcentaje_autoevaluacion` double DEFAULT 50,
  `nivel` int(11) DEFAULT 1,
  `version` int(11) DEFAULT 1,
  PRIMARY KEY (`id_objetivo`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `agenda_programacion_ingles_objetivos_copy` (
  `id_objetivo` int(11) NOT NULL AUTO_INCREMENT,
  `id_evaluacion` int(11) DEFAULT 0,
  `id_area` int(11) NOT NULL DEFAULT 0,
  `nombre_objetivo` varchar(255) NOT NULL,
  `descripcion_objetivo` text DEFAULT NULL,
  `edad` int(11) DEFAULT 0,
  `mes` int(11) DEFAULT 0,
  `porcentaje_autoevaluacion` double DEFAULT 50,
  `nivel` int(11) DEFAULT 1,
  PRIMARY KEY (`id_objetivo`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `agenda_programacion_ingles_objetivos_recuperados` (
  `id_fila` int(11) NOT NULL AUTO_INCREMENT,
  `id_objetivo` int(11) DEFAULT NULL,
  `mes` int(11) DEFAULT NULL,
  `anyo` int(11) DEFAULT NULL,
  `aula` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_fila`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `agenda_programacion_ingles_objetivos_temp` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_objetivo` int(11) DEFAULT NULL,
  `id_evaluacion` int(11) DEFAULT NULL,
  `id_alumno` int(11) DEFAULT NULL,
  `superado` int(11) DEFAULT NULL,
  `id_area` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `agenda_programacion_ingles_tipos_actividades` (
  `id_tipo_actividad` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_tipo_actividad` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id_tipo_actividad`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `agenda_programacion_objetivos` (
  `id_objetivo` int(11) NOT NULL AUTO_INCREMENT,
  `id_evaluacion` int(11) DEFAULT 0,
  `id_area` int(11) NOT NULL DEFAULT 0,
  `nombre_objetivo` varchar(255) NOT NULL,
  `descripcion_objetivo` text DEFAULT NULL,
  `edad` int(11) DEFAULT 0,
  `mes` int(11) DEFAULT 0,
  `porcentaje_autoevaluacion` double DEFAULT 50,
  `nivel` int(11) DEFAULT 1,
  `unidad` int(11) DEFAULT 0,
  `editorial` int(11) DEFAULT 0,
  PRIMARY KEY (`id_objetivo`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `agenda_programacion_objetivos_comedor` (
  `id_objetivo` int(11) NOT NULL AUTO_INCREMENT,
  `id_objetivo_g` int(11) DEFAULT 0,
  `id_evaluacion` int(11) DEFAULT 0,
  `id_area` int(11) NOT NULL DEFAULT 0,
  `nombre_objetivo` varchar(255) NOT NULL,
  `descripcion_objetivo` text DEFAULT NULL,
  `edad` int(11) DEFAULT 0,
  `mes` int(11) DEFAULT 0,
  `porcentaje_autoevaluacion` double DEFAULT 50,
  `nivel` int(11) DEFAULT 1,
  PRIMARY KEY (`id_objetivo`),
  KEY `indice1` (`id_area`,`edad`,`mes`),
  KEY `indice2` (`id_evaluacion`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `agenda_programacion_objetivos_menta` (
  `id_objetivo` int(11) NOT NULL AUTO_INCREMENT,
  `id_evaluacion` int(11) DEFAULT 0,
  `id_area` int(11) NOT NULL DEFAULT 0,
  `nombre_objetivo` varchar(255) NOT NULL,
  `descripcion_objetivo` text DEFAULT NULL,
  `edad` int(11) DEFAULT 0,
  `mes` int(11) DEFAULT 0,
  `porcentaje_autoevaluacion` double DEFAULT 50,
  `nivel` int(11) DEFAULT 1,
  `unidad` int(11) DEFAULT 0,
  `editorial` int(11) DEFAULT 0,
  PRIMARY KEY (`id_objetivo`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `agenda_programacion_objetivos_recuperados` (
  `id_fila` int(11) NOT NULL AUTO_INCREMENT,
  `id_objetivo` int(11) DEFAULT NULL,
  `mes` int(11) DEFAULT NULL,
  `anyo` int(11) DEFAULT NULL,
  `aula` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_fila`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `agenda_programacion_objetivos_temp` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_objetivo` int(11) DEFAULT NULL,
  `id_evaluacion` int(11) DEFAULT NULL,
  `id_alumno` int(11) DEFAULT NULL,
  `superado` int(11) DEFAULT NULL,
  `id_area` int(11) DEFAULT NULL,
  `texto` varchar(100) DEFAULT NULL,
  `mes` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `agenda_programacion_plantillas` (
  `id_plantilla` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(255) DEFAULT NULL,
  `nivel` int(11) DEFAULT 0,
  `num_sesiones` int(11) DEFAULT 0,
  `creada_por` int(11) DEFAULT 0,
  `fecha_ultima_mod` date DEFAULT NULL,
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp(),
  `modif_por` int(11) DEFAULT 0,
  `monitor` int(11) DEFAULT 0,
  `periodicidad` int(11) DEFAULT 0,
  `lista_asignaciones` text DEFAULT NULL,
  PRIMARY KEY (`id_plantilla`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `agenda_programacion_plantillas_actividades` (
  `id_prog` int(11) NOT NULL AUTO_INCREMENT,
  `id_plantilla` int(11) DEFAULT 0,
  `numero_sesion` int(11) DEFAULT 0,
  `tiempo_trabajo` int(11) DEFAULT 1,
  `area` int(11) DEFAULT 0,
  `id_actividad` int(11) DEFAULT 0,
  `actividad` longtext DEFAULT NULL,
  `metodologia` int(11) DEFAULT 0,
  `tiempo` int(11) DEFAULT 0,
  `materiales` longtext DEFAULT NULL,
  `lugar` varchar(100) DEFAULT '0',
  `evaluacion` longtext DEFAULT NULL,
  `orden_actividad` int(11) DEFAULT 1,
  `objetivo` int(11) DEFAULT 0,
  `edad` int(11) DEFAULT 0,
  `objetivos` varchar(255) DEFAULT '',
  `turno` tinyint(4) DEFAULT 0,
  `grupo` int(11) DEFAULT 0,
  `realizada` int(11) DEFAULT 1,
  `id_aula` int(11) DEFAULT 0,
  PRIMARY KEY (`id_prog`),
  KEY `id_aula` (`id_plantilla`,`numero_sesion`,`tiempo_trabajo`,`edad`,`objetivo`,`objetivos`),
  KEY `turno` (`turno`,`grupo`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `agenda_programacion_tipos_actividades` (
  `id_tipo_actividad` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_tipo_actividad` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id_tipo_actividad`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `agenda_registro_lecturas` (
  `id_registro` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `dia_informe` date DEFAULT NULL,
  `dia` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `id_alumno` int(11) DEFAULT 0,
  `id_padre` int(11) DEFAULT 0,
  `id_usuario` int(11) DEFAULT 0,
  `REMOTE_ADDR` varchar(20) DEFAULT NULL,
  `HTTP_ACCEPT_LANGUAGE` varchar(50) DEFAULT NULL,
  `HTTP_User_Agent` varchar(100) DEFAULT NULL,
  `sesion` varchar(75) DEFAULT NULL,
  PRIMARY KEY (`id_registro`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `agenda_temp` (
  `id_alumno` int(11) DEFAULT 0,
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `texto` text DEFAULT NULL,
  `encabezado` tinyint(4) DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `albaranes_entrega` (
  `id_albaran` int(11) NOT NULL AUTO_INCREMENT,
  `id_pedido` int(11) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `id_aula` int(11) DEFAULT NULL,
  `fecha_entrega` date DEFAULT NULL,
  `observaciones` longtext DEFAULT NULL,
  PRIMARY KEY (`id_albaran`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `albaranes_proveedor` (
  `id_pedido` int(11) NOT NULL AUTO_INCREMENT,
  `id_proveedor` int(11) DEFAULT NULL,
  `fecha_albaran` date NOT NULL DEFAULT '0000-00-00',
  `fecha_entrega` date DEFAULT NULL,
  PRIMARY KEY (`id_pedido`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `almacenes` (
  `id_almacen` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_almacen` varchar(255) DEFAULT NULL,
  `descripcion` longtext DEFAULT NULL,
  `id_centro` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_almacen`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `alumnos` (
  `id_alumno` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) DEFAULT NULL,
  `id_curso` int(11) NOT NULL DEFAULT 0,
  `fecha_inscripcion` date NOT NULL DEFAULT '0000-00-00',
  `fecha_matricula` date DEFAULT '0000-00-00',
  `fecha_baja` date NOT NULL DEFAULT '0000-00-00',
  `motivo_baja` varchar(255) DEFAULT NULL,
  `aula` int(11) DEFAULT NULL,
  `orden_en_aula` int(11) DEFAULT 0,
  `sustituye_a` int(11) DEFAULT 0,
  `id_centro` int(11) DEFAULT NULL,
  `tipo_mensualidad` int(11) DEFAULT NULL,
  `hora_entrada1` time NOT NULL DEFAULT '00:00:00',
  `hora_salida1` time DEFAULT NULL,
  `hora_entrada2` time DEFAULT NULL,
  `hora_salida2` time DEFAULT NULL,
  `comedor` tinyint(4) DEFAULT 0,
  `tipo_menu_comedor` int(11) NOT NULL DEFAULT 0,
  `numero_tomas` int(11) NOT NULL DEFAULT 1,
  `banco_entidad` varchar(255) DEFAULT NULL,
  `banco_poblacion` varchar(255) DEFAULT NULL,
  `banco_titular` varchar(255) DEFAULT NULL,
  `banco_dni_titular` varchar(255) NOT NULL DEFAULT '',
  `banco_cuenta` varchar(255) NOT NULL DEFAULT '',
  `banco_iban` varchar(4) DEFAULT NULL,
  `banco_domicilio` varchar(255) DEFAULT NULL,
  `proceso_decision` int(11) DEFAULT 1,
  `forma_de_pago` int(11) DEFAULT 0,
  `menu_actual` int(11) DEFAULT 0,
  `aula_ayudas` int(11) DEFAULT 0,
  `comedor_especial` tinyint(4) DEFAULT 0,
  `comedor_especial_precio` double DEFAULT 0,
  `agenda_grupo` int(11) DEFAULT 0,
  `no_merienda` tinyint(4) DEFAULT 0,
  `nivel_actual` float DEFAULT 0,
  `num_dias_asiste` int(11) DEFAULT 1,
  `nivel_proximo` int(11) DEFAULT 0,
  `nivel_maximo_superado` int(11) DEFAULT 0,
  `nivel_maximo_trabajado` int(11) DEFAULT 0,
  `agenda_2_grupo` int(11) DEFAULT 0,
  `agenda_3_grupo` int(11) DEFAULT 0,
  `swiming` tinyint(4) DEFAULT 0,
  `nivel_proximo1` int(11) DEFAULT NULL,
  `codigo_inscripcion` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id_alumno`),
  KEY `idx_alumno` (`id_usuario`,`id_curso`,`aula`,`id_centro`,`tipo_mensualidad`,`fecha_inscripcion`,`fecha_matricula`,`fecha_baja`,`comedor`,`tipo_menu_comedor`,`menu_actual`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `alumnos_documentacion_presentada` (
  `id_registro` int(11) NOT NULL AUTO_INCREMENT,
  `id_doc` int(11) DEFAULT 0,
  `id_alumno` int(11) DEFAULT 0,
  `fecha_entrega` date DEFAULT NULL,
  `recoge` int(11) DEFAULT 0,
  PRIMARY KEY (`id_registro`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `alumnos_documentacion_requerida` (
  `id_tipo_doc` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_tipo_doc` varchar(50) DEFAULT NULL,
  `requerido` tinyint(4) DEFAULT 0,
  `nombre_plantilla` varchar(150) DEFAULT NULL,
  `nombre_doc` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id_tipo_doc`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `alumnos_presupuestos` (
  `id_presupuesto` int(11) NOT NULL AUTO_INCREMENT,
  `id_curso` int(11) DEFAULT NULL,
  `id_contacto` int(11) DEFAULT 0,
  `id_aula` int(11) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `fecha_ini` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `observaciones` longtext DEFAULT NULL,
  PRIMARY KEY (`id_presupuesto`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `alumnos_presupuestos_detalles` (
  `id_detalle` int(11) NOT NULL AUTO_INCREMENT,
  `id_presupuesto` int(11) DEFAULT 0,
  `id_servicio` int(11) DEFAULT 0,
  `cantidad_servicio` double DEFAULT 0,
  PRIMARY KEY (`id_detalle`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `asistencia_docentes` (
  `id_asistencia` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) DEFAULT NULL,
  `turno` int(11) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `hora_entrada` time DEFAULT NULL,
  `hora_salida` time DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `incidencia` tinyint(4) DEFAULT 0,
  `resuelta` tinyint(4) DEFAULT 0,
  `medidas_adoptadas` longtext NOT NULL,
  `comedor` tinyint(11) NOT NULL DEFAULT 0,
  `menu_seleccionado_hoy` int(11) DEFAULT 0,
  `id_centro_asistencia` int(11) DEFAULT 0,
  `sesion_e` varchar(40) DEFAULT NULL,
  `sesion_s` varchar(40) DEFAULT NULL,
  `info_e` text DEFAULT NULL,
  `info_s` text DEFAULT NULL,
  PRIMARY KEY (`id_asistencia`),
  KEY `idx_docente` (`id_usuario`,`turno`,`fecha`,`comedor`,`menu_seleccionado_hoy`,`id_centro_asistencia`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `bancos_recibos` (
  `id_banco` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_entidad` varchar(40) DEFAULT NULL,
  `nif_presentador` varchar(9) DEFAULT NULL,
  `nombre_presentador` varchar(40) DEFAULT NULL,
  `entidad_receptora_presentador` varchar(4) DEFAULT NULL,
  `oficina_receptora_presentador` varchar(4) DEFAULT NULL,
  `nif_ordenante` varchar(9) DEFAULT NULL,
  `nombre_ordenante` varchar(40) DEFAULT NULL,
  `entidad_receptora_ordenante` varchar(4) DEFAULT NULL,
  `oficina_receptora_ordenante` varchar(4) DEFAULT NULL,
  `dc_receptora_ordenante` varchar(2) DEFAULT NULL,
  `cuenta_receptora_ordenante` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id_banco`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `bic_swift` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cod_bancario` varchar(4) DEFAULT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `BIC` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `calendario_laboral` (
  `id_dia` int(11) NOT NULL AUTO_INCREMENT,
  `id_curso` int(11) DEFAULT NULL,
  `id_centro` int(11) DEFAULT NULL,
  `dia` date DEFAULT NULL,
  `estado` int(11) DEFAULT 0,
  PRIMARY KEY (`id_dia`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `centro_aulas` (
  `id_aula` int(11) NOT NULL AUTO_INCREMENT,
  `id_centro` int(11) DEFAULT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `capacidad` int(11) DEFAULT NULL,
  `ocupacion_minima` int(11) DEFAULT NULL,
  `descripcion` longtext DEFAULT NULL,
  `equipamiento` longtext DEFAULT NULL,
  `edad_minima` int(11) DEFAULT NULL,
  `edad_maxima` int(11) DEFAULT NULL,
  `id_curso` int(11) DEFAULT NULL,
  `tipo` int(11) NOT NULL DEFAULT 0,
  `docente_titular` int(11) DEFAULT NULL,
  `id_menu_predefinido` int(11) NOT NULL DEFAULT 0,
  `mes_minimo` int(11) DEFAULT NULL,
  `anio_minimo` int(11) DEFAULT NULL,
  `mes_maximo` int(11) DEFAULT NULL,
  `anio_maximo` int(11) DEFAULT NULL,
  `ciclo` int(11) DEFAULT 0,
  `edad_minima_prog` int(11) DEFAULT 0,
  `edad_maxima_prog` int(11) DEFAULT 0,
  `id_aula_pedidos` int(11) DEFAULT 0,
  `evaluacion_por_edades` tinyint(4) DEFAULT 0,
  `evaluacion_por_grupos` tinyint(4) DEFAULT 0,
  `visibilidad` tinyint(4) DEFAULT 1,
  PRIMARY KEY (`id_aula`),
  KEY `idx_aulas` (`id_centro`,`nombre`,`id_curso`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `centros` (
  `id_centro` int(11) NOT NULL AUTO_INCREMENT,
  `centro_propio` int(11) DEFAULT 1,
  `nombre_centro` varchar(255) DEFAULT NULL,
  `prefijo_centro` varchar(10) NOT NULL DEFAULT '',
  `CIF_centro` varchar(255) NOT NULL DEFAULT '',
  `CP_centro` varchar(5) DEFAULT NULL,
  `domicilio_centro` varchar(255) DEFAULT NULL,
  `poblacion_centro` varchar(100) DEFAULT NULL,
  `telefono_centro` varchar(100) DEFAULT NULL,
  `fax_centro` varchar(100) DEFAULT NULL,
  `link_centro` varchar(255) DEFAULT NULL,
  `razon_social` varchar(255) DEFAULT NULL,
  `precio_hora_extra` double NOT NULL DEFAULT 0,
  `precio_menu_1` double NOT NULL DEFAULT 0,
  `precio_menu_2` double NOT NULL DEFAULT 0,
  `precio_menu_3` double NOT NULL DEFAULT 0,
  `precio_menu_4` double NOT NULL DEFAULT 0,
  `precio_menu_5` double NOT NULL DEFAULT 0,
  `precio_semana_1` double NOT NULL DEFAULT 0,
  `precio_semana_2` double NOT NULL DEFAULT 0,
  `precio_semana_3` double NOT NULL DEFAULT 0,
  `precio_semana_4` double NOT NULL DEFAULT 0,
  `precio_semana_5` double NOT NULL DEFAULT 0,
  `precio_quincena_1` double NOT NULL DEFAULT 0,
  `precio_quincena_2` double NOT NULL DEFAULT 0,
  `precio_quincena_3` double NOT NULL DEFAULT 0,
  `precio_quincena_4` double NOT NULL DEFAULT 0,
  `precio_quincena_5` double NOT NULL DEFAULT 0,
  `precio_mes_1` double NOT NULL DEFAULT 0,
  `precio_mes_2` double NOT NULL DEFAULT 0,
  `precio_mes_3` double NOT NULL DEFAULT 0,
  `precio_mes_4` double NOT NULL DEFAULT 0,
  `precio_mes_5` double NOT NULL DEFAULT 0,
  `division_horas` int(11) NOT NULL DEFAULT 30,
  `periodo_gracia` int(11) NOT NULL DEFAULT 15,
  `hora_entrada1` time DEFAULT NULL,
  `hora_salida1` time DEFAULT NULL,
  `hora_entrada2` time DEFAULT NULL,
  `hora_salida2` time DEFAULT NULL,
  `hora_entrada1_mj` time NOT NULL DEFAULT '00:00:00',
  `hora_salida1_mj` time NOT NULL DEFAULT '00:00:00',
  `hora_entrada2_mj` time NOT NULL DEFAULT '00:00:00',
  `hora_salida2_mj` time NOT NULL DEFAULT '00:00:00',
  `tope_maximo2` double DEFAULT 0,
  `subvencion2` double DEFAULT 0,
  `tope_mes_01` double DEFAULT 0,
  `tope_mes_12` double DEFAULT 0,
  `tope_mes_23` double DEFAULT 0,
  `titular` varchar(150) NOT NULL,
  `codigo_centro` varchar(40) NOT NULL,
  `nombre_completo` varchar(150) NOT NULL,
  `alumnos_ciclo2` int(11) DEFAULT 0,
  `lat` double DEFAULT NULL,
  `lon` double DEFAULT NULL,
  `ip_fija` tinyint(4) DEFAULT 0,
  `piscina` tinyint(4) DEFAULT 1,
  `lista_compra` int(11) DEFAULT 1,
  PRIMARY KEY (`id_centro`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `centros_cursos` (
  `id_curso_centro` int(11) NOT NULL AUTO_INCREMENT,
  `id_centro` int(11) DEFAULT NULL,
  `id_curso` int(11) DEFAULT NULL,
  `tope_maximo2` double DEFAULT 0,
  `subvencion2` double DEFAULT 0,
  `tope_mes_01` double DEFAULT 0,
  `tope_mes_12` double DEFAULT 0,
  `tope_mes_23` double DEFAULT 0,
  `titular` varchar(150) DEFAULT NULL,
  `codigo_centro` varchar(40) DEFAULT NULL,
  `nombre_completo` varchar(150) DEFAULT NULL,
  `alumnos_ciclo2` int(11) DEFAULT 0,
  `precio_coste_01` double DEFAULT 0,
  `precio_coste_12` double DEFAULT 0,
  `precio_coste_23` double DEFAULT 0,
  PRIMARY KEY (`id_curso_centro`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `ciclos_formativos` (
  `id_ciclo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_ciclo` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id_ciclo`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `cierres_caja` (
  `id_cierre` int(11) NOT NULL AUTO_INCREMENT,
  `id_centro` int(11) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `ultimo_movimiento` int(11) DEFAULT NULL,
  `importe_al_cierre` double NOT NULL DEFAULT 0,
  `importe_al_cierre_real` double NOT NULL DEFAULT 0,
  `cerrado_por` int(11) DEFAULT NULL,
  `comentario` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_cierre`),
  KEY `id_centro` (`id_centro`,`fecha`,`ultimo_movimiento`,`hora`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `config_facturacion` (
  `id_config` int(11) NOT NULL AUTO_INCREMENT,
  `primera_factura` int(11) DEFAULT 1,
  `ejercicio` varchar(4) DEFAULT NULL,
  `hora_entrada1` time DEFAULT NULL,
  `hora_salida1` time DEFAULT NULL,
  `hora_entrada2` time DEFAULT NULL,
  `hora_salida2` time DEFAULT NULL,
  PRIMARY KEY (`id_config`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `consultas_padres` (
  `id_consulta` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) DEFAULT NULL,
  `texto_presentacion` text DEFAULT NULL,
  `id_centro` int(11) DEFAULT 0,
  `id_curso` int(11) DEFAULT 0,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `numero_respuestas_posibles` int(11) DEFAULT 3,
  `texto_pregunta` varchar(255) DEFAULT NULL,
  `texto_pregunta_horario` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_consulta`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_uca1400_ai_ci;

CREATE TABLE `consultas_padres_respuestas` (
  `id_respuesta` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_consulta` int(11) DEFAULT 0,
  `id_alumno` int(11) DEFAULT 0,
  `id_usuario` int(11) DEFAULT 0,
  `asistira` tinyint(4) DEFAULT 0,
  `horario_deseado` varchar(255) DEFAULT NULL,
  `fecha_registro` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_respuesta`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_uca1400_ai_ci;

CREATE TABLE `contactos` (
  `id_contacto` int(11) NOT NULL AUTO_INCREMENT,
  `id_centro` int(11) DEFAULT 0,
  `Nombre` varchar(255) NOT NULL DEFAULT '',
  `fecha_contacto` date DEFAULT NULL,
  `proceso_decision` char(2) DEFAULT 'b1',
  `persona_contacto` varchar(255) DEFAULT NULL,
  `comentarios` longtext DEFAULT NULL,
  `telefono` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `Apellido1` varchar(255) NOT NULL DEFAULT '',
  `Apellido2` varchar(255) NOT NULL DEFAULT '',
  `calle` varchar(250) DEFAULT NULL,
  `codigo_postal` varchar(10) DEFAULT NULL,
  `poblacion` varchar(255) DEFAULT NULL,
  `coordenadas` varchar(50) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `sexo` varchar(50) DEFAULT NULL,
  `fecha_informacion` date DEFAULT NULL,
  `fecha_b1` date DEFAULT NULL,
  `fecha_b2` date DEFAULT NULL,
  `fecha_b3` date DEFAULT NULL,
  `fecha_c` date DEFAULT NULL,
  `id_curso` int(11) DEFAULT 0,
  `nuevo_alumno` tinyint(4) DEFAULT 0,
  `id_usuario` int(11) DEFAULT 0,
  `nonato` tinyint(4) DEFAULT 0,
  `prematuro` tinyint(4) DEFAULT 0,
  `prematuro1` tinyint(4) DEFAULT 0,
  `como_conocio` tinyint(4) DEFAULT 0,
  `motivo_cancelacion` int(11) DEFAULT 0,
  `SIP` varchar(30) DEFAULT '',
  PRIMARY KEY (`id_contacto`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `contactos_motivos_cancelacion` (
  `id_motivo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_motivo` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id_motivo`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `contactos_seguimiento` (
  `id_nota` int(11) NOT NULL AUTO_INCREMENT,
  `fecha` date DEFAULT NULL,
  `comentarios` longtext NOT NULL,
  `id_contacto` int(11) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `fecha_modificacion` date DEFAULT NULL,
  PRIMARY KEY (`id_nota`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `contactos_tipo` (
  `nombre_tipo` varchar(100) DEFAULT NULL,
  `id_tipo` int(11) DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `contactos_web` (
  `id_contacto` int(11) NOT NULL AUTO_INCREMENT,
  `como_conocio_texto` varchar(255) DEFAULT NULL,
  `id_centro` int(11) DEFAULT 0,
  `id_curso` int(11) DEFAULT 0,
  `fecha_contacto` timestamp NULL DEFAULT NULL,
  `proceso_decision` char(2) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT 'b1',
  `id_usuario_alumno` int(11) DEFAULT 0,
  `Nombre` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `Apellido1` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `Apellido2` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `sexo` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `SIP` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `comentarios` longtext CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `id_usuario_padre1` int(11) DEFAULT 0,
  `nombre_c1` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `apellido1_c1` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `apellido2_c1` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `telefono_padre1` varchar(100) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `telefono_t1` varchar(100) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `email_padre1` varchar(100) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `dni_padre1` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `parentesco_padre1` int(11) DEFAULT 0,
  `id_usuario_padre2` int(11) DEFAULT 0,
  `nombre_c2` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `apellido1_c2` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `apellido2_c2` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `telefono_padre2` varchar(100) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `telefono_t2` varchar(100) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `email_padre2` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `dni_padre2` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `parentesco_padre2` int(11) DEFAULT 0,
  `calle` varchar(250) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `codigo_postal` varchar(10) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `poblacion` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `coordenadas` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `fecha_informacion` date DEFAULT NULL,
  `fecha_b1` date DEFAULT NULL,
  `fecha_b2` date DEFAULT NULL,
  `fecha_b3` date DEFAULT NULL,
  `fecha_c` date DEFAULT NULL,
  `nuevo_alumno` tinyint(4) DEFAULT 0,
  `nonato` tinyint(4) DEFAULT 0,
  `prematuro` tinyint(4) DEFAULT 0,
  `prematuro1` tinyint(4) DEFAULT 0,
  `como_conocio` tinyint(4) DEFAULT 0,
  `motivo_cancelacion` int(11) DEFAULT 0,
  `no_publicidad` tinyint(4) DEFAULT 0,
  `origen` int(11) DEFAULT 0,
  `observaciones_informacion` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT '',
  `horario` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `mensualidad` varchar(25) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `desayuno` varchar(2) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `almuerzo` varchar(2) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `comida` varchar(2) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `merienda` varchar(2) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `horas_extra` varchar(2) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `numero_horas_extra` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `horario_horas_extra` varchar(100) DEFAULT NULL,
  `consentimiento_publicidad` tinyint(4) DEFAULT 0,
  `consentimiento_web` tinyint(4) DEFAULT 0,
  `consentimiento_blog` tinyint(4) DEFAULT 0,
  `consentimiento_redes` tinyint(4) DEFAULT 0,
  `informado_tratamiento_datos` tinyint(4) DEFAULT 0,
  `ev_semanas` text DEFAULT NULL,
  `forma_de_pago` tinyint(4) DEFAULT NULL,
  `IBAN` varchar(100) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `alergias_lacteos` tinyint(4) NOT NULL DEFAULT 0,
  `alergias_huevo` tinyint(4) NOT NULL DEFAULT 0,
  `alergias_proternera` tinyint(4) NOT NULL DEFAULT 0,
  `alergias_frutossecos` tinyint(4) NOT NULL DEFAULT 0,
  `alergias_marisco` tinyint(4) NOT NULL DEFAULT 0,
  `alergias_pielmelocoton` tinyint(4) NOT NULL DEFAULT 0,
  `alergias_gluten` tinyint(4) NOT NULL DEFAULT 0,
  `alergias_pescado` tinyint(4) NOT NULL DEFAULT 0,
  `alergias_soja` tinyint(4) DEFAULT 0,
  `alergias_sulfitos` tinyint(4) DEFAULT 0,
  `alergias_cerdo` tinyint(4) DEFAULT 0,
  `alergias_cacahuetes` tinyint(4) DEFAULT 0,
  `alergias_altramuces` tinyint(4) DEFAULT 0,
  `alergias_apio` tinyint(4) DEFAULT 0,
  `alergias_mostaza` tinyint(4) DEFAULT 0,
  `alergias_sesamo` tinyint(4) DEFAULT 0,
  `alergias_crustaceos` tinyint(4) DEFAULT 0,
  `alergias_moluscos` tinyint(4) DEFAULT 0,
  `alergias_berenjena` tinyint(4) DEFAULT 0,
  PRIMARY KEY (`id_contacto`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `contactos_web_sp` (
  `id_contacto` int(11) NOT NULL AUTO_INCREMENT,
  `id_centro` int(11) DEFAULT 0,
  `id_curso` int(11) DEFAULT 0,
  `fecha_contacto` timestamp NULL DEFAULT NULL,
  `proceso_decision` char(2) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT 'b1',
  `id_usuario_alumno` int(11) DEFAULT 0,
  `Nombre` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `Apellido1` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `Apellido2` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `sexo` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `SIP` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `comentarios` longtext CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `id_usuario_padre1` int(11) DEFAULT 0,
  `nombre_c1` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `apellido1_c1` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `apellido2_c1` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `telefono_padre1` varchar(100) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `telefono_t1` varchar(100) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `email_padre1` varchar(100) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `dni_padre1` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `parentesco_padre1` int(11) DEFAULT 0,
  `id_usuario_padre2` int(11) DEFAULT 0,
  `nombre_c2` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `apellido1_c2` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `apellido2_c2` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `telefono_padre2` varchar(100) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `telefono_t2` varchar(100) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `email_padre2` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `dni_padre2` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `parentesco_padre2` int(11) DEFAULT 0,
  `calle` varchar(250) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `codigo_postal` varchar(10) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `poblacion` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `coordenadas` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `fecha_informacion` date DEFAULT NULL,
  `fecha_b1` date DEFAULT NULL,
  `fecha_b2` date DEFAULT NULL,
  `fecha_b3` date DEFAULT NULL,
  `fecha_c` date DEFAULT NULL,
  `nuevo_alumno` tinyint(4) DEFAULT 0,
  `nonato` tinyint(4) DEFAULT 0,
  `prematuro` tinyint(4) DEFAULT 0,
  `prematuro1` tinyint(4) DEFAULT 0,
  `como_conocio` tinyint(4) DEFAULT 0,
  `motivo_cancelacion` int(11) DEFAULT 0,
  `no_publicidad` tinyint(4) DEFAULT 0,
  `origen` int(11) DEFAULT 0,
  `observaciones_informacion` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT '',
  `horario` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `mensualidad` varchar(25) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `desayuno` varchar(2) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `almuerzo` varchar(2) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `comida` varchar(2) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `merienda` varchar(2) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `horas_extra` varchar(2) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `numero_horas_extra` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `consentimiento_publicidad` tinyint(4) DEFAULT 0,
  `consentimiento_web` tinyint(4) DEFAULT 0,
  `consentimiento_blog` tinyint(4) DEFAULT 0,
  `consentimiento_redes` tinyint(4) DEFAULT 0,
  `informado_tratamiento_datos` tinyint(4) DEFAULT 0,
  `ev_semanas` text DEFAULT NULL,
  PRIMARY KEY (`id_contacto`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `control_acceso_padres` (
  `id_acceso` int(11) NOT NULL AUTO_INCREMENT,
  `sesion` varchar(100) NOT NULL DEFAULT '',
  `id_padre` int(11) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `remote_host` varchar(150) DEFAULT NULL,
  `HTTP_User_Agent` varchar(150) DEFAULT NULL,
  `HTTP_ACCEPT_LANGUAGE` varchar(150) DEFAULT NULL,
  `logacceso` text DEFAULT NULL,
  PRIMARY KEY (`id_acceso`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `control_acceso_padres_2017-09-27` (
  `id_acceso` int(11) NOT NULL AUTO_INCREMENT,
  `sesion` varchar(100) NOT NULL DEFAULT '',
  `id_padre` int(11) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `remote_host` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id_acceso`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `control_acceso_padres_2024-10-05` (
  `id_acceso` int(11) NOT NULL AUTO_INCREMENT,
  `sesion` varchar(100) NOT NULL DEFAULT '',
  `id_padre` int(11) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `remote_host` varchar(150) DEFAULT NULL,
  `HTTP_User_Agent` varchar(150) DEFAULT NULL,
  `HTTP_ACCEPT_LANGUAGE` varchar(150) DEFAULT NULL,
  `logacceso` text DEFAULT NULL,
  PRIMARY KEY (`id_acceso`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `cuentas_gastos` (
  `id_cuenta` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_cuenta` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id_cuenta`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `cursos_academicos` (
  `id_curso` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) DEFAULT NULL,
  `fecha_inicio` date NOT NULL DEFAULT '0000-00-00',
  `fecha_fin` date NOT NULL DEFAULT '0000-00-00',
  `estado` int(11) DEFAULT 0,
  `comprobar_inscripcion` tinyint(4) DEFAULT 0,
  `comprobar_cuotas` tinyint(4) DEFAULT 0,
  `fecha_limite_presentacion` date NOT NULL,
  `tipo_curso` int(11) DEFAULT 0,
  `dia_fin_pedidos` int(11) DEFAULT 23,
  `dia_tramitacion_pedidos` int(11) DEFAULT 2,
  `tipo_curso_academico` int(11) DEFAULT 1,
  `inicio_trim1_1` date DEFAULT NULL,
  `fin_trim1_1` date DEFAULT NULL,
  `inicio_trim2_1` date DEFAULT NULL,
  `fin_trim2_1` date DEFAULT NULL,
  `inicio_trim3_1` date DEFAULT NULL,
  `fin_trim3_1` date DEFAULT NULL,
  `inicio_trim1_2` date DEFAULT NULL,
  `fin_trim1_2` date DEFAULT NULL,
  `inicio_trim2_2` date DEFAULT NULL,
  `fin_trim2_2` date DEFAULT NULL,
  `inicio_trim3_2` date DEFAULT NULL,
  `fin_trim3_2` date DEFAULT NULL,
  PRIMARY KEY (`id_curso`),
  KEY `id_fechas` (`fecha_inicio`,`fecha_fin`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `cursos_escuela_verano_semanas` (
  `id_semana` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_curso` int(11) DEFAULT 0,
  `orden` int(11) DEFAULT 0,
  `inicio_semana` date DEFAULT NULL,
  `fin_semana` date DEFAULT NULL,
  `mes_completo` tinyint(4) DEFAULT 0,
  PRIMARY KEY (`id_semana`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `despachos_centros` (
  `id_despacho` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_despacho` varchar(60) DEFAULT NULL,
  `id_centro` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_despacho`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `detalles_albaran_entrega` (
  `id_detalle` int(11) NOT NULL AUTO_INCREMENT,
  `id_albaran` int(11) NOT NULL DEFAULT 0,
  `id_detalle_pedido` int(11) DEFAULT NULL,
  `generico_solicitado` varchar(255) DEFAULT NULL,
  `id_material` int(11) DEFAULT NULL,
  `cantidad` double DEFAULT NULL,
  `entregado` tinyint(4) DEFAULT NULL,
  `fecha_entrega` date DEFAULT NULL,
  `observaciones` longtext DEFAULT NULL,
  PRIMARY KEY (`id_detalle`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `detalles_albaran_proveedor` (
  `id_detalle` int(11) NOT NULL AUTO_INCREMENT,
  `id_pedido` int(11) DEFAULT NULL,
  `id_articulo` int(11) DEFAULT NULL,
  `cantidad` double DEFAULT NULL,
  `precio` double DEFAULT NULL,
  `tipo_iva` double DEFAULT NULL,
  PRIMARY KEY (`id_detalle`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `detalles_factura` (
  `id_detalle` int(11) NOT NULL AUTO_INCREMENT,
  `id_factura` int(11) DEFAULT NULL,
  `articulo` int(11) DEFAULT NULL,
  `precio` double DEFAULT NULL,
  `cantidad` double DEFAULT NULL,
  `dto` double NOT NULL DEFAULT 0,
  `tipo_iva` double NOT NULL DEFAULT 0.16,
  `concepto` text DEFAULT NULL,
  `cod_facturacion` varchar(100) DEFAULT NULL,
  `subvencion` tinyint(4) DEFAULT 0,
  `subvencion_cobrada` tinyint(4) DEFAULT 0,
  `concepto_desglosar` int(11) DEFAULT 0,
  PRIMARY KEY (`id_detalle`),
  KEY `id_factura` (`id_factura`,`articulo`,`subvencion`),
  FULLTEXT KEY `idx_texto` (`concepto`,`cod_facturacion`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `detalles_factura_backup_2025-12-23` (
  `id_detalle` int(11) NOT NULL AUTO_INCREMENT,
  `id_factura` int(11) DEFAULT NULL,
  `articulo` int(11) DEFAULT NULL,
  `precio` double DEFAULT NULL,
  `cantidad` double DEFAULT NULL,
  `dto` double NOT NULL DEFAULT 0,
  `tipo_iva` double NOT NULL DEFAULT 0.16,
  `concepto` text DEFAULT NULL,
  `cod_facturacion` varchar(100) DEFAULT NULL,
  `subvencion` tinyint(4) DEFAULT 0,
  `subvencion_cobrada` tinyint(4) DEFAULT 0,
  `concepto_desglosar` int(11) DEFAULT 0,
  PRIMARY KEY (`id_detalle`),
  KEY `id_factura` (`id_factura`,`articulo`,`subvencion`),
  FULLTEXT KEY `idx_texto` (`concepto`,`cod_facturacion`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `detalles_factura_copy` (
  `id_detalle` int(11) NOT NULL AUTO_INCREMENT,
  `id_factura` int(11) DEFAULT NULL,
  `articulo` int(11) DEFAULT NULL,
  `precio` double DEFAULT NULL,
  `cantidad` double DEFAULT NULL,
  `dto` double NOT NULL DEFAULT 0,
  `tipo_iva` double NOT NULL DEFAULT 0.16,
  `concepto` text DEFAULT NULL,
  `cod_facturacion` varchar(100) DEFAULT NULL,
  `subvencion` tinyint(4) DEFAULT 0,
  `subvencion_cobrada` tinyint(4) DEFAULT 0,
  `concepto_desglosar` int(11) DEFAULT 0,
  PRIMARY KEY (`id_detalle`),
  KEY `id_factura` (`id_factura`,`articulo`,`subvencion`),
  FULLTEXT KEY `idx_texto` (`concepto`,`cod_facturacion`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `detalles_pedido_centro` (
  `id_articulo` int(11) NOT NULL AUTO_INCREMENT,
  `id_pedido` int(11) DEFAULT NULL,
  `generico` varchar(255) DEFAULT NULL,
  `id_tarifa` int(11) DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_articulo`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `detalles_pedido_empresa` (
  `id_articulo` int(11) NOT NULL AUTO_INCREMENT,
  `id_pedido_empresa` int(11) DEFAULT NULL,
  `generico` varchar(255) DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `pedido` int(11) DEFAULT 0,
  `id_tarifa` int(11) DEFAULT 0,
  PRIMARY KEY (`id_articulo`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `detalles_pedido_empresa_extendido` (
  `id_extendido` int(11) NOT NULL AUTO_INCREMENT,
  `id_tarifa` int(11) DEFAULT 0,
  `cantidad_pedido` double DEFAULT 0,
  `id_articulo_pedido` int(11) DEFAULT 0,
  `stock_articulo` double DEFAULT 0,
  `cantidad_resultante` double DEFAULT 0,
  PRIMARY KEY (`id_extendido`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `detalles_pedido_extendido` (
  `id_articulo` int(11) NOT NULL AUTO_INCREMENT,
  `id_pedido` int(11) DEFAULT NULL,
  `id_detalle_pedido` int(11) DEFAULT NULL,
  `id_tarifa` int(11) DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_articulo`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `detalles_pedido_material` (
  `id_detalle` int(11) NOT NULL AUTO_INCREMENT,
  `id_pedido` int(11) DEFAULT NULL,
  `id_pedido_centro` int(11) DEFAULT 0,
  `id_pedido_empresa` int(11) DEFAULT 0,
  `generico_solicitado` varchar(255) DEFAULT NULL,
  `id_material` int(11) DEFAULT NULL,
  `cantidad` double DEFAULT NULL,
  `cantidad_original` double DEFAULT NULL,
  `entregado` tinyint(4) DEFAULT NULL,
  `observaciones` longtext DEFAULT NULL,
  `id_albaran` int(11) NOT NULL DEFAULT 0,
  `confirmado` tinyint(4) DEFAULT 0,
  PRIMARY KEY (`id_detalle`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `detalles_pedido_material_backup` (
  `id_detalle` int(11) NOT NULL AUTO_INCREMENT,
  `id_pedido` int(11) DEFAULT NULL,
  `id_pedido_centro` int(11) DEFAULT 0,
  `id_pedido_empresa` int(11) DEFAULT 0,
  `generico_solicitado` varchar(255) DEFAULT NULL,
  `id_material` int(11) DEFAULT NULL,
  `cantidad` double DEFAULT NULL,
  `cantidad_original` double DEFAULT NULL,
  `entregado` tinyint(4) DEFAULT NULL,
  `observaciones` longtext DEFAULT NULL,
  `id_albaran` int(11) NOT NULL DEFAULT 0,
  `confirmado` tinyint(4) DEFAULT 0,
  PRIMARY KEY (`id_detalle`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `detalles_pedido_proveedor` (
  `id_detalle` int(11) NOT NULL AUTO_INCREMENT,
  `id_pedido` int(11) DEFAULT NULL,
  `id_articulo` int(11) DEFAULT NULL,
  `cantidad` double DEFAULT NULL,
  `precio` double DEFAULT NULL,
  `tipo_iva` double DEFAULT NULL,
  `id_origen` int(11) DEFAULT 0,
  PRIMARY KEY (`id_detalle`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `docentes_en_aulas` (
  `id_docente` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) DEFAULT NULL,
  `id_aula` int(11) DEFAULT NULL,
  `observaciones` longtext DEFAULT NULL,
  `funcion` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_docente`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `equivalencias_partidas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `partida` varchar(255) DEFAULT NULL,
  `partida_consell` varchar(255) DEFAULT NULL,
  `otros` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `escuelas_competencia` (
  `id_competencia` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_escuela` varchar(150) DEFAULT NULL,
  `latitud` varchar(50) DEFAULT NULL,
  `longitud` varchar(50) DEFAULT NULL,
  `calle` varchar(250) DEFAULT NULL,
  `poblacion` varchar(150) DEFAULT NULL,
  `cp` varchar(5) DEFAULT NULL,
  `cod_centro` varchar(30) DEFAULT NULL,
  `municipal` tinyint(4) DEFAULT 0,
  `propia` tinyint(4) DEFAULT 0,
  PRIMARY KEY (`id_competencia`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `estadisticas1` (
  `Contador` int(11) NOT NULL AUTO_INCREMENT,
  `Sesion` char(100) DEFAULT NULL,
  `Fecha` date DEFAULT NULL,
  `Hora` time DEFAULT NULL,
  `REMOTE_ADDR` char(50) DEFAULT NULL,
  `HTTP_ACCEPT_LANGUAGE` char(50) DEFAULT NULL,
  `HTTP_User_Agent` char(50) DEFAULT NULL,
  `Pagina_visitada` char(255) DEFAULT NULL,
  `usuario` varchar(100) DEFAULT NULL,
  `info_adicional` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`Contador`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `eventos_tipos` (
  `id_tipo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_evento` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_tipo`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `existencias_aulas` (
  `id_stock` int(11) NOT NULL AUTO_INCREMENT,
  `id_aula` int(11) DEFAULT NULL,
  `nombre_generico` varchar(255) DEFAULT NULL,
  `stock_actual` double DEFAULT NULL,
  `fecha_ultimo_cambio` date DEFAULT NULL,
  PRIMARY KEY (`id_stock`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `facturas_emitidas` (
  `id_factura` int(11) NOT NULL AUTO_INCREMENT,
  `serie` varchar(3) DEFAULT NULL,
  `num_factura` varchar(20) DEFAULT NULL,
  `ejercicio_factura` varchar(4) NOT NULL DEFAULT '',
  `id_cliente` int(11) DEFAULT NULL,
  `fecha_emision` date DEFAULT NULL,
  `OBSERVACIONES` longtext DEFAULT NULL,
  `abono` tinyint(4) DEFAULT NULL,
  `periodo_facturado` varchar(255) DEFAULT NULL,
  `estado` tinyint(4) DEFAULT 0,
  `forma_pago_bis` int(11) NOT NULL DEFAULT 0,
  `subvencion_cobrada` tinyint(4) DEFAULT 0,
  `fecha_orden_cobro` date NOT NULL DEFAULT '0000-00-00',
  `id_asistencia` int(11) DEFAULT 0,
  `total_subvenciones` double DEFAULT 0,
  `total_factura` double NOT NULL DEFAULT 0,
  `filtros_educacion` text DEFAULT NULL,
  `total_subvenciones_cobrado` double DEFAULT 0,
  PRIMARY KEY (`id_factura`),
  KEY `id_factura` (`ejercicio_factura`,`num_factura`,`id_cliente`,`fecha_emision`,`serie`),
  KEY `idx_modern_id_cliente_fecha_emision` (`id_cliente`,`fecha_emision`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `family_access_credentials` (
  `credential_key` char(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `sealed_password` text CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `parent_id` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`credential_key`),
  KEY `family_credential_parent` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `formas_de_pago_proveedores` (
  `id_forma_pago` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `vencimiento1` int(11) DEFAULT 0,
  `vencimiento2` int(11) DEFAULT 0,
  `vencimiento3` int(11) DEFAULT 0,
  PRIMARY KEY (`id_forma_pago`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `gestion_costes` (
  `id_coste` int(11) NOT NULL AUTO_INCREMENT,
  `id_centro` int(11) DEFAULT 0,
  `proveedor` int(11) DEFAULT NULL,
  `num_factura` varchar(255) DEFAULT NULL,
  `fecha_factura` date DEFAULT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `tipo_coste` tinyint(4) DEFAULT NULL,
  `importe` double DEFAULT NULL,
  `forma_pago` tinyint(4) DEFAULT NULL,
  `banco` int(11) DEFAULT NULL,
  `observaciones` longtext DEFAULT NULL,
  `introducido_por` int(11) DEFAULT NULL,
  `fecha_insercion` date DEFAULT NULL,
  `finalizado` tinyint(4) DEFAULT 0,
  `id_ingreso` int(11) NOT NULL DEFAULT 0,
  `pagado` tinyint(4) NOT NULL DEFAULT 0,
  `partida` varchar(255) DEFAULT NULL,
  `proporcional` int(11) DEFAULT 0,
  `escuelas` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id_coste`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `gestion_costes_copy` (
  `id_coste` int(11) NOT NULL AUTO_INCREMENT,
  `id_centro` int(11) DEFAULT 0,
  `proveedor` int(11) DEFAULT NULL,
  `num_factura` varchar(255) DEFAULT NULL,
  `fecha_factura` date DEFAULT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `tipo_coste` tinyint(4) DEFAULT NULL,
  `importe` double DEFAULT NULL,
  `forma_pago` tinyint(4) DEFAULT NULL,
  `banco` int(11) DEFAULT NULL,
  `observaciones` longtext DEFAULT NULL,
  `introducido_por` int(11) DEFAULT NULL,
  `fecha_insercion` date DEFAULT NULL,
  `finalizado` tinyint(4) DEFAULT 0,
  `id_ingreso` int(11) NOT NULL DEFAULT 0,
  `pagado` tinyint(4) NOT NULL DEFAULT 0,
  `partida` varchar(255) DEFAULT NULL,
  `proporcional` int(11) DEFAULT 0,
  `escuelas` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id_coste`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `gestion_ingresos` (
  `id_ingreso` int(11) NOT NULL AUTO_INCREMENT,
  `id_centro` int(11) DEFAULT 0,
  `fecha_entrada` date DEFAULT NULL,
  `tipo_ingreso` tinyint(4) DEFAULT NULL,
  `num_albaran` varchar(50) DEFAULT NULL,
  `num_factura` varchar(50) DEFAULT NULL,
  `cliente` int(11) DEFAULT NULL,
  `importe` double DEFAULT NULL,
  `cuenta_ingreso` int(11) DEFAULT NULL,
  `observaciones` longtext DEFAULT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `introducido_por` int(11) DEFAULT NULL,
  `fecha_insercion` date DEFAULT NULL,
  `finalizado` tinyint(4) DEFAULT 0,
  `partida` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_ingreso`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `historial_educativo_cuestionarios` (
  `id_cuestionario` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) DEFAULT 0,
  `id_curso` int(11) DEFAULT 0,
  `fecha_historial` date DEFAULT NULL,
  `lugar_nacimiento` varchar(100) DEFAULT NULL,
  `nombre_padre` varchar(50) DEFAULT NULL,
  `apellido1_padre` varchar(50) DEFAULT NULL,
  `apellido2_padre` varchar(50) DEFAULT NULL,
  `profesion_padre` varchar(100) DEFAULT NULL,
  `estudios_padre` varchar(100) DEFAULT NULL,
  `sit_laboral_padre` varchar(100) DEFAULT NULL,
  `hor_laboral_padre` varchar(100) DEFAULT NULL,
  `nombre_madre` varchar(50) DEFAULT NULL,
  `apellido1_madre` varchar(50) DEFAULT NULL,
  `apellido2_madre` varchar(50) DEFAULT NULL,
  `profesion_madre` varchar(100) DEFAULT NULL,
  `estudios_madre` varchar(100) DEFAULT NULL,
  `sit_laboral_madre` varchar(100) DEFAULT NULL,
  `hor_laboral_madre` varchar(100) DEFAULT NULL,
  `circustancias_sig` text DEFAULT NULL,
  `vive_con` varchar(100) DEFAULT NULL,
  `numero_hermanos` int(11) DEFAULT 0,
  `lugar_hermanos` int(11) DEFAULT 0,
  `viven_otros_fam` tinyint(4) DEFAULT 0,
  `parentesco` varchar(255) DEFAULT NULL,
  `problema_Salud` tinyint(4) DEFAULT 0,
  `que_problema` varchar(255) DEFAULT NULL,
  `estado_gen_salud` varchar(255) DEFAULT NULL,
  `vacunaciones` varchar(100) DEFAULT NULL,
  `problemas_salud` text DEFAULT NULL,
  `tratamiento_medico` varchar(255) DEFAULT NULL,
  `indicaciones_medico` text DEFAULT NULL,
  `depende_padres` tinyint(4) DEFAULT 0,
  `tiene_miedo` tinyint(4) DEFAULT 0,
  `tiene_pesadillas` tinyint(4) DEFAULT 0,
  `come_todo` tinyint(4) DEFAULT 0,
  `estimular_comida` tinyint(4) DEFAULT 0,
  `horas_duerme_noche` int(11) DEFAULT 0,
  `duerme_solo` tinyint(4) DEFAULT 0,
  `con_quien` varchar(100) DEFAULT NULL,
  `entretiene_solo` tinyint(4) DEFAULT 0,
  `presencia_otro` tinyint(4) DEFAULT 0,
  `juego_preferido` varchar(255) DEFAULT NULL,
  `juguetes_gustan` varchar(255) DEFAULT NULL,
  `actitud_juguetes` varchar(255) DEFAULT NULL,
  `juego_activo` tinyint(4) DEFAULT 0,
  `padres_juegan_diario` tinyint(4) DEFAULT 0,
  `ve_tv` tinyint(4) DEFAULT 0,
  `seleccion_programas_tv` tinyint(4) DEFAULT 0,
  `tiempo_familiares` varchar(100) DEFAULT NULL,
  `quien_dedica_mas_tiempo` varchar(100) DEFAULT NULL,
  `relacion_hermanos` varchar(255) DEFAULT NULL,
  `estan_padres_de_acuerdo` tinyint(4) DEFAULT 0,
  `aspectos_a_resaltar` text DEFAULT NULL,
  `llora_al_llegar` tinyint(4) DEFAULT 0,
  `saluda_al_entrar` tinyint(4) DEFAULT 0,
  `sonrie_y_contento` tinyint(4) DEFAULT 0,
  `llama_atencion` tinyint(4) DEFAULT 0,
  `llama_atencion_solo_necesita` tinyint(4) DEFAULT 0,
  `lleva_panales` tinyint(4) DEFAULT 0,
  `despide_salir` tinyint(4) DEFAULT 0,
  `come_todo_cole` tinyint(4) DEFAULT 0,
  `rechaza_alimento` tinyint(4) DEFAULT 0,
  `interes_comer_solo` tinyint(4) DEFAULT 0,
  `se_da_de_comer` tinyint(4) DEFAULT 0,
  `aspectos_observados_edu` text DEFAULT NULL,
  PRIMARY KEY (`id_cuestionario`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `historial_educativo_cuestionarios_anex1a` (
  `id_cuestionario` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) DEFAULT 0,
  `id_curso` int(11) DEFAULT 0,
  `fecha_historial` date DEFAULT NULL,
  `lugar_nacimiento` varchar(100) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `nombre_padre` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `apellido1_padre` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `apellido2_padre` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `profesion_padre` varchar(100) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `edad_padre` int(11) DEFAULT NULL,
  `nombre_madre` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `apellido1_madre` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `apellido2_madre` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `profesion_madre` varchar(100) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `edad_madre` int(11) DEFAULT NULL,
  `circustancias_sig` text CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `numero_hermanos` int(11) DEFAULT 0,
  `lugar_hermanos` int(11) DEFAULT 0,
  `viven_otros_fam` tinyint(4) DEFAULT 0,
  `parentesco` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `estado_gen_salud` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `vacunaciones` varchar(100) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `problemas_embarazo` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `peso_parto` varchar(25) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `talla_parto` varchar(25) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `prematuro` tinyint(4) DEFAULT NULL,
  `cesarea` tinyint(4) DEFAULT NULL,
  `lactancia` tinyint(4) DEFAULT NULL,
  `meses_lactancia` int(11) DEFAULT NULL,
  `mes_denticion` int(11) DEFAULT NULL,
  `denticion_acorde` tinyint(4) DEFAULT NULL,
  `problemas_salud` text CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `tratamiento_medico` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `indicaciones_medico` text CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `mes_gateo` int(11) DEFAULT NULL,
  `mes_andar` int(11) DEFAULT NULL,
  `mes_hablar` int(11) DEFAULT NULL,
  `num_palabras_dice` int(11) DEFAULT NULL,
  `palabras_dice` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `idiomas_casa` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `tiene_miedo` tinyint(4) DEFAULT 0,
  `que_miedo` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `tiene_pesadillas` tinyint(4) DEFAULT 0,
  `come_todo` tinyint(4) DEFAULT 0,
  `estimular_comida` tinyint(4) DEFAULT 0,
  `horas_duerme_noche` int(11) DEFAULT 0,
  `duerme_solo` tinyint(4) DEFAULT 0,
  `con_quien` varchar(100) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `entretiene_solo` tinyint(4) DEFAULT 0,
  `juego_preferido` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `padres_juegan_diario` tinyint(4) DEFAULT 0,
  `ve_tv` tinyint(4) DEFAULT 0,
  `seleccion_programas_tv` tinyint(4) DEFAULT 0,
  `tiempo_familiares` varchar(100) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `quien_dedica_mas_tiempo` varchar(100) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `relacion_hermanos` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `estan_padres_de_acuerdo` tinyint(4) DEFAULT 0,
  `tipo_castigo` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `duracion_castigo` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `efecto_castigo` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `tipo_recompensa` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `duracion_recompensa` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `efecto_recompensa` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `aspectos_a_resaltar` text CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `llora_al_llegar` tinyint(4) DEFAULT 0,
  `saluda_al_entrar` tinyint(4) DEFAULT 0,
  `sonrie_y_contento` tinyint(4) DEFAULT 0,
  `llama_atencion` tinyint(4) DEFAULT 0,
  `llama_atencion_solo_necesita` tinyint(4) DEFAULT 0,
  `lleva_panales` tinyint(4) DEFAULT 0,
  `despide_salir` tinyint(4) DEFAULT 0,
  `come_todo_cole` tinyint(4) DEFAULT 0,
  `rechaza_alimento` tinyint(4) DEFAULT 0,
  `interes_comer_solo` tinyint(4) DEFAULT 0,
  `se_da_de_comer` tinyint(4) DEFAULT 0,
  `aspectos_observados_edu` text CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`id_cuestionario`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `historial_educativo_cuestionarios_anex2` (
  `id_cuestionario` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) DEFAULT 0,
  `id_curso` int(11) DEFAULT 0,
  `fecha_historial` date DEFAULT NULL,
  `ciclo_termina` int(11) DEFAULT NULL,
  `municipio_nacimiento` varchar(100) DEFAULT NULL,
  `provincia_nacimiento` varchar(100) DEFAULT NULL,
  `pais_nacimiento` varchar(100) DEFAULT NULL,
  `nacionalidad` varchar(100) DEFAULT NULL,
  `nombre_padre` varchar(50) DEFAULT NULL,
  `apellido1_padre` varchar(50) DEFAULT NULL,
  `apellido2_padre` varchar(50) DEFAULT NULL,
  `telefono_padre` varchar(100) DEFAULT NULL,
  `nombre_madre` varchar(50) DEFAULT NULL,
  `apellido1_madre` varchar(50) DEFAULT NULL,
  `apellido2_madre` varchar(50) DEFAULT NULL,
  `telefono_madre` varchar(100) DEFAULT NULL,
  `codigo_centro_pro` varchar(50) DEFAULT NULL,
  `nombre_centro_pro` varchar(255) DEFAULT NULL,
  `localidad_centro_pro` varchar(100) DEFAULT NULL,
  `cursos_centro_pro` varchar(255) DEFAULT NULL,
  `diagnostico_clinico` text DEFAULT NULL,
  `cambio_dom_dir` varchar(255) DEFAULT NULL,
  `cambio_dom_num` varchar(25) DEFAULT NULL,
  `cambio_dom_pta` varchar(25) DEFAULT NULL,
  `cambio_dom_tel` varchar(255) DEFAULT NULL,
  `cambio_dom_loc` varchar(100) DEFAULT NULL,
  `cambio_dom_cp` varchar(10) DEFAULT NULL,
  `cambio_dom_pro` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_cuestionario`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `historial_educativo_cuestiones` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `orden` int(11) DEFAULT NULL,
  `seccion` varchar(4) DEFAULT NULL,
  `texto_form` varchar(100) DEFAULT NULL,
  `campo_db` varchar(100) DEFAULT NULL,
  `tipo_campo` int(11) DEFAULT 0,
  `longitud_campo` int(11) DEFAULT NULL,
  `id_formulario` int(11) DEFAULT 0,
  `numero_opciones` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `historial_educativo_cuestiones_anex1a` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `orden` int(11) DEFAULT NULL,
  `seccion` varchar(4) DEFAULT NULL,
  `texto_form` varchar(100) DEFAULT NULL,
  `campo_db` varchar(100) DEFAULT NULL,
  `tipo_campo` int(11) DEFAULT 0,
  `longitud_campo` int(11) DEFAULT NULL,
  `id_formulario` int(11) DEFAULT 0,
  `numero_opciones` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `historial_educativo_cuestiones_anex2` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `orden` int(11) DEFAULT NULL,
  `seccion` varchar(4) DEFAULT NULL,
  `texto_form` varchar(100) DEFAULT NULL,
  `campo_db` varchar(100) DEFAULT NULL,
  `tipo_campo` int(11) DEFAULT 0,
  `longitud_campo` int(11) DEFAULT NULL,
  `id_formulario` int(11) DEFAULT 0,
  `numero_opciones` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `historial_educativo_secciones` (
  `id_seccion` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `num_seccion` varchar(4) DEFAULT NULL,
  `nombre_seccion` varchar(50) DEFAULT NULL,
  `texto_seccion` text DEFAULT NULL,
  PRIMARY KEY (`id_seccion`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `historial_educativo_secciones_anex1a` (
  `id_seccion` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `num_seccion` varchar(4) DEFAULT NULL,
  `nombre_seccion` varchar(50) DEFAULT NULL,
  `texto_seccion` text DEFAULT NULL,
  PRIMARY KEY (`id_seccion`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `historial_educativo_secciones_anex2` (
  `id_seccion` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `num_seccion` varchar(4) DEFAULT NULL,
  `nombre_seccion` varchar(50) DEFAULT NULL,
  `texto_seccion` text DEFAULT NULL,
  PRIMARY KEY (`id_seccion`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `horario_alumnos_dias` (
  `id_horario` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `no_comedor` int(11) NOT NULL DEFAULT 0,
  `no_asistencia` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_horario`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `horario_docentes` (
  `id_horario` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) DEFAULT NULL,
  `id_centro` int(11) DEFAULT NULL,
  `fecha_inicio` date NOT NULL DEFAULT '0000-00-00',
  `fecha_fin` date NOT NULL DEFAULT '0000-00-00',
  `hora_entrada1` time NOT NULL DEFAULT '00:00:00',
  `hora_salida1` time NOT NULL DEFAULT '00:00:00',
  `hora_entrada2` time NOT NULL DEFAULT '00:00:00',
  `hora_salida2` time NOT NULL DEFAULT '00:00:00',
  `observaciones` longtext DEFAULT NULL,
  `l` tinyint(4) NOT NULL DEFAULT 1,
  `m` tinyint(4) NOT NULL DEFAULT 1,
  `x` tinyint(4) NOT NULL DEFAULT 1,
  `j` tinyint(4) NOT NULL DEFAULT 1,
  `v` tinyint(4) NOT NULL DEFAULT 1,
  `s` tinyint(4) NOT NULL DEFAULT 0,
  `d` tinyint(4) NOT NULL DEFAULT 0,
  `horas_semana` double DEFAULT 0,
  PRIMARY KEY (`id_horario`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `horario_docentes_dias` (
  `id_horario` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `hora_entrada1` time NOT NULL DEFAULT '00:00:00',
  `hora_salida1` time NOT NULL DEFAULT '00:00:00',
  `hora_entrada2` time NOT NULL DEFAULT '00:00:00',
  `hora_salida2` time NOT NULL DEFAULT '00:00:00',
  `no_asistencia` int(11) NOT NULL DEFAULT 0,
  `tipo_ausencia` int(11) DEFAULT 0,
  `sustituido_por` int(11) DEFAULT 0,
  `hora_ini_falta` time DEFAULT NULL,
  `hora_fin_falta` time DEFAULT NULL,
  PRIMARY KEY (`id_horario`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `incidencias` (
  `id_incidencia` int(11) NOT NULL AUTO_INCREMENT,
  `id_centro` int(11) DEFAULT NULL,
  `id_usuario_incidencia` int(11) NOT NULL DEFAULT 0,
  `tipo` int(11) NOT NULL DEFAULT 0,
  `nombre_incidencia` varchar(255) DEFAULT NULL,
  `fecha_incidencia` date NOT NULL DEFAULT '0000-00-00',
  `hora_incidencia` time NOT NULL DEFAULT '00:00:00',
  `notificada_por` int(11) NOT NULL DEFAULT 0,
  `seguimiento_por` int(11) NOT NULL DEFAULT 0,
  `descripcion` longtext DEFAULT NULL,
  `medidas_adoptadas1` longtext NOT NULL,
  `medidas_adoptadas` longtext DEFAULT NULL,
  `resultado` longtext DEFAULT NULL,
  `resuelta` int(11) NOT NULL DEFAULT 0,
  `tipo_medida` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_incidencia`),
  KEY `id_centro` (`id_centro`,`id_usuario_incidencia`,`tipo`,`fecha_incidencia`,`notificada_por`,`seguimiento_por`,`resuelta`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `incidencias_tipos` (
  `id_tipo` int(11) NOT NULL DEFAULT 0,
  `nombre_tipo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_tipo`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `lista_espera` (
  `id_usu_lista` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) DEFAULT NULL,
  `id_curso` int(11) DEFAULT NULL,
  `edad_espera` int(11) DEFAULT 0,
  `fecha_alta` date DEFAULT NULL,
  `fecha_deseada` date DEFAULT NULL,
  `fecha_baja` date DEFAULT NULL,
  `motivo_baja` int(11) DEFAULT 0,
  `observaciones_lista_espera` longtext DEFAULT NULL,
  `id_centro` int(11) DEFAULT 0,
  PRIMARY KEY (`id_usu_lista`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `lista_espera_cuestionarios` (
  `id_tipo_punto` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(255) DEFAULT NULL,
  `valor` double DEFAULT NULL,
  `desactivada` tinyint(4) DEFAULT 0,
  `id_curso` int(11) DEFAULT 0,
  PRIMARY KEY (`id_tipo_punto`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `lista_espera_cuestionarios_2015-2016` (
  `id_tipo_punto` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(255) DEFAULT NULL,
  `valor` double DEFAULT NULL,
  `desactivada` tinyint(4) DEFAULT 0,
  `id_curso` int(11) DEFAULT 0,
  PRIMARY KEY (`id_tipo_punto`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `lista_espera_motivos_baja` (
  `id_motivo` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion_motivo` varchar(255) NOT NULL,
  `valor_motivo` int(11) DEFAULT 0,
  PRIMARY KEY (`id_motivo`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `lista_espera_respuestas` (
  `id_puntuacion` int(11) NOT NULL AUTO_INCREMENT,
  `id_usu_lista` int(11) DEFAULT NULL,
  `id_tipo_puntos` int(11) DEFAULT NULL,
  `valor` tinyint(4) DEFAULT 0,
  PRIMARY KEY (`id_puntuacion`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `log_api` (
  `Contador` int(11) NOT NULL AUTO_INCREMENT,
  `Fecha` date DEFAULT NULL,
  `Hora` time DEFAULT NULL,
  `REMOTE_ADDR` char(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `HTTP_ACCEPT_LANGUAGE` char(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `HTTP_User_Agent` char(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `log_api` text DEFAULT NULL,
  PRIMARY KEY (`Contador`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_uca1400_ai_ci;

CREATE TABLE `mensajeria_interna` (
  `id_mensaje` int(11) NOT NULL AUTO_INCREMENT,
  `id_centro_destino` int(11) DEFAULT 0,
  `tipo_usuarios_destino` int(11) DEFAULT 0,
  `asunto` varchar(255) DEFAULT NULL,
  `cuerpo` longtext DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_caducidad` date DEFAULT NULL,
  `fecha_publicacion` date DEFAULT NULL,
  `requerida` tinyint(4) DEFAULT 0,
  PRIMARY KEY (`id_mensaje`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `mensajes_registro_lectura` (
  `id_registro` int(11) NOT NULL AUTO_INCREMENT,
  `id_mensaje` int(11) DEFAULT NULL,
  `id_mensaje_email` int(11) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `id_centro` int(11) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `leido` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_registro`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `mensajes_temporal` (
  `id_mensaje` int(11) NOT NULL AUTO_INCREMENT,
  `asunto` varchar(255) DEFAULT NULL,
  `texto_editor` longtext DEFAULT NULL,
  `id_centro` int(11) DEFAULT NULL,
  `cen` varchar(50) DEFAULT NULL,
  `aul` varchar(10) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `retorno` varchar(255) DEFAULT NULL,
  `destino1` varchar(255) DEFAULT NULL,
  `destino2` varchar(255) DEFAULT NULL,
  `destino3` varchar(255) DEFAULT NULL,
  `enviado_por` varchar(30) DEFAULT NULL,
  `id_curso` int(11) DEFAULT 0,
  `ant` varchar(50) DEFAULT NULL,
  `estado` tinyint(4) DEFAULT 0,
  `contactos` varchar(2) DEFAULT '0',
  PRIMARY KEY (`id_mensaje`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `menu_administrador` (
  `id_menu` tinyint(4) NOT NULL AUTO_INCREMENT,
  `texto_ayuda` longtext DEFAULT NULL,
  `link` text DEFAULT NULL,
  `Orden` tinyint(4) DEFAULT NULL,
  `Texto` varchar(255) DEFAULT NULL,
  `nivel_acceso` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_menu`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `menu_alimentos` (
  `id_alimento` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_alimento` varchar(150) DEFAULT NULL,
  `kcal_gramo` float DEFAULT 0,
  `hc_gramo` float DEFAULT 0,
  `prot_gramo` float DEFAULT 0,
  `grasas_gramo` float DEFAULT 0,
  `unidad_medida` tinyint(4) DEFAULT 0,
  `alimentos_verdura` tinyint(4) DEFAULT 0,
  `alimentos_pollo` tinyint(4) DEFAULT 0,
  `alimentos_ternera` tinyint(4) DEFAULT 0,
  `alimentos_pescado` tinyint(4) DEFAULT 0,
  `alimentos_huevo` tinyint(4) DEFAULT 0,
  `alimentos_lentejas` tinyint(4) DEFAULT 0,
  `alimentos_lacteos` tinyint(4) DEFAULT 0,
  `alergias_lacteos` tinyint(4) DEFAULT 0,
  `alergias_huevo` tinyint(4) DEFAULT 0,
  `alergias_proternera` tinyint(4) DEFAULT 0,
  `alergias_frutossecos` tinyint(4) DEFAULT 0,
  `alergias_marisco` tinyint(4) DEFAULT 0,
  `alergias_pielmelocoton` tinyint(4) DEFAULT 0,
  `alergias_gluten` tinyint(4) DEFAULT 0,
  `alergias_pescado` tinyint(4) DEFAULT 0,
  `alergias_soja` tinyint(4) DEFAULT 0,
  `alergias_sulfitos` tinyint(4) DEFAULT 0,
  `alergias_cacahuetes` tinyint(4) DEFAULT 0,
  `alergias_altramuces` tinyint(4) DEFAULT 0,
  `alergias_apio` tinyint(4) DEFAULT 0,
  `alergias_mostaza` tinyint(4) DEFAULT 0,
  `alergias_sesamo` tinyint(4) DEFAULT 0,
  `alergias_crustaceos` tinyint(4) DEFAULT 0,
  `alergias_moluscos` tinyint(4) DEFAULT 0,
  `alergias_berenjena` tinyint(4) DEFAULT 0,
  `peso_unidad` double DEFAULT 1,
  PRIMARY KEY (`id_alimento`),
  KEY `nombre_alimento` (`nombre_alimento`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `menu_calendario` (
  `id_dia` int(11) NOT NULL AUTO_INCREMENT,
  `fecha` date DEFAULT NULL,
  `id_menu` int(11) DEFAULT 0,
  `comida1` int(11) DEFAULT 0,
  `comida2` int(11) DEFAULT 0,
  `comida3` int(11) DEFAULT 0,
  `comida4` int(11) DEFAULT 0,
  `comida5` int(11) DEFAULT 0,
  `postre1` int(11) DEFAULT 0,
  `merienda1` int(11) DEFAULT 0,
  `merienda2` int(11) DEFAULT 0,
  PRIMARY KEY (`id_dia`),
  KEY `idx_fecha` (`fecha`,`id_menu`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `menu_ingredientes` (
  `id_ingrediente` int(11) NOT NULL AUTO_INCREMENT,
  `id_alimento` int(11) DEFAULT NULL,
  `id_plato` int(11) DEFAULT NULL,
  `cantidad_racion` float DEFAULT 0,
  `cantidad_medida` varchar(150) DEFAULT NULL,
  `preparar_separado` tinyint(4) DEFAULT 0,
  PRIMARY KEY (`id_ingrediente`),
  KEY `id_alimento` (`id_alimento`,`id_plato`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `menu_lista_compra` (
  `id_lista` int(11) NOT NULL AUTO_INCREMENT,
  `fecha_ini` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `observaciones` longtext DEFAULT NULL,
  PRIMARY KEY (`id_lista`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `menu_listado_ingredientes_usar` (
  `id_ingrediente` int(11) NOT NULL AUTO_INCREMENT,
  `id_ingrediente_usar` int(11) DEFAULT NULL,
  `id_plato` int(11) DEFAULT NULL,
  `cantidad` double DEFAULT NULL,
  PRIMARY KEY (`id_ingrediente`),
  KEY `id_ingrediente_usar` (`id_ingrediente_usar`,`id_plato`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `menu_listado_menus_preparar` (
  `id_lista` int(11) NOT NULL AUTO_INCREMENT,
  `dia` date DEFAULT NULL,
  `id_plato` int(11) DEFAULT NULL,
  `platos_a_servir` double DEFAULT NULL,
  `platos_servidor` double DEFAULT NULL,
  PRIMARY KEY (`id_lista`),
  KEY `dia` (`dia`,`id_plato`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `menu_menus` (
  `id_menu` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_menu` varchar(150) DEFAULT NULL,
  `id_centro` int(11) DEFAULT 0,
  `id_curso` int(11) DEFAULT 0,
  `edad_ini_menu` double NOT NULL DEFAULT 0,
  `edad_fin_menu` double NOT NULL DEFAULT 100,
  `regimen` tinyint(4) DEFAULT 0,
  PRIMARY KEY (`id_menu`),
  KEY `idx_menus` (`nombre_menu`,`id_centro`,`id_curso`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `menu_menus_borrados` (
  `id_menu_borrado` int(11) NOT NULL AUTO_INCREMENT,
  `id_menu` int(11) DEFAULT NULL,
  `nombre_menu` varchar(150) DEFAULT NULL,
  `id_centro` int(11) DEFAULT 0,
  `id_curso` int(11) DEFAULT 0,
  `edad_ini_menu` double NOT NULL DEFAULT 0,
  `edad_fin_menu` double NOT NULL DEFAULT 100,
  `regimen` tinyint(4) DEFAULT 0,
  PRIMARY KEY (`id_menu_borrado`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `menu_platos` (
  `id_plato` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_plato` varchar(150) DEFAULT NULL,
  `tipo_plato` int(11) DEFAULT 0,
  `edad_ini_plato` double NOT NULL DEFAULT 0,
  `edad_fin_plato` double NOT NULL DEFAULT 100,
  `nombre_recipiente` varchar(100) DEFAULT NULL,
  `raciones` int(11) DEFAULT 1,
  `instrucciones_preparacion` longtext DEFAULT NULL,
  `instrucciones_servir` longtext DEFAULT NULL,
  PRIMARY KEY (`id_plato`),
  KEY `idx_plato` (`nombre_plato`,`tipo_plato`,`edad_ini_plato`,`edad_fin_plato`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `menu_productos_consumo_cocina` (
  `id_producto_consumido` int(11) NOT NULL AUTO_INCREMENT,
  `id_producto_tarifa` int(11) DEFAULT NULL,
  `id_dia_menu` int(11) DEFAULT NULL,
  `id_plato` int(11) DEFAULT NULL,
  `cantidad` double DEFAULT NULL,
  `dia` date DEFAULT NULL,
  `id_alimento` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_producto_consumido`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `menu_productos_lista` (
  `id_producto` int(11) NOT NULL AUTO_INCREMENT,
  `id_lista` int(11) DEFAULT NULL,
  `id_alimento` int(11) DEFAULT NULL,
  `cantidad_recomendada` double DEFAULT NULL,
  `cantidad_redondeada` double DEFAULT 0,
  PRIMARY KEY (`id_producto`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `menu_productos_proveedor` (
  `id_producto_lista` int(11) NOT NULL AUTO_INCREMENT,
  `id_producto_tarifa` int(11) DEFAULT NULL,
  `id_lista` int(11) DEFAULT NULL,
  `cantidad` double DEFAULT NULL,
  `stock` double DEFAULT 0,
  `cantidad_resultante` double DEFAULT 0,
  `unidades` double DEFAULT 0,
  `pedido` tinyint(4) DEFAULT 0,
  PRIMARY KEY (`id_producto_lista`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `movimientos_caja` (
  `id_movimiento` int(11) NOT NULL AUTO_INCREMENT,
  `importe` double DEFAULT 0,
  `signo` int(11) NOT NULL DEFAULT 1,
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `comentario` varchar(255) DEFAULT NULL,
  `id_centro` int(11) DEFAULT NULL,
  `id_cierre_anterior` int(11) DEFAULT NULL,
  `anotado_por` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_movimiento`),
  KEY `fecha` (`fecha`,`hora`,`id_centro`,`id_cierre_anterior`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `movimientos_material` (
  `id_mov` int(11) NOT NULL AUTO_INCREMENT,
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `id_material` int(11) DEFAULT 0,
  `cantidad` double DEFAULT NULL,
  `concepto` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_mov`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `movimientos_material_escuelas` (
  `id_mov` int(11) NOT NULL AUTO_INCREMENT,
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `id_material` int(11) DEFAULT 0,
  `id_escuela` int(11) DEFAULT 0,
  `cantidad` double DEFAULT NULL,
  `concepto` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_mov`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `no_recibir_mail` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT '',
  `fecha` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `notificaciones_app` (
  `id_notificacion` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `creada_por` int(11) DEFAULT 0,
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp(),
  `fecha_envio` date DEFAULT NULL,
  `fecha_enviada` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `tipo` enum('token','topic','aula','centro') DEFAULT NULL,
  `destino` varchar(255) DEFAULT NULL,
  `titulo` varchar(255) DEFAULT NULL,
  `mensaje` text DEFAULT NULL,
  `enviada` tinyint(4) DEFAULT 0,
  `id_mensaje` int(11) DEFAULT 0,
  `id_nota_seguimiento` int(11) DEFAULT 0,
  PRIMARY KEY (`id_notificacion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_uca1400_ai_ci;

CREATE TABLE `padres` (
  `id_padre` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) DEFAULT NULL,
  `padre_de` int(11) DEFAULT NULL,
  `parentesco` int(11) NOT NULL DEFAULT 0,
  `emitir_factura` tinyint(4) DEFAULT NULL,
  `usuario_webcam` varchar(10) NOT NULL DEFAULT '',
  `password_webcam` varchar(255) DEFAULT '',
  `denegar_acceso_agenda` tinyint(4) DEFAULT 0,
  PRIMARY KEY (`id_padre`),
  KEY `id_usuario` (`id_usuario`,`padre_de`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `padres_profesiones` (
  `id_profesion` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_profesion` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id_profesion`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `pagos_a_cuenta` (
  `id_pago` int(11) NOT NULL AUTO_INCREMENT,
  `id_centro` int(11) DEFAULT 0,
  `id_centro_cobro` int(11) DEFAULT 0,
  `id_factura` int(11) DEFAULT NULL,
  `fecha_pago` date NOT NULL DEFAULT '0000-00-00',
  `importe_pago` double DEFAULT NULL,
  `forma_pago` varchar(255) DEFAULT NULL,
  `observaciones_pago` longtext DEFAULT NULL,
  `hora_pago` time DEFAULT NULL,
  `comentario` varchar(255) DEFAULT NULL,
  `signo` int(11) DEFAULT 1,
  `anotado_por` int(11) DEFAULT NULL,
  `cuenta_banco` int(11) NOT NULL DEFAULT 0,
  `id_asistencia` int(11) DEFAULT 0,
  PRIMARY KEY (`id_pago`),
  KEY `id_pago` (`id_factura`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `parte_asistencia` (
  `id_asistencia` int(11) NOT NULL AUTO_INCREMENT,
  `id_alumno` int(11) DEFAULT NULL,
  `id_aula` int(11) DEFAULT NULL,
  `tipo_evento` tinyint(4) DEFAULT NULL,
  `aula` int(11) DEFAULT NULL,
  `hora` time NOT NULL DEFAULT '00:00:00',
  `fecha` date DEFAULT NULL,
  PRIMARY KEY (`id_asistencia`),
  KEY `id_alumno` (`id_alumno`,`id_aula`,`fecha`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `partidas_gastos` (
  `nombre_partida` varchar(250) DEFAULT NULL,
  `id_partida` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `pedidos_material` (
  `id_pedido` int(11) NOT NULL AUTO_INCREMENT,
  `id_aula` int(11) NOT NULL DEFAULT 0,
  `id_usuario` int(11) NOT NULL DEFAULT 0,
  `fecha_pedido` date NOT NULL DEFAULT '0000-00-00',
  `fecha_entrega` date NOT NULL DEFAULT '0000-00-00',
  `observaciones` longtext NOT NULL,
  `validado_coordinador` tinyint(4) DEFAULT 0,
  PRIMARY KEY (`id_pedido`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `pedidos_material_backup` (
  `id_pedido` int(11) NOT NULL AUTO_INCREMENT,
  `id_aula` int(11) NOT NULL DEFAULT 0,
  `id_usuario` int(11) NOT NULL DEFAULT 0,
  `fecha_pedido` date NOT NULL DEFAULT '0000-00-00',
  `fecha_entrega` date NOT NULL DEFAULT '0000-00-00',
  `observaciones` longtext NOT NULL,
  `validado_coordinador` tinyint(4) DEFAULT 0,
  PRIMARY KEY (`id_pedido`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `pedidos_material_centros` (
  `id_pedido_centro` int(11) NOT NULL AUTO_INCREMENT,
  `id_centro` int(11) DEFAULT 0,
  `id_usuario` int(11) DEFAULT 0,
  `fecha_pedido` date DEFAULT NULL,
  `fecha_entrega` date DEFAULT NULL,
  `observaciones` longtext DEFAULT NULL,
  `exportado` tinyint(4) DEFAULT 0,
  PRIMARY KEY (`id_pedido_centro`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `pedidos_material_empresa` (
  `id_pedido_empresa` int(11) NOT NULL AUTO_INCREMENT,
  `fecha_pedido` date DEFAULT NULL,
  `observaciones` longtext DEFAULT NULL,
  PRIMARY KEY (`id_pedido_empresa`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `pedidos_proveedor` (
  `id_pedido` int(11) NOT NULL AUTO_INCREMENT,
  `id_proveedor` int(11) DEFAULT NULL,
  `fecha_pedido` date DEFAULT NULL,
  `fecha_entrega` date DEFAULT NULL,
  `fecha_factura` varchar(50) DEFAULT NULL,
  `numero_factura` varchar(50) DEFAULT NULL,
  `observaciones` longtext DEFAULT NULL,
  PRIMARY KEY (`id_pedido`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `personal_cat_laborales` (
  `id_categoria` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_categoria` varchar(80) DEFAULT NULL,
  PRIMARY KEY (`id_categoria`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `personal_contratos` (
  `id_contrato` int(11) NOT NULL AUTO_INCREMENT,
  `id_empleado` int(11) DEFAULT NULL,
  `id_categoria` int(11) DEFAULT NULL,
  `tipo_contrato` int(11) DEFAULT 0,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `salario_base` double DEFAULT 0,
  `observaciones` text DEFAULT NULL,
  `especialidad` int(11) NOT NULL DEFAULT 0,
  `salario_bruto` double NOT NULL DEFAULT 0,
  `irpf` double NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_contrato`),
  KEY `id_empleado` (`id_empleado`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `personal_doc_prevencion` (
  `id_doc` int(11) NOT NULL AUTO_INCREMENT,
  `id_contrato` int(11) NOT NULL DEFAULT 0,
  `tipo_doc` int(11) DEFAULT 0,
  `fecha_registro` date DEFAULT NULL,
  `hora_registro` time DEFAULT NULL,
  `fecha_entrega` date DEFAULT NULL,
  PRIMARY KEY (`id_doc`),
  KEY `id_contrato` (`id_contrato`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `personal_especialidades` (
  `id_especialidad` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_especialidad` varchar(50) NOT NULL,
  PRIMARY KEY (`id_especialidad`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `personal_historial` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_empleado` int(11) DEFAULT NULL,
  `id_escuela` int(11) DEFAULT NULL,
  `id_aula` int(11) DEFAULT 0,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_empleado` (`id_empleado`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `personal_pagos` (
  `id_pago` int(11) NOT NULL AUTO_INCREMENT,
  `id_empleado` int(11) DEFAULT 0,
  `id_contrato` int(11) DEFAULT 0,
  `tipo_pago` tinyint(4) DEFAULT 0,
  `importe` double DEFAULT 0,
  `fecha_pago` date DEFAULT NULL,
  `cuenta` tinyint(4) DEFAULT 0,
  `observaciones` text DEFAULT NULL,
  PRIMARY KEY (`id_pago`),
  KEY `id_empleado` (`id_empleado`,`id_contrato`,`tipo_pago`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `personal_puestos` (
  `id_puesto` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_puesto` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_puesto`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `personal_tipos_contrato` (
  `id_tipo_contrato` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_tipo_contrato` varchar(50) DEFAULT NULL,
  `abreviatura` varchar(2) DEFAULT NULL,
  PRIMARY KEY (`id_tipo_contrato`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `personal_tipos_cuentas_pago` (
  `id_cuenta` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_cuenta` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_cuenta`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `personal_tipos_doc_prevencion` (
  `id_tipo_doc` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_tipo_doc` varchar(50) DEFAULT NULL,
  `requerido` tinyint(4) DEFAULT 0,
  `nombre_plantilla` varchar(150) DEFAULT NULL,
  `nombre_doc` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id_tipo_doc`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `personal_tipos_pagos` (
  `id_tipo_pago` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_tipo_pago` varchar(50) DEFAULT NULL,
  `directo` tinyint(4) DEFAULT 0,
  PRIMARY KEY (`id_tipo_pago`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `programacion_actividades` (
  `id_actividad` int(11) NOT NULL AUTO_INCREMENT,
  `id_objetivo` int(11) NOT NULL DEFAULT 0,
  `id_modelo_actividad` int(11) NOT NULL,
  `nivel` int(11) NOT NULL DEFAULT 0,
  `orden` int(11) NOT NULL DEFAULT 0,
  `padre` int(11) DEFAULT 0,
  PRIMARY KEY (`id_actividad`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `programacion_areas` (
  `id_area` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_area` varchar(255) NOT NULL,
  `nivel` int(11) NOT NULL DEFAULT 0,
  `orden` int(11) NOT NULL DEFAULT 0,
  `padre` int(11) DEFAULT 0,
  `descripcion_area` text DEFAULT NULL,
  PRIMARY KEY (`id_area`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `programacion_calendario` (
  `id_dia` int(11) NOT NULL AUTO_INCREMENT,
  `fecha` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `id_aula` int(11) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `evaluacion_texto` text DEFAULT NULL,
  `evaluacion_puntos` int(11) DEFAULT 0,
  PRIMARY KEY (`id_dia`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `programacion_modelos_actividades` (
  `id_modelo_actividad` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_actividad` varchar(255) DEFAULT NULL,
  `descripcion_actividad` text DEFAULT NULL,
  `autor` int(11) DEFAULT NULL,
  `metodologia` text DEFAULT NULL,
  `tiempo` double DEFAULT NULL,
  `lugar` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_modelo_actividad`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `programacion_objetivos` (
  `id_objetivo` int(11) NOT NULL AUTO_INCREMENT,
  `id_area` int(11) NOT NULL DEFAULT 0,
  `nombre_objetivo` varchar(255) NOT NULL,
  `nivel` int(11) NOT NULL DEFAULT 0,
  `orden` int(11) NOT NULL DEFAULT 0,
  `padre` int(11) DEFAULT 0,
  `descripcion_objetivo` text DEFAULT NULL,
  `edad` int(11) DEFAULT 0,
  `mes` int(11) DEFAULT 0,
  `area_curricular` int(11) DEFAULT 0,
  PRIMARY KEY (`id_objetivo`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `proveedores` (
  `ID_PROVEEDOR` int(11) NOT NULL AUTO_INCREMENT,
  `tipo_proveedor` int(11) DEFAULT 0,
  `NIF` varchar(100) DEFAULT NULL,
  `RAZON_SOCIAL` varchar(255) DEFAULT NULL,
  `DOMICILIO` varchar(255) DEFAULT NULL,
  `LOCALIDAD` varchar(255) DEFAULT NULL,
  `CP` varchar(255) DEFAULT NULL,
  `TELEFONO1` varchar(100) DEFAULT NULL,
  `TELEFONO2` varchar(100) DEFAULT NULL,
  `MOVIL` varchar(100) DEFAULT NULL,
  `FAX` varchar(100) DEFAULT NULL,
  `EMAIL` varchar(255) DEFAULT NULL,
  `CUENTA_BANCO` varchar(50) DEFAULT NULL,
  `NOMBRE_CONT` varchar(255) DEFAULT NULL,
  `TELEFONO_CONT` varchar(100) DEFAULT NULL,
  `EMAIL_CONT` varchar(255) DEFAULT NULL,
  `PUESTO_CONT` varchar(255) DEFAULT NULL,
  `REGISTRO_SANITARIO` varchar(255) DEFAULT NULL,
  `OBSERVACIONES` longtext DEFAULT NULL,
  `fechaalta` date DEFAULT NULL,
  `FORMA_DE_PAGO` int(11) DEFAULT 1,
  `CUENTA_CARGO` varchar(50) DEFAULT NULL,
  `nombre_comercial` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`ID_PROVEEDOR`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `proveedores_cuestiones` (
  `id_cuestion` int(11) DEFAULT 0,
  `cuestion` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `proveedores_evaluaciones` (
  `id_evaluacion` int(11) NOT NULL AUTO_INCREMENT,
  `id_proveedor` int(11) DEFAULT 0,
  `cuestion1` int(11) DEFAULT 0,
  `cuestion2` int(11) DEFAULT 0,
  `cuestion3` int(11) DEFAULT 0,
  `cuestion4` int(11) DEFAULT 0,
  `cuestion5` int(11) DEFAULT 0,
  `cuestion6` int(11) DEFAULT 0,
  `cuestion7` int(11) DEFAULT 0,
  `cuestion8` int(11) DEFAULT 0,
  `cuestion9` int(11) DEFAULT 0,
  `cuestion10` int(11) DEFAULT 0,
  `observaciones` longtext DEFAULT NULL,
  `fecha_valoracion` date NOT NULL DEFAULT '0000-00-00',
  `valorada_por` int(11) DEFAULT 0,
  `introducida_por` int(11) DEFAULT 0,
  `fecha_insercion` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id_evaluacion`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `provincias` (
  `id_provincia` int(11) NOT NULL DEFAULT 0,
  `comunidad_aut` varchar(50) DEFAULT NULL,
  `provincia` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id_provincia`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `pruebas_brunet` (
  `id_prueba` int(11) NOT NULL AUTO_INCREMENT,
  `id_alumno` int(11) DEFAULT NULL,
  `er` double DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `observaciones_prueba` longtext DEFAULT NULL,
  `hora_fin` time DEFAULT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `apellido1` varchar(50) DEFAULT NULL,
  `apellido2` varchar(50) DEFAULT NULL,
  `fecha_nac` date DEFAULT NULL,
  `nombre_completo_padre` varchar(200) DEFAULT NULL,
  `telefono_padre` varchar(25) DEFAULT NULL,
  `email_padre` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id_prueba`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `pruebas_brunet_ampliado` (
  `id_prueba` int(11) NOT NULL AUTO_INCREMENT,
  `id_alumno` int(11) DEFAULT NULL,
  `er` double DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `observaciones_prueba` longtext DEFAULT NULL,
  `hora_fin` time DEFAULT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `apellido1` varchar(50) DEFAULT NULL,
  `apellido2` varchar(50) DEFAULT NULL,
  `fecha_nac` date DEFAULT NULL,
  `nombre_completo_padre` varchar(200) DEFAULT NULL,
  `telefono_padre` varchar(25) DEFAULT NULL,
  `email_padre` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id_prueba`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `pruebas_brunet_ampliado_respuestas` (
  `id_respuesta` int(11) NOT NULL AUTO_INCREMENT,
  `id_prueba` int(11) DEFAULT NULL,
  `id_item` int(11) DEFAULT NULL,
  `respuesta` tinyint(4) DEFAULT 0,
  PRIMARY KEY (`id_respuesta`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `pruebas_brunet_informe` (
  `id_parrafo` int(11) NOT NULL AUTO_INCREMENT,
  `tipo` varchar(1) DEFAULT NULL,
  `valor` int(11) DEFAULT 0,
  `alumnos` tinyint(4) DEFAULT 0,
  `texto` longtext DEFAULT NULL,
  PRIMARY KEY (`id_parrafo`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `pruebas_brunet_items` (
  `id_item` int(11) NOT NULL AUTO_INCREMENT,
  `orden_aplicacion` int(11) DEFAULT NULL,
  `num_orden` int(11) DEFAULT NULL,
  `escala` varchar(1) DEFAULT NULL,
  `orden_edad` int(11) DEFAULT NULL,
  `edad` int(11) DEFAULT NULL,
  `item` varchar(255) DEFAULT NULL,
  `puntuacion` int(11) DEFAULT NULL,
  `pregunta` int(11) DEFAULT NULL,
  `ampliado` tinyint(4) DEFAULT 0,
  PRIMARY KEY (`id_item`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `pruebas_brunet_respuestas` (
  `id_respuesta` int(11) NOT NULL AUTO_INCREMENT,
  `id_prueba` int(11) DEFAULT NULL,
  `id_item` int(11) DEFAULT NULL,
  `respuesta` tinyint(4) DEFAULT 0,
  PRIMARY KEY (`id_respuesta`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `pruebas_comparadas` (
  `id_prueba` int(11) NOT NULL AUTO_INCREMENT,
  `id_alumno` int(11) DEFAULT NULL,
  `er` double DEFAULT 0,
  `fecha` date DEFAULT NULL,
  `id_prueba1` varchar(10) DEFAULT NULL,
  `id_prueba2` varchar(10) DEFAULT NULL,
  `observaciones_prueba` longtext DEFAULT NULL,
  PRIMARY KEY (`id_prueba`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `pruebas_mcarhty_ejercicios` (
  `id_ejercicio` int(11) NOT NULL,
  `nombre_ejercicio` varchar(150) DEFAULT NULL,
  `tipo_ejercicio_p1` int(11) DEFAULT 0,
  `tipo_ejercicio_p2` int(11) DEFAULT 0,
  `tipo_ejercicio_p3` int(11) DEFAULT 0,
  `v` tinyint(4) DEFAULT 0,
  `pm` tinyint(4) DEFAULT 0,
  `n` tinyint(4) DEFAULT 0,
  `mem` tinyint(4) DEFAULT 0,
  `mot` tinyint(4) DEFAULT 0,
  `multiplicador1` double DEFAULT 1,
  `multiplicador2` double DEFAULT 1,
  `multiplicador3` double DEFAULT 1
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `pruebas_mcarthy` (
  `id_prueba` int(11) NOT NULL AUTO_INCREMENT,
  `id_alumno` int(11) DEFAULT NULL,
  `er` double DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `observaciones_prueba` longtext DEFAULT NULL,
  `hora_fin` time DEFAULT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `apellido1` varchar(50) DEFAULT NULL,
  `apellido2` varchar(50) DEFAULT NULL,
  `fecha_nac` date DEFAULT NULL,
  `nombre_completo_padre` varchar(200) DEFAULT NULL,
  `telefono_padre` varchar(25) DEFAULT NULL,
  `email_padre` varchar(150) DEFAULT NULL,
  `correccion_p` int(11) DEFAULT 0,
  `correccion_c` int(11) DEFAULT 0,
  `correccion_l` int(11) DEFAULT 0,
  `correccion_em` int(11) DEFAULT 0,
  PRIMARY KEY (`id_prueba`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `pruebas_mcarthy_conversion1` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ejercicio` int(11) DEFAULT NULL,
  `parte` int(11) DEFAULT NULL,
  `2_5` double DEFAULT NULL,
  `3_0` double DEFAULT NULL,
  `3_5` double DEFAULT NULL,
  `4_0` double DEFAULT NULL,
  `4_5` double DEFAULT NULL,
  `5_0` double DEFAULT NULL,
  `5_5` double DEFAULT NULL,
  `6_5` double DEFAULT NULL,
  `7_5` double DEFAULT NULL,
  `8_5` double DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `pruebas_mcarthy_conversion_gci` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `desde` int(11) DEFAULT NULL,
  `hasta` int(11) DEFAULT NULL,
  `2_5` int(11) DEFAULT NULL,
  `2_75` int(11) DEFAULT NULL,
  `3_0` int(11) DEFAULT 0,
  `3_25` int(11) DEFAULT NULL,
  `3_5` int(11) DEFAULT NULL,
  `3_75` int(11) DEFAULT NULL,
  `4_0` int(11) DEFAULT NULL,
  `4_25` int(11) DEFAULT NULL,
  `4_5` int(11) DEFAULT NULL,
  `4_75` int(11) DEFAULT NULL,
  `5_0` int(11) DEFAULT NULL,
  `5_25` int(11) DEFAULT NULL,
  `5_5` int(11) DEFAULT NULL,
  `5_75` int(11) DEFAULT NULL,
  `6_0` int(11) DEFAULT NULL,
  `6_25` int(11) DEFAULT NULL,
  `6_5` int(11) DEFAULT NULL,
  `6_75` int(11) DEFAULT NULL,
  `7_0` int(11) DEFAULT NULL,
  `7_25` int(11) DEFAULT NULL,
  `7_5` int(11) DEFAULT NULL,
  `7_75` int(11) DEFAULT NULL,
  `8_0` int(11) DEFAULT NULL,
  `8_25` int(11) DEFAULT NULL,
  `8_5` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `pruebas_mcarthy_informe` (
  `id_parrafo` int(11) NOT NULL AUTO_INCREMENT,
  `tipo` varchar(3) DEFAULT NULL,
  `valor` int(11) DEFAULT 0,
  `alumnos` tinyint(4) DEFAULT 0,
  `texto` longtext DEFAULT NULL,
  PRIMARY KEY (`id_parrafo`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `pruebas_mcarthy_items` (
  `id_item` int(11) NOT NULL AUTO_INCREMENT,
  `orden_aplicacion` int(11) DEFAULT NULL,
  `num_ejercicio` int(11) DEFAULT NULL,
  `parte` int(11) DEFAULT NULL,
  `orden_ejercicio` int(11) DEFAULT NULL,
  `edad` int(11) DEFAULT NULL,
  `item` varchar(255) DEFAULT NULL,
  `correcto` varchar(50) DEFAULT NULL,
  `tiempo_limite` int(11) DEFAULT NULL,
  `puntuacion_max` int(11) DEFAULT NULL,
  `formula` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_item`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `pruebas_mcarthy_puntos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `desde` int(11) DEFAULT 0,
  `hasta` int(11) DEFAULT 0,
  `desde_t` varchar(50) DEFAULT NULL,
  `hasta_t` varchar(50) DEFAULT NULL,
  `gci` int(11) DEFAULT 0,
  `pdc` int(11) DEFAULT 0,
  `pdc_t` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `pruebas_mcarthy_puntuacion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `desde_a` int(11) DEFAULT NULL,
  `desde_m` int(11) DEFAULT NULL,
  `desde_d` int(11) DEFAULT NULL,
  `desde_mt` double DEFAULT NULL,
  `hasta_a` int(11) DEFAULT NULL,
  `hasta_m` int(11) DEFAULT NULL,
  `hasta_d` int(11) DEFAULT NULL,
  `hasta_mt` double DEFAULT NULL,
  `gci` int(11) DEFAULT NULL,
  `pdc` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `pruebas_mcarthy_puntuacion_mem` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `desde_a` int(11) DEFAULT NULL,
  `desde_m` int(11) DEFAULT NULL,
  `desde_d` int(11) DEFAULT NULL,
  `desde_mt` double DEFAULT NULL,
  `hasta_a` int(11) DEFAULT NULL,
  `hasta_m` int(11) DEFAULT NULL,
  `hasta_d` int(11) DEFAULT NULL,
  `hasta_mt` double DEFAULT NULL,
  `gci` int(11) DEFAULT NULL,
  `pdc_t` varchar(50) DEFAULT NULL,
  `pdc` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `pruebas_mcarthy_puntuacion_mot` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `desde_a` int(11) DEFAULT NULL,
  `desde_m` int(11) DEFAULT NULL,
  `desde_d` int(11) DEFAULT NULL,
  `desde_mt` double DEFAULT NULL,
  `hasta_a` int(11) DEFAULT NULL,
  `hasta_m` int(11) DEFAULT NULL,
  `hasta_d` int(11) DEFAULT NULL,
  `hasta_mt` double DEFAULT NULL,
  `gci` int(11) DEFAULT NULL,
  `pdc_t` varchar(50) DEFAULT NULL,
  `pdc` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `pruebas_mcarthy_puntuacion_n` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `desde_a` int(11) DEFAULT NULL,
  `desde_m` int(11) DEFAULT NULL,
  `desde_d` int(11) DEFAULT NULL,
  `desde_mt` double DEFAULT NULL,
  `hasta_a` int(11) DEFAULT NULL,
  `hasta_m` int(11) DEFAULT NULL,
  `hasta_d` int(11) DEFAULT NULL,
  `hasta_mt` double DEFAULT NULL,
  `gci` int(11) DEFAULT NULL,
  `pdc_t` varchar(50) DEFAULT NULL,
  `pdc` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `pruebas_mcarthy_puntuacion_pm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `desde_a` int(11) DEFAULT NULL,
  `desde_m` int(11) DEFAULT NULL,
  `desde_d` int(11) DEFAULT NULL,
  `desde_mt` double DEFAULT NULL,
  `hasta_a` int(11) DEFAULT NULL,
  `hasta_m` int(11) DEFAULT NULL,
  `hasta_d` int(11) DEFAULT NULL,
  `hasta_mt` double DEFAULT NULL,
  `gci` int(11) DEFAULT NULL,
  `pdc_t` varchar(50) DEFAULT NULL,
  `pdc` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `pruebas_mcarthy_puntuacion_v` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `desde_a` int(11) DEFAULT NULL,
  `desde_m` int(11) DEFAULT NULL,
  `desde_d` int(11) DEFAULT NULL,
  `desde_mt` double DEFAULT NULL,
  `hasta_a` int(11) DEFAULT NULL,
  `hasta_m` int(11) DEFAULT NULL,
  `hasta_d` int(11) DEFAULT NULL,
  `hasta_mt` double DEFAULT NULL,
  `gci` int(11) DEFAULT NULL,
  `pdc_t` varchar(50) DEFAULT NULL,
  `pdc` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `pruebas_mcarthy_respuestas` (
  `id_respuesta` int(11) NOT NULL AUTO_INCREMENT,
  `id_prueba` int(11) DEFAULT NULL,
  `id_item` int(11) DEFAULT NULL,
  `dato1` int(11) DEFAULT NULL,
  `dato2` int(11) DEFAULT NULL,
  `dato3` int(11) DEFAULT NULL,
  `dato4` int(11) DEFAULT NULL,
  `dato5` int(11) DEFAULT NULL,
  `verdaderofalso` tinyint(4) DEFAULT NULL,
  `respuesta` varchar(255) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `lateralidad` varchar(1) DEFAULT NULL,
  PRIMARY KEY (`id_respuesta`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `pruebas_puntuacion_brunet` (
  `id_fila` int(11) NOT NULL AUTO_INCREMENT,
  `area` varchar(1) DEFAULT NULL,
  `p` int(11) DEFAULT NULL,
  `m` int(11) DEFAULT NULL,
  `d` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_fila`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `registro_contactos` (
  `id_registro` int(11) NOT NULL AUTO_INCREMENT,
  `id_contacto` int(11) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `comentarios` longtext DEFAULT NULL,
  PRIMARY KEY (`id_registro`),
  KEY `id_contacto` (`id_contacto`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `registro_copias_registro` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hora` time NOT NULL,
  `fecha` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `registro_lectura_mensajes` (
  `id_registro` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_mensaje` int(11) DEFAULT 0,
  `id_padre` int(11) DEFAULT 0,
  `fecha` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_registro`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `registro_vista_imagenes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nombre_imagen` text DEFAULT NULL,
  `fecha_lectura` date DEFAULT NULL,
  `hora_lectura` time DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `relaciones_tipos` (
  `id_tipo_relacion` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_relacion` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_tipo_relacion`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `reserva_servicios` (
  `id_reserva` int(11) NOT NULL AUTO_INCREMENT,
  `id_alumno` int(11) DEFAULT 0,
  `id_servicio` int(11) DEFAULT 0,
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `cantidad` double DEFAULT NULL,
  PRIMARY KEY (`id_reserva`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `rrhh_categorias_documentos` (
  `id_categoria` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_categoria` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_categoria`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `rrhh_documentacion` (
  `id_doc` int(11) NOT NULL AUTO_INCREMENT,
  `categoria` varchar(255) DEFAULT NULL,
  `denominacion` varchar(255) DEFAULT NULL,
  `archivo_plantilla` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_doc`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `rrhh_documentacion_basica` (
  `id_doc` int(11) NOT NULL AUTO_INCREMENT,
  `categoria` varchar(255) DEFAULT NULL,
  `denominacion` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_doc`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `rrhh_documentacion_presentada` (
  `id_documentacion` int(11) NOT NULL AUTO_INCREMENT,
  `categoria` varchar(255) DEFAULT NULL,
  `denominacion` varchar(255) DEFAULT NULL,
  `fecha_entrega` date NOT NULL DEFAULT '0000-00-00',
  `id_expediente` int(11) DEFAULT NULL,
  `observaciones` longtext DEFAULT NULL,
  `nombre_archivo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_documentacion`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `rrhh_seguimiento_empresas` (
  `id_nota` int(11) NOT NULL AUTO_INCREMENT,
  `fecha` date DEFAULT NULL,
  `comentarios` longtext NOT NULL,
  `id_expediente` int(11) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `fecha_modificacion` date DEFAULT NULL,
  PRIMARY KEY (`id_nota`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `servicios_centros` (
  `id_servicio` int(11) NOT NULL AUTO_INCREMENT,
  `id_centro` int(11) DEFAULT NULL,
  `nombre_servicio` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_servicio`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `servicios_comedor_contratados` (
  `id_servicio_contratado` int(11) NOT NULL AUTO_INCREMENT,
  `id_alumno` int(11) DEFAULT NULL,
  `id_servicio` int(11) DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `periodicidad` int(11) NOT NULL DEFAULT 0,
  `descuento` double DEFAULT 0,
  `cantidad` double DEFAULT NULL,
  `notas` varchar(255) DEFAULT NULL,
  `no_facturar` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_servicio_contratado`),
  KEY `id_alumno` (`id_alumno`,`id_servicio`,`no_facturar`,`fecha_inicio`,`fecha_fin`,`periodicidad`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `servicios_consumidos` (
  `id_servicio_consumido` int(11) NOT NULL AUTO_INCREMENT,
  `id_alumno` int(11) DEFAULT NULL,
  `id_servicio` int(11) DEFAULT NULL,
  `turno` int(11) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `hora_entrada` time DEFAULT NULL,
  `hora_salida` time DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `tipo_menu` int(11) DEFAULT NULL,
  `tipo_menu_comedor` int(11) NOT NULL DEFAULT 0,
  `menu_seleccionado_hoy` int(11) DEFAULT 0,
  `observaciones_menu` longtext DEFAULT NULL,
  `almuerzo` char(1) DEFAULT NULL,
  `comida1` char(1) DEFAULT NULL,
  `comida2` char(1) DEFAULT NULL,
  `postre` char(1) DEFAULT NULL,
  `merienda` char(1) DEFAULT NULL,
  `deposicion1` char(1) DEFAULT NULL,
  `deposicion2` char(1) DEFAULT NULL,
  `deposicion3` char(1) DEFAULT NULL,
  `siesta` char(1) DEFAULT NULL,
  `hora_entrada_c` time DEFAULT NULL,
  `hora_salida_c` time DEFAULT NULL,
  `incidencia` tinyint(4) DEFAULT 0,
  `resuelta` tinyint(4) DEFAULT 0,
  `medidas_adoptadas` longtext NOT NULL,
  `no_facturar` tinyint(4) DEFAULT 0,
  `no_facturar_menu` tinyint(4) DEFAULT 0,
  `salud_sintomas` text DEFAULT NULL,
  PRIMARY KEY (`id_servicio_consumido`),
  KEY `inx_asistencia` (`id_alumno`,`fecha`,`turno`,`tipo_menu`,`tipo_menu_comedor`,`menu_seleccionado_hoy`,`id_servicio`,`incidencia`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `servicios_consumidos2006` (
  `id_servicio_consumido` int(11) NOT NULL AUTO_INCREMENT,
  `id_alumno` int(11) DEFAULT NULL,
  `id_servicio` int(11) DEFAULT NULL,
  `turno` int(11) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `hora_entrada` time DEFAULT NULL,
  `hora_salida` time DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `tipo_menu` int(11) DEFAULT NULL,
  `tipo_menu_comedor` int(11) NOT NULL DEFAULT 0,
  `menu_seleccionado_hoy` int(11) DEFAULT 0,
  `observaciones_menu` longtext DEFAULT NULL,
  `almuerzo` char(1) DEFAULT NULL,
  `comida1` char(1) DEFAULT NULL,
  `comida2` char(1) DEFAULT NULL,
  `postre` char(1) DEFAULT NULL,
  `merienda` char(1) DEFAULT NULL,
  `deposicion1` char(1) DEFAULT NULL,
  `deposicion2` char(1) DEFAULT NULL,
  `deposicion3` char(1) DEFAULT NULL,
  `siesta` char(1) DEFAULT NULL,
  `hora_entrada_c` time DEFAULT NULL,
  `hora_salida_c` time DEFAULT NULL,
  `incidencia` tinyint(4) DEFAULT 0,
  `resuelta` tinyint(4) DEFAULT 0,
  `medidas_adoptadas` longtext NOT NULL,
  `no_facturar` tinyint(4) DEFAULT 0,
  `no_facturar_menu` tinyint(4) DEFAULT 0,
  PRIMARY KEY (`id_servicio_consumido`),
  KEY `inx_asistencia` (`id_alumno`,`fecha`,`turno`,`tipo_menu`,`tipo_menu_comedor`,`menu_seleccionado_hoy`,`id_servicio`,`incidencia`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `servicios_consumidos_2017-09-27` (
  `id_servicio_consumido` int(11) NOT NULL AUTO_INCREMENT,
  `id_alumno` int(11) DEFAULT NULL,
  `id_servicio` int(11) DEFAULT NULL,
  `turno` int(11) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `hora_entrada` time DEFAULT NULL,
  `hora_salida` time DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `tipo_menu` int(11) DEFAULT NULL,
  `tipo_menu_comedor` int(11) NOT NULL DEFAULT 0,
  `menu_seleccionado_hoy` int(11) DEFAULT 0,
  `observaciones_menu` longtext DEFAULT NULL,
  `almuerzo` char(1) DEFAULT NULL,
  `comida1` char(1) DEFAULT NULL,
  `comida2` char(1) DEFAULT NULL,
  `postre` char(1) DEFAULT NULL,
  `merienda` char(1) DEFAULT NULL,
  `deposicion1` char(1) DEFAULT NULL,
  `deposicion2` char(1) DEFAULT NULL,
  `deposicion3` char(1) DEFAULT NULL,
  `siesta` char(1) DEFAULT NULL,
  `hora_entrada_c` time DEFAULT NULL,
  `hora_salida_c` time DEFAULT NULL,
  `incidencia` tinyint(4) DEFAULT 0,
  `resuelta` tinyint(4) DEFAULT 0,
  `medidas_adoptadas` longtext NOT NULL,
  `no_facturar` tinyint(4) DEFAULT 0,
  `no_facturar_menu` tinyint(4) DEFAULT 0,
  `salud_sintomas` text DEFAULT NULL,
  PRIMARY KEY (`id_servicio_consumido`),
  KEY `inx_asistencia` (`id_alumno`,`fecha`,`turno`,`tipo_menu`,`tipo_menu_comedor`,`menu_seleccionado_hoy`,`id_servicio`,`incidencia`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `servicios_consumidos_asistencia` (
  `id_servicio_consumido` int(11) NOT NULL AUTO_INCREMENT,
  `id_alumno` int(11) DEFAULT NULL,
  `id_servicio` int(11) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `hora_entrada` time DEFAULT NULL,
  `hora_salida` time DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `no_facturar` tinyint(4) DEFAULT 0,
  `no_facturar_menu` tinyint(4) DEFAULT 0,
  `reserva` tinyint(4) DEFAULT 0,
  `pagado` tinyint(4) DEFAULT 0,
  `no_asiste` tinyint(4) DEFAULT 0,
  PRIMARY KEY (`id_servicio_consumido`),
  KEY `idx_sc` (`id_alumno`,`id_servicio`,`fecha`,`no_facturar`,`reserva`,`pagado`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `servicios_consumidos_copy_2025-11-13` (
  `id_servicio_consumido` int(11) NOT NULL AUTO_INCREMENT,
  `id_alumno` int(11) DEFAULT NULL,
  `id_servicio` int(11) DEFAULT NULL,
  `turno` int(11) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `hora_entrada` time DEFAULT NULL,
  `hora_salida` time DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `tipo_menu` int(11) DEFAULT NULL,
  `tipo_menu_comedor` int(11) NOT NULL DEFAULT 0,
  `menu_seleccionado_hoy` int(11) DEFAULT 0,
  `observaciones_menu` longtext DEFAULT NULL,
  `almuerzo` char(1) DEFAULT NULL,
  `comida1` char(1) DEFAULT NULL,
  `comida2` char(1) DEFAULT NULL,
  `postre` char(1) DEFAULT NULL,
  `merienda` char(1) DEFAULT NULL,
  `deposicion1` char(1) DEFAULT NULL,
  `deposicion2` char(1) DEFAULT NULL,
  `deposicion3` char(1) DEFAULT NULL,
  `siesta` char(1) DEFAULT NULL,
  `hora_entrada_c` time DEFAULT NULL,
  `hora_salida_c` time DEFAULT NULL,
  `incidencia` tinyint(4) DEFAULT 0,
  `resuelta` tinyint(4) DEFAULT 0,
  `medidas_adoptadas` longtext NOT NULL,
  `no_facturar` tinyint(4) DEFAULT 0,
  `no_facturar_menu` tinyint(4) DEFAULT 0,
  `salud_sintomas` text DEFAULT NULL,
  PRIMARY KEY (`id_servicio_consumido`),
  KEY `inx_asistencia` (`id_alumno`,`fecha`,`turno`,`tipo_menu`,`tipo_menu_comedor`,`menu_seleccionado_hoy`,`id_servicio`,`incidencia`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `servicios_contratados` (
  `id_servicio_contratado` int(11) NOT NULL AUTO_INCREMENT,
  `id_alumno` int(11) DEFAULT NULL,
  `id_servicio` int(11) DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `periodicidad` int(11) NOT NULL DEFAULT 0,
  `descuento` double DEFAULT 0,
  `cantidad` double DEFAULT NULL,
  `notas` varchar(255) DEFAULT NULL,
  `no_facturar` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_servicio_contratado`),
  KEY `id_alumno` (`id_alumno`,`id_servicio`,`no_facturar`,`fecha_inicio`,`fecha_fin`,`periodicidad`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `servicios_contratados_aula` (
  `id_servicio_contratado` int(11) NOT NULL AUTO_INCREMENT,
  `id_aula` int(11) DEFAULT NULL,
  `id_servicio` int(11) DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `periodicidad` int(11) NOT NULL DEFAULT 0,
  `descuento` double DEFAULT 0,
  `cantidad` double DEFAULT NULL,
  `notas` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_servicio_contratado`),
  KEY `id_aula` (`id_aula`,`id_servicio`,`fecha_inicio`,`fecha_fin`,`periodicidad`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `servicios_no_contratados` (
  `id_no_contratado` int(11) NOT NULL AUTO_INCREMENT,
  `id_servicio_aula` int(11) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_no_contratado`),
  KEY `id_servicio_aula` (`id_servicio_aula`,`id_usuario`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `sesion` (
  `Contador` int(11) NOT NULL AUTO_INCREMENT,
  `id_session` char(255) DEFAULT NULL,
  `Usuario` char(255) DEFAULT NULL,
  `Tipo` char(1) DEFAULT NULL,
  `Fecha` date DEFAULT NULL,
  `Hora` time DEFAULT NULL,
  `REMOTE_ADDR` char(255) DEFAULT NULL,
  `HTTP_ACCEPT_LANGUAGE` char(255) DEFAULT NULL,
  `HTTP_User_Agent` char(255) DEFAULT NULL,
  `Hora_cierre` time DEFAULT NULL,
  `Cerrada` tinyint(1) DEFAULT NULL,
  `Fecha_cierre` date DEFAULT '0000-00-00',
  `nombre_usuario` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`Contador`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `sesion_2017-09-29` (
  `Contador` int(11) NOT NULL AUTO_INCREMENT,
  `id_session` char(255) DEFAULT NULL,
  `Usuario` char(255) DEFAULT NULL,
  `Tipo` char(1) DEFAULT NULL,
  `Fecha` date DEFAULT NULL,
  `Hora` time DEFAULT NULL,
  `REMOTE_ADDR` char(255) DEFAULT NULL,
  `HTTP_ACCEPT_LANGUAGE` char(255) DEFAULT NULL,
  `HTTP_User_Agent` char(255) DEFAULT NULL,
  `Hora_cierre` time DEFAULT NULL,
  `Cerrada` tinyint(1) DEFAULT NULL,
  `Fecha_cierre` date DEFAULT '0000-00-00',
  `nombre_usuario` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`Contador`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `sesion_fallos` (
  `Contador` int(11) NOT NULL AUTO_INCREMENT,
  `id_session` char(255) DEFAULT NULL,
  `Usuario` char(255) DEFAULT NULL,
  `Pass` char(255) DEFAULT NULL,
  `Fecha` date DEFAULT NULL,
  `Hora` time DEFAULT NULL,
  `REMOTE_ADDR` char(255) DEFAULT NULL,
  `HTTP_ACCEPT_LANGUAGE` char(255) DEFAULT NULL,
  `HTTP_User_Agent` char(255) DEFAULT NULL,
  `descripcion_fallo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`Contador`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `sesion_fallos_padres` (
  `Contador` int(11) NOT NULL AUTO_INCREMENT,
  `id_session` char(255) DEFAULT NULL,
  `Usuario` char(255) DEFAULT NULL,
  `Pass` char(255) DEFAULT NULL,
  `Fecha` date DEFAULT NULL,
  `Hora` time DEFAULT NULL,
  `REMOTE_ADDR` char(255) DEFAULT NULL,
  `HTTP_ACCEPT_LANGUAGE` char(255) DEFAULT NULL,
  `HTTP_User_Agent` char(255) DEFAULT NULL,
  `descripcion_fallo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`Contador`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `subcategorias` (
  `id_subcat` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_subcat` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_subcat`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `tarifa` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `CATEGORIA` int(11) DEFAULT NULL,
  `subcat` int(11) DEFAULT 0,
  `EAN` varchar(255) DEFAULT NULL,
  `ARTICULO` varchar(255) DEFAULT NULL,
  `PRECIO_VENTA` double DEFAULT NULL,
  `PRECIO_COSTE` double DEFAULT NULL,
  `STOCK_MINIMO` int(11) DEFAULT NULL,
  `PROVEEDOR` int(11) DEFAULT NULL,
  `UNIDAD` varchar(25) DEFAULT NULL,
  `porcion_individual` double DEFAULT 0,
  `TIPO_SERVICIO` int(11) NOT NULL DEFAULT 0,
  `ARTICULO_GENERICO` varchar(255) DEFAULT NULL,
  `ID_ALIMENTO_GENERICO` int(11) DEFAULT 0,
  `SUBVENCION` tinyint(4) DEFAULT 0,
  `CONTROL_ASISTENCIA` tinyint(4) DEFAULT 0,
  `ID_CENTRO_ASISTENCIA` varchar(255) DEFAULT '0',
  `FECHA_INICIO_ASISTENCIA` date NOT NULL DEFAULT '0000-00-00',
  `FECHA_FIN_ASISTENCIA` date NOT NULL DEFAULT '0000-00-00',
  `INCLUIR_INFORME` tinyint(4) DEFAULT 0,
  `anula_comedor` tinyint(4) DEFAULT 0,
  `anula_horas_extra` tinyint(4) DEFAULT 0,
  `id_curso` int(11) NOT NULL DEFAULT 0,
  `tipo_facturacion_extra` int(11) DEFAULT 0,
  `desglosar_como` int(11) DEFAULT 0,
  `nacidos_en` int(11) DEFAULT NULL,
  `unidad_medida` varchar(25) DEFAULT 'uds.',
  `id_centro_ventas` varchar(255) DEFAULT NULL,
  `findemes` tinyint(4) DEFAULT 1,
  `desglose_informe` enum('Subvenciones','Mensualidad','Comedor','Hora extra mensual','Material complementario','Servicios complementarios','Cuota laboral','Horas extra sueltas','Otros conceptos','Educación dias sueltos') DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `CATEGORIA` (`CATEGORIA`,`ARTICULO`,`CONTROL_ASISTENCIA`,`ID_CENTRO_ASISTENCIA`,`anula_comedor`,`anula_horas_extra`,`id_curso`,`tipo_facturacion_extra`,`SUBVENCION`,`subcat`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `tarifa_desglose_informe` (
  `id_desglose` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nombre_desglose` char(255) DEFAULT NULL,
  PRIMARY KEY (`id_desglose`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `temp_agenda` (
  `hora` time DEFAULT NULL,
  `texto` longtext DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `tipos_categorias` (
  `id_tipo` int(11) DEFAULT NULL,
  `nombre_tipo_cat` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `tipos_deslose_facturacion` (
  `id_desglose` int(11) NOT NULL,
  `nombre_desglose` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id_desglose`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `tipos_iva_servicios` (
  `id_tipo_iva` int(11) NOT NULL AUTO_INCREMENT,
  `categoria` varchar(255) DEFAULT NULL,
  `porcentaje_iva` double DEFAULT NULL,
  `tipo_servicios` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_tipo_iva`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `tmp_alumnos_objetivo` (
  `id_alumno` int(11) NOT NULL,
  `servicio_educacion` varchar(255) DEFAULT NULL,
  `cantidad_educacion` double DEFAULT 0,
  `coste_educacion` double DEFAULT 0,
  PRIMARY KEY (`id_alumno`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_uca1400_ai_ci;

CREATE TABLE `tokens_activos` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `token` text DEFAULT NULL,
  `creacion` timestamp NULL DEFAULT current_timestamp(),
  `expiracion` int(11) DEFAULT 24,
  `id_usuario` int(11) DEFAULT NULL,
  `id_hijo` int(11) DEFAULT NULL,
  `id_padre` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `tokens_descarga` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `token` varchar(256) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT '',
  `creacion` timestamp NULL DEFAULT current_timestamp(),
  `expiracion` int(11) DEFAULT 24,
  `id_usuario` int(11) DEFAULT NULL,
  `id_hijo` int(11) DEFAULT NULL,
  `id_padre` int(11) DEFAULT NULL,
  `id_factura` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_uca1400_ai_ci;

CREATE TABLE `tokens_fcm` (
  `id_fcm_token` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) DEFAULT NULL,
  `id_padre` int(11) DEFAULT NULL,
  `FCMtoken` text DEFAULT NULL,
  `creacion` timestamp NULL DEFAULT current_timestamp(),
  `ultimo_acceso` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_fcm_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_uca1400_ai_ci;

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL AUTO_INCREMENT,
  `tipo_inscripcion` int(11) DEFAULT NULL,
  `Nombre` varchar(255) DEFAULT NULL,
  `Apellido1` varchar(255) DEFAULT NULL,
  `Apellido2` varchar(255) DEFAULT NULL,
  `sexo` varchar(5) DEFAULT NULL,
  `DNI` varchar(255) DEFAULT NULL,
  `Direccion` text DEFAULT NULL,
  `coordenadas` varchar(150) DEFAULT NULL,
  `cp` varchar(5) DEFAULT NULL,
  `Poblacion` varchar(255) DEFAULT NULL,
  `Provincia` varchar(255) DEFAULT NULL,
  `telefono1` varchar(50) DEFAULT NULL,
  `telefono2` varchar(50) DEFAULT NULL,
  `movil` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `huelladigital` varchar(255) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `prematuro` tinyint(4) DEFAULT 0,
  `prematuro1` tinyint(4) DEFAULT 0,
  `nonato` tinyint(4) DEFAULT 0,
  `titulacion` varchar(255) DEFAULT NULL,
  `especialidad` varchar(150) DEFAULT NULL,
  `profesion` varchar(255) DEFAULT NULL,
  `usuario` varchar(50) DEFAULT NULL,
  `pass` varchar(255) NOT NULL DEFAULT '',
  `fechaalta` date NOT NULL DEFAULT '0000-00-00',
  `nivel_acceso` smallint(11) NOT NULL DEFAULT 0,
  `alergias` longtext DEFAULT NULL,
  `vacunaciones` longtext DEFAULT NULL,
  `observaciones` longtext DEFAULT NULL,
  `fecha_edicion` date DEFAULT NULL,
  `contacto1` varchar(255) DEFAULT NULL,
  `telefono_contacto1` varchar(100) DEFAULT NULL,
  `contacto2` varchar(255) DEFAULT NULL,
  `telefono_contacto2` varchar(100) DEFAULT NULL,
  `contacto_medico` varchar(255) DEFAULT NULL,
  `telefono_medico` varchar(100) DEFAULT NULL,
  `id_centro` int(11) DEFAULT NULL,
  `alimentos_verdura` tinyint(4) DEFAULT 1,
  `alimentos_pollo` tinyint(4) NOT NULL DEFAULT 1,
  `alimentos_ternera` tinyint(4) NOT NULL DEFAULT 1,
  `alimentos_pescado` tinyint(4) NOT NULL DEFAULT 1,
  `alimentos_huevo` tinyint(4) NOT NULL DEFAULT 1,
  `alimentos_lentejas` tinyint(4) NOT NULL DEFAULT 1,
  `alimentos_lacteos` tinyint(4) NOT NULL DEFAULT 1,
  `alergias_lacteos` tinyint(4) NOT NULL DEFAULT 0,
  `alergias_huevo` tinyint(4) NOT NULL DEFAULT 0,
  `alergias_proternera` tinyint(4) NOT NULL DEFAULT 0,
  `alergias_frutossecos` tinyint(4) NOT NULL DEFAULT 0,
  `alergias_marisco` tinyint(4) NOT NULL DEFAULT 0,
  `alergias_pielmelocoton` tinyint(4) NOT NULL DEFAULT 0,
  `alergias_gluten` tinyint(4) NOT NULL DEFAULT 0,
  `alergias_pescado` tinyint(4) DEFAULT 0,
  `alergias_soja` tinyint(4) DEFAULT 0,
  `alergias_sulfitos` tinyint(4) DEFAULT 0,
  `alergias_cerdo` tinyint(4) DEFAULT 0,
  `alergias_cacahuetes` tinyint(4) DEFAULT 0,
  `alergias_altramuces` tinyint(4) DEFAULT 0,
  `alergias_apio` tinyint(4) DEFAULT 0,
  `alergias_mostaza` tinyint(4) DEFAULT 0,
  `alergias_sesamo` tinyint(4) DEFAULT 0,
  `alergias_crustaceos` tinyint(4) DEFAULT 0,
  `alergias_moluscos` tinyint(4) DEFAULT 0,
  `alergias_berenjena` tinyint(4) DEFAULT 0,
  `fecha_prevencion` date NOT NULL DEFAULT '0000-00-00',
  `curriculum` longtext DEFAULT NULL,
  `multicentro` tinyint(4) DEFAULT 0,
  `zona_alumnos` tinyint(4) DEFAULT 0,
  `zona_aulas` tinyint(4) DEFAULT 0,
  `zona_facturacion` tinyint(4) DEFAULT 0,
  `zona_comedor` tinyint(4) DEFAULT 0,
  `zona_almacen` tinyint(4) DEFAULT 0,
  `zona_incidencias` tinyint(4) DEFAULT 0,
  `zona_personal` tinyint(4) DEFAULT 0,
  `zona_pedidos` tinyint(4) DEFAULT 0,
  `zona_inferior` tinyint(4) DEFAULT 0,
  `zona_mensajes` tinyint(4) DEFAULT 0,
  `zona_compras` int(11) DEFAULT 0,
  `zona_gabinete` tinyint(4) DEFAULT 0,
  `zona_corregir_pruebas` tinyint(4) DEFAULT 0,
  `zona_actividades` tinyint(4) DEFAULT 0,
  `nacionalidad` varchar(20) DEFAULT NULL,
  `NSS` varchar(30) NOT NULL,
  `codigo_nominaplus` int(11) DEFAULT NULL,
  `servicio_comun` int(11) DEFAULT 0,
  `medidas_complementarias` text DEFAULT NULL,
  `nuevo_pass` varchar(20) DEFAULT '',
  `old_pass` varchar(20) DEFAULT NULL,
  `SIP` varchar(50) DEFAULT '',
  PRIMARY KEY (`id_usuario`),
  KEY `idx_usuarios` (`id_centro`,`tipo_inscripcion`,`nivel_acceso`,`Nombre`,`Apellido1`,`Apellido2`),
  KEY `idx_alimentos` (`alimentos_verdura`,`alimentos_pollo`,`alimentos_ternera`,`alimentos_pescado`,`alimentos_huevo`,`alimentos_lentejas`,`alimentos_lacteos`,`alergias_lacteos`,`alergias_huevo`,`alergias_proternera`,`alergias_frutossecos`,`alergias_marisco`,`alergias_pielmelocoton`,`alergias_gluten`),
  KEY `idx_personal` (`tipo_inscripcion`,`nivel_acceso`),
  KEY `alergias_pescado` (`alergias_pescado`,`alergias_soja`,`alergias_sulfitos`),
  KEY `alergias_cerdo` (`alergias_cerdo`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `usuarios_copy` (
  `id_usuario` int(11) NOT NULL AUTO_INCREMENT,
  `tipo_inscripcion` int(11) DEFAULT NULL,
  `Nombre` varchar(255) DEFAULT NULL,
  `Apellido1` varchar(255) DEFAULT NULL,
  `Apellido2` varchar(255) DEFAULT NULL,
  `sexo` varchar(5) DEFAULT NULL,
  `DNI` varchar(255) DEFAULT NULL,
  `Direccion` text DEFAULT NULL,
  `coordenadas` varchar(150) DEFAULT NULL,
  `cp` varchar(5) DEFAULT NULL,
  `Poblacion` varchar(255) DEFAULT NULL,
  `Provincia` varchar(255) DEFAULT NULL,
  `telefono1` varchar(50) DEFAULT NULL,
  `telefono2` varchar(50) DEFAULT NULL,
  `movil` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `huelladigital` varchar(255) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `prematuro` tinyint(4) DEFAULT 0,
  `prematuro1` tinyint(4) DEFAULT 0,
  `nonato` tinyint(4) DEFAULT 0,
  `titulacion` varchar(255) DEFAULT NULL,
  `especialidad` varchar(150) DEFAULT NULL,
  `profesion` varchar(255) DEFAULT NULL,
  `usuario` varchar(50) DEFAULT NULL,
  `pass` varchar(20) DEFAULT NULL,
  `fechaalta` date NOT NULL DEFAULT '0000-00-00',
  `nivel_acceso` smallint(11) NOT NULL DEFAULT 0,
  `alergias` longtext DEFAULT NULL,
  `vacunaciones` longtext DEFAULT NULL,
  `observaciones` longtext DEFAULT NULL,
  `fecha_edicion` date DEFAULT NULL,
  `contacto1` varchar(255) DEFAULT NULL,
  `telefono_contacto1` varchar(100) DEFAULT NULL,
  `contacto2` varchar(255) DEFAULT NULL,
  `telefono_contacto2` varchar(100) DEFAULT NULL,
  `contacto_medico` varchar(255) DEFAULT NULL,
  `telefono_medico` varchar(100) DEFAULT NULL,
  `id_centro` int(11) DEFAULT NULL,
  `alimentos_verdura` tinyint(4) DEFAULT 1,
  `alimentos_pollo` tinyint(4) NOT NULL DEFAULT 1,
  `alimentos_ternera` tinyint(4) NOT NULL DEFAULT 1,
  `alimentos_pescado` tinyint(4) NOT NULL DEFAULT 1,
  `alimentos_huevo` tinyint(4) NOT NULL DEFAULT 1,
  `alimentos_lentejas` tinyint(4) NOT NULL DEFAULT 1,
  `alimentos_lacteos` tinyint(4) NOT NULL DEFAULT 1,
  `alergias_lacteos` tinyint(4) NOT NULL DEFAULT 0,
  `alergias_huevo` tinyint(4) NOT NULL DEFAULT 0,
  `alergias_proternera` tinyint(4) NOT NULL DEFAULT 0,
  `alergias_frutossecos` tinyint(4) NOT NULL DEFAULT 0,
  `alergias_marisco` tinyint(4) NOT NULL DEFAULT 0,
  `alergias_pielmelocoton` tinyint(4) NOT NULL DEFAULT 0,
  `alergias_gluten` tinyint(4) NOT NULL DEFAULT 0,
  `alergias_pescado` tinyint(4) DEFAULT 0,
  `alergias_soja` tinyint(4) DEFAULT 0,
  `alergias_sulfitos` tinyint(4) DEFAULT 0,
  `alergias_cerdo` tinyint(4) DEFAULT 0,
  `fecha_prevencion` date NOT NULL DEFAULT '0000-00-00',
  `curriculum` longtext DEFAULT NULL,
  `multicentro` tinyint(4) DEFAULT 0,
  `zona_alumnos` tinyint(4) DEFAULT 0,
  `zona_aulas` tinyint(4) DEFAULT 0,
  `zona_facturacion` tinyint(4) DEFAULT 0,
  `zona_comedor` tinyint(4) DEFAULT 0,
  `zona_almacen` tinyint(4) DEFAULT 0,
  `zona_incidencias` tinyint(4) DEFAULT 0,
  `zona_personal` tinyint(4) DEFAULT 0,
  `zona_pedidos` tinyint(4) DEFAULT 0,
  `zona_inferior` tinyint(4) DEFAULT 0,
  `zona_mensajes` tinyint(4) DEFAULT 0,
  `zona_compras` int(11) DEFAULT 0,
  `zona_gabinete` tinyint(4) DEFAULT 0,
  `zona_corregir_pruebas` tinyint(4) DEFAULT 0,
  `zona_actividades` tinyint(4) DEFAULT 0,
  `nacionalidad` varchar(20) DEFAULT NULL,
  `NSS` varchar(30) NOT NULL,
  `codigo_nominaplus` int(11) DEFAULT NULL,
  `servicio_comun` int(11) DEFAULT 0,
  `medidas_complementarias` text DEFAULT NULL,
  `nuevo_pass` varchar(20) DEFAULT '',
  `old_pass` varchar(20) DEFAULT NULL,
  `SIP` varchar(50) DEFAULT '',
  PRIMARY KEY (`id_usuario`),
  KEY `idx_usuarios` (`id_centro`,`tipo_inscripcion`,`nivel_acceso`,`Nombre`,`Apellido1`,`Apellido2`),
  KEY `idx_alimentos` (`alimentos_verdura`,`alimentos_pollo`,`alimentos_ternera`,`alimentos_pescado`,`alimentos_huevo`,`alimentos_lentejas`,`alimentos_lacteos`,`alergias_lacteos`,`alergias_huevo`,`alergias_proternera`,`alergias_frutossecos`,`alergias_marisco`,`alergias_pielmelocoton`,`alergias_gluten`),
  KEY `idx_personal` (`tipo_inscripcion`,`nivel_acceso`),
  KEY `alergias_pescado` (`alergias_pescado`,`alergias_soja`,`alergias_sulfitos`),
  KEY `alergias_cerdo` (`alergias_cerdo`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `webcam` (
  `id_webcam` int(11) NOT NULL AUTO_INCREMENT,
  `id_centro` int(11) DEFAULT NULL,
  `id_aula` int(11) NOT NULL DEFAULT 0,
  `nombre_webcam` varchar(255) DEFAULT NULL,
  `url_webcam` varchar(255) DEFAULT NULL,
  `puerto_webcam` int(11) DEFAULT NULL,
  `tipo` int(11) NOT NULL DEFAULT 0,
  `hora_conexion` time DEFAULT NULL,
  `hora_desconexion` time DEFAULT NULL,
  `usuario_visor` varchar(50) NOT NULL DEFAULT '',
  `password_visor` varchar(50) NOT NULL DEFAULT '',
  `modelo` int(11) DEFAULT 0,
  `ip_local` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`id_webcam`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

SET FOREIGN_KEY_CHECKS=1;
