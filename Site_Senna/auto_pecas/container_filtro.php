<div class="filtro-ativo-container">
    <?php
        $filtrosAtivos = [];

        if (!empty($_GET['categoria'])) {
            $filtrosAtivos[] = "<i>Categoria</i> ➜ " . htmlspecialchars(trim($_GET['categoria'], "'"));
        }

        if (!empty($_GET['marca'])) {
            $filtrosAtivos[] = "<i>Marca</i> ➜ " . htmlspecialchars(trim($_GET['marca'], "'"));
        }

        if (!empty($_GET['id_fornecedor'])) {
            // Buscar o nome do fornecedor pelo ID (opcional)
            $id_forn = (int)$_GET['id_fornecedor'];
            $stmt = $pdo->prepare("SELECT nome FROM fornecedor WHERE id_fornecedor = :id");
            $stmt->bindParam(":id", $id_forn, PDO::PARAM_INT);
            $stmt->execute();
            $nome_forn = $stmt->fetchColumn();
            $filtrosAtivos[] = "<i>Fornecedor</i> ➜ " . htmlspecialchars($nome_forn ?? 'Desconhecido');
        }

        if (isset($_GET['valor_min']) && isset($_GET['valor_max'])) {
            $min = htmlspecialchars($_GET['valor_min']);
            $max = htmlspecialchars($_GET['valor_max']);
            $filtrosAtivos[] = "Preço entre R$ {$min} e R$ {$max}";
        }

        if (!empty($filtrosAtivos)) {
            echo '<div class="filtros-ativos">';
            echo '<strong>Filtros aplicados:</strong> ' . implode(' | ', $filtrosAtivos);
            echo '</div>';
        }
    ?>
</div>