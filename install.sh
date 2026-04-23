#!/usr/bin/env bash
# =============================================================================
# install.sh — Installation du thème Stacy NB sur un site WordPress
#
# Prérequis :
#   - Accès SSH au serveur du site WordPress
#   - WP-CLI installé (https://wp-cli.org/)      [recommandé]
#   - OU : accès à la base MySQL + commande `mysql` (mode manuel)
#
# Usage :
#   ./install.sh <chemin-racine-wordpress>
#   ex: ./install.sh /var/www/html
#
# Ce que fait le script :
#   1. Vérifie les prérequis (WP-CLI, archive zip, racine WP)
#   2. Sauvegarde la base (wp db export)
#   3. Sauvegarde l'arborescence wp-content/themes/
#   4. Déploie stacy-nb dans wp-content/themes/stacy-nb/
#   5. Copie theme_mods_stacy -> theme_mods_stacy-nb (si présent)
#   6. Active le thème stacy-nb
#   7. Affiche les étapes manuelles restantes
#
# Rollback : voir README_STACY_NB.md section « Rollback »
# =============================================================================

set -euo pipefail

# --- Couleurs ----------------------------------------------------------------
RED=$'\033[0;31m'
GREEN=$'\033[0;32m'
YELLOW=$'\033[1;33m'
BLUE=$'\033[0;34m'
NC=$'\033[0m'

info()  { printf "%b[INFO]%b  %s\n" "$BLUE"   "$NC" "$*"; }
ok()    { printf "%b[OK]%b    %s\n" "$GREEN"  "$NC" "$*"; }
warn()  { printf "%b[WARN]%b  %s\n" "$YELLOW" "$NC" "$*"; }
error() { printf "%b[ERR]%b   %s\n" "$RED"    "$NC" "$*" >&2; }

# --- Arguments ---------------------------------------------------------------
if [ $# -lt 1 ]; then
    error "Usage : $0 <chemin-racine-wordpress>"
    error "Exemple : $0 /var/www/html"
    exit 1
fi

WP_ROOT="$1"
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ZIP_PATH="$SCRIPT_DIR/stacy-nb.zip"
THEME_SRC_DIR="$SCRIPT_DIR/stacy-nb"
BACKUP_DIR="$SCRIPT_DIR/backup-$(date +%Y%m%d-%H%M%S)"
THEME_SLUG="stacy-nb"
OLD_THEME_SLUG="stacy"

# --- Vérifications -----------------------------------------------------------
info "Vérification de la racine WordPress : $WP_ROOT"
if [ ! -f "$WP_ROOT/wp-config.php" ]; then
    error "wp-config.php introuvable dans $WP_ROOT"
    error "Ce n'est pas une installation WordPress valide."
    exit 1
fi
ok "Racine WordPress valide."

if [ ! -f "$ZIP_PATH" ] && [ ! -d "$THEME_SRC_DIR" ]; then
    error "Ni $ZIP_PATH ni $THEME_SRC_DIR introuvables."
    exit 1
fi

if ! command -v wp >/dev/null 2>&1; then
    warn "WP-CLI non détecté. Le script fonctionnera en mode dégradé :"
    warn "  - pas de backup DB automatique"
    warn "  - pas de copie theme_mods automatique"
    warn "  - pas d'activation automatique"
    warn "Il est fortement recommandé d'installer WP-CLI : https://wp-cli.org/"
    read -r -p "Continuer quand même ? [y/N] " answer
    [[ "$answer" =~ ^[Yy]$ ]] || exit 1
    WP_CLI_AVAILABLE=false
else
    WP_CLI_AVAILABLE=true
    ok "WP-CLI détecté : $(wp --version 2>/dev/null || echo 'version inconnue')"
fi

# --- Backup ------------------------------------------------------------------
info "Création du dossier de backup : $BACKUP_DIR"
mkdir -p "$BACKUP_DIR"

if [ "$WP_CLI_AVAILABLE" = true ]; then
    info "Export de la base de données…"
    (cd "$WP_ROOT" && wp db export "$BACKUP_DIR/db-before-install.sql" --allow-root 2>/dev/null || \
     wp db export "$BACKUP_DIR/db-before-install.sql" --path="$WP_ROOT")
    ok "Base exportée : $BACKUP_DIR/db-before-install.sql"
fi

info "Backup du dossier wp-content/themes/ (tar.gz)…"
tar -czf "$BACKUP_DIR/themes-before-install.tar.gz" -C "$WP_ROOT/wp-content" themes 2>/dev/null
ok "Themes sauvegardés : $BACKUP_DIR/themes-before-install.tar.gz"

# --- Déploiement -------------------------------------------------------------
TARGET_DIR="$WP_ROOT/wp-content/themes/$THEME_SLUG"

if [ -d "$TARGET_DIR" ]; then
    warn "Le dossier $TARGET_DIR existe déjà."
    read -r -p "Écraser ? [y/N] " answer
    if [[ ! "$answer" =~ ^[Yy]$ ]]; then
        info "Annulé par l'utilisateur."
        exit 0
    fi
    rm -rf "$TARGET_DIR"
fi

info "Déploiement du thème vers $TARGET_DIR"
if [ -f "$ZIP_PATH" ]; then
    unzip -q "$ZIP_PATH" -d "$WP_ROOT/wp-content/themes/"
else
    cp -r "$THEME_SRC_DIR" "$TARGET_DIR"
fi
ok "Thème déployé."

# Ajustement des permissions (aligne sur le propriétaire du wp-content parent)
if [ "$(id -u)" -eq 0 ]; then
    PARENT_OWNER=$(stat -c '%U:%G' "$WP_ROOT/wp-content")
    chown -R "$PARENT_OWNER" "$TARGET_DIR"
    info "Permissions alignées sur $PARENT_OWNER."
fi

# --- Vérification que SpicePress est présent ---------------------------------
if [ ! -d "$WP_ROOT/wp-content/themes/spicepress" ]; then
    warn "SpicePress (thème parent) n'est PAS présent dans wp-content/themes/."
    warn "Stacy NB ne fonctionnera pas sans lui."
    warn "Installe-le depuis l'admin WP ou via : wp theme install spicepress --activate=false"
fi

# --- Migration des theme_mods ------------------------------------------------
if [ "$WP_CLI_AVAILABLE" = true ]; then
    info "Vérification de theme_mods_$OLD_THEME_SLUG …"
    OLD_MODS=$(cd "$WP_ROOT" && wp option get "theme_mods_$OLD_THEME_SLUG" --format=json 2>/dev/null || echo "")

    if [ -n "$OLD_MODS" ] && [ "$OLD_MODS" != "false" ] && [ "$OLD_MODS" != "[]" ]; then
        info "Options Customizer de stacy trouvées — copie vers stacy-nb…"
        NEW_MODS=$(cd "$WP_ROOT" && wp option get "theme_mods_$THEME_SLUG" --format=json 2>/dev/null || echo "")
        if [ -n "$NEW_MODS" ] && [ "$NEW_MODS" != "false" ] && [ "$NEW_MODS" != "[]" ]; then
            warn "theme_mods_$THEME_SLUG existe déjà (non écrasé)."
            warn "Pour forcer : wp option delete theme_mods_$THEME_SLUG puis relance ce script."
        else
            (cd "$WP_ROOT" && wp option update "theme_mods_$THEME_SLUG" "$OLD_MODS" --format=json)
            ok "theme_mods copiées : $OLD_THEME_SLUG -> $THEME_SLUG"
        fi
    else
        info "Pas de theme_mods_$OLD_THEME_SLUG existantes — rien à copier."
    fi
fi

# --- Activation --------------------------------------------------------------
if [ "$WP_CLI_AVAILABLE" = true ]; then
    read -r -p "Activer le thème Stacy NB maintenant ? [y/N] " answer
    if [[ "$answer" =~ ^[Yy]$ ]]; then
        (cd "$WP_ROOT" && wp theme activate "$THEME_SLUG")
        ok "Thème Stacy NB activé."
    else
        info "Activation différée. Active-le depuis Apparence > Thèmes."
    fi
fi

# --- Fin ---------------------------------------------------------------------
cat <<EOF

${GREEN}===========================================================${NC}
${GREEN}  Installation terminée${NC}
${GREEN}===========================================================${NC}

  Thème déployé dans : $TARGET_DIR
  Backup             : $BACKUP_DIR

  Étapes manuelles restantes :
  1. Vérifier l'affichage du site (accueil, page, article)
  2. Réassigner les menus : Apparence > Menus
  3. Vérifier les widgets : Apparence > Widgets
  4. Tester sur mobile et desktop
  5. Après 2-3 semaines sans régression : supprimer l'ancien thème stacy

  En cas de problème, voir README_STACY_NB.md section « Rollback ».

EOF
