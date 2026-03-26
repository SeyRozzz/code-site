    <?php
    // modifierarbre.php

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

    $stmt = $pdo->prepare("SELECT * FROM arbres WHERE id = ?");
    $stmt->execute([$id]);
    $arbre = $stmt->fetch();

    if (!$arbre) {
        header("Location: index.php?page=carte");
        exit();
    }

    if ($_SESSION['role'] === 'forestier') {
        // Forestier: vérifier qu'il a accès au projet
        $stmt = $pdo->prepare("
            SELECT COUNT(*) FROM projets_forestiers 
            WHERE id_projet = ? AND id_forestier = ?
        ");
        $stmt->execute([$arbre['id_projet'], $_SESSION['user_id']]);
        $has_access = $stmt->fetchColumn();
        
        if (!$has_access) {
            header("Location: index.php?page=carte&error=acces_refuse");
            exit();
        }
    } elseif ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'superadmin') {
        // Admin: accès à tous les arbres
    } else {
        // Rôle inconnu: refuser
        header("Location: index.php?page=carte&error=acces_refuse");
        exit();
    }

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
    if ($_SESSION['role'] === 'forestier') {
        $stmt = $pdo->prepare("
            SELECT p.id, p.nom 
            FROM projets p
            JOIN projets_forestiers pf ON p.id = pf.id_projet
            WHERE pf.id_forestier = ?
            ORDER BY p.nom ASC
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $projets = $stmt->fetchAll();
    } else {
        // Admins voient tous les projets
        $projets = $pdo->query("SELECT id, nom FROM projets")->fetchAll();
    }

    include 'modifierarbreVue.php';