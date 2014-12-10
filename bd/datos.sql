-- CARGA DATOS PARA PRUEBAS TIENDA ON LINE --
-- Partimos de la BBDD vacía --

-- USUARIOS --

    insert into usuarios (nick, password)
                         values ('admin', md5('admin')),
                                ('roberto', md5('roberto')),
                                ('pepe', md5('pepe')),
                                ('juan', md5('juan')),
                                ('maria', md5('maria'));

-- TUITS --

    insert into tuits (usuario_id, mensaje, fecha)
                         values (1, 'Este es mi primer mensaje en este tuiter', '2014-12-01 18:59:27.435856'),
                                (1, 'Aquí no escribe nadie más, a ver si se anima la cosa', '2014-12-02 10:59:27.435856'),
                                (2, 'Al final me compraré la Nikon 5200', '2014-11-30 10:59:27.435856'),
                                (4, 'Este es mi primer mensaje en este tuiter', '2014-12-02 14:59:27.435856'),
                                (1, 'Aquí no escribe nadie más, a ver si se anima la cosa', '2014-11-09 12:59:27.435856'),
                                (2, 'Al final me compraré la Nikon 5200', '2014-11-30 15:59:27.435856'),
                                (2, 'Aquí no escribe nadie más, a ver si se anima la cosa', '2014-11-06 13:59:27.435856'),
                                (1, 'Al final me compraré la Nikon 5200', '2014-10-02 14:59:27.435856'),
                                (5, 'Este es mi primer mensaje en este tuiter', '2014-10-02 02:59:27.435856'),
                                (2, 'Aquí no escribe nadie más, a ver si se anima la cosa', '2014-11-02 15:59:27.435856'),
                                (1, 'Al final me compraré la Nikon 5200', '2014-12-01 03:59:27.435856'),
                                (3, 'Aquí no escribe nadie más, a ver si se anima la cosa', '2014-12-02 17:59:27.435856'),
                                (4, 'Al final me compraré la Nikon 5200', '2014-11-21 06:59:27.435856'),
                                (3, 'Este es mi primer mensaje en este tuiter', '2014-10-02 09:59:27.435856'),
                                (4, 'Aquí no escribe nadie más, a ver si se anima la cosa', '2014-12-02 21:59:27.435856'),
                                (3, 'Al final me compraré la Nikon 5200', '2014-10-22 10:59:27.435856'),
                                (3, 'A ver si podemos alquilar una casa en navidades', '2014-12-02 20:59:27.435856');

-- FOLLOWERS --

    insert into followers (id_fr, id_fd) values (1, 2);
    insert into followers (id_fr, id_fd) values (1, 3);
    insert into followers (id_fr, id_fd) values (1, 4);
    insert into followers (id_fr, id_fd) values (1, 5);
    insert into followers (id_fr, id_fd) values (2, 1);
    insert into followers (id_fr, id_fd) values (3, 2);
    insert into followers (id_fr, id_fd) values (4, 1);
    insert into followers (id_fr, id_fd) values (5, 1);
    insert into followers (id_fr, id_fd) values (5, 3);
    insert into followers (id_fr, id_fd) values (4, 2);
    insert into followers (id_fr, id_fd) values (3, 5);
    insert into followers (id_fr, id_fd) values (4, 3);
    insert into followers (id_fr, id_fd) values (6, 3);
    insert into followers (id_fr, id_fd) values (3, 6);
