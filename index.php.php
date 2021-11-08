<?php
//Verificar se há dados
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $id = (isset($_POST["id"]) && $_POST["id"] != null) ? $_POST["id"] : "";
    $nome = (isset($_POST["nome"]) && $_POST["nome"] != null) ? $_POST["nome"] : "";
    $presente = (isset($_POST["presente"]) && $_POST["presente"] != null) ? $_POST["presente"] : "";
    $celular = (isset($_POST["celular"]) && $_POST["celular"] != null) ? $_POST["celular"] : NULL;
} else if (!isset($id)) {
    $id = (isset($_GET["id"]) && $_GET["id"] != null) ? $_GET["id"] : "";
    $nome = NULL;
    $presente = NULL;
    $celular = NULL;
}


//Conexão
try {
    $conexao = new PDO("mysql:host=localhost; dbname=oficial", "root");
    $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conexao->exec("set names utf8");
} catch (PDOException $erro) {
  echo "Erro na conexão:" . $erro->getMessage();
}


///Realizar o cadastro
if (isset($_REQUEST["act"]) && $_REQUEST["act"] == "save" && $nome != "") {
    try {
        $stmt = $conexao->prepare("INSERT INTO pessoa_presente (nome, presente, celular) VALUES (?, ?, ?)");
        $stmt->bindParam(1, $nome);
        $stmt->bindParam(2, $presente);
        $stmt->bindParam(3, $celular);
         
        if ($stmt->execute()) {
            if ($stmt->rowCount() > 0) {
                echo "Cadastrados Realizado com sucesso!";
                $id = null;
                $nome = null;
                $presente = null;
                $celular = null;
            } else {
                echo "Erro ao Realizar o Cadastro";
            }
        } else {
               throw new PDOException("Erro: Não foi possível executar a declaração sql");
        }
    } catch (PDOException $erro) {
        echo "Erro: " . $erro->getMessage();
    }
}


//UPDATE
if (isset($_REQUEST["act"]) && $_REQUEST["act"] == "upd" && $id != "") {
    try {
        $stmt = $conexao->prepare("SELECT * FROM pessoa_presente WHERE id = ?");
        $stmt->bindParam(1, $id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            $rs = $stmt->fetch(PDO::FETCH_OBJ);
            $id = $rs->id;
            $nome = $rs->nome;
            $presente = $rs->presente;
            $celular = $rs->celular;
        } else {  
            throw new PDOException("Erro: Não foi possível executar a declaração sql");
        }
    } catch (PDOException $erro) {
        echo "Erro: ".$erro->getMessage();
    }
}


// DELETE
if (isset($_REQUEST["act"]) && $_REQUEST["act"] == "del" && $id != "") {
    try {
        $stmt = $conexao->prepare("DELETE FROM pessoa_presente WHERE id = ?");
        $stmt->bindParam(1, $id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            echo "Registo foi excluído com êxito";
            $id = null;
        } else {
            throw new PDOException("Erro: Não foi possível executar a declaração sql");
        }
    } catch (PDOException $erro) {
        echo "Erro: ".$erro->getMessage();
    }
}
?>


<!-- Formulário -->
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Chá de Casa Nova</title>
    </head>
    <body>
        <form action="?act=save" method="POST" name="form1" >
            <h1>Chá de Casa Nova</h1>
            <hr>
            <input type="hidden" name="id" <?php
            if (isset($id) && $id != null || $id != "") {
                echo "value=\"{$id}\"";
            }
            ?> />
            Nome:
            <input type="text" name="nome" <?php
            if (isset($nome) && $nome != null || $nome != ""){
                echo "value=\"{$nome}\"";
            } ?>/>
            Presente:
            <input type="text" name="presente" <?php 
            if (isset($presente) && $presente != null || $nome != "") {
                echo "value=\"{$presente}\"";
            }   ?> />
            Celular:
            <input type="text" name="celular" maxlenght="15" <?php 
            if (isset($celular) && $celular != null || $nome != "") {
                echo "value=\"{$celular}\"";
            }   ?> />

            <input type="submit" value="Salvar" />
            <input type="reset" value="Novo" />
            <hr>
       </form>


       <!-- Listagem -->
       <table border="1" width="100%">
            <tr>
                <th>Nome</th>
                <th>Presente</th>
                <th>Celular</th>
                <th>Ações</th>
            </tr>
            <?php
                try {
                    $stmt = $conexao->prepare("SELECT * FROM pessoa_presente");
                    if ($stmt->execute()) {
                        while ($rs = $stmt->fetch(PDO::FETCH_OBJ)) {
                            echo "<tr>";
                            echo "<td>".$rs->nome."</td>
                                <td>".$rs->presente."</td>
                                <td>".$rs->celular."</td>
                                <td><center><a href=\"?act=upd&id=".$rs->id."\">[Alterar]</a>"
                                ."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"
                                ."<a href=\"?act=del&id=".$rs->id."\">[Excluir]</a></center></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "Erro: Não foi possível recuperar os dados do banco de dados";
                    }
                } catch (PDOException $erro) {
                    echo "Erro: ".$erro->getMessage();
                }
            ?>
        </table>
    </body>
</html>