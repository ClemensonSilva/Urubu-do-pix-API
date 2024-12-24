CREATE database IF NOT EXISTS urubuDoPix;
use urubuDoPix; 

create table if not exists users(
    id int auto_increment primary key,
    user_name varchar(35) not null,
    user_balance real
);

create table if not exists transactions(
    id int auto_increment primary key,
    userId int,
    depositValue float not null, 
    depositDate date not null, 
    FOREIGN KEY (userId) REFERENCES users(id)
);
