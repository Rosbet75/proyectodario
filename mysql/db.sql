drop database eneto;
create database eneto;
use eneto;

create table usuarios(nickname varchar(50) primary key not null, 
                    apellidoPaterno varchar(50) not null, 
                    apellidomaterno varchar(50) not null, 
                    nombre varchar(50) not null, 
                    sexo varchar(15) not null,
                    correo varchar(100) not null,
                    contra varchar(100) not null,
                    createdAt TIMESTAMP default CURRENT_TIMESTAMP,
                    updatedAt timestamp default current_timestamp on update CURRENT_TIMESTAMP);
create table privilegios(idPriv int primary key auto_increment,
                        rol varchar(30) not null,
                        createdAt TIMESTAMP default CURRENT_TIMESTAMP,
                        updatedAt timestamp default current_timestamp on update CURRENT_TIMESTAMP);
insert into privilegios values(null, "root", default, default);
insert into privilegios values(null, "admin", default, default);
create table cargoLaboral(idCargo int PRIMARY key AUTO_INCREMENT,
                        cargo varchar(50) not null,
                        sueldoHora int not null,
                        createdAt TIMESTAMP default CURRENT_TIMESTAMP,
                        updatedAt timestamp default current_timestamp on update CURRENT_TIMESTAMP);
insert into cargoLaboral values(null, "Jefe de comunicacioens", 300, default, default);
insert into cargoLaboral values(null, "Atencion de radio", 50, default, default);
insert into cargoLaboral values(null, "Chofer", 150, default, default);
create table empleados(curp varchar(18) not null primary key,
                    apellidoPaterno varchar(50) not null,
                    apellidoMaterno varchar(50) not null,
                    nombre varchar(50) not null,
                    idCargo int,
                    horarioEntrada time not null,
                    horarioSalida time not null,
                    createdAt TIMESTAMP default CURRENT_TIMESTAMP,
                    updatedAt timestamp default current_timestamp on update CURRENT_TIMESTAMP,
                    foreign key(idCargo) references cargoLaboral(idCargo) on delete cascade on update cascade);

create table admins(nickname varchar(50) primary key not null,
                    correo varchar(100) not null,
                    contra varchar(100) not null,
                    curp varchar(18) not null,
                    privilegios int not null,
                    createdAt TIMESTAMP default CURRENT_TIMESTAMP,
                    updatedAt timestamp default current_timestamp on update CURRENT_TIMESTAMP,
                    foreign key(curp) references empleados(curp) on delete cascade on update cascade,
                    Foreign Key(privilegios) REFERENCES privilegios(idPriv) on delete cascade on update cascade);
create table vehiculos(idMatricula varchar(8) primary key not null,
                    anoVehiculo varchar(4) not null,
                    modelo varchar(20) not null,
                    plazas int not null,
                    color varchar(20) not null,
                    disponible int not null,
                    createdAt TIMESTAMP default CURRENT_TIMESTAMP,
                    updatedAt timestamp default current_timestamp on update CURRENT_TIMESTAMP);

create table choferes(idChofer int primary key auto_increment,
                    curp varchar(18) not null unique,
                    num_licencia varchar(12) not null UNIQUE,
                    foreign Key(curp) references empleados(curp) on delete cascade on update cascade,
                    createdAt TIMESTAMP default CURRENT_TIMESTAMP,
                    updatedAt timestamp default current_timestamp on update CURRENT_TIMESTAMP);
create table viajes(idViaje int primary key auto_increment,
                idChofer int not null,
                idUsuario varchar(50) not null,
                destino varchar(50),
                costo_viaje int not null,
                cuota_ganancia int not null,
                idMatricula varchar(8) not null,
                foreign key(idChofer) references choferes(idChofer) on delete cascade on update cascade,
                foreign key(idUsuario) references usuarios(nickname) on delete cascade on update cascade,
                foreign key(idMatricula) references vehiculos(idMatricula) on delete cascade on update CASCADE,
                createdAt TIMESTAMP default CURRENT_TIMESTAMP,
                updatedAt timestamp default current_timestamp on update CURRENT_TIMESTAMP);
create table queja(idQueja int primary key auto_increment,
                idViaje int not null,
                comentarios varchar(500) not null,
                atendido BOOLEAN not null,
                foreign key(idViaje) references viajes(idViaje)on delete restrict on update cascade,
                createdAt TIMESTAMP default CURRENT_TIMESTAMP,
                updatedAt timestamp default current_timestamp on update CURRENT_TIMESTAMP);

create table reviews(idReview int primary key auto_increment,
                    idViaje int not null,
                    rating int not null,
                    comentarios varchar(500) not null,
                    foreign key (idViaje) references viajes(idViaje)on delete cascade on update cascade,
                    createdAt TIMESTAMP default CURRENT_TIMESTAMP,
                    updatedAt timestamp default current_timestamp on update CURRENT_TIMESTAMP);
create table tarjetas(numTar varchar(18) not null primary key,
                    fechaExp varchar(5) not null,
                    cvv varchar(4) not null);
create table pagos(idPago int primary key auto_increment,
                    idTarjeta varchar(18) not null,
                    monto int not null,
                    idViaje int not null,
                    estadoPago boolean not null,
                    foreign key(idTarjeta) references tarjetas(numTar) on delete cascade on update cascade,
                    foreign key(idViaje) references viajes(idViaje) on delete cascade on update cascade,
                    createdAt TIMESTAMP default CURRENT_TIMESTAMP,
                    updatedAt timestamp default current_timestamp on update CURRENT_TIMESTAMP);
create table conceptoCitas(idConcepto int primary key AUTO_INCREMENT,
                            concepto varchar(100) not null unique,
                            createdAt TIMESTAMP default CURRENT_TIMESTAMP,
                            updatedAt timestamp default current_timestamp on update CURRENT_TIMESTAMP);
create table conceptoSanciones(idSancion int primary key auto_increment,
                                sancion varchar(100) not null unique,
                                createdAt TIMESTAMP default CURRENT_TIMESTAMP,
                                updatedAt timestamp default current_timestamp on update CURRENT_TIMESTAMP);
create table citas(idCita int primary key auto_increment,
                    idChofer int not null,
                    fechaCita datetime not null,
                    concepto int not null,
                    comentarios varchar(1000) not null,
                    idQueja int,
                    sancion int,
                    foreign Key(idChofer) REFERENCES choferes(idChofer) on delete cascade on update cascade,
                    foreign key (concepto) references conceptoCitas(idConcepto) on delete cascade on update cascade,
                    foreign key (sancion) references conceptoSanciones(idSancion) on delete cascade on update cascade,
                    foreign key(idQueja) references queja(idQueja) on delete restrict on update cascade,
                    createdAt TIMESTAMP default CURRENT_TIMESTAMP,
                    updatedAt timestamp default current_timestamp on update CURRENT_TIMESTAMP);

create table atencionQueja(idAtencionQueja int primary key auto_increment,
                            idQueja int not null,
                            comentarios varchar(1000) not null,
                            foreign key(idQueja) references queja(idQueja) on delete cascade on update cascade,
                            createdAt TIMESTAMP default CURRENT_TIMESTAMP,
                            updatedAt timestamp default current_timestamp on update CURRENT_TIMESTAMP);



INSERT INTO empleados (curp, apellidoPaterno, apellidoMaterno, nombre, idCargo, horarioEntrada, horarioSalida) 
VALUES 
('CURP12345678901234', 'Gomez', 'Perez', 'Juan', 1, '08:00:00', '17:00:00'),
('CURP23456789012345', 'Lopez', 'Martinez', 'Maria', 2, '09:00:00', '18:00:00'),
('CURP34567890123456', 'Sanchez', 'Hernandez', 'Pedro', 3, '08:30:00', '17:30:00'),
('CURP45678901234567', 'Rodriguez', 'Garcia', 'Ana', 1, '07:45:00', '16:45:00'),
('CURP56789012345678', 'Torres', 'Vargas', 'Carlos', 2, '08:15:00', '17:15:00');


INSERT INTO empleados (curp, apellidoPaterno, apellidoMaterno, nombre, idCargo, horarioEntrada, horarioSalida)
VALUES
('ABC123456789012345', 'Pérez', 'González', 'Juan', 1, '08:00:00', '17:00:00'),
('XYZ987654321012345', 'López', 'Martínez', 'Ana', 2, '09:00:00', '18:00:00'),
('LMN654321098765432', 'Hernández', 'Ruiz', 'Carlos', 1, '08:30:00', '17:30:00'),
('OPQ432109876543210', 'Díaz', 'Rodríguez', 'María', 3, '07:45:00', '16:45:00'),
('RST321987654321098', 'Vargas', 'Sánchez', 'Luis', 2, '09:30:00', '18:30:00');

INSERT INTO choferes (curp, num_licencia)
VALUES
('ABC123456789012345', 'LIC123456789'),
('XYZ987654321012345', 'LIC987654321'),
('LMN654321098765432', 'LIC654321098'),
('OPQ432109876543210', 'LIC432109876'),
('RST321987654321098', 'LIC321987654');
INSERT INTO usuarios (nickname, apellidoPaterno, apellidomaterno, nombre, sexo, correo, contra)
VALUES
('user1', 'González', 'Pérez', 'Laura', 'Femenino', 'laura@mail.com', 'password123'),
('user2', 'Martínez', 'Hernández', 'Carlos', 'Masculino', 'carlos@mail.com', 'password123'),
('user3', 'López', 'García', 'Ana', 'Femenino', 'ana@mail.com', 'password123'),
('user4', 'Ramírez', 'Díaz', 'Jorge', 'Masculino', 'jorge@mail.com', 'password123'),
('user5', 'Fernández', 'Sánchez', 'María', 'Femenino', 'maria@mail.com', 'password123');
INSERT INTO vehiculos (idMatricula, anoVehiculo, modelo, plazas, color, disponible)
VALUES
('MAT12345', '2022', 'Toyota Corolla', 5, 'Blanco', 1),
('MAT54321', '2020', 'Nissan Sentra', 5, 'Negro', 1),
('MAT67890', '2021', 'Honda Civic', 5, 'Azul', 1),
('MAT98765', '2019', 'Chevrolet Spark', 4, 'Rojo', 0),
('MAT45678', '2023', 'Mazda 3', 5, 'Gris', 1),
('MAT11223', '2018', 'Ford Fiesta', 5, 'Plata', 1),
('MAT33445', '2023', 'Volkswagen Jetta', 5, 'Negro', 0),
('MAT55667', '2021', 'Hyundai Elantra', 5, 'Blanco', 1),
('MAT77889', '2020', 'Kia Forte', 5, 'Azul', 0),
('MAT99001', '2019', 'Renault Duster', 5, 'Verde', 1);


INSERT INTO viajes (idChofer, idUsuario, destino, costo_viaje, cuota_ganancia, idMatricula)
VALUES
(1, 'user1', 'Ciudad de México', 500, 100, 'MAT12345'), -- Viaje 1
(1, 'user2', 'Guadalajara', 800, 200, 'MAT12345'),     -- Viaje 2
(2, 'user3', 'Monterrey', 700, 150, 'MAT54321'),       -- Viaje 3
(3, 'user4', 'Cancún', 1200, 300, 'MAT67890'),         -- Viaje 4
(4, 'user5', 'Puebla', 400, 90, 'MAT98765');           -- Viaje 5

INSERT INTO queja (idViaje, comentarios, atendido)
VALUES
(1, 'El chofer llegó tarde al destino', FALSE),  -- Queja 1 (Viaje 1)
(2, 'El vehículo estaba en mal estado', TRUE),   -- Queja 2 (Viaje 2)
(3, 'El chofer fue grosero durante el viaje', FALSE), -- Queja 3 (Viaje 3)
(2, 'El viaje fue cancelado sin previo aviso', TRUE); -- Queja 4 (Viaje 5)


SELECT 
    c.idChofer,
    e.nombre AS nombreChofer,
    e.apellidoPaterno,
    e.apellidoMaterno,
    c.num_licencia,
    IFNULL(COUNT(v.idViaje), 0) AS totalViajes,
    IFNULL(SUM(CASE WHEN q.idQueja IS NOT NULL THEN 1 ELSE 0 END), 0) AS totalQuejas
FROM 
    choferes c
INNER JOIN 
    empleados e ON c.curp = e.curp
LEFT JOIN 
    viajes v ON c.idChofer = v.idChofer
LEFT JOIN 
    queja q ON v.idViaje = q.idViaje
GROUP BY 
    c.idChofer, e.nombre, e.apellidoPaterno, e.apellidoMaterno, c.num_licencia;