# Stacy NB — thème enfant direct de SpicePress

## Qu'est-ce que Stacy NB ?

**Stacy NB** est un fork du thème `Stacy` (SpiceThemes, disponible sur wordpress.org)
reconfiguré comme **thème enfant direct de SpicePress**.

WordPress ne permet pas les thèmes « petit-enfants » (un child theme ne peut pas
avoir lui-même un child theme). La solution était donc de **fusionner** toutes les
personnalisations de Stacy dans un unique thème enfant de SpicePress — c'est ce
qu'est Stacy NB.

### Conservations par rapport à Stacy 1.5.1

- Toutes les variantes de header (default, center) + les fichiers template associés
- Les deux layouts de blog (default grid, list)
- Les sections services et témoignages custom
- La palette de couleurs dynamique via le Customizer (`link_color`, `custom_color_enable`)
- Le panneau admin "About" avec onglets (Getting Started, Free vs Pro, etc.)
- Les traductions (fichier `.pot` renommé en `stacy-nb.pot`)

### Changements par rapport à Stacy 1.5.1

| Élément                      | Stacy                                 | Stacy NB                                 |
|------------------------------|---------------------------------------|------------------------------------------|
| Parent theme                 | SpicePress                            | SpicePress (direct)                      |
| Constantes PHP               | `STACY_*`                             | `STACY_NB_*`                             |
| Fonctions PHP                | `stacy_*()`                           | `stacy_nb_*()`                           |
| Text domain                  | `stacy`                               | `stacy-nb`                               |
| Options WP                   | `theme_mods_stacy`, `stacy_user`, ... | `theme_mods_stacy-nb`, `stacy_nb_user`   |
| Handles CSS                  | `stacy-parent-style`, ...             | `stacy-nb-parent-style`, ...             |
| Enqueue bogué `bootstrap`    | bug `ST_TEMPLATE_DIR` non défini      | supprimé (bootstrap venait déjà du parent) |
| Double enqueue child-style   | enqueue 2× (bug upstream)             | enqueue 1× avec dépendance parent        |

---

## Arborescence

```
child_stacy_nb_spice_direct/
├── install.sh              # Script d'installation automatique
├── README_STACY_NB.md      # Ce fichier
├── stacy-nb.zip            # Archive prête à uploader dans WP
├── docker/                 # Stack Docker pour tester en local (WP + MariaDB + WP-CLI)
│   └── docker-compose.yml
├── stacy-nb/               # Sources du thème
│   ├── style.css           # Header thème + CSS custom
│   ├── functions.php
│   ├── header.php
│   ├── footer.php
│   ├── index-news.php      # Section blog de la home
│   ├── screenshot.png
│   ├── readme.txt
│   ├── admin/              # Panneau "About" côté admin
│   ├── css/                # default.css, customizer.css, media-responsive.css, ...
│   ├── functions/          # Customizer controls + template-functions.php
│   ├── images/             # Aperçus pour le Customizer
│   └── languages/          # stacy-nb.pot
├── spicepress/             # Sources parent SpicePress 3.8.2 (référence)
├── spicepress.zip
├── stacy/                  # Sources original Stacy 1.5.1 (référence)
└── stacy.zip
```

---

## Installation automatique (recommandée)

### Prérequis

- Accès SSH au serveur WordPress
- **WP-CLI** installé sur le serveur (fortement recommandé)
  ```bash
  curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
  chmod +x wp-cli.phar && sudo mv wp-cli.phar /usr/local/bin/wp
  ```
- Thème parent **SpicePress** déjà présent dans `wp-content/themes/spicepress/`
  (sinon : `wp theme install spicepress`)

### Étapes

```bash
# 1. Transférer le dossier d'installation sur le serveur
scp -r child_stacy_nb_spice_direct/ user@serveur:/tmp/

# 2. Se connecter en SSH
ssh user@serveur

# 3. Lancer l'installation (adapter le chemin de WordPress)
cd /tmp/child_stacy_nb_spice_direct/
chmod +x install.sh
./install.sh /var/www/html
```

### Ce que fait le script

1. Vérifie que `wp-config.php` existe à la racine fournie
2. Détecte WP-CLI (et propose un mode dégradé sinon)
3. **Backup** : export DB + tar.gz des thèmes dans un dossier horodaté
4. Déploie `stacy-nb/` dans `wp-content/themes/stacy-nb/`
5. Ajuste les permissions au propriétaire de `wp-content/`
6. Vérifie que SpicePress est bien installé
7. Copie `theme_mods_stacy` → `theme_mods_stacy-nb` si le site avait déjà `stacy`
8. Propose d'activer le thème

---

## Tester en local avec Docker (recommandé avant prod)

Le dossier `docker/` contient une stack complète pour vérifier que le thème
s'active sans erreur avant de le déployer sur le site réel.

### Démarrer la stack

```bash
cd docker/
docker compose up -d
```

3 conteneurs démarrent :
- `stacy_nb_db` : MariaDB 11
- `stacy_nb_wp` : WordPress 6.9+ / Apache / PHP 8.2 (avec `WP_DEBUG=1`)
- `stacy_nb_cli` : WP-CLI (exécution via `docker exec`)

Le thème `stacy-nb/` est **monté en volume** : toute modification des fichiers
est visible immédiatement dans le conteneur (pas besoin de rebuild).

### Première installation

```bash
# 1. Installer WordPress
docker exec stacy_nb_cli wp core install \
  --url=http://localhost:8080 --title="Stacy NB Test" \
  --admin_user=admin --admin_password=admin \
  --admin_email=admin@test.local --skip-email

# 2. Installer SpicePress (parent) depuis wordpress.org
docker exec stacy_nb_cli wp theme install spicepress

# 3. Activer Stacy NB
docker exec stacy_nb_cli wp theme activate stacy-nb
```

### URLs de test

| Page | URL |
|---|---|
| Front office | http://localhost:8080/ |
| Admin | http://localhost:8080/wp-admin/ (`admin` / `admin`) |
| Customizer | http://localhost:8080/wp-admin/customize.php |
| Thèmes | http://localhost:8080/wp-admin/themes.php |

### Scan d'erreurs PHP

```bash
# Errors liées au thème (doit être vide)
docker logs stacy_nb_wp 2>&1 | grep -iE "PHP (Fatal|Parse|Warning)" | grep "themes/stacy-nb"

# Debug log WordPress (si présent)
docker exec stacy_nb_wp cat /var/www/html/wp-content/debug.log 2>/dev/null || echo "(pas d'erreur)"
```

### Test rapide : bascule des variantes

```bash
docker exec stacy_nb_cli wp theme mod set header_type center   # ou default
docker exec stacy_nb_cli wp theme mod set blog_type list       # ou default
```

Puis recharger http://localhost:8080/ pour voir le résultat.

### Test WooCommerce

```bash
docker exec stacy_nb_cli wp plugin install woocommerce --activate
docker exec stacy_nb_cli wp post create --post_type=product \
  --post_title="Produit test" --post_status=publish
```
Vérifier : boutique, panier, checkout, compte (URLs `/?page_id=6..9`).

### Arrêter / Nettoyer

```bash
docker compose down        # garde les volumes (DB + WP)
docker compose down -v     # efface tout, repart propre
```

---

## Utiliser le Customizer

Le **Customizer** (*Apparence > Personnaliser*) est l'outil live de WordPress
pour modifier l'apparence **sans toucher au code**. Panneau de réglages à
gauche, aperçu en direct à droite. Rien n'est appliqué tant que tu n'as pas
cliqué sur **Publier**.

### Sections disponibles dans Stacy NB

Sections standard (héritées de SpicePress) :
- **Identité du site** — logo, nom, slogan, favicon
- **Couleurs** — couleur d'accent (`link_color`)
- **Menus** — assigner les menus aux emplacements
- **Widgets** — sidebar, footer
- **Page d'accueil** — statique ou derniers articles
- **CSS additionnel** — champ libre pour du CSS ponctuel

Sections spécifiques Stacy NB :
- **Header Layout Settings** — variante du header :
  - `default` : logo à gauche/centre/droite + menu classique
  - `center` : logo centré sur fond blanc + navbar sombre dessous
- **Blog Layout Settings** — variante de la section blog :
  - `default` : grille 3 colonnes
  - `list` : liste avec miniature à gauche
- **Custom color enable** — active la couleur custom (sinon palette par défaut)
- **Menu breakpoint** — largeur (px) de bascule desktop/mobile (défaut 1100)

⚠ La section *Copyright footer* du parent SpicePress a été **supprimée** par
Stacy NB (héritage de Stacy upstream, ligne 137-142 de `functions.php`).
Le copyright est codé en dur dans `footer.php`.

### Workflow typique

1. *Apparence > Personnaliser*
2. Modifier les réglages, voir l'aperçu en direct
3. Icônes mobile/tablette/desktop en bas → prévisualiser les tailles
4. **Publier** (en haut à gauche) pour appliquer au site public

Les réglages sont stockés dans `wp_options` sous la clé
`theme_mods_stacy-nb` — c'est cette clé que `install.sh` duplique depuis
`theme_mods_stacy` lors de la migration.

---

## Installation manuelle (sans WP-CLI)

### 1. Backup

```bash
# Backup DB
mysqldump -u WP_USER -p WP_DB > backup-db-$(date +%F).sql

# Backup themes
tar -czf backup-themes-$(date +%F).tar.gz -C /var/www/html/wp-content themes
```

### 2. Upload du zip

**Via l'admin WordPress** :
- *Apparence > Thèmes > Ajouter > Téléverser un thème*
- Sélectionner `stacy-nb.zip`
- *Installer maintenant* (ne pas encore activer)

**Via SSH** :
```bash
unzip stacy-nb.zip -d /var/www/html/wp-content/themes/
chown -R www-data:www-data /var/www/html/wp-content/themes/stacy-nb/
```

### 3. Migration des theme_mods (seulement si tu utilisais déjà `stacy`)

Dans phpMyAdmin ou via le CLI MySQL :

```sql
-- Vérifier que stacy avait des options
SELECT * FROM wp_options WHERE option_name = 'theme_mods_stacy';

-- Copier vers stacy_nb
INSERT INTO wp_options (option_name, option_value, autoload)
SELECT 'theme_mods_stacy-nb', option_value, autoload
FROM wp_options
WHERE option_name = 'theme_mods_stacy'
ON DUPLICATE KEY UPDATE option_value = VALUES(option_value);
```

⚠ Remplacer `wp_` par le préfixe de ta base si différent (voir `$table_prefix` dans
`wp-config.php`).

### 4. Activation

*Apparence > Thèmes > Stacy NB > Activer*

---

## Étapes post-installation

1. **Menus** — *Apparence > Menus > Gérer les emplacements*
   Les emplacements de menus doivent être réassignés (WP ne les conserve pas
   entre thèmes).

2. **Widgets** — *Apparence > Widgets*
   Vérifier que les widgets des zones (sidebar, footer) sont à la bonne place.

3. **Customizer** — *Apparence > Personnaliser*
   Revue rapide : logo, couleurs, variantes header/blog, footer.

4. **Tests visuels** — Dans cet ordre :
   - Page d'accueil (desktop + mobile)
   - Une page statique
   - Un article
   - Une catégorie
   - La page de recherche
   - Le 404
   - Si WooCommerce est présent : boutique + produit + panier

5. **Plugins critiques** — vérifier qu'aucun plugin ne dépendait du text domain
   `stacy` ou de fonctions `stacy_*` (rare mais possible avec des plugins maison).

---

## Rollback

Si un problème bloquant apparaît :

### Rollback rapide (revenir à stacy)

```bash
# Via WP-CLI
wp theme activate stacy

# Ou via l'admin : Apparence > Thèmes > Stacy > Activer
```

Les `theme_mods_stacy` d'origine n'ont **pas** été modifiées par l'installation,
donc la bascule est immédiate et sans perte.

### Rollback complet (restaurer DB + thèmes)

```bash
# Retrouver le dossier backup créé par install.sh
ls -lt backup-*/

# Restaurer la DB
wp db import backup-YYYYMMDD-HHMMSS/db-before-install.sql

# Restaurer les thèmes
rm -rf /var/www/html/wp-content/themes
tar -xzf backup-YYYYMMDD-HHMMSS/themes-before-install.tar.gz \
    -C /var/www/html/wp-content/
```

---

## Désinstallation propre

Après quelques semaines sans régression :

```bash
# Supprimer l'ancien thème stacy
wp theme delete stacy

# Nettoyer les vieilles options
wp option delete theme_mods_stacy
wp option delete stacy_user
wp option delete stacy_user_with_1_3_7
```

---

## Dépannage

| Symptôme                                     | Cause probable                             | Solution                                                  |
|----------------------------------------------|--------------------------------------------|-----------------------------------------------------------|
| Thème invisible dans la liste WP             | Mauvais chemin d'extraction                | Vérifier `ls wp-content/themes/stacy-nb/style.css`        |
| « Thème parent manquant »                    | SpicePress pas installé                    | `wp theme install spicepress`                             |
| Site cassé (écran blanc) après activation    | Conflit avec un plugin maison              | Désactiver les plugins un par un, lire `debug.log`        |
| Couleurs du Customizer perdues               | `theme_mods` pas migrées                   | Rejouer le SQL de la section *Migration des theme_mods*   |
| Menus disparus                               | Normal — les emplacements doivent être réassignés | *Apparence > Menus > Gérer les emplacements*        |
| Traductions manquantes                       | Text domain changé (`stacy` → `stacy-nb`)  | Regénérer un `.po` à partir de `languages/stacy-nb.pot`   |

Pour activer le mode debug WordPress (à mettre dans `wp-config.php`) :
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```
Les erreurs apparaîtront dans `wp-content/debug.log`.

---

## Licence

Stacy NB hérite de la licence de Stacy : **GPL v2 or later**.

- Stacy © SpiceThemes — https://spicethemes.com/stacy-wordpress-theme/
- SpicePress © SpiceThemes — https://spicethemes.com/spicepress/
