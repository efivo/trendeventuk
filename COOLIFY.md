# Instalimi në Coolify

Projekti ka tre pjesë: faqja React (Vite), API në PHP (`api/`) dhe databaza MySQL/MariaDB.
`docker-compose.yml` i ngre të gjitha bashkë:

| Shërbimi | Çfarë bën |
|---|---|
| `app` | Ndërton faqen React dhe e shërben me Apache + PHP 8.3 bashkë me `/api` (porta 80) |
| `db`  | MariaDB 11.4 — importon `database/trend_events.sql` automatikisht herën e parë |

## Hapat

1. **Coolify → Projects → + New → Resource → Public/Private Repository (GitHub)**
   - Repo: `xhentil-oss/trendevents.uk`, branch-i që ka këto skedarë
   - **Build Pack: `Docker Compose`**, Docker Compose Location: `/docker-compose.yml`
2. **Domain**: te shërbimi `app` vendos `https://trendevents.uk` (dhe/ose `https://www.trendevents.uk`).
   Coolify krijon vetë certifikatën SSL.
3. **Environment Variables** (Coolify i shfaq vetë nga compose-i) — të detyrueshme:

   | Variabla | Vlera |
   |---|---|
   | `DB_PASSWORD` | fjalëkalim i fortë për databazën |
   | `DB_ROOT_PASSWORD` | fjalëkalim tjetër i fortë (root) |
   | `SMTP_PASS` | fjalëkalimi i email-it `no-reply@trendevents.uk` — **pa këtë nuk dërgohen email-e** (kontejneri nuk ka server poste) |

   Opsionale (kanë vlera të paracaktuara): `SITE_URL`, `NOTIFY_EMAIL`, `MAIL_FROM`, `SMTP_HOST`,
   `SMTP_PORT`, `SMTP_USER`, `SMTP_VERIFY_SSL`, `ALLOWED_ORIGINS` (ndarë me presje), `DEBUG`, `DB_NAME`, `DB_USER`.
4. **Deploy**. Kontrollo: `https://trendevents.uk/api/` → `{"name":"Trend Events API","status":"ok"}`.

## Pas instalimit

- **Bëj një përdorues admin**: regjistrohu në faqe, pastaj Coolify → shërbimi `db` → Terminal:
  ```bash
  mariadb -uroot -p"$MARIADB_ROOT_PASSWORD" trendevents -e "UPDATE users SET role='admin' WHERE email='ti@example.com'"
  ```
- **DNS**: nëse përdor Cloudflare, vendos SSL mode **Full** (jo Flexible), që të funksionojë certifikata e Coolify.
- **Të dhënat ruhen në volume** (mbeten pas çdo redeploy):
  `db-data` (databaza), `uploads` (fotot e ngarkuara nga admini), `sessions` (hyrjet e përdoruesve).
- **Migrimi nga cPanel**: nëse do të dhënat aktuale, eksporto databazën nga phpMyAdmin dhe importoje në
  shërbimin `db`; kopjo edhe dosjen `public_html/uploads` në volumin `uploads`.
- `database/trend_events.sql` importohet **vetëm herën e parë** (kur volumi `db-data` është bosh).

## Testim lokal

```bash
cp .env.example .env   # ndrysho fjalëkalimet
docker compose up -d --build
```
Pastaj hap `http://localhost` (shto `ports: ["8080:80"]` te `app` nëse do port lokal).
