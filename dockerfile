FROM php:apache

# Apache-Module aktivieren
RUN a2enmod rewrite ssl

# Certbot und Cron installieren
RUN apt-get update && \
    apt-get install -y certbot python3-certbot-apache cron && \
    rm -rf /var/lib/apt/lists/*

# Nutzer wechseln und Arbeitsverzeichnis setzen
USER www-data
WORKDIR /var/www/html

# Kopiere die App-Dateien
COPY --chown=www-data:www-data zendo ./zendo
COPY --chown=www-data:www-data coins ./coins

# HTTP und HTTPS Ports freigeben
EXPOSE 80 443

# Automatische Zertifikat-Erneuerung per Cronjob einrichten
USER root
RUN echo "0 3 * * * root certbot renew --quiet" > /etc/cron.d/certbot-renew && \
    chmod 0644 /etc/cron.d/certbot-renew && \
    crontab /etc/cron.d/certbot-renew

# Starte Cron und Apache (mit erstmaliger Zertifikat-Erstellung)
CMD ["/bin/bash", "-c", "\
  certbot --apache --non-interactive --agree-tos \
    --email konstantin.meyer@gmail.com -d meyerk.de && \
  cron && apache2-foreground"]
