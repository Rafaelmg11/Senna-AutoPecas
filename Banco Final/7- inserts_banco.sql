
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


INSERT IGNORE INTO perfil (nome_perfil) VALUES
('Administrador'),
('Gerente'),
('Funcionário'),
('Almoxarife'),
('Cliente');

INSERT IGNORE INTO usuario (nome_usuario, email, senha, id_perfil, senha_temporaria, imagem_usuario) VALUES
('João Silva', 'joao.silva@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 0, NULL),
('Maria Santos', 'maria.santos@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, 0, NULL),
('Pedro Costa', 'pedro.costa@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 1, NULL),
('Ana Oliveira', 'ana.oliveira@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 4, 0, NULL),
('Carlos Pereira', 'carlos.pereira@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 5, 1, NULL),
('Juliana Rodrigues', 'juliana.rodrigues@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 0, NULL),
('Fernando Souza', 'fernando.souza@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, 0, NULL),
('Amanda Lima', 'amanda.lima@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 1, NULL),
('Ricardo Alves', 'ricardo.alves@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 4, 0, NULL),
('Patrícia Costa', 'patricia.costa@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 5, 0, NULL),
('Bruno Martins', 'bruno.martins@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 1, NULL),
('Lúcia Ferreira', 'lucia.ferreira@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, 0, NULL),
('Eduardo Rocha', 'eduardo.rocha@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 0, NULL),
('Sandra Nunes', 'sandra.nunes@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 4, 1, NULL),
('Paulo Mendes', 'paulo.mendes@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 5, 0, NULL),
('Tânia Carvalho', 'tania.carvalho@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 0, NULL);
('Shawn', 'shawnmendezes@gmail.com', '$2y$10$Mp9R5OsQKF9os0gIEF7CUOV9cxU3p.jMnETf1tjMHo6O3DQNRQA.O', 1, 0, NULL),
('Joycee', 'joycest@gmail.com', '$2y$10$W/xFmwg2joSXYM.RKixGWONkV6LgCr2N/dm6wqWTPk2YEA62Q3UtO', 2, 0, NULL),
('ViniDiesel', 'vinidiesel@gmail.com', '$2y$10$z4sEuxjSudzg3bZZ7vSBX..w/oXdqxO7nATf49rQM/RVpa9rK.Jem', 4, 0, NULL),
('Will', 'bezerrodbz@gmail.com', '$2y$10$7St1Yx.ezzAAHfECp9WElew1VERVKqiD9z/G/WGE8uiyDlA3isI06', 3, 0, NULL);

INSERT IGNORE INTO cliente (id_usuario, nome_cliente, endereco, cpf, data_nascimento, telefone) VALUES
(2, 'Maria Santos', 'Rua das Flores, 123 - Centro', '123.456.789-00', '1985-05-15', '(11) 99999-1111'),
(4, 'Ana Oliveira', 'Av. Paulista, 1000 - Bela Vista', '234.567.890-11', '1990-08-22', '(11) 98888-2222'),
(6, 'Juliana Rodrigues', 'Rua Augusta, 500 - Consolação', '345.678.901-22', '1988-12-05', '(11) 97777-3333'),
(8, 'Amanda Lima', 'Alameda Santos, 200 - Jardins', '456.789.012-33', '1992-03-30', '(11) 96666-4444'),
(10, 'Patrícia Costa', 'Rua Oscar Freire, 800 - Pinheiros', '567.890.123-44', '1987-07-18', '(11) 95555-5555'),
(12, 'Lúcia Ferreira', 'Av. Brigadeiro Faria Lima, 1500 - Itaim', '678.901.234-55', '1995-01-25', '(11) 94444-6666'),
(14, 'Sandra Nunes', 'Rua Haddock Lobo, 900 - Cerqueira César', '789.012.345-66', '1983-11-12', '(11) 93333-7777'),
(16, 'Tânia Carvalho', 'Av. Rebouças, 700 - Pinheiros', '890.123.456-77', '1991-09-08', '(11) 92222-8888'),
(3, 'Pedro Costa', 'Rua da Consolação, 300 - Centro', '901.234.567-88', '1986-04-17', '(11) 91111-9999'),
(5, 'Carlos Pereira', 'Alameda Jaú, 400 - Jardim Paulista', '012.345.678-99', '1993-06-20', '(11) 90000-0000'),
(7, 'Fernando Souza', 'Rua Bela Cintra, 600 - Consolação', '102.345.678-90', '1989-02-14', '(11) 91234-5678'),
(9, 'Ricardo Alves', 'Av. Europa, 1100 - Jardim Europa', '201.456.789-01', '1984-10-31', '(11) 92345-6789'),
(11, 'Bruno Martins', 'Rua Estados Unidos, 1200 - Jardim América', '301.567.890-12', '1994-07-07', '(11) 93456-7890'),
(13, 'Eduardo Rocha', 'Av. São Gabriel, 1300 - Itaim Bibi', '401.678.901-23', '1982-12-25', '(11) 94567-8901'),
(15, 'Paulo Mendes', 'Rua Joaquim Floriano, 1400 - Itaim', '501.789.012-34', '1996-08-11', '(11) 95678-9012'),
(1, 'João Silva', 'Rua Colorado, 1500 - Brooklin', '601.890.123-45', '1980-05-01', '(11) 96789-0123');

INSERT IGNORE INTO funcionario (id_usuario, nome_funcionario, cargo, salario, endereco, imagem, cpf, telefone, data_admissao, data_nascimento) VALUES
(1, 'João Silva', 'Gerente', 6500.00, 'Rua Colorado, 1500 - Brooklin', NULL, '601.890.123-45', '(11) 96789-0123', '2020-03-15', '1980-05-01'),
(3, 'Pedro Costa', 'Vendedor', 2500.00, 'Rua da Consolação, 300 - Centro', NULL, '901.234.567-88', '(11) 91111-9999', '2021-06-20', '1986-04-17'),
(5, 'Carlos Pereira', 'Estoquista', 2200.00, 'Alameda Jaú, 400 - Jardim Paulista', NULL, '012.345.678-99', '(11) 90000-0000', '2022-01-10', '1993-06-20'),
(7, 'Fernando Souza', 'Vendedor', 2500.00, 'Rua Bela Cintra, 600 - Consolação', NULL, '102.345.678-90', '(11) 91234-5678', '2021-08-12', '1989-02-14'),
(9, 'Ricardo Alves', 'Financeiro', 3200.00, 'Av. Europa, 1100 - Jardim Europa', NULL, '201.456.789-01', '(11) 92345-6789', '2020-11-05', '1984-10-31'),
(11, 'Bruno Martins', 'TI', 4200.00, 'Rua Estados Unidos, 1200 - Jardim América', NULL, '301.567.890-12', '(11) 93456-7890', '2019-09-18', '1994-07-07'),
(13, 'Eduardo Rocha', 'Supervisor', 3800.00, 'Av. São Gabriel, 1300 - Itaim Bibi', NULL, '401.678.901-23', '(11) 94567-8901', '2022-03-22', '1982-12-25'),
(15, 'Paulo Mendes', 'Vendedor', 2500.00, 'Rua Joaquim Floriano, 1400 - Itaim', NULL, '501.789.012-34', '(11) 95678-9012', '2023-02-14', '1996-08-11'),
(2, 'Maria Santos', 'RH', 3500.00, 'Rua das Flores, 123 - Centro', NULL, '123.456.789-00', '(11) 99999-1111', '2020-05-10', '1985-05-15'),
(4, 'Ana Oliveira', 'Marketing', 3100.00, 'Av. Paulista, 1000 - Bela Vista', NULL, '234.567.890-11', '(11) 98888-2222', '2021-02-18', '1990-08-22'),
(6, 'Juliana Rodrigues', 'Vendedora', 2500.00, 'Rua Augusta, 500 - Consolação', NULL, '345.678.901-22', '(11) 97777-3333', '2022-07-30', '1988-12-05'),
(8, 'Amanda Lima', 'Atendimento', 2300.00, 'Alameda Santos, 200 - Jardins', NULL, '456.789.012-33', '(11) 96666-4444', '2023-01-08', '1992-03-30'),
(10, 'Patrícia Costa', 'Financeiro', 3200.00, 'Rua Oscar Freire, 800 - Pinheiros', NULL, '567.890.123-44', '(11) 95555-5555', '2020-09-25', '1987-07-18'),
(12, 'Lúcia Ferreira', 'Vendedora', 2500.00, 'Av. Brigadeiro Faria Lima, 1500 - Itaim', NULL, '678.901.234-55', '(11) 94444-6666', '2021-11-11', '1995-01-25'),
(14, 'Sandra Nunes', 'Coord. Vendas', 4100.00, 'Rua Haddock Lobo, 900 - Cerqueira César', NULL, '789.012.345-66', '(11) 93333-7777', '2019-12-01', '1983-11-12'),
(16, 'Tânia Carvalho', 'Gerente Adm', 6200.00, 'Av. Rebouças, 700 - Pinheiros', NULL, '890.123.456-77', '(11) 92222-8888', '2020-04-05', '1991-09-08');

INSERT IGNORE INTO carrinho (id_usuario, data_criacao) VALUES
(5, '2024-09-01 10:15:00'),
(10, '2024-09-01 11:20:00'),
(15, '2024-09-01 14:35:00');

INSERT IGNORE INTO carrinho_item (id_carrinho, id_peca, quantidade) VALUES
(1, 3, 1),
(1, 5, 2),
(2, 7, 1),
(3, 2, 1),
(3, 8, 1),
(2, 10, 3),
(1, 12, 2),
(2, 15, 1),
(3, 1, 1),
(2, 4, 2),
(2, 6, 1),
(1, 9, 1),
(1, 11, 2),
(3, 13, 1),
(3, 14, 1),
(2, 16, 3);

INSERT IGNORE INTO compra (id_usuario, id_cliente, id_funcionario, data_compra, tipo_pagamento, valor_total) VALUES
(2, 1, 3, '2024-08-25 09:15:00', 'cartão_credito', 770.00),
(4, 2, 7, '2024-08-26 10:30:00', 'pix', 520.00),
(6, 3, 11, '2024-08-27 11:45:00', 'cartão_debito', 610.00),
(8, 4, 15, '2024-08-28 14:20:00', 'dinheiro', 360.00),
(10, 5, 3, '2024-08-29 15:35:00', 'cartão_credito', 300.00),
(12, 6, 7, '2024-08-30 16:50:00', 'pix', 670.00),
(14, 7, 11, '2024-08-31 09:05:00', 'cartão_debito', 1250.00),
(16, 8, 15, '2024-09-01 10:15:00', 'dinheiro', 230.00),
(3, 9, 3, '2024-09-02 11:25:00', 'cartão_credito', 890.00),
(5, 10, 7, '2024-09-03 13:40:00', 'pix', 180.00),
(7, 11, 11, '2024-09-04 14:55:00', 'cartão_debito', 980.00),
(9, 12, 15, '2024-09-05 16:10:00', 'dinheiro', 150.00),
(11, 13, 3, '2024-09-06 08:20:00', 'cartão_credito', 390.00),
(13, 14, 7, '2024-09-07 09:35:00', 'pix', 120.00),
(15, 15, 11, '2024-09-08 10:50:00', 'cartão_debito', 450.00),
(1, 16, 15, '2024-09-09 12:05:00', 'dinheiro', 280.00);

INSERT IGNORE INTO compra_item (id_compra, id_peca, quantidade, valor_unitario) VALUES
(1, 3, 1, 320.00),
(1, 5, 2, 35.00),
(2, 7, 1, 520.00),
(3, 2, 1, 280.00),
(3, 8, 1, 210.00),
(4, 10, 3, 180.00),
(5, 12, 2, 150.00),
(6, 15, 1, 670.00),
(7, 1, 1, 450.00),
(8, 4, 2, 380.00),
(9, 6, 1, 890.00),
(10, 9, 1, 1250.00),
(11, 11, 2, 980.00),
(12, 13, 1, 390.00),
(13, 14, 1, 120.00),
(14, 16, 3, 230.00);

INSERT IGNORE INTO favorito (id_usuario, id_peca, data_adicionado) VALUES
(2, 5, '2024-08-20 10:00:00'),
(4, 12, '2024-08-21 11:15:00'),
(6, 3, '2024-08-22 12:30:00'),
(8, 7, '2024-08-23 13:45:00'),
(10, 1, '2024-08-24 14:00:00'),
(12, 9, '2024-08-25 15:15:00'),
(14, 14, '2024-08-26 16:30:00'),
(16, 6, '2024-08-27 17:45:00'),
(3, 11, '2024-08-28 18:00:00'),
(5, 2, '2024-08-29 19:15:00'),
(7, 8, '2024-08-30 20:30:00'),
(9, 15, '2024-08-31 21:45:00'),
(11, 4, '2024-09-01 22:00:00'),
(13, 10, '2024-09-02 23:15:00'),
(15, 13, '2024-09-03 08:30:00'),
(1, 16, '2024-09-04 09:45:00');

INSERT IGNORE INTO avaliacao (id_usuario, id_peca, avaliacao) VALUES
(5, 5, 5),
(5, 12, 4),
(5, 3, 5),
(10, 7, 3),
(10, 1, 4),
(15, 9, 5),
(15, 14, 2),
(10, 6, 5),
(5, 11, 4),
(15, 2, 3),
(10, 8, 5),
(5, 15, 4),
(15, 4, 5),
(10, 10, 3),
(5, 13, 5),
(5, 16, 4);

COMMIT;