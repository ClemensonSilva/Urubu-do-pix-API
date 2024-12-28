CREATE database IF NOT EXISTS urubuDoPix;

use urubuDoPix;

create table if not exists users (
    id int auto_increment primary key,
    user_name varchar(35) not null,
    user_balance real
);

create table if not exists transactions (
    id int auto_increment primary key,
    userId int not null,
    depositValue float not null,
    depositDate date not null,
    FOREIGN KEY (userId) REFERENCES users (id)
);

create table if not exists actions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    userId INT NOT NULL,
    transactionId INT NOT NULL,
    action VARCHAR(255) NOT NULL,
    FOREIGN KEY (userId) REFERENCES users (id),
    FOREIGN KEY (transactionId) REFERENCES transactions (id)
);
