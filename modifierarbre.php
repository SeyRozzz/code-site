    <?php
    // modifier.php
    // ✅ ADAPTÉ À LA NOUVELLE BDD : Gestion id_projet + id_createur

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    require_once 'config.php';

    // SÉCURITÉ : Vérification de la connexion
    if (!isset($_SESSION['user_id'])) {
        header("Location: index.php?page=login");
        exit();
    }

    $id = (int)($_GET['id'] ?? 0);
    $message = "";

    if ($id <= 0) {
        header("Location: index.php?page=carte");
        exit();
    }

    // 1. Récupération des infos de l'arbre et vérification des droits
    $stmt = $pdo->prepare("SELECT * FROM arbres WHERE id = ?");
    $stmt->execute([$id]);
    $arbre = $stmt->fetch();

    if (!$arbre) {
        header("Location: index.php?page=carte");
        exit();
    }

    // SÉCURITÉ : Seul le créateur ou un admin peut modifier (Audit de sécurité)
    if ($_SESSION['role'] !== 'admin' && $_SESSION['user_id'] != $arbre['id_createur']) {
        header("Location: index.php?page=carte&error=acces_refuse");
        exit();
    }

    // 2. Traitement du formulaire (POST)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $essence = trim($_POST['essence'] ?? '');
        $hauteur = trim($_POST['hauteur'] ?? '');
        $diametre = trim($_POST['diametre'] ?? '');
        $lat = trim($_POST['latitude'] ?? '');
        $lon = trim($_POST['longitude'] ?? '');
        $id_projet = (int)($_POST['id_projet'] ?? 0); // Nouveau champ id_projet

        if (empty($essence) || empty($lat) || empty($lon) || $id_projet <= 0) {
            $message = "Tous les champs (y compris le projet) sont obligatoires.";
        } elseif (!is_numeric($lat) || !is_numeric($lon)) {
            $message = "Les coordonnées doivent être des nombres.";
        } else {
            try {
                // ✅ UPDATE incluant le lien vers le projet (image_fde60b.png)
                $sql = "UPDATE arbres 
                        SET essence=?, hauteur=?, diametre=?, latitude=?, longitude=?, id_projet=? 
                        WHERE id=?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$essence, $hauteur, $diametre, $lat, $lon, $id_projet, $id]);
                
                header("Location: index.php?page=carte&msg=succes_modif");
                exit();
            } catch (PDOException $e) {
                error_log("Erreur BDD: " . $e->getMessage());
                $message = "Erreur technique lors de la mise à jour.";
            }
        }
    }

    // 3. Récupérer la liste des projets pour le formulaire (pour pouvoir changer l'arbre de projet)
    $projets = $pdo->query("SELECT id, nom FROM projets")->fetchAll();

    include 'modifierarbreVue.php';