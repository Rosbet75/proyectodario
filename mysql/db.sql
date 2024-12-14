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
create table privilegios(idPriv int not null primary key auto_increment,
                        rol varchar(30) not null,
                        createdAt TIMESTAMP default CURRENT_TIMESTAMP,
                        updatedAt timestamp default current_timestamp on update CURRENT_TIMESTAMP);
create table cargoLaboral(idCargo varchar(50) not null,
                        sueldoHora int not null);
create table empleados(curp varchar(18) not null primary key,
                    apellidoPaterno varchar(50) not null,
                    apellidoMaterno varchar(50) not null,
                    nombre varchar(50) not null,
                    idCargo varchar(50) not null,
                    horarioEntrada time not null,
                    horarioSalida time not null,
                    createdAt TIMESTAMP default CURRENT_TIMESTAMP,
                    updatedAt timestamp default current_timestamp on update CURRENT_TIMESTAMP,
                    foreign key(idCargo) references cargoLaboral(idCargo) on delete set null on update cascade);

create table admins(nickname varchar(50) primary key not null,
                    correo varchar(100) not null,
                    contra varchar(100) not null,
                    curp varchar(18) not null,
                    privilegios int not null,
                    createdAt TIMESTAMP default CURRENT_TIMESTAMP,
                    updatedAt timestamp default current_timestamp on update CURRENT_TIMESTAMP,
                    foreign key(curp) references empleados(curp) on delete cascade on update cascade,
                    Foreign Key(privilegios) REFERENCES privilegios(idPriv) on delete set null on update cascade);
create table vehiculos(idMatricula varchar(8) primary key not null,
                    anoVehiculo varchar(4) not null,
                    modelo varchar(20) not null,
                    plazas int not null,
                    color varchar(20) not null,
                    disponible int not null,
                    createdAt TIMESTAMP default CURRENT_TIMESTAMP,
                    updatedAt timestamp default current_timestamp on update CURRENT_TIMESTAMP);
create table choferes(idChofer int not null primary key auto_increment,
                    curp varchar(18) not null unique,
                    num_licencia varchar(12) not null UNIQUE
                    Foreign Key (curp) REFERENCES empleados(curp) on delete no action on update cascade);
create table viajes(idViaje int not null primary key auto_increment,
                idChofer varchar(18) not null,
                idUsuario varchar(50) not null,
                destino varchar(50),
                costo_viaje int not null,
                cuota_ganancia int not null,
                idMatricula varchar(8) not null,
                foreign key idChofer references choferes(idChofer) on delete no action on update cascade,
                foreign key idUsuario references usuarios(nickname) on delete no action on update cascade,
                foreign key idMatricula references vehiculos(idMatricula) on delete no action on update CASCADE,
                createdAt TIMESTAMP default CURRENT_TIMESTAMP,
                updatedAt timestamp default current_timestamp on update CURRENT_TIMESTAMP);
create table queja(idQueja int not null primary key auto_increment,
                idViaje int not null,
                comentarios varchar(500) not null,
                atendido BOOLEAN not null,
                foreign key(idViaje) references viajes(idViaje),
                createdAt TIMESTAMP default CURRENT_TIMESTAMP,
                updatedAt timestamp default current_timestamp on update CURRENT_TIMESTAMP);

create table reviews(idReview intnot null primary key auto_increment,
                    idViaje int not null,
                    rating int not null,
                    comentarios varchar(500) not null,
                    foreign key (idViaje) references viajes(idViaje)on delete cascade on update cascade,
                    createdAt TIMESTAMP default CURRENT_TIMESTAMP,
                    updatedAt timestamp default current_timestamp on update CURRENT_TIMESTAMP);
create table tarjetas(numTar varchar(18) not null primary key,
                    fechaExp varchar(5) not null,
                    cvv varchar(4) not null)
create table pagos(idPago int not null primary key auto_increment,
                    idTarjeta varchar(18) not null,
                    monto int not null,
                    idViaje int not null,
                    estadoPago boolean not null,
                    foreign key(idTarjeta) references tarjetas(numTar) on delete no action on update cascade,
                    foreign key(idViaje) references viajes(idViaje) on delete cascade on update cascade,
                    createdAt TIMESTAMP default CURRENT_TIMESTAMP,
                    updatedAt timestamp default current_timestamp on update CURRENT_TIMESTAMP);
create table conceptoCitas(idConcepto int not null primary key AUTO_INCREMENT,
                            concepto varchar(100) not null unique,
                            createdAt TIMESTAMP default CURRENT_TIMESTAMP,
                            updatedAt timestamp default current_timestamp on update CURRENT_TIMESTAMP);
create table conceptoSanciones(idSancion int not null primary key auto_increment,
                                sancion varchar(100) not null unique,
                                createdAt TIMESTAMP default CURRENT_TIMESTAMP,
                                updatedAt timestamp default current_timestamp on update CURRENT_TIMESTAMP);
create table citas(idCita int not null primary key auto_increment,
                    idChofer int not null,
                    fechaCita datetime not null,
                    concepto int not null,
                    comentarios varchar(1000) not null,
                    sancion int,
                    Foreign Key (idChofer) REFERENCES (choferes(idChofer)) on delete cascade on update cascade,
                    foreign key concepto references conceptoCitas(idConcepto) on delete set null on update cascade,
                    foreign key sancion references conceptoSanciones(idSancion) on delete set null on update cascade,
                    createdAt TIMESTAMP default CURRENT_TIMESTAMP,
                    updatedAt timestamp default current_timestamp on update CURRENT_TIMESTAMP);

create table atencionQueja(idAtencionQueja int not null primary key auto_increment,
                            idQueja int not null,
                            comentarios varchar(1000) not null,
                            idCita int,
                            foreign key idQueja references queja(idQueja) on delete cascade on update cascade,
                            foreign key idCita references citas(idCita) on delete no action on update cascade,
                            createdAt TIMESTAMP default CURRENT_TIMESTAMP,
                            updatedAt timestamp default current_timestamp on update CURRENT_TIMESTAMP);

