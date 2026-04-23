# Stacy NB — thème enfant direct de SpicePress

Fork du thème [Stacy](https://wordpress.org/themes/stacy/) (SpiceThemes) reconfiguré
en thème enfant direct de SpicePress, pour contourner la limitation WordPress qui
interdit un child-of-child.

**Voir le guide complet** → [`README_STACY_NB.md`](README_STACY_NB.md)

## Structure du dépôt

```
.
├── stacy-nb/              Sources du thème (à déployer dans wp-content/themes/)
├── docker/                Stack Docker pour tester en local
├── demo/                  Contenu de démo (site vitrine "Cloud NB")
├── install.sh             Script d'installation automatique (WP-CLI)
├── README_STACY_NB.md     Guide complet : install, test, Customizer, rollback
└── README.md              Ce fichier
```

## Démarrage rapide

### Tester en local

```bash
cd docker/
docker compose up -d
docker exec stacy_nb_cli wp core install --url=http://localhost:8080 \
  --title="Test" --admin_user=admin --admin_password=admin \
  --admin_email=admin@test.local --skip-email
docker exec stacy_nb_cli wp theme install spicepress
docker exec stacy_nb_cli wp theme activate stacy-nb
```

Le site est sur http://localhost:8080 (admin `admin` / `admin`).

### Importer la démo Cloud NB

```bash
docker exec stacy_nb_cli wp plugin install wordpress-importer --activate
docker cp demo/cloud-nb-demo.xml stacy_nb_cli:/tmp/
docker exec stacy_nb_cli wp import /tmp/cloud-nb-demo.xml --authors=create
```

### Déployer en prod

```bash
./install.sh /chemin/vers/wordpress
```

## Licence

GPL v2 or later — basé sur Stacy (GPL v2) par SpiceThemes.
