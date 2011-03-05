-- Fundar
-- By Mauro Parra-Miranda <mauropm@gmail.com>

-- Create the db
-- create database api_fundar1; 

-- this is the kind of stuff I need. Normalized, please. 

create table if not exists admins ( 
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    password CHAR(40) NOT NULL,
    mail CHAR(80) NOT NULL
);

create table if not exists users ( 
       id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
       mail CHAR(120) NOT NULL, 
       nickname CHAR(64), 
       apikey CHAR(32) NOT NULL 
       );

create table if not exists albums (
       apikey CHAR(32) NOT NULL, 
       sticker INT NOT NULL,
       copies INT NOT NULL, 
       team INT NOT NULL, 
       eid INT 
       );

create table if not exists stickers ( 
       id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
       player CHAR(120) NOT NULL,       
       team INT NOT NULL,    		  
       image CHAR(128) NOT NULL,
       published INT NOT NULL, 
       texto CHAR(140),
       num INT NOT NULL
       );

create table if not exists teams (
       id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
       country CHAR(32) NOT NULL, 
       flag CHAR(128) NOT NULL,
       texto CHAR(140)
       );

create table if not exists sobres (
       eid INT NOT NULL, 
       leido TINYINT(1) NOT NULL, 
       api CHAR(32) NOT NULL
);     
       			
       
