/* Вставка данных*/
/*Таблица categories*/
INSERT INTO yeticave_1.categories (label,name) VALUES 
('boards'		,'Доски и лыжи'),
('attachment'	,'Крепления'),
('boots'		,'Ботинки'),
('clothing'		,'Одежда'),
('tools'		,'Инструменты'),
('other'		,'Разное');
/*Удаление строки*/
/*DELETE FROM `yeticave_1`.`categories`;*/

/* Вставка данных*/
/*Таблица users*/
/*- пароль в виде хэша MD5 - 36 символовalter
- вставит ИД, хоть они и генерируются, но есть ссылки (ИД) в таблице лотов*/
INSERT INTO yeticave_1.users (id,email, name, password, contact_info, created_at) VALUES
(11,'Ivan@example.com', 'Иван', '202cb962ac59075b964b07152d234b70', '+1-800-555-1234', '2025-02-26 09:15:00'),
(12,'Alex@example.com', 'Алексей', '5baa61e4c9b93f3f0682250b6cf8331b', '+1-800-555-5678', '2025-02-25 14:22:00'),
(13,'Piter@example.com', 'Питер', '098f6bcd4621d373cade4e832627b4f6', '+1-800-555-9101', '2025-02-24 17:45:00'),
(14,'Andrey@example.com', 'Андрей', 'a2c0e8c4ea29d3d4c5987f7e798b750d', '+1-800-555-1122', '2025-02-23 11:30:00'),
(15,'Nikita@example.com', 'Nikita', 'd41d8cd98f00b204e9800998ecf8427e', '+1-800-555-3344', '2025-02-22 08:00:00');

/*Удаление всех данных из таблицы*/
/*SET SQL_SAFE_UPDATES = 0;
DELETE FROM yeticave_1.lots;*/

/* Вставка данных*/
/*Таблица lots*/
/*- вставит ИД, хоть они и генерируются, но есть ссылки (ИД) в таблице ставок*/
INSERT INTO yeticave_1.lots(id,created_at, name, description, image_path, start_price, expiration_date, price_step, author_id, winner_id, category_id) VALUES
(1,'2025-01-01', '2014 Rossignol District Snowboard', 'здесь какое-то описание','img/lot-1.jpg',1099,'2025-04-30',50,11,NULL,1),
(2,'2025-01-01', 'DC Ply Mens 2016/2017 Snowboard', 'здесь какое-то описание','img/lot-2.jpg',159999,'2025-04-30',1000,11,NULL,1),
(3,'2025-02-01', 'Крепления Union Contact Pro 2015 года размер L/XL', 'здесь какое-то описание','img/lot-3.jpg',8000,'2025-04-30',500,12,NULL,2),
(4,'2025-01-04', 'Ботинки для сноуборда DC Mutiny Charocal', 'здесь какое-то описание','img/lot-4.jpg',10999,'2025-04-30',500,12,NULL,3), 
(5,'2025-01-03', 'Куртка для сноуборда DC Mutiny Charocall', 'здесь какое-то описание','img/lot-5.jpg',7500,'2025-04-30',500,12,13,4), 
(6,'2025-01-02', 'Маска Oakley Canopy', 'здесь какое-то описание','img/lot-6.jpg',5400,'2025-04-30',500,12,15,6); 

/*Таблица bets*/
INSERT INTO yeticave_1.bets (date,price,user_id,lot_id) VALUES
('2025-02-25 23:10',5400,14,6),
('2025-02-25 23:15',5900,15,6);
INSERT INTO yeticave_1.bets (date,price,user_id,lot_id) VALUES
('2025-02-24',7500,14,5),
('2025-02-24 20:18',8000,15,5),
('2025-02-24 22:10',8500,13,5);
INSERT INTO yeticave_1.bets (date,price,user_id,lot_id) VALUES
('2025-02-24',159999,14,2),
('2025-02-24 20:18',160999,15,2),
('2025-02-24 22:10',161999,13,2);

/*удаление всех данных
DELETE FROM `yeticave_1`.`bets`;
DELETE FROM `yeticave_1`.`lots`;
DELETE FROM `yeticave_1`.`categories`;
DELETE FROM `yeticave_1`.`users`;
*/


/*1. Получить все категории*/
SELECT id, name FROM yeticave_1.categories;

/*2. получить самые новые, ОТКРЫТЫЕ лоты. 
	Каждый лот должен включать:
    1) название
    2) стартовую цену
    3) ссылку на изображение,
	4) цену -- максимальная ставка. если нет - первоначальная цена
    5) название категории;
*/
SELECT 
	lots.id as lot_ad,
	lots.name as lot_name,
	lots.start_price as start_price,
	lots.image_path as image_path,
    categories.name as category,
    IFNULL(prices.price,lots.start_price) as price -- можно сделать 'prices.price + lots.price_step', если нужна последняя ставка + шаг
FROM yeticave_1.lots as lots
	LEFT JOIN yeticave_1.categories as categories ON
		lots.category_id = categories.id
	LEFT JOIN (SELECT
		bets.lot_id as lot_id,
        max(bets.price) as price
        FROM yeticave_1.bets as bets
        GROUP BY bets.lot_id) as prices ON 
        lots.id = prices.lot_id
WHERE
	lots.winner_id IS NULL
order by created_at DESC;

/*3. показать лот по его ID. Получите также название категории, к которой принадлежит лот;*/
SET @lot_id = 3;
SELECT 
	lots.*,
    categories.name as category_name
FROM yeticave_1.lots as lots
LEFT JOIN yeticave_1.categories as categories ON
	lots.category_id = categories.id
WHERE
	lots.id = @lot_id;
    
/*4. обновить название лота по его идентификатору;*/
SET @lot_id = 3;
SET @new_name = 'тестовое название';
UPDATE yeticave_1.lots as lots SET name = @new_name
	WHERE lots.id = @lot_id;
    
/*5. получить список ставок для лота по его идентификатору с сортировкой по дате.*/
SET @lot_id = 2;
SELECT
	bets.*
FROM
	yeticave_1.bets as bets
WHERE
	bets.lot_id = @lot_id
ORDER BY bets.date desc


