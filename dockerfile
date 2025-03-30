FROM php:apache

# Enable Apache modules for rewrite and SSL
RUN a2enmod rewrite ssl

# Install Certbot, cron, and dependencies
RUN apt-get update && \
    apt-get install -y certbot python3-certbot-apache cron && \
    rm -rf /var/lib/apt/lists/*

# Set user and working directory
USER www-data
WORKDIR /var/www/html

# Copy application files (current folder to 'zendo') and coins folder
COPY --chown=www-data:www-data . ./zendo
COPY --chown=www-data:www-data coins ./coins

# Expose ports for HTTP and HTTPS
EXPOSE 80 443

# Set up automatic certificate renewal cron job and run Apache in foreground
USER root
RUN echo "0 3 * * * root certbot renew --quiet" > /etc/cron.d/certbot-renew && \
    chmod 0644 /etc/cron.d/certbot-renew && \
    crontab /etc/cron.d/certbot-renew

CMD ["/bin/bash", "-c", "\
  certbot --apache --non-interactive --agree-tos \
  --email konstantin.meyer@gmail.com -d meyerk.de && \
  cron && apache2-foreground"]
