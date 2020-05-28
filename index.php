<?php
$token = '';
$url = 'https://api.codenation.dev/v1/challenge/dev-ps/generate-data?token=';

function callAPI($method, $url, $data)
{
    $curl = curl_init();
    switch ($method) {
        case "POST":
            curl_setopt($curl, CURLOPT_POST, true);
            if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            break;
        case "PUT":
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
            if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            break;
        default:
            if ($data)
                $url = sprintf("%s?%s", $url, http_build_query($data));
    }
    // OPTIONS:
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'APIKEY: 111111111111111111111',
        'Content-Type: application/json',
    ));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    // EXECUTE:
    $result = curl_exec($curl);
    if (!$result) {
        die("Connection Failure");
    }
    curl_close($curl);
    return $result;
}

function caesarCypher($json_cifrado)
{
    //ASCII 97 - a ate 122 - z
    $passo = $json_cifrado["numero_casas"];
    $cifra = strtolower($json_cifrado["cifrado"]);
    $json_cifrado["decifrado"] = "";
    $json_cifrado["resumo_criptografico"] = "";

    for ($i = 0; $i < strlen($cifra); $i++) {
        $ascii_char = ord($cifra[$i]);

        if ($ascii_char < 0 || $ascii_char > 127) {
            echo "Caracter invalido ou nao disponivel na tabela ASCII";
        } else if ($ascii_char < 97 || $ascii_char > 122) {
            $json_cifrado["decifrado"] .= chr($ascii_char);
        } else if ($ascii_char - $passo < 97) {
            $novo_passo = $passo - ($ascii_char - 97) - 1;
            $json_cifrado["decifrado"] .= chr(122 - $novo_passo);
        } else {
            $json_cifrado["decifrado"] .= chr($ascii_char - $passo);
        }
    }

    $json_cifrado["resumo_criptografico"] = sha1($json_cifrado["decifrado"]);
    $resposta = json_encode($json_cifrado);
    return $resposta;
}

// $get_data = callAPI('GET', $url . $token, false);
// file_put_contents('answer.json', $get_data);
$json_data = file_get_contents('answer.json');
$response = json_decode($json_data, true);

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Codenation - Aceleracao Java </title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>

<body>
    <div class="container-fluid">
        <div class="text-center">
            <h1>Codenation - Aceleracao Java</h1>
            <h2>Cifra de Cesar</h2>
        </div>

    </div>

    <div class="container my-4">
        <h2>Input</h2>
        <?php
        // var_dump($json_data);
        // $teste = $response["cifrado"];
        // echo "<br>" . ord('a') . " + 2 = " . chr(ord('a') + 2);
        // echo "<br>A =" . ord('A');
        // echo "<br>strtolower('A') = " . ord(strtolower('Aghgjghj'));
        // echo "<br>" . $teste[0];
        echo "<h3>" . $response["cifrado"] . "</h3>";
        ?>
    </div>

    <div class="container my-4">
        <h2>Output</h2>
        <?php
        $resposta = caesarCypher($response);
        echo "<h3>" . $resposta . "</h3>";

        file_put_contents('answer.json', $resposta);
        ?>
    </div>

    <div class="container my-4">
        <div class="row">
            <div class="col-md-6">
                <h2>Envio</h2>
                <form action="https://api.codenation.dev/v1/challenge/dev-ps/submit-solution?token=<?= $token?>"  method="POST" enctype="multipart/form-data" class="form-group">
                    <label for="answer">Envio do arquivo de resposta</label>
                    <input type="file" name="answer" id="answer" class="form-control">
                    <input type="submit" value="submit" class="btn btn-primary mt-4">
                </form>
            </div>
        </div>
    </div>


    <footer>

    </footer>

    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

</body>

</html>