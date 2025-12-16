DELIMITER $$

CREATE TRIGGER diminuir_quantidade_produto
AFTER INSERT ON compra_item
FOR EACH ROW
BEGIN
    UPDATE peca
    SET qtde_estoque = qtde_estoque - NEW.quantidade
    WHERE id_peca = NEW.id_peca;
END$$

DELIMITER ;