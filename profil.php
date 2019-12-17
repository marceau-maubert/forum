<?php
    if (!isset($_GET["id"])) {
        header("Location: index.php");
        die;
    }

    session_start();

    if (isset($_SESSION["user"])) {
        extract($_SESSION["user"]);
    }

    $rankNames = [
        1 => "Membre",
        2 => "Modérateur",
        3 => "Administrateur",
    ];

    $db = new PDO("mysql:host=localhost;dbname=forum", "root", "");

    if (isset($_POST["rang"]) && is_numeric($_POST["rang"]) && $_POST["rang"] <= 3 && $_POST["rang"] >= 1 && $id_rang >= 3) {
        $request = "UPDATE utilisateurs SET id_rang = ? WHERE id = ?";
        $stmt = $db->prepare($request);
        $editSuccess = $stmt->execute([$_POST["rang"], $_GET["id"]]);
        if (!$editSuccess) {
            echo "Erreur MySQL: {$stmt->errorInfo()[2]}";
            die;
        }
    }

    $request = "SELECT * FROM utilisateurs WHERE id = ?";
    $stmt = $db->prepare($request);
    $success = $stmt->execute([$_GET["id"]]);
    $results = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($success) {
        $user = $results;
    } else {
        echo "Erreur MySQL: {$stmt->errorInfo()[2]}";
        die;
    }
?>

<title>Profil</title>
<h1>Profil</h1>
<a href="index.php">Retour</a>

<?php
    if (isset($user)) { ?>
<?php   if (isset($editSuccess) && $editSuccess) { ?>
            <h4 class="success">Modifications enregistrées avec succès !</h4>
<?php   } ?>

        <h2><?= $user["login"] ?></h2>

<?php   if ($id_rang >= 3 && $user["id"] != $id) { ?>
            <form method="post">
                <select name="rang" onchange="this.form.submit()">
                <?php foreach ($rankNames as $k => $v) { ?>
                    <option value="<?= $k ?>" <?= $user["id_rang"] == $k ? "selected" : "" ?>><?= $v ?></option>
                <?php } ?>
                </select>
            </form>
<?php   } else { ?>
            <h3><?= $rankNames[$user["id_rang"]] ?? "Rang inconnu" ?></h3>
<?php   } ?>

<?php   if (isset($user["email"])) { ?>
            <p><b>E-mail</b>: <code><?= $user["email"] ?></code></p>
<?php   } ?>

<?php   if (isset($user["naissance"])) { ?>
            <p><b>Né(e) le</b>: <code><?= $user["naissance"] ?></code></p>
<?php   } ?>

<?php   if (isset($user["bio"])) { ?>
            <p><b>Biographie</b>:</p>
            <p style="padding: 0.5em; display: inline; border: 1px solid black; border-radius: 2px;">
                <code><?= $user['bio'] ?></code>
            </p>
<?php   } ?>

        <p><b>Inscrit le</b>: <code><?= $user["inscription"] ?></code></p>

<?php   if ($user["id"] == $id) { ?>
            <a href="modifier_profil.php">Modifier</a>
<?php   }
    }
?>