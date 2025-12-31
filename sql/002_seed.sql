USE receipt_app;

INSERT INTO users (username, password_hash, role) VALUES
('admin', '$2y$10$gjWjU0YJIUKGwEuITdSb9eRA8T1IvotEBZLr6I/TYgY6GAnrJjNXO', 'admin');

INSERT INTO code_groups (code, name) VALUES
('delivery_method', '배송방법'),
('pot_size', '화분사이즈'),
('pot_type', '화분종류'),
('pot_color', '화분색상'),
('plant_size', '식물사이즈'),
('plant_type', '식물종류'),
('delivery_time', '배달시간');

INSERT INTO codes (group_code, code, name, sort_order) VALUES
('delivery_method', 'standard', '일반배송', 1),
('delivery_method', 'express', '퀵/특송', 2),
('pot_size', 's', '소', 1),
('pot_size', 'm', '중', 2),
('pot_size', 'l', '대', 3),
('pot_type', 'ceramic', '도자기', 1),
('pot_type', 'plastic', '플라스틱', 2),
('pot_color', 'white', '흰색', 1),
('pot_color', 'black', '검정', 2),
('plant_size', 'small', '소형', 1),
('plant_size', 'big', '대형', 2),
('plant_type', 'ficus', '고무나무', 1),
('plant_type', 'monstera', '몬스테라', 2),
('delivery_time', 'morning', '오전', 1),
('delivery_time', 'afternoon', '오후', 2),
('delivery_time', 'evening', '저녁', 3);

INSERT INTO accessory_items (name) VALUES
('리본(빨강)'),
('리본(파랑)'),
('카드메세지'),
('쇼핑백'),
('포장지'),
('보호캡'),
('스티커'),
('완충재'),
('화분받침'),
('사진촬영'),
('메모지'),
('배송메모라벨');
