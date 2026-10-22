-- Estado civil indicado durante el alta del alumno.
-- La columna se mantiene en usuarios porque la ficha del alumno se almacena
-- en esa tabla y asi sigue disponible en todos sus pasos de edicion.
ALTER TABLE usuarios
    ADD COLUMN estado_civil VARCHAR(30) NOT NULL DEFAULT '' AFTER nacionalidad;
