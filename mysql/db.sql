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
                    Foreign Key (curp) REFERENCES empleados(curp) on delete cascade on update cascade);