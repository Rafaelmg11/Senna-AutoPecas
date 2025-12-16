<?php

if($_SERVER['REQUEST_METHOD'] === 'POST'){
  $cep_origem = "89219510"; //CEP fixo do Senai Norte
  $cep_destino = preg_replace('/\D/', '', $_POST['cep_destino']); // Só números
  $altura = (int) $_POST['altura'];
  $largura = (int) $_POST['largura'];
  $comprimento = (int) $_POST['comprimento'];
  $peso = (float) $_POST['peso'];

  $curl = curl_init();

  $data = [
      "from" => ["postal_code" => "89228450"],
      "to" => ["postal_code" => "13274465"],
      "package" => [
          "height" => 4,
          "width" => 12,
          "length" => 17,
          "weight" => 0.3
      ]
  ];

  curl_setopt_array($curl, [
    CURLOPT_URL => "https://www.melhorenvio.com.br/api/v2/me/shipment/calculate",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($data),
    CURLOPT_HTTPHEADER => [
        "Accept: application/json",
        "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiNWNhNzhiMWY0MTQ5ZjJkMDRlZDQwMTY1N2RlZjkyMWY2NjRiM2Q4ZDJmMzFhNDIzYzEzYjdiYzM1NzdlZjQyNTljNGIyYTc1MjI2YWJjZmYiLCJpYXQiOjE3NTY5NjMxMjEuNzU5ODE5LCJuYmYiOjE3NTY5NjMxMjEuNzU5ODIsImV4cCI6MTc4ODQ5OTEyMS43NDc4NTUsInN1YiI6IjllZjA1Zjc1LTQ1MmYtNGUyYy04ZDEwLTdhYmNmMjQ0MmRiOCIsInNjb3BlcyI6WyJzaGlwcGluZy1jYWxjdWxhdGUiXX0.v9BrK85rDSgDt6x8IiPyIVeBNqUWmznryWlQpxuP-ghb3vY5p1xWEGZxfdwaXjsr3UL5deXvHwMLBVtkLfb8RqZEQqgNw8B2eyvn-WZ6Xus8xG9mXp-dE1GaKEqr7mmB5hxfT_uQGgMQtBwVBnuucgFry9LkBb-KCLP4Wtpp7H36UYfju21s61tQHhW0jtDhjxvtLXl21tVILagD94nXDkohIKUPElphASkuBdjw44sAteX0ED6BqWFPA_XV6tltaoWf5dyKnyJ01ETlttdYfuhXOBpoMtzCeSpz_46kgBlIKtZ69yWUz3liG2DNxrljGLDve_o1-ZTFu8iN6aPnkIL1Z0E6AMBPh30x1v4hSOuHOAcq7IOX5DYs3K47JFBJsBZoALvvp_RBIhnEEjPxDESe9SOQSB0NNzEEgL7bvSdbdh-h7r-u6ht7xyhSKhWMZfOsMpXhNVJrmO-n_g3dEhi-nPWwcLXAnzcrtTfOEwYuW915OwII_cmVczH8ZlXgW6J8JaKwwTUl3QkDL9bDV-n0blWYzjwHhJ0M1s4BHbbvzTl5nEQ0OeMxKwKwWTFQJbXFQ3JqJ9gHP_WeUNRDK6QAak8RAGRLRufv0vNqb7XSFGPBAl9-xIb7ubbr-RuXQM4ujyU5WxxtPOiXaDkzPJmEEUYMMPzw64CpBjIe0Q4", 
        "Content-Type: application/json",
        "User-Agent: Aplicação almeidarafaelmagalhaes@gmail.com"
    ],
    CURLOPT_SSL_VERIFYPEER => false, // ignora verificação SSL
    CURLOPT_SSL_VERIFYHOST => false, // ignora verificação do host
  ]);

  $response = curl_exec($curl);
  $error = curl_error($curl);
  curl_close($curl);

  if ($error) {
      echo "Erro cURL: $error";
  } else {
      $result = json_decode($response, true); // transforma JSON em array
      echo "<pre>";
      print_r($result); // exibe de forma legível
      echo "</pre>";
  }

}else{
  echo "Acesso inválido";
}

?>
