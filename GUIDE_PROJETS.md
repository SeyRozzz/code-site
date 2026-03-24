# 🎯 Guide - Système de Gestion des Projets et Forestiers

## 📋 Vue d'ensemble

Le système permet:
- ✅ Les **ADMINS** de créer/gérer les projets forestiers
- ✅ Les **ADMINS** d'affecter les forestiers aux projets  
- ✅ Les **FORESTIERS** de voir uniquement les projets auxquels ils sont affectés
- ✅ Les **FORESTIERS** de modifier seulement les arbres de leurs projets

---

## 🔧 Installation

### Étape 1: Créer la table de liaison

Accédez à cette URL pour créer la table automatiquement:
```
http://locaris.alwaysdata.net/install_projets_forestiers.php
```

⚠️ **Important**: Cette table lie les forestiers aux projets:
```sql
CREATE TABLE projets_forestiers (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    id_projet INT(11) NOT NULL,
    id_forestier INT(11) NOT NULL,
    date_affectation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_affectation (id_projet, id_forestier),
    FOREIGN KEY (id_projet) REFERENCES projets(id) ON DELETE CASCADE,
    FOREIGN KEY (id_forestier) REFERENCES utilisateurs(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

---

## 📖 Mode d'emploi

### Pour les ADMINS:

#### 1️⃣ Créer un projet
- Aller au **Panel Admin** → **Gérer Projets**
- Entrer le nom du projet
- Cliquer **Créer**

#### 2️⃣ Affecter des forestiers au projet
- Aller au **Panel Admin** → **Gérer Projets**
- Cliquer sur **Affecter** du projet
- Sélectionner un forestier et cliquer **Affecter**

#### 3️⃣ Retirer un forestier du projet
- Aller au **Panel Admin** → **Gérer Projets** → **Affecter**
- Cliquer **Retirer** à côté du forestier

#### 4️⃣ Supprimer un projet
- Aller au **Panel Admin** → **Gérer Projets**
- Cliquer **Supprimer** du projet
- ⚠️ Cela supprimera également tous les arbres du projet

---

### Pour les FORESTIERS:

#### 1️⃣ Voir mes projets
- Aller à la **Carte**
- En haut, un menu déroulant montre **uniquement les projets affectés**

#### 2️⃣ Ajouter un arbre
- Cliquer **Ajouter un Arbre** (page `/ajouter`)
- Sélectionner un de **ses projets**
- Remplir les informations
- Cliquer **Enregistrer**

#### 3️⃣ Modifier un arbre
- Cliquer sur un arbre sur la carte
- Les forestiers ne peuvent modifier que les **arbres de leurs projets**

#### 4️⃣ C'est quoi les projets disponibles?
- Uniquement ceux **affectés par un admin**
- Les forestiers ne peuvent pas créer de projets seuls

---

## 🔐 Sécurité

### Vérifications appliquées:

| Action | Forestier | Admin |
|--------|-----------|-------|
| Voir projets | Seulement les siens | Tous |
| Créer projets | ❌ Non | ✅ Oui |
| Ajouter arbres | Seulement ses projets | Tous projets |
| Modifier arbres | Seulement ses arbres | Tous arbres |
| Affecter forestiers | ❌ Non | ✅ Oui |

### Sécurité intégrée:
- ✅ **CSRF tokens** sur tous les formulaires
- ✅ **Vérification des droits** avant chaque action
- ✅ **Prepared statements** pour les requêtes SQL (prévention SQL injection)
- ✅ **Hashage des mots de passe** (bcrypt)
- ✅ **Séparation admin/forestier** stricte

---

## 📝 Notes techniques

### Fichiers modifiés/créés:
- ✅ `install_projets_forestiers.php` - Script d'installation
- ✅ `gererProjet.php` - Gestion des projets (admin)
- ✅ `gererProjetVue.php` - Vue gestion des projets
- ✅ `affecterForestier.php` - Affectation des forestiers
- ✅ `affecterForestierVue.php` - Vue affectation
- ✅ `carte.php` - Modification filtrage par rôle
- ✅ `ajouter.php` - Vérification accès projet
- ✅ `modifierarbre.php` - Sécurisation accès forestier
- ✅ `index.php` - Ajout des routes
- ✅ `adminVue.php` - Ajout bouton gestion projets

### Flux de sécurité:

```
1. Forestier accède à /carte
   ↓
2. Script vérifie le rôle
   ↓
3. Si forestier → récupère UNIQUEMENT ses projets affectés
   ↓
4. Forestier sélectionne un projet
   ↓
5. Ajouter/modifier arbre:
   ↓
6. Vérification: est-ce un projet du forestier?
   ↓
7. Si non → Erreur d'accès / Si oui → Autoriser
```

---

## ❓ FAQ

**Q: Un forestier peut-il créer un projet?**
R: Non. Seuls les admis peuvent créer des projets via "Gérer Projets".

**Q: Un forestier peut-il voir les projets d'autres forestiers?**
R: Non. Il voit uniquement les projets auxquels il est affecté.

**Q: Que se passe-t-il si je supprime un projet?**
R: Tous les arbres du projet sont aussi supprimés, ainsi que toutes les affectations forestiers.

**Q: Un admin peut-il modifier les arbres?**
R: Oui, sans restriction. Un admin peut modifier n'importe quel arbre.

**Q: Comment retirer un forestier d'un projet?**
R: Panel Admin → Gérer Projets → Affecter du projet → Bouton "Retirer"

---

## 🚀 Prochaines étapes optionnelles

- [ ] Ajouter une **date de fin** aux affectations
- [ ] Créer un **rapport** des activités par projet/forestier
- [ ] Implémenter une **approbation** avant d'ajouter un arbre
- [ ] Ajouter des **permissions granulaires** (lecture seule, écriture, etc.)

