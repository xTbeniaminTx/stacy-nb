# Démo Cloud NB

Contenu d'exemple pour le thème Stacy NB — site vitrine fictif d'un service cloud.

## Fichiers

- `cloud-nb-demo.xml` — export WordPress (WXR) : 5 pages, 6 articles, 3 catégories,
  1 menu
- `theme_mods_stacy-nb.json` — réglages Customizer (header `center`, blog `list`,
  copyright personnalisé)

## Contenu

### Pages
1. **Accueil** — hero + grille 3 services
2. **Nos services** — hébergement / stockage / calcul
3. **Tarifs et facturation** — 3 offres (Starter / Pro / Business)
4. **Apprendre le cloud** — IaaS/PaaS/SaaS, FAQ
5. **Contact**

### Articles
- *Tutoriels* : "Le cloud en 5 minutes" · "Pourquoi on facture à l'heure"
- *Annonces* : "Snapshots automatiques" · "Migration PHP 8.3"
- *Ludique* : "Quiz : connais-tu le cloud ?" · "Jeu : construis ton infra"

### Menu `primary`
Accueil → Services → Tarifs → Apprendre → Contact

## Import sur un site neuf

### Via WP-CLI (recommandé)

```bash
# 1. Plugin importer WordPress
wp plugin install wordpress-importer --activate

# 2. Import du contenu
wp import /chemin/vers/cloud-nb-demo.xml --authors=create

# 3. Import des réglages Customizer (theme_mods)
MODS=$(cat /chemin/vers/theme_mods_stacy-nb.json)
wp option update theme_mods_stacy-nb "$MODS" --format=json

# 4. Définir la page d'accueil (trouver l'ID)
HOME_ID=$(wp post list --post_type=page --name=accueil --field=ID)
wp option update show_on_front page
wp option update page_on_front $HOME_ID

# 5. Assigner le menu
MENU_ID=$(wp menu list --fields=term_id,name | grep -i principal | awk '{print $1}')
wp menu location assign $MENU_ID primary
```

### Via l'admin WordPress

1. *Outils > Importer > WordPress* (installer le plugin si absent)
2. Uploader `cloud-nb-demo.xml`
3. Assigner les auteurs à ton compte
4. *Apparence > Menus > Gérer les emplacements* → Menu principal = Principal
5. *Apparence > Personnaliser* → Réglages page d'accueil → statique → Accueil
6. Pour les theme_mods : via phpMyAdmin, exécuter
   ```sql
   UPDATE wp_options SET option_value = '<contenu de theme_mods_stacy-nb.json>'
   WHERE option_name = 'theme_mods_stacy-nb';
   ```

## Reset

Pour repartir d'une démo propre :

```bash
wp db reset --yes
wp core install --url=... --title="Cloud NB" --admin_user=admin --admin_password=admin --admin_email=admin@test.local
wp theme install spicepress
wp theme activate stacy-nb
# puis ré-importer la démo
```
